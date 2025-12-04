<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Update user profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user->update($request->only(['first_name', 'last_name', 'email', 'phone']));

        return $this->success([
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
        ], 'Profile updated successfully');
    }

    /**
     * Regenerate API and App keys.
     */
    public function regenerateKeys(Request $request)
    {
        $user = $request->user();

        $user->api_key = \App\Models\User::generateApiKey();
        $user->app_key = \App\Models\User::generateAppKey();
        $user->save();

        return $this->success([
            'api_key' => $user->api_key,
            'app_key' => $user->app_key,
        ], 'API keys regenerated successfully');
    }

    /**
     * Set transaction PIN.
     */
    public function setTransactionPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = $request->user();
        $user->transaction_pin = \Hash::make($request->transaction_pin);
        $user->save();

        return $this->success(null, 'Transaction PIN set successfully');
    }
}

