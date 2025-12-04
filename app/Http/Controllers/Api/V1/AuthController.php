<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Device;
use App\Services\OtpService;
use App\Helpers\EmailHelper;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'gender' => 'required|in:male,female,other',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street_address' => 'required|string|max:500',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'gender' => $request->gender,
            'state' => $request->state,
            'city' => $request->city,
            'street_address' => $request->street_address,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_tier' => 'tier1', // Default to tier1
            // api_key and app_key will be auto-generated in User model boot()
        ]);

        // Assign default tier1 role
        $user->assignRole('tier1');

        // Create wallet for user
        $user->wallet()->create([
            'currency' => 'NGN',
            'balance' => 0,
            'status' => 'active',
        ]);

        // Register device if provided
        if ($request->device_id) {
            Device::create([
                'user_id' => $user->id,
                'device_id' => $request->device_id,
                'device_name' => $request->device_name,
                'device_type' => $request->device_type,
                'last_ip' => $request->ip(),
                'last_seen' => now(),
            ]);
        }

        // Generate and send OTP for email verification
        if ($user->email) {
            try {
                \Log::info("=== REGISTRATION OTP PROCESS START ===");
                \Log::info("User ID: {$user->id}, Email: {$user->email}");
                
                $otpCode = OtpService::generate($user, 'register', 1); // 1 minute expiration
                \Log::info("Generated OTP for user {$user->id}: {$otpCode}");
                
                \Log::info("Calling EmailHelper::sendRegisterOtp...");
                EmailHelper::sendRegisterOtp($user, $otpCode, $request, 1);
                \Log::info("✓ OTP email sent successfully to {$user->email} for registration");
                \Log::info("=== REGISTRATION OTP PROCESS END ===");
            } catch (\Exception $e) {
                \Log::error("✗ FAILED to send OTP email to {$user->email}");
                \Log::error("Error message: " . $e->getMessage());
                \Log::error("Error class: " . get_class($e));
                \Log::error("Stack trace: " . $e->getTraceAsString());
                // Continue registration even if email fails - user can resend OTP
            }
        } else {
            \Log::warning("User registered without email - cannot send OTP");
        }

        // Fire UserRegistered event for virtual account creation
        event(new \App\Events\UserRegistered($user));

        // Don't return token yet - user needs to verify OTP first
        // $token = $user->createToken('auth-token', ['*'])->plainTextToken;

        // Refresh to get generated keys
        $user->refresh();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'name' => $user->name, // Full name accessor
                'username' => $user->username,
                'gender' => $user->gender,
                'state' => $user->state,
                'city' => $user->city,
                'street_address' => $user->street_address,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_tier' => $user->user_tier,
                'api_key' => $user->api_key,
                'app_key' => $user->app_key,
                'kyc_status' => $user->kyc_status,
            ],
            // 'token' => $token, // Token will be provided after OTP verification
        ], 'User registered successfully. Please verify your email with the OTP sent.', 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'password' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        // Update or create device
        if ($request->device_id) {
            Device::updateOrCreate(
                ['device_id' => $request->device_id, 'user_id' => $user->id],
                [
                    'device_name' => $request->device_name,
                    'device_type' => $request->device_type,
                    'last_ip' => $request->ip(),
                    'last_seen' => now(),
                ]
            );
        }

        $token = $user->createToken('auth-token', ['*'])->plainTextToken;

        // Ensure keys exist (regenerate if missing)
        if (!$user->api_key || !$user->app_key) {
            if (!$user->api_key) {
                $user->api_key = User::generateApiKey();
            }
            if (!$user->app_key) {
                $user->app_key = User::generateAppKey();
            }
            $user->save();
        }

        return $this->success([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'name' => $user->name, // Full name accessor
                'email' => $user->email,
                'phone' => $user->phone,
                'user_tier' => $user->user_tier,
                'api_key' => $user->api_key,
                'app_key' => $user->app_key,
                'kyc_status' => $user->kyc_status,
            ],
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return $this->success([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->name, // Full name accessor
            'email' => $user->email,
            'phone' => $user->phone,
            'user_tier' => $user->user_tier,
            'api_key' => $user->api_key,
            'app_key' => $user->app_key,
            'kyc_status' => $user->kyc_status,
            'email_verified_at' => $user->email_verified_at,
            'phone_verified_at' => $user->phone_verified_at,
            'created_at' => $user->created_at,
        ], 'User retrieved successfully');
    }

    /**
     * Verify OTP.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'otp' => 'required|string|size:6',
            'type' => 'required|string|in:register,login,password_reset',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user) {
            return $this->error('User not found', 404);
        }

        if (!OtpService::verify($user, $request->otp, $request->type)) {
            return $this->error('Invalid or expired OTP', 400);
        }

        // Mark email/phone as verified if it's a registration OTP
        if ($request->type === 'register') {
            if ($request->email) {
                $user->email_verified_at = now();
            }
            if ($request->phone) {
                $user->phone_verified_at = now();
            }
            $user->save();
        }

        // Generate token for login/register
        if (in_array($request->type, ['register', 'login'])) {
            $token = $user->createToken('auth-token', ['*'])->plainTextToken;
            
            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'user_tier' => $user->user_tier,
                    'api_key' => $user->api_key,
                    'app_key' => $user->app_key,
                    'kyc_status' => $user->kyc_status,
                ],
                'token' => $token,
            ], 'OTP verified successfully');
        }

        return $this->success(null, 'OTP verified successfully');
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'type' => 'required|string|in:register,login,password_reset',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $otpCode = OtpService::generate($user, $request->type, 1); // 1 minute expiration

        // Send email if email is provided
        if ($request->email && $user->email) {
            if ($request->type === 'register') {
                EmailHelper::sendRegisterOtp($user, $otpCode, $request);
            } elseif ($request->type === 'login') {
                EmailHelper::sendLoginOtp($user, $otpCode, $request);
            } elseif ($request->type === 'password_reset') {
                EmailHelper::sendPasswordResetOtp($user, $otpCode, $request);
            }
        }

        return $this->success(null, 'OTP sent successfully');
    }

    /**
     * Forgot password - send reset OTP.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user) {
            // Don't reveal if user exists for security
            return $this->success(null, 'If the account exists, a password reset code has been sent.');
        }

        $otpCode = OtpService::generate($user, 'password_reset', 1); // 1 minute expiration

        // Send email if email is provided
        if ($request->email && $user->email) {
            EmailHelper::sendPasswordResetOtp($user, $otpCode, $request);
        }

        return $this->success(null, 'If the account exists, a password reset code has been sent.');
    }

    /**
     * Reset password using OTP.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user) {
            return $this->error('User not found', 404);
        }

        if (!OtpService::verify($user, $request->otp, 'password_reset')) {
            return $this->error('Invalid or expired OTP', 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return $this->success(null, 'Password reset successfully');
    }
}

