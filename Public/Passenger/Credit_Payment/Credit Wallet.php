<?php
include 'Database.php';
$userId = 1; // Example user ID
$query = "SELECT balance FROM transactions WHERE id = $userId";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
  $balance = htmlspecialchars($row['balance']) . " credits";
} else {
  $balance = "Unable to fetch balance";
}
include 'credit_wallet.html';
?>
