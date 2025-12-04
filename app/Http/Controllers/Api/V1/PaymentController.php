<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\PaymentService;
use App\Services\BellBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymentService;
    protected $bellBankService;

    public function __construct(PaymentService $paymentService, BellBankService $bellBankService)
    {
        $this->paymentService = $paymentService;
        $this->bellBankService = $bellBankService;
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

    /**
     * Internal name enquiry - verify BellBank account details.
     */
    public function internalNameEnquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $response = $this->bellBankService->internalNameEnquiry($request->account_number);
            
            // Extract data from response
            $data = $response['data'] ?? $response;

            return $this->success($data, 'Account details retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * External name enquiry - verify external bank account details.
     */
    public function nameEnquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
            'bank_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $response = $this->bellBankService->nameEnquiry(
                $request->account_number,
                $request->bank_code
            );
            
            // Extract data from response
            $data = $response['data'] ?? $response;

            return $this->success($data, 'Account details retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get list of supported banks.
     */
    public function listBanks(Request $request)
    {
        try {
            $response = $this->bellBankService->listBanks();
            
            // Extract data from response
            $data = $response['data'] ?? $response;

            return $this->success($data, 'Banks retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get transaction status by reference.
     */
    public function getTransactionByReference(Request $request, $reference)
    {
        try {
            $response = $this->bellBankService->getTransactionByReference($reference);
            
            // Extract data from response
            $data = $response['data'] ?? $response;

            return $this->success($data, 'Transaction details retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Requery transfer status by transaction ID.
     */
    public function requeryTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $response = $this->bellBankService->requeryTransfer($request->transaction_id);
            
            // Extract data from response
            $data = $response['data'] ?? $response;

            return $this->success($data, 'Transfer status retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Get all transactions with pagination.
     */
    public function getAllTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 30);
            
            $response = $this->bellBankService->getAllTransactions($page, $limit);
            
            // Extract data from response
            $data = $response['data'] ?? $response;

            return $this->success($data, 'Transactions retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}

