<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Device;
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // Ensure at least email or phone is provided
        if (!$request->email && !$request->phone) {
            return $this->error('Either email or phone must be provided', 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_tier' => 'tier1', // Default to tier1
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

        // Fire UserRegistered event for virtual account creation
        event(new \App\Events\UserRegistered($user));

        $token = $user->createToken('auth-token', ['*'])->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'User registered successfully', 201);
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

        return $this->success([
            'user' => $user,
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
        return $this->success($request->user(), 'User retrieved successfully');
    }
}

