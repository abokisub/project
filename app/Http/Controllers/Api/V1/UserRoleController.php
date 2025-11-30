<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    use ApiResponse;

    /**
     * Assign role to user (Admin only).
     */
    public function assignRole(Request $request, $userId)
    {
        // Check if user is admin
        if (!$request->user()->hasRole('admin')) {
            return $this->error('Unauthorized. Admin access required.', 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,tier1,tier2,tier3,tier4,tier5,merchant,agent',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = User::findOrFail($userId);
        
        // Remove all existing roles
        $user->roles()->detach();
        
        // Assign new role
        $user->assignRole($request->role);
        
        // Update user_tier if it's a tier role
        if (str_starts_with($request->role, 'tier')) {
            $user->update(['user_tier' => $request->role]);
        } elseif ($request->role === 'merchant') {
            $user->update(['user_tier' => 'merchant']);
        } elseif ($request->role === 'agent') {
            $user->update(['user_tier' => 'agent']);
        }

        return $this->success([
            'user' => $user->load('roles'),
            'role' => $request->role,
        ], 'Role assigned successfully');
    }

    /**
     * Get user roles.
     */
    public function getUserRoles(Request $request, $userId = null)
    {
        $targetUser = $userId ? User::findOrFail($userId) : $request->user();
        
        // Users can view their own roles, admins can view any user's roles
        if ($userId && !$request->user()->hasRole('admin') && $targetUser->id !== $request->user()->id) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success([
            'user' => $targetUser,
            'roles' => $targetUser->getRoleNames(),
            'permissions' => $targetUser->getAllPermissions()->pluck('name'),
            'tier' => $targetUser->getTier(),
        ], 'User roles retrieved successfully');
    }

    /**
     * Upgrade user tier (Admin only).
     */
    public function upgradeTier(Request $request, $userId)
    {
        if (!$request->user()->hasRole('admin')) {
            return $this->error('Unauthorized. Admin access required.', 403);
        }

        $validator = Validator::make($request->all(), [
            'tier' => 'required|in:tier1,tier2,tier3,tier4,tier5',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $user = User::findOrFail($userId);
        
        // Remove tier roles
        $user->removeRole(['tier1', 'tier2', 'tier3', 'tier4', 'tier5']);
        
        // Assign new tier
        $user->assignRole($request->tier);
        $user->update(['user_tier' => $request->tier]);

        return $this->success([
            'user' => $user->load('roles'),
            'tier' => $request->tier,
        ], 'User tier upgraded successfully');
    }
}

