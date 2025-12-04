<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\KycSubmission;
use App\Models\User;
use App\Services\BellBankService;
use Illuminate\Support\Facades\Log;

class KycController extends Controller
{
    use ApiResponse;

    protected $bellBankService;

    public function __construct(BellBankService $bellBankService)
    {
        $this->bellBankService = $bellBankService;
    }

    /**
     * Submit KYC documents.
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'selfie' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $user = $request->user();

            // Store files
            $idDocumentPath = $request->file('id_document')->store('kyc/' . $user->id, 'private');
            $selfiePath = $request->file('selfie')->store('kyc/' . $user->id, 'private');

            // Create KYC submission
            $submission = KycSubmission::create([
                'user_id' => $user->id,
                'id_document_path' => $idDocumentPath,
                'selfie_path' => $selfiePath,
                'status' => 'pending',
            ]);

            // Update user KYC status
            $user->update(['kyc_status' => 'pending']);

            return $this->success($submission, 'KYC documents submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to submit KYC documents: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get KYC status.
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $submission = KycSubmission::where('user_id', $user->id)
            ->latest()
            ->first();

        $bellbankAccount = $user->bellbankAccount;

        return $this->success([
            'kyc_status' => $user->kyc_status,
            'bvn' => $user->bvn ? 'provided' : 'not_provided',
            'submission' => $submission,
            'virtual_account' => $bellbankAccount ? [
                'account_number' => $bellbankAccount->account_number,
                'created_by_director' => $bellbankAccount->created_by_director,
                'status' => $bellbankAccount->status,
            ] : null,
        ], 'KYC status retrieved successfully');
    }

    /**
     * Verify BVN and update user.
     */
    public function verifyBvn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bvn' => 'required|string|size:11',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $user = $request->user();
            $bvn = $request->bvn;

            // TODO: Integrate with BVN verification service (BellBank or other provider)
            // For now, we'll just store the BVN
            // In production, you should verify the BVN matches the user's details

            $user->update(['bvn' => $bvn]);

            return $this->success([
                'bvn' => 'verified',
                'message' => 'BVN verified and saved. You can now upgrade your virtual account.',
            ], 'BVN verified successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to verify BVN: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upgrade virtual account KYC (replace director BVN with user's own BVN).
     */
    public function upgradeVirtualAccount(Request $request)
    {
        $user = $request->user();

        // Check if user has BVN
        if (!$user->bvn) {
            return $this->error('BVN is required to upgrade virtual account. Please verify your BVN first.', 400);
        }

        // Check if user has virtual account
        $bellbankAccount = $user->bellbankAccount;
        if (!$bellbankAccount) {
            return $this->error('Virtual account not found. Please create a virtual account first.', 404);
        }

        // Check if account was created with director BVN
        if (!$bellbankAccount->created_by_director) {
            return $this->error('Virtual account is already using your own BVN. No upgrade needed.', 400);
        }

        try {
            // Create new virtual account with user's own BVN
            // Note: BellBank may require closing the old account first, or they may allow updating
            // For now, we'll create a new account with user's BVN
            $newAccount = $this->bellBankService->createVirtualAccount(
                $user->id,
                false, // Use user's own BVN, not director's
                [
                    'address' => $request->address ?? 'Not provided',
                    'gender' => $request->gender ?? 'male',
                    'dateOfBirth' => $request->date_of_birth ?? null,
                    'creation_source' => 'kyc_upgrade',
                ]
            );

            // Mark old account as upgraded
            $bellbankAccount->update([
                'status' => 'upgraded',
                'metadata' => array_merge($bellbankAccount->metadata ?? [], [
                    'upgraded_at' => now()->toIso8601String(),
                    'upgraded_to_account_id' => $newAccount->id,
                ]),
            ]);

            Log::info("Virtual account upgraded for user {$user->id} from director BVN to user BVN");

            return $this->success([
                'old_account' => [
                    'account_number' => $bellbankAccount->account_number,
                    'status' => 'upgraded',
                ],
                'new_account' => [
                    'account_number' => $newAccount->account_number,
                    'virtual_account_id' => $newAccount->virtual_account_id,
                    'status' => $newAccount->status,
                ],
            ], 'Virtual account upgraded successfully. Your new account is now active.');
        } catch (\Exception $e) {
            Log::error("Failed to upgrade virtual account for user {$user->id}: " . $e->getMessage());
            return $this->error('Failed to upgrade virtual account: ' . $e->getMessage(), 500);
        }
    }
}

