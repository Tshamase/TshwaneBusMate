<?php
require_once 'config/db_connect.php';

try {
    // Check if columns exist and add them if they don't
    $columns = ['role', 'bus_card_number', 'driver_id', 'password', 'admin_id'];
    $existingColumns = [];

    $stmt = $conn->query("DESCRIBE logins");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }

    foreach ($columns as $column) {
        if (!in_array($column, $existingColumns)) {
            $type = ($column === 'password') ? 'VARCHAR(255)' : (($column === 'role') ? 'VARCHAR(20)' : 'VARCHAR(50)');
            $conn->exec("ALTER TABLE logins ADD COLUMN $column $type DEFAULT NULL");
            echo "<p>Added column: $column</p>";
        }
    }

    echo "<h2>Table structure ready</h2>";

    // Insert test data for each role
    $testUsers = [
        // Commuter
        ['full_name' => 'John Commuter', 'role' => 'commuter', 'bus_card_number' => '1234567890123456', 'password' => password_hash('commuter123', PASSWORD_DEFAULT)],
        // Driver
        ['full_name' => 'Jane Driver', 'role' => 'driver', 'driver_id' => 'DRV001', 'password' => password_hash('driver123', PASSWORD_DEFAULT)],
        // Admin
        ['full_name' => 'Bob Admin', 'role' => 'admin', 'admin_id' => 'ADM001', 'password' => password_hash('admin123', PASSWORD_DEFAULT)]
    ];

    foreach ($testUsers as $user) {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM logins WHERE full_name = ? AND role = ?");
        $stmt->execute([$user['full_name'], $user['role']]);
        if ($stmt->rowCount() > 0) {
            echo "<p>⚠ User already exists: " . $user['full_name'] . " (" . $user['role'] . ")</p>";
            continue;
        }

        $stmt = $conn->prepare('INSERT INTO logins (full_name, role, bus_card_number, driver_id, password, admin_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $user['full_name'],
            $user['role'],
            $user['bus_card_number'] ?? null,
            $user['driver_id'] ?? null,
            $user['password'] ?? null,
            $user['admin_id'] ?? null
        ]);
        echo "<p>✓ Inserted " . $user['role'] . ": " . $user['full_name'] . "</p>";
    }

    echo "<h2>All test users inserted successfully</h2>";
    echo "<h3>Test Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Commuter:</strong> Name: John Commuter, Password: commuter123</li>";
    echo "<li><strong>Driver:</strong> Driver ID: DRV001, Password: driver123</li>";
    echo "<li><strong>Admin:</strong> Admin ID: ADM001, Password: admin123</li>";
    echo "</ul>";

} catch(PDOException $e) {
    echo "<h2>Error: " . $e->getMessage() . "</h2>";
}
?>
