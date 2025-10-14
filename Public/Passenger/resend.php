<?php
//Starts session to access saved user data
session_start();

//Makes sure user came from signup and has required session info
if (!isset($_SESSION['pending_user_id'], $_SESSION['pending_phone'])) {
    //If not, send them back to signup page
    header("Location: signup1.php");
    exit();
}

//Gets user ID and phone number from session
$userId = (int)$_SESSION['pending_user_id'];
$phone = $_SESSION['pending_phone'];

//Connects to the tshwanebusmate database
$conn = new mysqli('localhost', 'root', '', 'tshwanebusmate');
if ($conn->connect_error) die('Connection Failed: ' . $conn->connect_error);

//Checks when the last OTP was sent
$check = $conn->prepare("SELECT created_at FROM otp_codes WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$check->bind_param("i", $userId);
$check->execute();
$res = $check->get_result();
$last = $res->fetch_assoc();
$check->close();

//If last OTP was sent less than 60 seconds ago, block resend
//To avoid sending it numerous times
if ($last && strtotime($last['created_at']) > time() - 60) {
    $_SESSION['otp_error'] = "Please wait a moment before requesting another code.";
    $conn->close();
    header("Location: otp.php");
    exit();
}

//Marks any old unused OTPs as used, due to time constraints
$conn->query("UPDATE otp_codes SET used = 1 WHERE user_id = $userId AND used = 0");

//Creates a new 4-digit OTP
$otp = random_int(1000, 9999);

//Hashes the OTP for security
//form of encryption
$hash = password_hash((string)$otp, PASSWORD_DEFAULT);

//Sets expiry time for OTP (5 minutes from now)
$expiresAt = date('Y-m-d H:i:s', time() + 5 * 60);

//Saves the new OTP in the database
$stmt = $conn->prepare("INSERT INTO otp_codes (user_id, phone, otp_hash, expires_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $userId, $phone, $hash, $expiresAt);
$stmt->execute();
$stmt->close();

//For testing: stores OTP in session so it can be shown on screen
$_SESSION['dev_otp'] = $otp;

//Saves the time OTP was sent for database table
$_SESSION['otp_last_sent'] = date('H:i:s');

//Counts how many times OTP has been resent
$_SESSION['otp_resend_count'] = ($_SESSION['otp_resend_count'] ?? 0) + 1;

//Logs OTP in server logs (for developers only)
error_log("DEV RESEND OTP for $phone: $otp");

//Closes database connection
$conn->close();

//Shows message to user and go back to OTP page
$_SESSION['otp_error'] = "A new code was sent at {$_SESSION['otp_last_sent']} (Resent {$_SESSION['otp_resend_count']} times)";
header("Location: otp.php");
exit();
?>
