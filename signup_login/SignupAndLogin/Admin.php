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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!--Main Content-->
<div class="container">
    <header>
        <h1>Admin Dashboard</h1>
        <div class="search-bar">
            <input id="searchInput" type="text" placeholder="search...">
        </div>
		<div style="margin-top: 20px;">
			<a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

<!-- Grid layout displaying dashboard cards -->
<!-- Each card includes a title, description, and a button linking to their respective pages -->
        <div class="dashboard-grid">
            <div class="dashboard-card user-card">
			<i class="fas fa-users card-icon"></i>
                <h3>User Management</h3>
                <p>Manage system users</p>
                <a href="user_management.php" class="btn">Manage Users</a>
            </div>

            <div class="dashboard-card routes-card">
			<i class="fas fa-bus card-icon"></i>
                <h3>Bus Routes</h3>
                <p>Manage bus routes and schedules</p>
                <a href="route_management.php" class="btn">Manage Routes</a>
            </div>

            <div class="dashboard-card reports-card">
			<i class="fas fa-chart-line card-icon"></i>
                <h3>Reports</h3>
                <p>View system reports</p>
                <a href="reports.php" class="btn">View Reports</a>
            </div>

            <div class="dashboard-card settings-card">
			<i class="fas fa-cogs card-icon"></i>
                <h3>Settings</h3>
                <p>System configuration</p>
                <a href="settings.php" class="btn">System Settings</a>
            </div>

        </div>


</div>


    <script src="admin.js"></script>
</body>
</html>
