<?php
require_once 'config/db_connect.php';

try {
    $conn->exec('UPDATE logins SET bus_card_number = "1234567890" WHERE role = "commuter"');
    echo 'Updated commuter bus card number to 10 digits';
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
