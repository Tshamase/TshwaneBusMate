<?php
// Test signup process with existing email scenario
session_start();
require_once 'config/db_connect.php';

echo "<h1>Signup with Existing Email Test</h1>";

// Ensure we have an existing user
$existingEmail = 'existing@test.com';
$existingName = 'Existing Test User';

try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$existingEmail]);
    if ($stmt->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$existingName, $existingEmail]);
        echo "<p style='color: green;'>✓ Created existing test user</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Existing test user already present</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Test the full signup process with existing email
echo "<h2>Testing Signup Process:</h2>";

$testData = [
    'fullname' => 'New User',
    'email' => $existingEmail  // This should fail
];

echo "<p>Attempting to signup with existing email: " . htmlspecialchars($testData['fullname']) . " (" . htmlspecialchars($testData['email']) . ")</p>";

// Simulate the process_signup.php logic
$_POST = $testData;

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

echo "<h3>Validation Results:</h3>";
echo "<p>Full Name: " . ($fullnameErr ? "<span style='color: red;'>$fullnameErr</span>" : "<span style='color: green;'>Valid</span>") . "</p>";
echo "<p>Email: " . ($emailErr ? "<span style='color: red;'>$emailErr</span>" : "<span style='color: green;'>Valid</span>") . "</p>";
echo "<p>Overall: " . ($isValid ? "<span style='color: green;'>PASSED</span>" : "<span style='color: red;'>FAILED (as expected)</span>") . "</p>";

if (!$isValid) {
    echo "<h3>Expected Behavior:</h3>";
    echo "<p style='color: green;'>✓ Correctly prevented signup with existing email</p>";
    echo "<p>Would redirect back to signup.php with error message: <em>'$emailErr'</em></p>";
} else {
    echo "<h3>Unexpected Behavior:</h3>";
    echo "<p style='color: red;'>✗ Should have failed validation but didn't</p>";
}

// Test with new email
echo "<h2>Testing with New Email:</h2>";

$newTestData = [
    'fullname' => 'New Test User',
    'email' => 'newtest' . rand(1000, 9999) . '@example.com'
];

echo "<p>Attempting to signup with new email: " . htmlspecialchars($newTestData['fullname']) . " (" . htmlspecialchars($newTestData['email']) . ")</p>";

$_POST = $newTestData;

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

echo "<h3>Validation Results:</h3>";
echo "<p>Full Name: " . ($fullnameErr ? "<span style='color: red;'>$fullnameErr</span>" : "<span style='color: green;'>Valid</span>") . "</p>";
echo "<p>Email: " . ($emailErr ? "<span style='color: red;'>$emailErr</span>" : "<span style='color: green;'>Valid</span>") . "</p>";
echo "<p>Overall: " . ($isValid ? "<span style='color: green;'>PASSED</span>" : "<span style='color: red;'>FAILED</span>") . "</p>";

if ($isValid) {
    echo "<h3>Expected Behavior:</h3>";
    echo "<p style='color: green;'>✓ Correctly allowed signup with new email</p>";
    echo "<p>Would proceed to verification step</p>";
} else {
    echo "<h3>Unexpected Behavior:</h3>";
    echo "<p style='color: red;'>✗ Should have passed validation but didn't: $fullnameErr $emailErr</p>";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
