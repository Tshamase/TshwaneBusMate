<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'config/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TshwaneBusMate</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>User Management</h3>
                <p>Manage system users</p>
                <a href="user_management.php" class="btn">Manage Users</a>
            </div>

            <div class="dashboard-card">
                <h3>Bus Routes</h3>
                <p>Manage bus routes and schedules</p>
                <a href="route_management.php" class="btn">Manage Routes</a>
            </div>

            <div class="dashboard-card">
                <h3>Reports</h3>
                <p>View system reports</p>
                <a href="reports.php" class="btn">View Reports</a>
            </div>

            <div class="dashboard-card">
                <h3>Settings</h3>
                <p>System configuration</p>
                <a href="settings.php" class="btn">System Settings</a>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>
</body>
</html>
