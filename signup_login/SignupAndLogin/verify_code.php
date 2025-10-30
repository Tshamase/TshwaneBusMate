<?php
session_start();
require_once 'config/db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// This file should only be accessed via POST from verification form
// If accessed directly, redirect to signup page
if (!isset($_SERVER["REQUEST_METHOD"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: signup.php");
    exit();
}

if (!isset($_SESSION['pending_verification'])) {
    $_SESSION['error'] = "No verification session found. Please start signup again.";
    header("Location: signup.php");
    exit();
}

$code = $_POST["verification_code"];
$stored = $_SESSION['pending_verification'];

// Debug: Log the codes for troubleshooting
error_log("Submitted code: " . $code);
error_log("Stored code: " . $stored['code']);
error_log("Time check: " . time() . " < " . $stored['expires'] . " = " . (time() < $stored['expires'] ? 'true' : 'false'));

if ($code == $stored['code'] && time() < $stored['expires']) {
    // Verification successful
    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$stored['fullname'], $stored['email']]);

        // Store user session data for login redirect
        $userId = $conn->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $stored['fullname'];
        $_SESSION['user_email'] = $stored['email'];
        $_SESSION['user_role'] = 'commuter'; // Default role for new signups

        unset($_SESSION['pending_verification']);
        header("Location: success_signup.html");
        exit();
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        error_log("Error code: " . $e->getCode());
        error_log("Attempting to insert: " . $stored['fullname'] . " / " . $stored['email']);

        // Check if it's a duplicate entry error
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "This email is already registered. Please try logging in instead.";
        } else {
            $_SESSION['error'] = "Error creating account. Please try again. Details: " . $e->getMessage();
        }
        header("Location: verify.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid or expired verification code.";
    header("Location: verify.php");
    exit();
}
?>
