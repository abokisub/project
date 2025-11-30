<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\OfflineTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfflineController extends Controller
{
    use ApiResponse;

    protected $offlineService;

    public function __construct(OfflineTransactionService $offlineService)
    {
        $this->offlineService = $offlineService;
    }

    /**
     * Generate offline voucher.
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_user_id' => 'sometimes|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'expiry_hours' => 'sometimes|integer|min:1|max:168', // Max 7 days
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $voucher = $this->offlineService->generateVoucher(
                $request->user()->id,
                $request->to_user_id,
                $request->amount,
                $request->expiry_hours ?? 24
            );
            return $this->success($voucher, 'Offline voucher generated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Sync offline transactions.
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'required|array',
            'transactions.*.code' => 'required|string',
            'transactions.*.amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $result = $this->offlineService->syncTransactions(
                $request->user()->id,
                $request->transactions
            );
            return $this->success($result, 'Offline transactions synced successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get user's offline vouchers.
     */
    public function vouchers(Request $request)
    {
        $vouchers = \App\Models\OfflineVoucher::where('from_user_id', $request->user()->id)
            ->orWhere('to_user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($vouchers, 'Vouchers retrieved successfully');
    }
}

