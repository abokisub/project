<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Send money (wallet to wallet).
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->paymentService->send(
                $request->user()->id,
                $request->to_user_id,
                $request->amount
            );
            return $this->success($transaction, 'Payment sent successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Bank transfer.
     */
    public function bankTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'account_number' => 'required|string',
            'bank_code' => 'required|string',
            'narration' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->paymentService->bankTransfer(
                $request->user()->id,
                $request->amount,
                $request->account_number,
                $request->bank_code,
                $request->narration ?? ''
            );
            return $this->success($transaction, 'Bank transfer initiated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * QR payment.
     */
    public function qr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required|exists:merchants,id',
            'amount' => 'required|numeric|min:0.01',
            'qr_code' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $transaction = $this->paymentService->qrPayment(
                $request->user()->id,
                $request->merchant_id,
                $request->amount,
                $request->qr_code
            );
            return $this->success($transaction, 'QR payment completed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get payment status.
     */
    public function status($id)
    {
        $transaction = \App\Models\Transaction::findOrFail($id);
        return $this->success($transaction, 'Payment status retrieved successfully');
    }
}

