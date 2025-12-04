<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Merchant;
use App\Services\BellBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    use ApiResponse;

    protected $bellBankService;

    public function __construct(BellBankService $bellBankService)
    {
        $this->bellBankService = $bellBankService;
    }

    /**
     * Apply to become a merchant.
     */
    public function apply(Request $request)
    {
        // Check if user has merchant role or can apply
        if (!$request->user()->hasRole('merchant') && !$request->user()->can('merchant.dashboard')) {
            // User needs to be assigned merchant role first by admin
            return $this->error('You need to be approved as a merchant first. Please contact support.', 403);
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'rc_number' => 'required|string|max:255',
            'email_address' => 'required|email',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'bvn' => 'sometimes|string',
            'incorporation_date' => 'sometimes|date',
            'date_of_birth' => 'sometimes|date',
            'settlement_account' => 'sometimes|string',
            'bank_name' => 'sometimes|string',
            'account_number' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // Check if user already has a merchant account
        if ($request->user()->merchant) {
            return $this->error('You already have a merchant account', 400);
        }

        try {
            // Create merchant record
            $merchant = Merchant::create([
                'user_id' => $request->user()->id,
                'business_name' => $request->business_name,
                'qr_code' => 'KP' . Str::random(12),
                'settlement_account' => $request->settlement_account,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'status' => 'pending',
            ]);

            // Create corporate virtual account via BellBank
            try {
                $corporateData = [
                    'rcNumber' => $request->rc_number,
                    'businessName' => $request->business_name,
                    'emailAddress' => $request->email_address,
                    'phoneNumber' => $request->phone_number,
                    'address' => $request->address,
                ];

                // Add optional fields
                if ($request->bvn) {
                    $corporateData['bvn'] = $request->bvn;
                }
                if ($request->incorporation_date) {
                    $corporateData['incorporationDate'] = $request->incorporation_date;
                }
                if ($request->date_of_birth) {
                    $corporateData['dateOfBirth'] = $request->date_of_birth;
                }

                $virtualAccount = $this->bellBankService->createCorporateVirtualAccount(
                    $request->user()->id,
                    $corporateData
                );

                // Update merchant with virtual account info
                $merchant->update([
                    'settlement_account' => $virtualAccount->account_number,
                    'bank_name' => $virtualAccount->bank_name,
                    'account_number' => $virtualAccount->account_number,
                ]);

                Log::info("Corporate virtual account created for merchant {$merchant->id}");
            } catch (\Exception $e) {
                Log::error("Failed to create corporate virtual account for merchant {$merchant->id}: " . $e->getMessage());
                // Don't fail the merchant creation, just log the error
            }

            return $this->success($merchant, 'Merchant application submitted successfully', 201);
        } catch (\Exception $e) {
            Log::error("Merchant application failed: " . $e->getMessage());
            return $this->error('Failed to submit merchant application: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get merchant dashboard data.
     */
    public function dashboard(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (!$merchant) {
            return $this->error('Merchant account not found', 404);
        }

        $stats = [
            'merchant' => $merchant,
            'total_transactions' => $merchant->transactions()->count(),
            'total_volume' => $merchant->transactions()->where('status', 'completed')->sum('amount'),
            'pending_settlement' => $merchant->transactions()->where('status', 'completed')->sum('amount'),
            'recent_transactions' => $merchant->transactions()->latest()->limit(10)->get(),
        ];

        return $this->success($stats, 'Merchant dashboard data retrieved successfully');
    }
}

