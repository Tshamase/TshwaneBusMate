<?php
session_start();
require 'db_payment.php';

// Retrieve amount from session
$amount = $_SESSION['payment_amount'] ?? 0;

// Connect to database
$conn = new mysqli($servername, $username, $password);
$conn->select_db($dbname);

// Update balance for userId=1 (assuming single user for now)
$userId = 1;
$query = "UPDATE transactions SET balance = balance + ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("di", $amount, $userId);
$stmt->execute();
$stmt->close();

// Insert transaction record for history
$insertQuery = "INSERT INTO transactions (user_id, amount, transaction_type, description) VALUES (?, ?, 'credit', 'Credit Reload')";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param("id", $userId, $amount);
$insertStmt->execute();
$insertStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successful transaction</title>
</head>

<body>
    <h2>✅ Payment Successful!</h2>
    <p>Thank you for your purchase. <br>
        Credits reloaded successfully.
    </p>
    <a href="Credit Wallet.php"> Back to credit wallet.</a>

</body>

</html>