<?php
require_once 'config/db_connect.php';

try {
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, created_at) VALUES (?, ?, NOW())");
    $stmt->execute(['Duplicate Test', 'test@example.com']);
    echo "Duplicate insertion succeeded (unexpected)\n";
} catch(PDOException $e) {
    echo "Duplicate insertion failed as expected: " . $e->getMessage() . "\n";
}
?>
