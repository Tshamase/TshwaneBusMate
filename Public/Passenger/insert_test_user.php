<?php
// Insert a test user directly into database
require_once 'config/db_connect.php';

echo "<h1>Insert Test User</h1>";

// Test data
$testUsers = [
    ['fullname' => 'John Doe', 'email' => 'john.doe@example.com'],
    ['fullname' => 'Jane Smith', 'email' => 'jane.smith@example.com'],
    ['fullname' => 'Bob Johnson', 'email' => 'bob.johnson@example.com'],
    ['fullname' => 'Alice Brown', 'email' => 'alice.brown@example.com'],
    ['fullname' => 'Charlie Wilson', 'email' => 'charlie.wilson@example.com']
];

echo "<h2>Available Test Users:</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Full Name</th><th>Email</th><th>Action</th></tr>";

foreach ($testUsers as $user) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td><a href='?insert=" . urlencode($user['fullname']) . "&email=" . urlencode($user['email']) . "'>Insert</a></td>";
    echo "</tr>";
}
echo "</table>";

// Handle insertion
if (isset($_GET['insert']) && isset($_GET['email'])) {
    $fullname = $_GET['insert'];
    $email = $_GET['email'];

    try {
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, created_at) VALUES (?, ?, NOW())");
        $result = $stmt->execute([$fullname, $email]);

        if ($result) {
            echo "<p style='color: green;'>✓ Successfully inserted user: $fullname ($email)</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to insert user</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    }
}

// Show current users
echo "<h2>Current Users in Database:</h2>";
try {
    $stmt = $conn->query("SELECT id, fullname, email, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Email</th><th>Created At</th><th>Action</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "<td><a href='?delete=" . $user['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found.</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error fetching users: " . $e->getMessage() . "</p>";
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo "<p style='color: green;'>✓ Successfully deleted user with ID: $id</p>";
            echo "<script>setTimeout(() => location.reload(), 1000);</script>";
        } else {
            echo "<p style='color: red;'>✗ Failed to delete user</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    }
}
?>
