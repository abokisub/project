<?php

namespace App\Services;

use App\Models\BellbankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BellBankService
{
    protected $apiUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->apiUrl = config('services.bellbank.api_url', env('BELLBANK_API_URL'));
        $this->apiKey = env('BELLBANK_API_KEY');
        $this->secretKey = env('BELLBANK_SECRET_KEY');
    }

    /**
     * List all banks.
     */
    public function listBanks()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/banks');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to fetch banks');
        } catch (\Exception $e) {
            Log::error('BellBank listBanks error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Name enquiry.
     */
    public function nameEnquiry($accountNumber, $bankCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/name-enquiry', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Name enquiry failed');
        } catch (\Exception $e) {
            Log::error('BellBank nameEnquiry error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create virtual account.
     */
    public function createVirtualAccount($userId, $useDirectorBvn = false)
    {
        try {
            $user = User::findOrFail($userId);
            $bvn = $useDirectorBvn ? env('BELLBANK_DIRECTOR_BVN') : $user->bvn;

            if (!$bvn) {
                throw new \Exception('BVN is required to create virtual account');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/virtual-accounts', [
                'bvn' => $bvn,
                'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                'last_name' => explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'phone' => $user->phone,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $account = BellbankAccount::create([
                    'user_id' => $userId,
                    'virtual_account_id' => $data['virtual_account_id'],
                    'account_number' => $data['account_number'],
                    'bank_code' => $data['bank_code'],
                    'bank_name' => $data['bank_name'] ?? null,
                    'status' => 'active',
                    'created_by_director' => $useDirectorBvn,
                    'metadata' => $data,
                ]);

                return $account;
            }

            throw new \Exception('Failed to create virtual account');
        } catch (\Exception $e) {
            Log::error('BellBank createVirtualAccount error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Transfer to bank account.
     */
    public function transfer($amount, $accountNumber, $bankCode, $narration = '')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/transfer', [
                'amount' => $amount,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'narration' => $narration,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Transfer failed');
        } catch (\Exception $e) {
            Log::error('BellBank transfer error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha256', $payload, env('BELLBANK_WEBHOOK_SECRET'));
        return hash_equals($expectedSignature, $signature);
    }
}

