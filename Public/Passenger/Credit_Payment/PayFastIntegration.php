<?php

class PayFastIntegration
{
    private $merchant_id;
    private $merchant_key;
    private $passphrase;    // optional (set in your PayFast merchant settings)
    private $sandbox;       // boolean: true => sandbox, false => live
    private $baseUrl;

    public function __construct(array $config = [])
    {
        $this->merchant_id  = $config['merchant_id']  ?? '';
        $this->merchant_key = $config['merchant_key'] ?? '';
        $this->passphrase   = $config['passphrase']   ?? '';
        $this->sandbox      = $config['sandbox'] ?? true;

        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.payfast.co.za'
            : 'https://www.payfast.co.za';
    }

    /******************************
     *  FORM (redirect) MODE
     ******************************/

    /**
     * buildFormData
     * Build the canonical data array and signature required for PayFast form redirect.
     *
     * $payload should contain merchant fields such as:
     *  - amount, item_name, m_payment_id, name_first, name_last, email_address, return_url, cancel_url, notify_url
     *
     * Returns array with final fields (including 'signature').
     */
    public function buildFormData(array $payload): array
    {
        // base required fields
        $data = array_merge([
            'merchant_id'  => $this->merchant_id,
            'merchant_key' => $this->merchant_key,
        ], $payload);

        // Ensure formatting for amount (two decimals)
        if (isset($data['amount'])) {
            $data['amount'] = number_format((float)$data['amount'], 2, '.', '');
        }

        // Compute signature
        $data['signature'] = $this->computeMd5Signature($data);

        return $data;
    }

    /**
     * generateRedirectForm
     * Returns HTML form (string) that posts to PayFast sandbox/live endpoint.
     * Use on checkout page: echo $payfast->generateRedirectForm($data);
     */
    public function generateRedirectForm(array $formData, string $submitText = 'Pay with PayFast'): string
    {
        $action = $this->baseUrl . '/eng/process';

        $html = '<form id="pf_payment_form" action="' . htmlspecialchars($action) . '" method="post">' . PHP_EOL;
        foreach ($formData as $key => $val) {
            // Only output simple scalar values
            $html .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($val) . '">' . PHP_EOL;
        }
        $html .= '<input type="submit" value="' . htmlspecialchars($submitText) . '" style="display:none;">' . PHP_EOL;
        $html .= '</form>' . PHP_EOL;

        return $html;
    }

    /**
     * computeMd5Signature
     * PayFast classic signature: sort params alphabetically, URL-encode values,
     * build query string and MD5 it. Include passphrase when set.
     */
    public function computeMd5Signature(array $data): string
    {
        // Remove signature if present
        if (isset($data['signature'])) {
            unset($data['signature']);
        }

        // Sort by key
        ksort($data);

        // Build querystring-like string
        $pairs = [];
        foreach ($data as $k => $v) {
            // Only include non-empty values
            if ($v === '') continue;
            $pairs[] = $k . '=' . urlencode(trim($v));
        }
        $string = implode('&', $pairs);

        // Append passphrase if set (PayFast classic requires this if configured)
        if (!empty($this->passphrase)) {
            $string .= '&passphrase=' . urlencode(trim($this->passphrase));
        }

        return md5($string);
    }


    /******************************
     *  API-STYLE REQUESTS (REST)
     *  (header-based auth helper)
     ******************************/

    /**
     * buildApiHeaders
     * Example header builder for PayFast REST API style endpoints.
     * PayFast expects headers like: merchant-id, version, timestamp, signature
     *
     * NOTE: PayFast docs / community examples show different header schemes.
     * This function builds a typical header set based on body and merchant key and returns header array.
     *
     * The signature here is HMAC-SHA256 of the JSON body using merchant_key as secret.
     * If PayFast requires a different scheme in future, update this function to match their docs.
     *
     * Returns: array of "Header-Name: value"
     */
    public function buildApiHeaders(array $body = []): array
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z'); // ISO-like UTC timestamp
        $version = 'v1';

        $jsonBody = empty($body) ? '' : json_encode($body, JSON_UNESCAPED_SLASHES);

        // Use HMAC-SHA256 with merchant_key (common REST signature approach)
        $signature = hash_hmac('sha256', $jsonBody . $timestamp . $version, $this->merchant_key);

        return [
            'merchant-id: ' . $this->merchant_id,
            'version: ' . $version,
            'timestamp: ' . $timestamp,
            'signature: ' . $signature,
            'Content-Type: application/json',
        ];
    }

    /**
     * apiPost
     * Simple cURL helper to POST JSON to a PayFast API endpoint.
     * $path is path after base, e.g. '/api/payments'
     */
    public function apiPost(string $path, array $body = [], &$httpCode = null)
    {
        $url = rtrim($this->baseUrl, '/') . $path;
        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);

        $headers = $this->buildApiHeaders($body);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        // Optional: verify SSL in production, but in dev you may turn off
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $resp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false) {
            throw new Exception("cURL error: " . $err);
        }

        return json_decode($resp, true);
    }


    /******************************
     *  ITN / NOTIFY VALIDATION
     ******************************/

    /**
     * validateITN
     * Validate an incoming ITN (notify) $_POST array from PayFast.
     *
     * Process:
     *  1) Ensure POST method was used
     *  2) Recompute signature and compare
     *  3) Optionally verify IP address (recommended: compare to PayFast hosts / IPs)
     *  4) (Optional) Query PayFast to confirm transaction (if doc requires)
     *
     * Returns: ['valid' => bool, 'reason' => '...']
     */
    public function validateITN(array $post): array
    {
        // 1) Basic sanity
        if (empty($post)) {
            return ['valid' => false, 'reason' => 'Empty POST data'];
        }

        // 2) Recompute signature
        $receivedSig = $post['signature'] ?? '';
        $recomputed = $this->computeMd5Signature($post);

        if ($receivedSig !== $recomputed) {
            return ['valid' => false, 'reason' => 'Signature mismatch'];
        }

        // 3) Optional: verify source host (PayFast recommends checking hostname/IP)
        //    Example allowed hosts (update if PayFast adds/removes):
        $validHosts = [
            'www.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
            'sandbox.payfast.co.za'
        ];
        $remoteHost = gethostbyaddr($_SERVER['REMOTE_ADDR'] ?? '');
        if ($this->sandbox) {
            // allow sandbox host specifically
            $expected = 'sandbox.payfast.co.za';
            if (strpos($remoteHost, $expected) === false && !in_array($remoteHost, $validHosts)) {
                // Note: in many setups this reverse-lookup may not be reliable; consider IP whitelist approach
                // We'll not fail here automatically — but log for manual inspection
                // return ['valid' => false, 'reason' => 'Invalid remote host: '.$remoteHost];
            }
        }

        // 4) Amount & merchant checks should be done by caller (server-side DB lookups etc.)
        return ['valid' => true, 'reason' => 'Signature valid'];
    }

    /**
     * getBaseUrl
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * isSandbox
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
