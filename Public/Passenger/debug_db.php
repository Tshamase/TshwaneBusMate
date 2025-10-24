<?php
try {
    $conn = new PDO('mysql:host=localhost;dbname=tshwane_busmate', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Database connection successful<br>';

    // Check if users table exists
    $result = $conn->query('SHOW TABLES LIKE "users"');
    if ($result->rowCount() > 0) {
        echo 'Users table exists<br>';

        // Check table structure
        $result = $conn->query('DESCRIBE users');
        echo 'Table structure:<br>';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . ' - ' . $row['Type'] . '<br>';
        }

        // Check if there are any users
        $result = $conn->query('SELECT COUNT(*) as count FROM users');
        $count = $result->fetch(PDO::FETCH_ASSOC);
        echo 'Total users: ' . $count['count'] . '<br>';
    } else {
        echo 'Users table does not exist<br>';
    }
} catch(PDOException $e) {
    echo 'Database error: ' . $e->getMessage() . '<br>';
}
?>
