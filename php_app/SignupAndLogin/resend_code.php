<?php
// Resend verification code
session_start();
require_once 'config/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

echo "<h1>Resend Verification Code</h1>";

// Check if there's a pending verification
if (!isset($_SESSION['pending_verification'])) {
    echo "<p style='color: red;'>No pending verification found. Please start the signup process first.</p>";
    echo "<a href='signup.html'>Go to Signup</a>";
    exit;
}

$pending = $_SESSION['pending_verification'];

echo "<h2>Current Verification Details:</h2>";
echo "<p>Name: " . htmlspecialchars($pending['fullname']) . "</p>";
echo "<p>Email: " . htmlspecialchars($pending['email']) . "</p>";
echo "<p>Expires: " . date('Y-m-d H:i:s', $pending['expires']) . "</p>";

// Check if code has expired
if (time() > $pending['expires']) {
    echo "<p style='color: red;'>Verification code has expired. Please restart the signup process.</p>";
    unset($_SESSION['pending_verification']);
    echo "<a href='signup.html'>Restart Signup</a>";
    exit;
}

// Generate new code
$newCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
$_SESSION['pending_verification']['code'] = $newCode;
$_SESSION['pending_verification']['expires'] = time() + (3 * 60); // Reset expiry

echo "<h2>New Verification Code Generated:</h2>";
echo "<p style='font-size: 24px; font-weight: bold; color: #27ae60;'>$newCode</p>";
echo "<p>This code will expire in 3 minutes.</p>";

// Attempt to send email
echo "<h2>Email Sending:</h2>";

try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tshwanebusmate@gmail.com';
    $mail->Password = 'your_password'; // Change this to actual password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('tshwanebusmate@gmail.com', 'TshwaneBusMate');
    $mail->addAddress($pending['email'], $pending['fullname']);

    $mail->isHTML(true);
    $mail->Subject = 'Your New Verification Code - TshwaneBusMate';

    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
        <div style='text-align: center; padding: 20px; background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; border-radius: 10px 10px 0 0;'>
            <h1 style='margin: 0; font-size: 24px;'>TshwaneBusMate</h1>
            <p style='margin: 10px 0 0 0; font-size: 16px;'>Your trusted transportation companion</p>
        </div>

        <div style='padding: 30px; text-align: center;'>
            <h2 style='color: #27ae60; margin-bottom: 20px;'>New Verification Code</h2>
            <p style='font-size: 16px; color: #555; margin-bottom: 30px;'>Dear {$pending['fullname']},<br><br>You requested a new verification code. Please use the following code to verify your email address:</p>

            <div style='background: #f8f9fa; border: 2px solid #27ae60; border-radius: 8px; padding: 20px; margin: 20px 0; display: inline-block;'>
                <span style='font-size: 32px; font-weight: bold; color: #27ae60; letter-spacing: 5px;'>$newCode</span>
            </div>

            <p style='font-size: 14px; color: #666; margin-top: 20px;'>This code will expire in 3 minutes. Please come with your ID to the nearest TshwaneBusMate office to complete your bus card application.</p>

            <p style='font-size: 14px; color: #666; margin-top: 20px;'>If you didn't request this verification, please ignore this email or contact our support team.</p>
        </div>

        <div style='text-align: center; padding: 20px; font-size: 14px; color: #666;'>
            <p>Need help? Contact us at support@tshwanebusmate.co.za</p>
            <p style='margin: 5px 0;'>Follow us on social media:</p>
            <div style='margin-top: 10px;'>
                <a href='#' style='color: #27ae60; text-decoration: none; margin: 0 10px;'>Facebook</a>
                <a href='#' style='color: #27ae60; text-decoration: none; margin: 0 10px;'>Twitter</a>
                <a href='#' style='color: #27ae60; text-decoration: none; margin: 0 10px;'>Instagram</a>
            </div>
        </div>

        <div style='text-align: center; padding: 20px; font-size: 12px; color: #666;'>
            <p>© 2025 TshwaneBusMate. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
    ";

    $mail->AltBody = "
    TshwaneBusMate - New Verification Code

    Dear {$pending['fullname']},

    You requested a new verification code. Please use the following code to verify your email address:

    $newCode

    This code will expire in 3 minutes. Please come with your ID to the nearest TshwaneBusMate office to complete your bus card application.

    If you didn't request this verification, please ignore this email or contact our support team.

    Need help? Contact us at support@tshwanebusmate.co.za

    © 2025 TshwaneBusMate. All rights reserved.
    ";

    $mail->send();

    echo "<p style='color: green;'>✓ New verification code sent successfully to " . htmlspecialchars($pending['email']) . "</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Failed to send email: " . $mail->ErrorInfo . "</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<p>Enter the new verification code on the <a href='verify.php'>verification page</a></p>";
echo "<p>Or <a href='signup.html'>restart the signup process</a></p>";
?>
