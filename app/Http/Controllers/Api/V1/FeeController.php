<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeController extends Controller
{
    use ApiResponse;

    protected $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * Get fee breakdown for a transaction.
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $breakdown = $this->feeService->getFeeBreakdown(
            $request->transaction_type,
            $request->amount,
            $request->user()
        );

        return $this->success($breakdown, 'Fee breakdown calculated successfully');
    }
}

