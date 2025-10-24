<?php
// Test email sending functionality
require_once 'config/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

echo "<h1>Email Sending Test</h1>";

// Test PHPMailer setup
echo "<h2>PHPMailer Configuration Test:</h2>";

try {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tshwanebusmate@gmail.com';
    $mail->Password = 'your_password'; // This should be changed to actual password for testing
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    echo "<p style='color: green;'>✓ PHPMailer configured successfully</p>";
    echo "<p>SMTP Host: smtp.gmail.com</p>";
    echo "<p>SMTP Auth: Enabled</p>";
    echo "<p>Encryption: TLS</p>";
    echo "<p>Port: 587</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ PHPMailer configuration error: " . $e->getMessage() . "</p>";
}

// Test email form
echo "<h2>Send Test Email:</h2>";
echo "<form method='POST'>";
echo "<label>To: <input type='email' name='to_email' value='test@example.com' required></label><br>";
echo "<label>Subject: <input type='text' name='subject' value='Test Email from TshwaneBusMate' required></label><br>";
echo "<label>Message: <textarea name='message' rows='4' required>Test message from TshwaneBusMate email testing system.</textarea></label><br>";
echo "<input type='submit' name='send_test' value='Send Test Email'>";
echo "</form>";

if (isset($_POST['send_test'])) {
    $to = $_POST['to_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tshwanebusmate@gmail.com';
        $mail->Password = 'your_password'; // Change this to actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tshwanebusmate@gmail.com', 'TshwaneBusMate Test');
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        echo "<p style='color: green;'>✓ Test email sent successfully to $to</p>";

    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Failed to send test email: " . $mail->ErrorInfo . "</p>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Test verification email template
echo "<h2>Verification Email Template Preview:</h2>";

$testCode = '1234';
$testName = 'John Doe';
$testEmail = 'john.doe@example.com';

$emailBody = "
Welcome to TshwaneBusMate!

Dear $testName,

Thank you for choosing TshwaneBusMate as your trusted transportation companion. To verify your email address, please use the following code:

$testCode

This code will expire in 3 minutes. Please come with your ID to the nearest TshwaneBusMate office to complete your bus card application.

If you didn't request this verification, please ignore this email or contact our support team.

Need help? Contact us at support@tshwanebusmate.co.za

© 2025 TshwaneBusMate. All rights reserved.
";

echo "<pre style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd;'>";
echo htmlspecialchars($emailBody);
echo "</pre>";

echo "<h2>HTML Email Template Preview:</h2>";

$htmlBody = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
    <div style='text-align: center; padding: 20px; background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; border-radius: 10px 10px 0 0;'>
        <h1 style='margin: 0; font-size: 24px;'>Welcome to TshwaneBusMate!</h1>
        <p style='margin: 10px 0 0 0; font-size: 16px;'>Your trusted transportation companion</p>
    </div>

    <div style='padding: 30px; text-align: center;'>
        <h2 style='color: #27ae60; margin-bottom: 20px;'>Verify Your Email Address</h2>
        <p style='font-size: 16px; color: #555; margin-bottom: 30px;'>Dear $testName,<br><br>Thank you for choosing TshwaneBusMate as your trusted transportation companion. To verify your email address, please use the following code:</p>

        <div style='background: #f8f9fa; border: 2px solid #27ae60; border-radius: 8px; padding: 20px; margin: 20px 0; display: inline-block;'>
            <span style='font-size: 32px; font-weight: bold; color: #27ae60; letter-spacing: 5px;'>$testCode</span>
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

echo "<div style='border: 1px solid #ddd; padding: 10px; background: #f9f9f9;'>";
echo $htmlBody;
echo "</div>";
?>
