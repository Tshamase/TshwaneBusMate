<?php
require_once 'config/db_connect.php';

try {
    $stmt = $conn->query('SELECT * FROM logins WHERE role = "commuter"');
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
