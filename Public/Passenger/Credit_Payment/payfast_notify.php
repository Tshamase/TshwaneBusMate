<?php
require 'db_payment.php';

// Connect to MySQL server (no database yet)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
$conn->select_db($dbname);

// STEP 1: Capture POST data
$pfData = $_POST;

// STEP 2: Verify signature
$signature = $pfData['signature'];
unset($pfData['signature']);

$pfOutput = '';
foreach ($pfData as $key => $val) {
    if (!empty($val)) {
        $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
    }
}
$passPhrase = ''; // Add if you have one
$checkSig = md5(substr($pfOutput, 0, -1) . ($passPhrase ? '&passphrase=' . urlencode($passPhrase) : ''));

if ($signature !== $checkSig) {
    http_response_code(400);
    exit("Invalid signature");
}

// STEP 3: Validate payment status
$order_id = $pfData['m_payment_id'];
$payment_status = $pfData['payment_status'];

if ($payment_status === "COMPLETE") {
    $update = $conn->prepare("UPDATE orders SET payment_status = 'COMPLETE' WHERE order_id = ?");
    $update->bind_param("s", $order_id);
    $update->execute();
    $update->close();

    // Also update balance in transactions table
    $userId = 1; // Assuming single user
    $balanceUpdate = $conn->prepare("UPDATE transactions SET balance = balance + (SELECT amount FROM orders WHERE order_id = ?) WHERE id = ?");
    $balanceUpdate->bind_param("si", $order_id, $userId);
    $balanceUpdate->execute();
    $balanceUpdate->close();
}

http_response_code(200);
echo "OK";

$conn->close();
