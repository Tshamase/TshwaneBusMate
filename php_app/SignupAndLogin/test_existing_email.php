<?php
// Test existing email handling
require_once 'config/db_connect.php';

echo "<h1>Existing Email Test</h1>";

// First, ensure we have a test user
$testEmail = 'existing@example.com';
$testName = 'Existing User';

try {
    // Check if test user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $exists = $stmt->rowCount() > 0;

    if (!$exists) {
        // Insert test user
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$testName, $testEmail]);
        echo "<p style='color: green;'>✓ Created test user: $testName ($testEmail)</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Test user already exists: $testName ($testEmail)</p>";
    }

} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Test the validation logic
echo "<h2>Testing Email Validation Logic:</h2>";

$testEmails = [
    $testEmail => 'Existing email',
    'new' . rand(1000, 9999) . '@example.com' => 'New email',
    'another' . rand(1000, 9999) . '@example.com' => 'Another new email'
];

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Email</th><th>Description</th><th>Exists in DB</th><th>Validation Result</th></tr>";

foreach ($testEmails as $email => $description) {
    // Check if email exists in database
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $exists = $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        $exists = 'Error';
    }

    // Test validation logic
    $validationError = '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationError = "Invalid email format";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $validationError = "Invalid email format";
    } elseif (strpos($email, '..') !== false || strpos($email, '@.') !== false || strpos($email, '.@') !== false) {
        $validationError = "Invalid email format";
    } elseif ($exists) {
        $validationError = "You have signed up before using these details. Please check your emails for the further information that was provided with the code. Then after completing that step, you may log in.";
    }

    $validationResult = empty($validationError) ? '✓ Valid' : '✗ ' . $validationError;

    echo "<tr>";
    echo "<td>" . htmlspecialchars($email) . "</td>";
    echo "<td>$description</td>";
    echo "<td style='color: " . ($exists === true ? 'red' : ($exists === false ? 'green' : 'orange')) . ";'>" . ($exists === true ? 'Yes' : ($exists === false ? 'No' : $exists)) . "</td>";
    echo "<td style='color: " . (empty($validationError) ? 'green' : 'red') . ";'>$validationResult</td>";
    echo "</tr>";
}

echo "</table>";

// Test signup process with existing email
echo "<h2>Test Signup Process with Existing Email:</h2>";
echo "<form method='POST'>";
echo "<label>Full Name: <input type='text' name='test_fullname' value='Test User' required></label><br>";
echo "<label>Email: <input type='email' name='test_email' value='$testEmail' required></label><br>";
echo "<input type='submit' name='test_signup' value='Test Signup with Existing Email'>";
echo "</form>";

if (isset($_POST['test_signup'])) {
    $fullname = $_POST['test_fullname'];
    $email = $_POST['test_email'];

    echo "<h3>Testing signup with: $fullname ($email)</h3>";

    $errors = [];

    // Validate Full Name
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    } else {
        if (!preg_match("/^[a-zA-Z ]*$/", $fullname)) {
            $errors[] = "Only letters and white space allowed";
        }
        $nameParts = explode(' ', $fullname);
        foreach ($nameParts as $part) {
            if (strlen($part) < 2) {
                $errors[] = "Name and surname are too short";
                break;
            }
        }
    }

    // Validate Email
    if (empty($email)) {
        $errors[] = "Email is required";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $errors[] = "Invalid email format";
        } elseif (strpos($email, '..') !== false || strpos($email, '@.') !== false || strpos($email, '.@') !== false) {
            $errors[] = "Invalid email format";
        } else {
            try {
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->rowCount() > 0) {
                    $errors[] = "You have signed up before using these details. Please check your emails for the further information that was provided with the code. Then after completing that step, you may log in.";
                }
            } catch(PDOException $e) {
                $errors[] = "Error checking email";
            }
        }
    }

    if (empty($errors)) {
        echo "<p style='color: green;'>✓ Validation passed - would proceed to verification</p>";
    } else {
        echo "<p style='color: red;'>✗ Validation failed:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
}
?>
