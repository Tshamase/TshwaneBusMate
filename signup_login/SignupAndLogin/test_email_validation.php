<?php
// Test email validation logic
echo "<h1>Email Validation Test</h1>";

// Test emails
$testEmails = [
    'valid@example.com' => 'Valid',
    'test.email+tag@example.com' => 'Valid with plus',
    'user@subdomain.example.com' => 'Valid subdomain',
    'invalid' => 'No @ symbol',
    '@example.com' => 'No local part',
    'user@' => 'No domain',
    'user..double@example.com' => 'Double dot',
    'user@.example.com' => 'Dot after @',
    '.user@example.com' => 'Dot before local',
    'user@example.' => 'Dot before TLD',
    'user@example' => 'No TLD',
    'user@example..com' => 'Double dot in domain',
    'user@exam ple.com' => 'Space in domain',
    'user name@example.com' => 'Space in local',
    'user@exam!ple.com' => 'Special char in domain',
    'user@exam-ple.com' => 'Valid hyphen',
    'user@exam_ple.com' => 'Underscore in domain',
    'user@123.456.789.000' => 'IP-like domain',
    'user@example.co.uk' => 'Valid UK domain',
    'user@example.info' => 'Valid info domain'
];

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Email</th><th>PHP filter_var</th><th>Custom Regex</th><th>Additional Checks</th><th>Overall</th></tr>";

foreach ($testEmails as $email => $description) {
    // PHP built-in validation
    $phpValid = filter_var($email, FILTER_VALIDATE_EMAIL) ? '✓' : '✗';

    // Custom regex validation
    $customRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    $customValid = preg_match($customRegex, $email) ? '✓' : '✗';

    // Additional checks
    $additionalValid = true;
    $additionalMsg = '';

    if (strpos($email, '..') !== false) {
        $additionalValid = false;
        $additionalMsg .= 'Double dot; ';
    }
    if (strpos($email, '@.') !== false) {
        $additionalValid = false;
        $additionalMsg .= '@ followed by dot; ';
    }
    if (strpos($email, '.@') !== false) {
        $additionalValid = false;
        $additionalMsg .= 'Dot before @; ';
    }

    if ($additionalValid && empty($additionalMsg)) {
        $additionalMsg = '✓';
    } else {
        $additionalMsg = rtrim($additionalMsg, '; ');
    }

    // Overall result
    $overall = ($phpValid === '✓' && $customValid === '✓' && $additionalValid) ? '✓' : '✗';

    echo "<tr>";
    echo "<td>" . htmlspecialchars($email) . "<br><small>$description</small></td>";
    echo "<td style='color: " . ($phpValid === '✓' ? 'green' : 'red') . ";'>$phpValid</td>";
    echo "<td style='color: " . ($customValid === '✓' ? 'green' : 'red') . ";'>$customValid</td>";
    echo "<td style='color: " . ($additionalValid ? 'green' : 'red') . ";'>$additionalMsg</td>";
    echo "<td style='color: " . ($overall === '✓' ? 'green' : 'red') . "; font-weight: bold;'>$overall</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Test Custom Validation Function:</h2>";
echo "<form method='POST'>";
echo "<label>Email to test: <input type='text' name='test_email' value='" . (isset($_POST['test_email']) ? htmlspecialchars($_POST['test_email']) : '') . "'></label>";
echo "<input type='submit' value='Test'>";
echo "</form>";

if (isset($_POST['test_email'])) {
    $testEmail = $_POST['test_email'];

    echo "<h3>Testing: " . htmlspecialchars($testEmail) . "</h3>";

    $errors = [];

    // Check if email is valid
    if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format (PHP filter_var)";
    }

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $testEmail)) {
        $errors[] = "Invalid email format (custom regex)";
    }

    if (strpos($testEmail, '..') !== false) {
        $errors[] = "Invalid email format (double dots)";
    }

    if (strpos($testEmail, '@.') !== false) {
        $errors[] = "Invalid email format (@ followed by dot)";
    }

    if (strpos($testEmail, '.@') !== false) {
        $errors[] = "Invalid email format (dot before @)";
    }

    if (empty($errors)) {
        echo "<p style='color: green;'>✓ Email validation PASSED</p>";
    } else {
        echo "<p style='color: red;'>✗ Email validation FAILED:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
}
?>
