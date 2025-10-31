<?php
require_once 'config/db_connect.php';

try {
    $conn->exec("ALTER TABLE logins DROP COLUMN email");
    echo "Email column dropped from logins table successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
