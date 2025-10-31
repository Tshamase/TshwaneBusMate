<?php
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "Testing SMTP connection...\n";

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'xuluslondiwe14@gmail.com';
    $mail->Password = 'ubgv nral whad vbgr';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->Debugoutput = 'echo'; // Output debug info to screen

    $mail->setFrom('tshwanebusmate@gmail.com', 'TshwaneBusMate');
    $mail->addAddress('tshwanebusmate@gmail.com', 'Test User'); // Send to yourself first

    $mail->isHTML(true);
    $mail->Subject = 'Test Email from TshwaneBusMate';
    $mail->Body = 'This is a test email to verify SMTP configuration.';

    echo "Attempting to send email...\n";
    $mail->send();
    echo "Email sent successfully!\n";
} catch (Exception $e) {
    echo "Email failed: " . $mail->ErrorInfo . "\n";
}
?>
