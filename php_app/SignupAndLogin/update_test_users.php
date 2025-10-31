<?php
require_once 'config/db_connect.php';

try {
    echo "<h2>Updating existing test users with missing fields</h2>";

    // Update commuter
    $stmt = $conn->prepare("UPDATE users SET role = 'commuter', bus_card_number = '1234567890', password = ? WHERE email = 'john@test.com'");
    $stmt->execute([password_hash('commuter123', PASSWORD_DEFAULT)]);
    echo "<p>✓ Updated John Commuter</p>";

    // Update driver
    $stmt = $conn->prepare("UPDATE users SET role = 'driver', driver_id = 'DRV001', password = ? WHERE email = 'jane@test.com'");
    $stmt->execute([password_hash('driver123', PASSWORD_DEFAULT)]);
    echo "<p>✓ Updated Jane Driver</p>";

    // Update admin
    $stmt = $conn->prepare("UPDATE users SET role = 'admin', admin_id = 'ADM001', password = ? WHERE email = 'bob@test.com'");
    $stmt->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
    echo "<p>✓ Updated Bob Admin</p>";

    echo "<h2>All test users updated successfully</h2>";
    echo "<h3>Test Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Commuter:</strong> Name: John Commuter, Bus Card: 1234567890</li>";
    echo "<li><strong>Driver:</strong> Driver ID: DRV001, Password: driver123</li>";
    echo "<li><strong>Admin:</strong> Admin ID: ADM001, Password: admin123</li>";
    echo "</ul>";

} catch(PDOException $e) {
    echo "<h2>Error: " . $e->getMessage() . "</h2>";
}
?>
