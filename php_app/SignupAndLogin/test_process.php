<?php
// Test the signup process
echo "<h1>Signup Process Test</h1>";

// Simulate POST data
$_POST['fullname'] = 'Test User';
$_POST['email'] = 'test' . rand(1000, 9999) . '@example.com';

echo "<h2>Simulated POST Data:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Include the process_signup.php logic
require_once 'config/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to sanitize input
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$fullnameErr = $emailErr = "";
$fullname = $email = "";
$isValid = true;

// Validate Full Name
if (empty($_POST["fullname"])) {
    $fullnameErr = "Full name is required";
    $isValid = false;
} else {
    $fullname = test_input($_POST["fullname"]);
    // Check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/", $fullname)) {
        $fullnameErr = "Only letters and white space allowed";
        $isValid = false;
    }
    // Check name parts length
    $nameParts = explode(' ', $fullname);
    foreach ($nameParts as $part) {
        if (strlen($part) < 2) {
            $fullnameErr = "Name and surname are too short";
            $isValid = false;
            break;
        }
    }
}

// Validate Email
if (empty($_POST["email"])) {
    $emailErr = "Email is required";
    $isValid = false;
} else {
    $email = test_input($_POST["email"]);
    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $isValid = false;
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $emailErr = "Invalid email format";
        $isValid = false;
    } elseif (strpos($email, '..') !== false || strpos($email, '@.') !== false || strpos($email, '.@') !== false) {
        $emailErr = "Invalid email format";
        $isValid = false;
    } else {
        // Check if email already exists
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

echo "<h2>Validation Results:</h2>";
echo "<p>Full Name: " . ($fullnameErr ? "<span style='color: red;'>$fullnameErr</span>" : "<span style='color: green;'>Valid</span>") . "</p>";
echo "<p>Email: " . ($emailErr ? "<span style='color: red;'>$emailErr</span>" : "<span style='color: green;'>Valid</span>") . "</p>";
echo "<p>Overall: " . ($isValid ? "<span style='color: green;'>PASSED</span>" : "<span style='color: red;'>FAILED</span>") . "</p>";

if ($isValid) {
    echo "<h2>Verification Code Generation:</h2>";
    $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    echo "<p>Generated code: <strong>$verificationCode</strong></p>";

    echo "<h2>Session Data to be Stored:</h2>";
    $sessionData = [
        'fullname' => $fullname,
        'email' => $email,
        'code' => $verificationCode,
        'expires' => time() + (3 * 60)
    ];
    echo "<pre>";
    print_r($sessionData);
    echo "</pre>";

    echo "<p style='color: green;'>✓ Process would redirect to verify.php</p>";
} else {
    echo "<p style='color: red;'>✗ Process would redirect back to signup.php with errors</p>";
}
?>
