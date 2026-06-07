<?php

class pga
{
    private $baseUrl;
    private $merchantUid;
    private $clientName;

    public function __construct()
    {
        $this->baseUrl = 'https://rest.otomatis.vip/api';
        $this->merchantUid = 'a379377a-fa37-4084-9699-04381445f533';
        $this->clientName = 'dewafafa188';
    }

    /**
     * Generate QRIS Payment
     * 
     * @param string $username
     * @param int $amount
     * @param int $expire (seconds, default: 1200)
     * @param string $customRef
     * @return array
     */
    public function generateQris($username, $amount, $expire = 1200, $customRef = "")
    {
        try {
            $payload = [
                'username' => $username,
                'amount' => (int) $amount,
                'uuid' => $this->merchantUid,
                'expire' => (int) $expire,
                'custom_ref' => $customRef
            ];

            $response = $this->makeRequest('POST', '/generate', $payload);

            return $this->formatResponse(true, 200, 'QRIS generated successfully', $response);

        } catch (Exception $e) {
            return $this->formatResponse(false, 500, $e->getMessage());
        }
    }

    /**
     * Check Payment Status
     * 
     * @param string $transactionId
     * @return array
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            $payload = [
                'uuid' => $this->merchantUid,
                'client' => $this->clientName,
            ];

            $response = $this->makeRequest('POST', '/checkstatus/v2/' . $transactionId, $payload);

            return $this->formatResponse(true, 200, 'Status retrieved successfully', $response);

        } catch (Exception $e) {
            return $this->formatResponse(false, 500, $e->getMessage());
        }
    }

    /**
     * Transfer Inquiry
     * 
     * @param int $amount
     * @param string $bankCode
     * @param string $accountNumber
     * @param int $type (default: 1)
     * @return array
     */
    public function inquiryTransfer($amount, $bankCode, $accountNumber, $type = 1)
    {
        try {
            $payload = [
                'merchant_uid' => $this->merchantUid,
                'client_name' => $this->clientName,
                'amount' => (int) $amount,
                'bank_code' => $bankCode,
                'account_number' => $accountNumber,
                'type' => (int) $type
            ];

            $response = $this->makeRequest('POST', '/transfer/inquiry', $payload);

            return $this->formatResponse(true, 200, 'Transfer inquiry successful', $response);

        } catch (Exception $e) {
            return $this->formatResponse(false, 500, $e->getMessage());
        }
    }

    /**
     * Execute Transfer
     * 
     * @param int $amount
     * @param string $bankCode
     * @param string $accountNumber
     * @param int $inquiryId
     * @param int $type (default: 1)
     * @return array
     */
    public function executeTransfer($amount, $bankCode, $accountNumber, $inquiryId, $type = 1)
    {
        try {
            $payload = [
                'merchant_uid' => $this->merchantUid,
                'client_name' => $this->clientName,
                'amount' => (int) $amount,
                'bank_code' => $bankCode,
                'account_number' => $accountNumber,
                'inquiry_id' => (int) $inquiryId,
                'type' => (int) $type
            ];

            $response = $this->makeRequest('POST', '/transfer/execute', $payload);

            return $this->formatResponse(true, 200, 'Transfer executed successfully', $response);

        } catch (Exception $e) {
            return $this->formatResponse(false, 500, $e->getMessage());
        }
    }

    /**
     * Get Bank Codes
     * 
     * @return array
     */
    public function getBankCodes()
    {
        try {
            $payload = [
                'merchant_uid' => $this->merchantUid,
                'client_name' => $this->clientName
            ];

            $response = $this->makeRequest('POST', '/bank/codes', $payload);

            return $this->formatResponse(true, 200, 'Bank codes retrieved successfully', $response);

        } catch (Exception $e) {
            return $this->formatResponse(false, 500, $e->getMessage());
        }
    }

    /**
     * Make HTTP Request using cURL
     * 
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makeRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new Exception('HTTP Error: ' . $httpCode . ' - ' . $response);
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }

        return $decodedResponse;
    }

    /**
     * Format response
     * 
     * @param bool $success
     * @param int $code
     * @param string $message
     * @param mixed $data
     * @return array
     */
    private function formatResponse($success, $code, $message, $data = null)
    {
        return [
            'success' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ];
    }
}

?>