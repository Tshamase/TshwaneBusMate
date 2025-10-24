<?php
// Test full signup process end-to-end
session_start();
require_once 'config/db_connect.php';

echo "<h1>Full Signup Process Test</h1>";

// Step 1: Simulate signup form submission
echo "<h2>Step 1: Signup Form Submission</h2>";

$testData = [
    'fullname' => 'Test User ' . rand(100, 999),
    'email' => 'test' . rand(1000, 9999) . '@example.com'
];

echo "<p>Submitting: " . htmlspecialchars($testData['fullname']) . " (" . htmlspecialchars($testData['email']) . ")</p>";

// Simulate POST data
$_POST = $testData;

// Include process_signup.php logic
$fullnameErr = $emailErr = "";
$fullname = $email = "";
$isValid = true;

// Validate Full Name
if (empty($_POST["fullname"])) {
    $fullnameErr = "Full name is required";
    $isValid = false;
} else {
    $fullname = test_input($_POST["fullname"]);
    if (!preg_match("/^[a-zA-Z ]*$/", $fullname)) {
        $fullnameErr = "Only letters and white space allowed";
        $isValid = false;
    }
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
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $emailErr = "Email already exists";
                $isValid = false;
            }
        } catch(PDOException $e) {
            $emailErr = "Database error";
            $isValid = false;
        }
    }
}

if ($isValid) {
    echo "<p style='color: green;'>✓ Validation passed</p>";

    $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $_SESSION['pending_verification'] = [
        'fullname' => $fullname,
        'email' => $email,
        'code' => $verificationCode,
        'expires' => time() + (3 * 60)
    ];

    echo "<p>Generated verification code: <strong>$verificationCode</strong></p>";
    echo "<p style='color: green;'>✓ Session data stored</p>";
} else {
    echo "<p style='color: red;'>✗ Validation failed: $fullnameErr $emailErr</p>";
    exit;
}

// Step 2: Simulate verification
echo "<h2>Step 2: Verification Code Submission</h2>";
echo "<p>Submitting verification code: <strong>$verificationCode</strong></p>";

$_POST['verification_code'] = $verificationCode;

if (!isset($_SESSION['pending_verification'])) {
    echo "<p style='color: red;'>✗ No verification session found</p>";
    exit;
}

$code = $_POST["verification_code"];
$stored = $_SESSION['pending_verification'];

if ($code == $stored['code'] && time() < $stored['expires']) {
    echo "<p style='color: green;'>✓ Code verification passed</p>";

    try {
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, created_at) VALUES (?, ?, NOW())");
        $result = $stmt->execute([$stored['fullname'], $stored['email']]);

        if ($result) {
            echo "<p style='color: green;'>✓ User inserted into database</p>";
            unset($_SESSION['pending_verification']);
            echo "<p style='color: green;'>✓ Session cleared</p>";
            echo "<p style='color: green;'>✓ Full signup process COMPLETED successfully!</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to insert user</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Code verification failed</p>";
    echo "<p>Submitted: $code, Stored: {$stored['code']}, Time check: " . (time() < $stored['expires'] ? 'valid' : 'expired') . "</p>";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
