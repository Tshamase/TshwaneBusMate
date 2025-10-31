//signup processing
<?php
session_start();
require_once 'config/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

//debug settings
error_reporting(E_ALL);
ini_set('display_errors', 1);

//access control
if (!isset($_SERVER["REQUEST_METHOD"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<h1>Access Denied</h1><p>This page can only be accessed via the signup form.</p><a href='signup.html'>Go back to signup</a>";
    exit();
}

//validation variables
$fullnameErr = $emailErr = "";
$fullname = $email = "";
$isValid = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //fullname validation
    if (empty($_POST["fullname"])) {
        $fullnameErr = "Full name is required";
        $isValid = false;
    } else {
        $fullname = test_input($_POST["fullname"]);
        //name format check
        if (!preg_match("/^[a-zA-Z ]*$/", $fullname)) {
            $fullnameErr = "Only letters and white space allowed";
            $isValid = false;
        }
        //name parts length check
        $nameParts = explode(' ', $fullname);
        foreach ($nameParts as $part) {
            if (strlen($part) < 2) {
                $fullnameErr = "Name and surname are too short";
                $isValid = false;
                break;
            }
        }
    }

    //email validation
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $isValid = false;
    } else {
        $email = test_input($_POST["email"]);
        //email format check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $isValid = false;
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@(gmail\.com|outlook\.com)$/', $email)) {
            $emailErr = "email domain must be outlook.com or gmail.com";
            $isValid = false;
        } else {
            //email uniqueness check
            try {
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->rowCount() > 0) {
                    $emailErr = "You have signed up before using these details. Please check your emails for the further information that was provided with the code. Then after completing that step, you may log in.";
                    $isValid = false;
                }
            } catch(PDOException $e) {
                error_log("Email check error: " . $e->getMessage());
                $emailErr = "Error checking email. Please try again.";
                $isValid = false;
            }
        }
    }

    //success processing
    if ($isValid) {
        try {
            //verification code generation
            $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            //session storage
            $_SESSION['pending_verification'] = [
                'fullname' => $fullname,
                'email' => $email,
                'code' => $verificationCode,
                'expires' => time() + (3 * 60) // 3 minutes
            ];

            //email sending
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
                $mail->addAddress($email, $fullname);

                $mail->isHTML(true);
                $mail->Subject = 'Verify Your TshwaneBusMate Account';

                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                        <div style='text-align: center; padding: 20px; background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; border-radius: 10px 10px 0 0;'>
                            <h1 style='margin: 0; font-size: 24px;'>Welcome to TshwaneBusMate!</h1>
                            <p style='margin: 10px 0 0 0; font-size: 16px;'>Your trusted transportation companion</p>
                        </div>

                        <div style='padding: 30px; text-align: center;'>
                            <h2 style='color: #27ae60; margin-bottom: 20px;'>Verify Your Email Address</h2>
                            <p style='font-size: 16px; color: #555; margin-bottom: 30px;'>Dear $fullname,<br><br>Thank you for choosing TshwaneBusMate as your trusted transportation companion. To verify your email address, please use the following code:</p>

                            <div style='background: #f8f9fa; border: 2px solid #27ae60; border-radius: 8px; padding: 20px; margin: 20px 0; display: inline-block;'>
                                <span style='font-size: 32px; font-weight: bold; color: #27ae60; letter-spacing: 5px;'>$verificationCode</span>
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

                // Also set plain text version for email clients that don't support HTML
                $mail->AltBody = "
                    Welcome to TshwaneBusMate!

                    Dear $fullname,

                    Thank you for choosing TshwaneBusMate as your trusted transportation companion. To verify your email address, please use the following code:

                    $verificationCode

                    This code will expire in 3 minutes. Please come with your ID to the nearest TshwaneBusMate office to complete your bus card application.

                    If you didn't request this verification, please ignore this email or contact our support team.

                    Need help? Contact us at support@tshwanebusmate.co.za

                    © 2025 TshwaneBusMate. All rights reserved.
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Email sending error: " . $e->getMessage());
                $_SESSION['error_message'] = "Failed to send verification email. Please try again.";
                header("Location: signup.php");
                exit();
            }

            // Store pending verification data
            $_SESSION['pending_verification'] = [
                'fullname' => $fullname,
                'email' => $email,
                'code' => $verificationCode,
                'expires' => time() + (3 * 60) // 3 minutes
            ];

            //redirect to verification
            header("Location: verify.php");
            exit();

        } catch(Exception $e) {
            error_log("General error: " . $e->getMessage());
            $_SESSION['error_message'] = "An error occurred. Please try again.";
            header("Location: signup.php");
            exit();
        }
    } else {
        //error handling redirect
        $errorParams = [];
        if ($fullnameErr) $errorParams[] = "fullname_error=" . urlencode($fullnameErr);
        if ($emailErr) $errorParams[] = "email_error=" . urlencode($emailErr);
        if ($fullname) $errorParams[] = "fullname=" . urlencode($fullname);
        if ($email) $errorParams[] = "email=" . urlencode($email);
        $errorParams[] = "error=validation";

        header("Location: signup.php?" . implode("&", $errorParams));
        exit();
    }
}

//input sanitization
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
