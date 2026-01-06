<?php

namespace App\Services;

use App\Models\Payment\NationalPayment;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConnectIPSService
{
    private $baseUrl;
    private $merchantId;
    private $appId;
    private $appName;
    private $password;
    private $privateKeyPath;
    private $certificatePassword;
    private $society;

    public function __construct($society = null)
    {
        $this->society = $society;
        
        // Load configuration from database if society is provided
        if ($society) {
            $paymentSetting = NationalPayment::where('society_id', $society->id)
                ->select('connectips_merchant_id', 'connectips_app_id', 'connectips_app_name', 'connectips_password')
                ->first();
            
            if ($paymentSetting) {
                $this->merchantId = $paymentSetting->connectips_merchant_id;
                $this->appId = $paymentSetting->connectips_app_id;
                $this->appName = $paymentSetting->connectips_app_name;
                $this->password = $paymentSetting->connectips_password;
            }
        }
        
        // Default values (can be overridden)
        $this->baseUrl = 'https://uat.connectips.com';
        $this->certificatePassword = '123'; // Default certificate password
        
        // Certificate paths
        $societyId = $society ? $society->id : 'default';
        $this->privateKeyPath = storage_path("certificates/private_key.pem");
    }

    /**
     * Generate token for payment request using digital signature
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function generateToken(array $data): string
    {
        // Create the message string as per ConnectIPS specification
        $message = sprintf(
            "MERCHANTID=%s,APPID=%s,APPNAME=%s,TXNID=%s,TXNDATE=%s,TXNCRNCY=%s,TXNAMT=%s,REFERENCEID=%s,REMARKS=%s,PARTICULARS=%s,TOKEN=TOKEN",
            $data['MERCHANTID'],
            $data['APPID'],
            $data['APPNAME'],
            $data['TXNID'],
            $data['TXNDATE'],
            $data['TXNCRNCY'],
            $data['TXNAMT'],
            $data['REFERENCEID'],
            $data['REMARKS'],
            $data['PARTICULARS']
        );

        Log::info('ConnectIPS Token Message: ' . $message);

        return $this->signMessage($message);
    }

    /**
     * Generate token for validation request
     *
     * @param string $referenceId
     * @param int $txnAmt
     * @return string
     * @throws Exception
     */
    public function generateValidationToken(string $referenceId, int $txnAmt): string
    {
        // Create the message string for validation
        $message = sprintf(
            "MERCHANTID=%s,APPID=%s,REFERENCEID=%s,TXNAMT=%s",
            $this->merchantId,
            $this->appId,
            $referenceId,
            $txnAmt
        );

        Log::info('ConnectIPS Validation Token Message: ' . $message);

        return $this->signMessage($message);
    }

    /**
     * Sign message with private key
     *
     * @param string $message
     * @return string
     * @throws Exception
     */
    private function signMessage(string $message): string
    {
        // Check if private key exists
        if (!file_exists($this->privateKeyPath)) {
            // Try to use a fallback simple signature if certificate is not available
            Log::warning('ConnectIPS: Private key not found, using fallback signature');
            return base64_encode(hash_hmac('sha256', $message, $this->password, true));
        }

        // Read the private key
        $privateKeyContent = file_get_contents($this->privateKeyPath);
        $privateKey = openssl_pkey_get_private($privateKeyContent, $this->certificatePassword);

        if (!$privateKey) {
            Log::warning('ConnectIPS: Failed to load private key, using fallback signature');
            return base64_encode(hash_hmac('sha256', $message, $this->password, true));
        }

        // Sign the message with SHA256withRSA
        $signature = '';
        $signResult = openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$signResult) {
            openssl_free_key($privateKey);
            throw new Exception("Failed to sign message: " . openssl_error_string());
        }

        // Free the key
        openssl_free_key($privateKey);

        // Convert to base64
        $token = base64_encode($signature);

        Log::info('ConnectIPS Generated Token: ' . substr($token, 0, 50) . '...');

        return $token;
    }

    /**
     * Prepare payment data for form submission
     *
     * @param array $paymentData
     * @return array
     * @throws Exception
     */
    public function preparePaymentData(array $paymentData): array
    {
        // Validate required fields
        $required = ['txnId', 'txnDate', 'txnAmt', 'referenceId', 'remarks', 'particulars'];
        foreach ($required as $field) {
            if (!isset($paymentData[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Convert amount to paisa (multiply by 100)
        $amountInPaisa = (int)($paymentData['txnAmt'] * 100);

        // Prepare the form data
        $formData = [
            'MERCHANTID' => $this->merchantId,
            'APPID' => $this->appId,
            'APPNAME' => $this->appName,
            'TXNID' => $paymentData['txnId'],
            'TXNDATE' => $paymentData['txnDate'], // Format: DD-MM-YYYY
            'TXNCRNCY' => 'NPR',
            'TXNAMT' => $amountInPaisa,
            'REFERENCEID' => $paymentData['referenceId'],
            'REMARKS' => $paymentData['remarks'],
            'PARTICULARS' => $paymentData['particulars'],
        ];

        // Generate token
        $formData['TOKEN'] = $this->generateToken($formData);

        // Add the action URL
        $formData['action_url'] = $this->baseUrl . '/connectipswebgw/loginpage';

        Log::info('ConnectIPS Payment Data Prepared', [
            'merchant_id' => $this->merchantId,
            'app_id' => $this->appId,
            'txn_id' => $paymentData['txnId'],
            'amount' => $amountInPaisa,
        ]);

        return $formData;
    }

    /**
     * Validate transaction
     *
     * @param string $referenceId
     * @param float $txnAmt
     * @return array
     * @throws Exception
     */
    public function validateTransaction(string $referenceId, float $txnAmt): array
    {
        // Convert amount to paisa
        $amountInPaisa = (int)($txnAmt * 100);

        // Generate token
        $token = $this->generateValidationToken($referenceId, $amountInPaisa);

        // Prepare request data
        $requestData = [
            'merchantId' => (int)$this->merchantId,
            'appId' => $this->appId,
            'referenceId' => $referenceId,
            'txnAmt' => $amountInPaisa,
            'token' => $token,
        ];

        Log::info('ConnectIPS Validate Transaction Request', $requestData);

        // Make API request with basic authentication
        $response = Http::withBasicAuth($this->appId, $this->password)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post($this->baseUrl . '/connectipswebws/api/creditor/validatetxn', $requestData);

        if (!$response->successful()) {
            Log::error('ConnectIPS Validation Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception("Transaction validation failed: " . $response->body());
        }

        $result = $response->json();
        Log::info('ConnectIPS Validation Response', $result);

        return $result;
    }

    /**
     * Get transaction details
     *
     * @param string $referenceId
     * @param float $txnAmt
     * @return array
     * @throws Exception
     */
    public function getTransactionDetails(string $referenceId, float $txnAmt): array
    {
        // Convert amount to paisa
        $amountInPaisa = (int)($txnAmt * 100);

        // Generate token
        $token = $this->generateValidationToken($referenceId, $amountInPaisa);

        // Prepare request data
        $requestData = [
            'merchantId' => (int)$this->merchantId,
            'appId' => $this->appId,
            'referenceId' => $referenceId,
            'txnAmt' => $amountInPaisa,
            'token' => $token,
        ];

        Log::info('ConnectIPS Get Transaction Details Request', $requestData);

        // Make API request with basic authentication
        $response = Http::withBasicAuth($this->appId, $this->password)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post($this->baseUrl . '/connectipswebws/api/creditor/gettxndetail', $requestData);

        if (!$response->successful()) {
            Log::error('ConnectIPS Get Transaction Details Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception("Get transaction details failed: " . $response->body());
        }

        $result = $response->json();
        Log::info('ConnectIPS Transaction Details Response', $result);

        return $result;
    }

    /**
     * Check if transaction was successful
     *
     * @param array $validationResponse
     * @return bool
     */
    public function isTransactionSuccessful(array $validationResponse): bool
    {
        // Check status
        $status = $validationResponse['status'] ?? '';
        
        return $status === 'SUCCESS';
    }
}
