<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    use ApiResponse;

    /**
     * Apply to become an agent.
     */
    public function apply(Request $request)
    {
        // Check if user has agent role
        if (!$request->user()->hasRole('agent')) {
            return $this->error('You need to be approved as an agent first. Please contact support.', 403);
        }

        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // Check if user already has an agent account
        if ($request->user()->agent) {
            return $this->error('You already have an agent account', 400);
        }

        $agent = Agent::create([
            'user_id' => $request->user()->id,
            'location' => $request->location,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'commission_rate' => 2.5, // Default commission rate
            'status' => 'pending',
        ]);

        return $this->success($agent, 'Agent application submitted successfully', 201);
    }

    /**
     * Get agent dashboard data.
     */
    public function dashboard(Request $request)
    {
        $agent = $request->user()->agent;

        if (!$agent) {
            return $this->error('Agent account not found', 404);
        }

        $stats = [
            'agent' => $agent,
            'total_commissions' => $agent->commissions()->where('status', 'paid')->sum('amount'),
            'pending_commissions' => $agent->commissions()->where('status', 'pending')->sum('amount'),
            'recent_commissions' => $agent->commissions()->latest()->limit(10)->get(),
        ];

        return $this->success($stats, 'Agent dashboard data retrieved successfully');
    }

    /**
     * Cash-in operation.
     */
    public function cashIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // TODO: Implement cash-in logic
        // This would fund the user's wallet and record the transaction

        return $this->success(null, 'Cash-in operation completed');
    }

    /**
     * Cash-out operation.
     */
    public function cashOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // TODO: Implement cash-out logic
        // This would deduct from user's wallet and record the transaction

        return $this->success(null, 'Cash-out operation completed');
    }
}

