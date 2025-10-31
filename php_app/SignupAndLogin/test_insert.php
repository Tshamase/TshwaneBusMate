<?php
require_once 'config/db_connect.php';

try {
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, created_at) VALUES (?, ?, NOW())");
    $stmt->execute(['Test User', 'test@example.com']);
    echo "Test user inserted successfully\n";

    // Verify insertion
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total users after insertion: " . $count['count'] . "\n";

    // Show the inserted user
    $stmt = $conn->query("SELECT id, full_name, email, created_at FROM users WHERE email = 'test@example.com'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "Inserted user: ID=" . $user['id'] . ", Name=" . $user['full_name'] . ", Email=" . $user['email'] . ", Created=" . $user['created_at'] . "\n";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
