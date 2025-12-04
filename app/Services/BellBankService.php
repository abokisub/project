<?php

namespace App\Services;

use App\Models\BellbankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BellBankService
{
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $validityTime;
    protected $webhookSecret;
    protected $directorBvn;
    protected $directorFirstname;
    protected $directorLastname;
    protected $directorDateOfBirth;

    public function __construct()
    {
        $this->baseUrl = config('services.bellbank.base_url');
        $this->consumerKey = config('services.bellbank.consumer_key');
        $this->consumerSecret = config('services.bellbank.consumer_secret');
        $this->validityTime = config('services.bellbank.validity_time', 1440);
        $this->webhookSecret = config('services.bellbank.webhook_secret');
        $this->directorBvn = config('services.bellbank.director_bvn');
        $this->directorFirstname = config('services.bellbank.director_firstname');
        $this->directorLastname = config('services.bellbank.director_lastname');
        $this->directorDateOfBirth = config('services.bellbank.director_date_of_birth', '1990/01/01');
    }

    /**
     * Generate authentication token.
     * 
     * @return string
     * @throws \Exception
     */
    public function generateToken(): string
    {
        // Check cache first (token is valid for validityTime minutes)
        $cacheKey = 'bellbank_token';
        $token = Cache::get($cacheKey);
        
        if ($token) {
            return $token;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'consumerKey' => $this->consumerKey,
                'consumerSecret' => $this->consumerSecret,
                'validityTime' => (string) $this->validityTime,
            ])->post($this->baseUrl . '/v1/generate-token');

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success'] && isset($data['token'])) {
                    $token = $data['token'];
                    
                    // Cache token for slightly less than validity time to ensure it's fresh
                    $cacheTime = ($this->validityTime - 5) * 60; // Convert to seconds, minus 5 minutes buffer
                    Cache::put($cacheKey, $token, now()->addSeconds($cacheTime));
                    
                    return $token;
                }
            }

            $errorMessage = $response->json()['message'] ?? 'Failed to generate token';
            Log::error('BellBank token generation failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            
            throw new \Exception('BellBank token generation failed: ' . $errorMessage);
        } catch (\Exception $e) {
            Log::error('BellBank generateToken error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Make authenticated API request.
     * 
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \Exception
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->generateToken();
        
        $http = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ]);

        try {
            $response = match(strtoupper($method)) {
                'GET' => $http->get($this->baseUrl . $endpoint, $data),
                'POST' => $http->post($this->baseUrl . $endpoint, $data),
                'PUT' => $http->put($this->baseUrl . $endpoint, $data),
                'DELETE' => $http->delete($this->baseUrl . $endpoint, $data),
                default => throw new \Exception("Unsupported HTTP method: {$method}"),
            };

            if ($response->successful()) {
                return $response->json();
            }

            $errorMessage = $response->json()['message'] ?? 'API request failed';
            Log::error('BellBank API request failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            throw new \Exception('BellBank API error: ' . $errorMessage);
        } catch (\Exception $e) {
            Log::error('BellBank API request error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * List all banks.
     * 
     * @return array Returns array of banks with institutionCode, institutionName, and category
     * @throws \Exception
     */
    public function listBanks(): array
    {
        return $this->makeRequest('GET', '/v1/transfer/banks');
    }

    /**
     * Name enquiry - verify account details (external banks).
     * 
     * @param string $accountNumber Account number to validate
     * @param string $bankCode Bank institutionCode from the bank list
     * @return array Returns account name, BVN, and verification details
     * @throws \Exception
     */
    public function nameEnquiry(string $accountNumber, string $bankCode): array
    {
        return $this->makeRequest('POST', '/v1/transfer/name-enquiry', [
            'accountNumber' => $accountNumber,
            'bankCode' => $bankCode,
        ]);
    }

    /**
     * Internal name enquiry - verify BellBank client account details.
     * 
     * @param string $accountNumber BellBank account number
     * @return array
     * @throws \Exception
     */
    public function internalNameEnquiry(string $accountNumber): array
    {
        return $this->makeRequest('GET', "/v1/client-enquiry/{$accountNumber}");
    }

    /**
     * Create virtual account (individual client).
     * 
     * @param int $userId
     * @param bool $useDirectorBvn
     * @param array $additionalData Optional additional data (middlename, address, gender, dateOfBirth, metadata, creation_source)
     * @return BellbankAccount
     * @throws \Exception
     */
    public function createVirtualAccount(int $userId, bool $useDirectorBvn = false, array $additionalData = []): BellbankAccount
    {
        $user = User::findOrFail($userId);
        $bvn = $useDirectorBvn ? $this->directorBvn : $user->bvn;

        if (!$bvn) {
            throw new \Exception('BVN is required to create virtual account');
        }

        // When using director BVN, use director's name; otherwise use user's name
        $firstname = $useDirectorBvn && $this->directorFirstname 
            ? $this->directorFirstname 
            : $user->first_name;
        $lastname = $useDirectorBvn && $this->directorLastname 
            ? $this->directorLastname 
            : $user->last_name;

        // Determine date of birth - required by BellBank
        // Priority: additionalData > user kyc_data > director DOB (if using director BVN) > default
        $dateOfBirth = $additionalData['dateOfBirth'] 
            ?? ($user->kyc_data['date_of_birth'] ?? null)
            ?? ($useDirectorBvn ? $this->directorDateOfBirth : '1990/01/01');

        // Prepare request body according to BellBank API
        $requestBody = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phoneNumber' => $user->phone,
            'address' => $additionalData['address'] ?? 'Not provided',
            'bvn' => $bvn,
            'gender' => $additionalData['gender'] ?? 'male', // Default to male if not provided
            'dateOfBirth' => $dateOfBirth, // Required by BellBank
        ];

        // Add optional fields
        if (isset($additionalData['middlename'])) {
            $requestBody['middlename'] = $additionalData['middlename'];
        }

        // Add metadata if provided
        if (isset($additionalData['metadata'])) {
            $requestBody['metadata'] = $additionalData['metadata'];
        }

        $response = $this->makeRequest('POST', '/v1/account/clients/individual', $requestBody);

        // Extract response data
        $data = $response['data'] ?? $response;

        // Get creation source from additionalData or default to 'auto'
        $creationSource = $additionalData['creation_source'] ?? 'auto';

        // Check if account already exists for this user
        $existingAccount = BellbankAccount::where('user_id', $userId)->first();
        
        if ($existingAccount) {
            // Update existing account
            $existingAccount->update([
                'virtual_account_id' => $data['id'] ?? $data['externalReference'] ?? null,
                'account_number' => $data['accountNumber'] ?? null,
                'bank_code' => '000000', // BellBank code (adjust if different)
                'bank_name' => 'Bell Microfinance Bank',
                'status' => 'active',
                'created_by_director' => $useDirectorBvn,
                'creation_source' => $creationSource,
                'metadata' => $data,
            ]);
            
            return $existingAccount->fresh();
        }

        // Create new account
        $account = BellbankAccount::create([
            'user_id' => $userId,
            'virtual_account_id' => $data['id'] ?? $data['externalReference'] ?? null,
            'account_number' => $data['accountNumber'] ?? null,
            'bank_code' => '000000', // BellBank code (adjust if different)
            'bank_name' => 'Bell Microfinance Bank',
            'status' => 'active',
            'created_by_director' => $useDirectorBvn,
            'creation_source' => $creationSource,
            'metadata' => $data,
        ]);

        return $account;
    }

    /**
     * Create corporate virtual account.
     * 
     * @param int $userId User ID (merchant/business owner)
     * @param array $corporateData Required: rcNumber, businessName, emailAddress, phoneNumber, address. Optional: bvn, incorporationDate, dateOfBirth, metadata
     * @return BellbankAccount
     * @throws \Exception
     */
    public function createCorporateVirtualAccount(int $userId, array $corporateData): BellbankAccount
    {
        $user = User::findOrFail($userId);

        // Validate required fields
        $required = ['rcNumber', 'businessName', 'emailAddress', 'phoneNumber', 'address'];
        foreach ($required as $field) {
            if (!isset($corporateData[$field])) {
                throw new \Exception("Required field missing: {$field}");
            }
        }

        // Prepare request body according to BellBank API
        $requestBody = [
            'rcNumber' => $corporateData['rcNumber'],
            'businessName' => $corporateData['businessName'],
            'emailAddress' => $corporateData['emailAddress'],
            'phoneNumber' => $corporateData['phoneNumber'],
            'address' => $corporateData['address'],
        ];

        // Add optional fields
        if (isset($corporateData['bvn'])) {
            $requestBody['bvn'] = $corporateData['bvn'];
        }
        if (isset($corporateData['incorporationDate'])) {
            $requestBody['incorporationDate'] = $corporateData['incorporationDate'];
        }
        if (isset($corporateData['dateOfBirth'])) {
            $requestBody['dateOfBirth'] = $corporateData['dateOfBirth'];
        }
        if (isset($corporateData['metadata'])) {
            $requestBody['metadata'] = $corporateData['metadata'];
        }

        $response = $this->makeRequest('POST', '/v1/account/clients/corporate', $requestBody);

        // Extract response data
        $data = $response['data'] ?? $response;

        // Check if account already exists for this user
        $existingAccount = BellbankAccount::where('user_id', $userId)->first();
        
        if ($existingAccount) {
            // Update existing account
            $existingAccount->update([
                'virtual_account_id' => $data['id'] ?? $data['externalReference'] ?? null,
                'account_number' => $data['accountNumber'] ?? null,
                'bank_code' => '000000', // BellBank code
                'bank_name' => 'Bell Microfinance Bank',
                'status' => 'active',
                'created_by_director' => false,
                'metadata' => $data,
            ]);
            
            return $existingAccount->fresh();
        }

        // Create new account
        $account = BellbankAccount::create([
            'user_id' => $userId,
            'virtual_account_id' => $data['id'] ?? $data['externalReference'] ?? null,
            'account_number' => $data['accountNumber'] ?? null,
            'bank_code' => '000000', // BellBank code
            'bank_name' => 'Bell Microfinance Bank',
            'status' => 'active',
            'created_by_director' => false,
            'metadata' => $data,
        ]);

        return $account;
    }

    /**
     * Transfer to bank account.
     * 
     * @param float $amount Amount to transfer (decimal should not exceed 2 digits, e.g., 230.00)
     * @param string $beneficiaryAccountNumber Receiver's account number
     * @param string $beneficiaryBankCode Receiver's bank code (institutionCode from bank list)
     * @param string $narration Transfer description
     * @param string|null $reference Transaction reference (optional, auto-generated if not provided)
     * @param string|null $senderName Sender name (optional, defaults to business name)
     * @return array Returns transfer details including transaction reference, status, charges, etc.
     * @throws \Exception
     */
    public function transfer(
        float $amount,
        string $beneficiaryAccountNumber,
        string $beneficiaryBankCode,
        string $narration = '',
        ?string $reference = null,
        ?string $senderName = null
    ): array {
        // Format amount to 2 decimal places
        $formattedAmount = number_format($amount, 2, '.', '');

        $requestBody = [
            'beneficiaryBankCode' => $beneficiaryBankCode,
            'beneficiaryAccountNumber' => $beneficiaryAccountNumber,
            'narration' => $narration,
            'amount' => (float) $formattedAmount,
        ];

        // Add optional fields
        if ($reference !== null) {
            $requestBody['reference'] = $reference;
        }
        if ($senderName !== null) {
            $requestBody['senderName'] = $senderName;
        }

        return $this->makeRequest('POST', '/v1/transfer', $requestBody);
    }

    /**
     * Verify webhook signature.
     * 
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (!$this->webhookSecret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get virtual account details.
     * 
     * @param string $virtualAccountId
     * @return array
     * @throws \Exception
     */
    public function getVirtualAccount(string $virtualAccountId): array
    {
        return $this->makeRequest('GET', "/v1/virtual-accounts/{$virtualAccountId}");
    }

    /**
     * List all client accounts (virtual accounts).
     * 
     * @param array $filters Optional filters:
     *   - accountType: string (individual, corporate)
     *   - validityType: string
     *   - status: string
     *   - page: number
     *   - limit: number
     * @return array
     * @throws \Exception
     */
    public function listVirtualAccounts(array $filters = []): array
    {
        // Build query string from filters
        $queryParams = [];
        if (isset($filters['accountType'])) {
            $queryParams['accountType'] = $filters['accountType'];
        }
        if (isset($filters['validityType'])) {
            $queryParams['validityType'] = $filters['validityType'];
        }
        if (isset($filters['status'])) {
            $queryParams['status'] = $filters['status'];
        }
        if (isset($filters['page'])) {
            $queryParams['page'] = $filters['page'];
        }
        if (isset($filters['limit'])) {
            $queryParams['limit'] = $filters['limit'];
        }

        $endpoint = '/v1/account/clients';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get a specific client account by ID.
     * 
     * @param int|string $clientId Client ID or external reference
     * @return array
     * @throws \Exception
     */
    public function getClientAccount($clientId): array
    {
        return $this->makeRequest('GET', "/v1/account/clients/{$clientId}");
    }

    /**
     * Get virtual account transactions.
     * 
     * @param string $virtualAccountId
     * @param array $filters Optional filters (page, limit, date_from, date_to, etc.)
     * @return array
     * @throws \Exception
     */
    public function getVirtualAccountTransactions(string $virtualAccountId, array $filters = []): array
    {
        $queryParams = http_build_query($filters);
        $endpoint = "/v1/virtual-accounts/{$virtualAccountId}/transactions";
        
        if ($queryParams) {
            $endpoint .= '?' . $queryParams;
        }
        
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Delete/Close virtual account.
     * 
     * Note: BellBank API does NOT support deleting virtual accounts.
     * This method is kept for future use if BellBank adds this feature.
     * 
     * @param string $clientId Client ID or external reference
     * @return array
     * @throws \Exception
     */
    public function deleteVirtualAccount($clientId): array
    {
        // BellBank API doesn't support deleting virtual accounts
        // This will always return 404
        // We keep this method for potential future use
        throw new \Exception('BellBank API does not support deleting virtual accounts. Accounts can only be deactivated manually through BellBank dashboard.');
    }

    /**
     * Get transaction status by reference.
     * 
     * @param string $reference Transaction reference
     * @return array Returns transaction details including status, amounts, account info, etc.
     * @throws \Exception
     */
    public function getTransactionByReference(string $reference): array
    {
        return $this->makeRequest('GET', "/v1/transactions/reference/{$reference}");
    }

    /**
     * Transfer requery by transaction ID.
     * 
     * @param string $transactionId Transaction ID from transfer response
     * @return array Returns transaction status (SUCCESSFUL, PENDING, FAILED) with feedback code
     * @throws \Exception
     */
    public function requeryTransfer(string $transactionId): array
    {
        $endpoint = "/v1/transfer/tsq?transactionId={$transactionId}";
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get all transactions with pagination.
     * 
     * @param int $page Page number (default: 1)
     * @param int $limit Number of transactions per page (default: 30)
     * @return array Returns array of transactions with pagination
     * @throws \Exception
     */
    public function getAllTransactions(int $page = 1, int $limit = 30): array
    {
        $queryParams = http_build_query([
            'page' => $page,
            'limit' => $limit,
        ]);
        
        $endpoint = "/v1/transactions?{$queryParams}";
        
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get transaction status (legacy method - kept for backward compatibility).
     * 
     * @param string $transactionReference
     * @return array
     * @throws \Exception
     */
    public function getTransactionStatus(string $transactionReference): array
    {
        // Try by reference first
        try {
            return $this->getTransactionByReference($transactionReference);
        } catch (\Exception $e) {
            // Fallback to old endpoint if reference lookup fails
            return $this->makeRequest('GET', "/v1/transactions/{$transactionReference}");
        }
    }

    /**
     * Get main business account information.
     * 
     * @return array
     * @throws \Exception
     */
    public function getAccountInfo(): array
    {
        return $this->makeRequest('GET', '/v1/account');
    }
}
