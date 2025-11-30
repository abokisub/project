<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\KycSubmission;

class KycController extends Controller
{
    use ApiResponse;

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

        return $this->success([
            'kyc_status' => $user->kyc_status,
            'submission' => $submission,
        ], 'KYC status retrieved successfully');
    }
}

