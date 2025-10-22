<?php
// notify.php - PayFast ITN (Instant Transaction Notification) handler

require 'PayFastIntegration.php';
require 'db_payment.php';

// PayFast configuration (must match payment_gateway.php)
$config = [
    'merchant_id' => '10042793',            // sandbox merchant id
    'merchant_key' => 'h84dovz8djy3j',      // sandbox merchant key
    'passphrase'   => '',                    // optional passphrase
    'sandbox'      => true
];

$pf = new PayFastIntegration($config);

// Capture raw POST (PayFast sends form-encoded POST)
$post = $_POST;

// Log for debugging (make sure logs are protected in production)
$logFile = 'payfast_itn.log';
$logMessage = date('c') . " RAW POST: " . print_r($post, true) . PHP_EOL;
file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

// Validate ITN signature and basic checks
$validation = $pf->validateITN($post);
if (!$validation['valid']) {
    // Log and exit with 400 or 200 depending on how strict you want it.
    file_put_contents($logFile, date('c') . " Validation failed: " . $validation['reason'] . PHP_EOL, FILE_APPEND | LOCK_EX);
    // MUST return HTTP 200 to PayFast to prevent retries
    http_response_code(200);
    exit;
}

// Verify amounts / merchant id / order exists
$m_payment_id = $post['m_payment_id'] ?? null;
$pfAmount = $post['amount_gross'] ?? $post['amount'] ?? null;
$pfStatus = $post['payment_status'] ?? null;

// Only process COMPLETE payments
if ($pfStatus !== 'COMPLETE') {
    file_put_contents($logFile, date('c') . " Payment status not COMPLETE: $pfStatus" . PHP_EOL, FILE_APPEND | LOCK_EX);
    http_response_code(200);
    exit;
}

// Load order from database
$query = "SELECT * FROM orders WHERE m_payment_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $m_payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    file_put_contents($logFile, date('c') . " Order not found for m_payment_id: $m_payment_id" . PHP_EOL, FILE_APPEND | LOCK_EX);
    http_response_code(200);
    exit;
}

$order = $result->fetch_assoc();
$userId = $order['user_id'];
$orderAmount = $order['amount'];
$transactionType = $order['transaction_type'];
$itemName = $order['item_name'];

// Verify amount matches
if (number_format($orderAmount, 2, '.', '') !== number_format($pfAmount, 2, '.', '')) {
    file_put_contents($logFile, date('c') . " Amount mismatch: order=$orderAmount, payfast=$pfAmount" . PHP_EOL, FILE_APPEND | LOCK_EX);
    http_response_code(200);
    exit;
}

// Check if already processed (prevent duplicate processing)
$query = "SELECT id FROM transactions WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $order['order_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    file_put_contents($logFile, date('c') . " Transaction already processed for order_id: " . $order['order_id'] . PHP_EOL, FILE_APPEND | LOCK_EX);
    http_response_code(200);
    exit;
}

// Calculate credits to add based on transaction type
$creditsToAdd = 0;
$description = '';

if ($transactionType === 'reload') {
    // For reload, calculate net credits after charges
    $charges = $orderAmount <= 60 ? 1.5 : $orderAmount * 0.025;
    $creditsToAdd = $orderAmount - $charges;
    $description = 'Credit Reload';
} elseif ($transactionType === 'purchase') {
    // For purchase, calculate credits earned based on product values
    $productValues = explode(', ', $itemName);
    foreach ($productValues as $product) {
        // Extract numeric value from product name
        preg_match('/(\d+)/', $product, $matches);
        if (isset($matches[1])) {
            $value = (float)$matches[1];
            // Calculate credits based on value (simplified logic)
            if ($value == 20) $creditsToAdd += 20;
            elseif ($value == 60) $creditsToAdd += 60;
            elseif ($value == 80) $creditsToAdd += 96;
            elseif ($value == 100) $creditsToAdd += 122;
            elseif ($value == 150) $creditsToAdd += 185;
            elseif ($value == 200) $creditsToAdd += 250;
            elseif ($value == 350) $creditsToAdd += 445;
            elseif ($value == 500) $creditsToAdd += 640;
        }
    }
    $description = 'Product Purchase: ' . $itemName;
}

// Get current balance
$query = "SELECT balance FROM transactions WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$currentBalance = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentBalance = $row['balance'];
}

$newBalance = $currentBalance + $creditsToAdd;

// Insert transaction record
$insertQuery = "INSERT INTO transactions (user_id, amount, transaction_type, description, balance, order_id) VALUES (?, ?, 'credit', ?, ?, ?)";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param("idsss", $userId, $creditsToAdd, $description, $newBalance, $order['order_id']);
$insertStmt->execute();
$insertStmt->close();

// Update order status
$updateQuery = "UPDATE orders SET payment_status = 'COMPLETE' WHERE m_payment_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("s", $m_payment_id);
$updateStmt->execute();
$updateStmt->close();

file_put_contents($logFile, date('c') . " Payment processed successfully for m_payment_id: $m_payment_id, credits added: $creditsToAdd, new balance: $newBalance" . PHP_EOL, FILE_APPEND | LOCK_EX);

// Respond 200
http_response_code(200);
echo "OK";
