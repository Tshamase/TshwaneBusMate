<?php
// Debug signup process
session_start();
require_once 'config/db_connect.php';

echo "<h1>Signup Debug Information</h1>";

// Check session data
echo "<h2>Session Data:</h2>";
if (isset($_SESSION['pending_verification'])) {
    echo "<pre>";
    print_r($_SESSION['pending_verification']);
    echo "</pre>";
} else {
    echo "<p>No pending verification session found.</p>";
}

// Check database connection
echo "<h2>Database Connection:</h2>";
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✓ Database connection successful. Total users: " . $result['count'] . "</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Show recent users
echo "<h2>Recent Users:</h2>";
try {
    $stmt = $conn->prepare("SELECT id, fullname, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Email</th><th>Created At</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database.</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error fetching users: " . $e->getMessage() . "</p>";
}

// Test form submission
echo "<h2>Test Signup Form:</h2>";
echo "<form method='POST' action='process_signup.php'>";
echo "<label>Full Name: <input type='text' name='fullname' value='Test User' required></label><br>";
echo "<label>Email: <input type='email' name='email' value='test" . rand(1000, 9999) . "@example.com' required></label><br>";
echo "<input type='submit' value='Test Signup'>";
echo "</form>";

// Clear session button
echo "<h2>Debug Actions:</h2>";
echo "<form method='POST'>";
echo "<input type='submit' name='clear_session' value='Clear Session'>";
echo "</form>";

if (isset($_POST['clear_session'])) {
    session_destroy();
    echo "<p style='color: green;'>Session cleared. Refreshing...</p>";
    echo "<script>setTimeout(() => location.reload(), 1000);</script>";
}
?>
