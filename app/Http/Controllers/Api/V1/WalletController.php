<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use ApiResponse;

    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get wallet balance.
     */
    public function balance(Request $request)
    {
        $balance = $this->walletService->getBalance($request->user()->id);

        if (!$balance) {
            return $this->error('Wallet not found', 404);
        }

        return $this->success($balance, 'Balance retrieved successfully');
    }

    /**
     * Fund wallet.
     */
    public function fund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
            'reference' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->walletService->fund(
                $request->user()->id,
                $request->amount,
                $request->currency ?? 'NGN',
                $request->reference
            );

            return $this->success($transaction, 'Wallet funded successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Transfer funds.
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->walletService->transfer(
                $request->user()->id,
                $request->to_user_id,
                $request->amount,
                $request->currency ?? 'NGN'
            );

            return $this->success($transaction, 'Transfer completed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get transaction history.
     */
    public function transactions(Request $request)
    {
        $transactions = $this->walletService->getTransactions(
            $request->user()->id,
            $request->get('limit', 20)
        );

        return $this->success($transactions, 'Transactions retrieved successfully');
    }

    /**
     * Get virtual account.
     */
    public function virtualAccount(Request $request)
    {
        $user = $request->user();
        $account = $user->bellbankAccount;

        if (!$account) {
            return $this->error('Virtual account not found', 404);
        }

        return $this->success($account, 'Virtual account retrieved successfully');
    }
}

