<?php
session_start();
require_once 'config/db_connect.php';

// PHPMailer setup
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle AJAX requests
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Handle resend code request
if (isset($_POST['action']) && $_POST['action'] === 'resend_code') {
    if (!isset($_SESSION['pending_verification'])) {
        echo json_encode(['success' => false, 'message' => 'No verification session found']);
        exit();
    }

    try {
        // Generate new 4-digit verification code
        $newCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Update session
        $_SESSION['pending_verification']['code'] = $newCode;
        $_SESSION['pending_verification']['expires'] = time() + (3 * 60); // Reset to 3 minutes

        try {

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'xuluslondiwe14@gmail.com';
            $mail->Password = 'ubgv nral whad vbgr'; // Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('xuluslondiwe14@gmail.com', 'TshwaneBusMate');
            $mail->addAddress($_SESSION['pending_verification']['email'], $_SESSION['pending_verification']['fullname']);

            $mail->isHTML(true);
            $mail->Subject = 'New Verification Code - TshwaneBusMate';

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <div style='text-align: center; padding: 20px; background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; border-radius: 10px 10px 0 0;'>
                        <h1 style='margin: 0; font-size: 24px;'>TshwaneBusMate</h1>
                        <p style='margin: 10px 0 0 0; font-size: 16px;'>New Verification Code</p>
                    </div>

                    <div style='padding: 30px; text-align: center;'>
                        <h2 style='color: #27ae60; margin-bottom: 20px;'>Your New Verification Code</h2>
                        <p style='font-size: 16px; color: #555; margin-bottom: 30px;'>Dear {$_SESSION['pending_verification']['fullname']},<br><br>Here is your new verification code:</p>

                        <div style='background: #f8f9fa; border: 2px solid #27ae60; border-radius: 8px; padding: 20px; margin: 20px 0; display: inline-block;'>
                            <span style='font-size: 32px; font-weight: bold; color: #27ae60; letter-spacing: 5px;'>$newCode</span>
                        </div>

                        <p style='font-size: 14px; color: #666; margin-top: 20px;'>This code will expire in 3 minutes.</p>
                    </div>

                    <div style='text-align: center; padding: 20px; font-size: 12px; color: #666;'>
                        <p>© 2025 TshwaneBusMate. All rights reserved.</p>
                    </div>
                </div>
            ";

            $mail->AltBody = "
                TshwaneBusMate - New Verification Code

                Dear {$_SESSION['pending_verification']['fullname']},

                Your new verification code is: $newCode

                This code will expire in 3 minutes.

                © 2025 TshwaneBusMate. All rights reserved.
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Resend email error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to send new code']);
            exit();
        }

        echo json_encode(['success' => true, 'newCode' => $newCode]);

    } catch (Exception $e) {
        error_log("Resend code error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to send new code']);
    }
    exit();
}

// If no valid action
echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit();
?>
