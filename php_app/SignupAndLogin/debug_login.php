<?php
require_once 'config/db_connect.php';

echo "<h2>Debug Login Test</h2>";

// Test commuter login
echo "<h3>Testing Commuter Login</h3>";
    $fullname = 'John Commuter';
    $buscard = '1234567890123456';
$password = 'commuter123';

try {
    $stmt = $conn->prepare("SELECT id, full_name, bus_card_number, password, role FROM logins WHERE full_name = ? AND bus_card_number = ? AND role = 'commuter'");
    $stmt->execute([$fullname, $buscard]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p>User found: " . htmlspecialchars($user['full_name']) . " (" . $user['bus_card_number'] . ")</p>";
        echo "<p>Password hash in DB: " . $user['password'] . "</p>";
        echo "<p>Password verify result: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE') . "</p>";
    } else {
        echo "<p style='color: red;'>User not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test driver login
echo "<h3>Testing Driver Login</h3>";
$driverid = 'DRV001';
$password = 'driver123';

try {
    $stmt = $conn->prepare("SELECT id, full_name, driver_id, password, role FROM logins WHERE driver_id = ? AND role = 'driver'");
    $stmt->execute([$driverid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p>User found: " . htmlspecialchars($user['full_name']) . " (" . $user['driver_id'] . ")</p>";
        echo "<p>Password hash in DB: " . $user['password'] . "</p>";
        echo "<p>Password verify result: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE') . "</p>";
    } else {
        echo "<p style='color: red;'>User not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test admin login
echo "<h3>Testing Admin Login</h3>";
$adminid = 'ADM001';
$password = 'admin123';

try {
    $stmt = $conn->prepare("SELECT id, full_name, admin_id, password, role FROM logins WHERE admin_id = ? AND role = 'admin'");
    $stmt->execute([$adminid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p>User found: " . htmlspecialchars($user['full_name']) . " (" . $user['admin_id'] . ")</p>";
        echo "<p>Password hash in DB: " . $user['password'] . "</p>";
        echo "<p>Password verify result: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE') . "</p>";
    } else {
        echo "<p style='color: red;'>User not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
