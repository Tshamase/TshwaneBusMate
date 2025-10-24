<?php
// Test database connection
require_once 'config/db_connect.php';

try {
    // Test connection
    $stmt = $conn->query("SELECT 1");
    if ($stmt) {
        echo "<h2>Database Connection Test</h2>";
        echo "<p style='color: green;'>✓ Database connection successful!</p>";

        // Test users table
        $stmt = $conn->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Users table exists</p>";

            // Show table structure
            $stmt = $conn->query("DESCRIBE users");
            echo "<h3>Users Table Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            // Count users
            $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Total users: " . $count['count'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Users table does not exist</p>";
        }
    }
} catch(PDOException $e) {
    echo "<h2>Database Error</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
