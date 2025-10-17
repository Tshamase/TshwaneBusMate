<?php
session_start();
require 'db_payment.php';

// Retrieve amount from session
$amount = $_SESSION['payment_amount'] ?? 0;
if ($amount <= 0) {
    die("Invalid amount");
}

// Order info
$order_id = 'ORD' . time();
$item_name = 'Credit Reload';
$transact_timestamp = date("Y-m-d H:i:s");

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
$conn->select_db($dbname);

// Save order to DB
$stmt = $conn->prepare("INSERT INTO orders (order_id, amount, item_name) VALUES (?, ?, ?)");
$stmt->bind_param("sds", $order_id, $amount, $item_name);
$stmt->execute();
$stmt->close();

// PayFast sandbox credentials
$merchant_id = "10000100";
$merchant_key = "46f0cd694581a";
$return_url = "success.php";
$cancel_url = "cancel.php";
$notify_url = "payfast_notify.php";

// Build form data
$data = array(
    'merchant_id' => $merchant_id,
    'merchant_key' => $merchant_key,
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'notify_url' => $notify_url,
    'm_payment_id' => $order_id,
    'amount' => number_format(sprintf('%.2f', $amount), 2, '.', ''),
    'item_name' => $item_name
);

// Sort & sign data
$pfOutput = '';
foreach ($data as $key => $val) {
    if (!empty($val)) {
        $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
    }
}

ksort($data);
$signatureString = http_build_query($data, '', '&');
// If using passphrase:
$signatureString .= '&passphrase=' . urlencode('YOUR_PASSPHRASE');
// signature:
$signature = md5($signatureString);


// PayFast URL
$payfast_url = "https://sandbox.payfast.co.za/eng/process";

$conn->close();

?>

<form action="<?php echo $payfast_url; ?>" method="post">
    <?php foreach ($data as $name => $value): ?>
        <input type="hidden" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($value); ?>">
    <?php endforeach; ?>
    <button type="submit">Pay with PayFast</button>
</form>