<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\SavingsService;
use App\Models\SavingsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavingsController extends Controller
{
    use ApiResponse;

    protected $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    /**
     * Create a savings account.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'target_amount' => 'sometimes|numeric|min:0.01',
            'type' => 'sometimes|in:regular,target,round_up,auto_save',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $account = $this->savingsService->createAccount($request->user()->id, $request->all());
            return $this->success($account, 'Savings account created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Deposit to savings.
     */
    public function deposit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->savingsService->deposit(
                $id,
                $request->user()->id,
                $request->amount
            );
            return $this->success($transaction, 'Deposit successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Withdraw from savings.
     */
    public function withdraw(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->savingsService->withdraw(
                $id,
                $request->user()->id,
                $request->amount
            );
            return $this->success($transaction, 'Withdrawal successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get user's savings accounts.
     */
    public function index(Request $request)
    {
        $accounts = $this->savingsService->getUserAccounts($request->user()->id);
        return $this->success($accounts, 'Savings accounts retrieved successfully');
    }

    /**
     * Get savings account details.
     */
    public function show($id)
    {
        $account = SavingsAccount::with('transactions')->findOrFail($id);
        return $this->success($account, 'Savings account retrieved successfully');
    }
}

