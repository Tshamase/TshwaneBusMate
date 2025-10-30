<?php
session_start();
include 'db_payment.php';

// Check if user is logged in
// Will perform operation in the future.

//$username = $_SESSION['user'];
$userId = 1; // Example user ID - in production, get from session

// Handle search and filters
$search = $_POST['search'] ?? '';
$filterType = $_POST['filter_type'] ?? 'all';
$filterDate = $_POST['filter_date'] ?? 'all';

// Build query with filters
$query = "SELECT * FROM transactions WHERE user_id = ?";
$params = [$userId];
$types = "i";

if (!empty($search)) {
    $query .= " AND (description LIKE ? OR transaction_type LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

if ($filterType !== 'all') {
    $query .= " AND transaction_type = ?";
    $params[] = $filterType;
    $types .= "s";
}

if ($filterDate !== 'all') {
    switch ($filterDate) {
        case 'today':
            $query .= " AND DATE(transaction_date) = CURDATE()";
            break;
        case 'week':
            $query .= " AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
    }
}

$query .= " ORDER BY transaction_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit History - TshwaneBusMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2f3a, #0d1b24);
            color: #f0f0f0;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #000 0%, #0d1b24 100%);
            padding: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            position: sticky;
            top: 0;
            z-index: 100;
            text-align: center;
        }

        header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff 60%, #FFD700 40%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            font-size: 20px;
            color: #27ae60;
            cursor: pointer;
            z-index: 1000;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -220px;
            width: 180px;
            height: 100vh;
            background: #0d1b24;
            padding: 20px 0;
            transition: left 0.3s ease;
            z-index: 999;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h2 {
            color: #ffd700;
            font-style: italic;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s ease;
            font-size: 0.9rem;
        }

        .sidebar ul li a {
            color: #fafafc;
            text-decoration: none;
            flex-grow: 1;
        }

        .sidebar ul li:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar ul li:hover a {
            color: #27ae60;
        }

        main {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
        }

        .filters-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .filters-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #f0f0f0;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #f0f0f0;
            font-size: 0.9rem;
        }

        .filter-group input::placeholder {
            color: rgba(240, 240, 240, 0.7);
        }

        .filter-group select option {
            background: #1a2f3a;
            color: #f0f0f0;
        }

        .btn-filter {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: flex-end;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .history-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .history-header h2 {
            font-size: 1.8rem;
            margin: 0;
        }

        .transaction-count {
            color: rgba(240, 240, 240, 0.7);
            font-size: 0.9rem;
        }

        .transaction-list {
            list-style: none;
        }

        .transaction-item {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .transaction-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .transaction-details {
            flex: 1;
        }

        .transaction-details h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            color: #f0f0f0;
        }

        .transaction-details p {
            margin: 0;
            color: rgba(240, 240, 240, 0.7);
            font-size: 0.9rem;
        }

        .transaction-amount {
            font-weight: 600;
            font-size: 1.1rem;
            text-align: right;
        }

        .credit-amount {
            color: #27ae60;
        }

        .debit-amount {
            color: #e74c3c;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(240, 240, 240, 0.7);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #666;
        }

        footer {
            background: linear-gradient(135deg, #000, #0d1b24);
            padding: 20px 0;
            text-align: center;
            color: #aaa;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .transaction-item {
                flex-direction: column;
                text-align: center;
            }

            .transaction-amount {
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>TshwaneBusMate</h1>
        <div class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>

        <div class="sidebar">
            <h2>TBM</h2>
            <ul>
                <li><a href="../home.html"><i class="fa-solid fa-house"></i>Home</a></li>
                <li><a href="#"><i class="fa-solid fa-bus"></i>About</a></li>
                <li><a href="../routes and tracking.html"><i class="fa-solid fa-map-location"></i>Bus Routes</a></li>
                <li><a href="credit_wallet.html"><i class="fa-solid fa-id-card-clip"></i>Bus Card</a></li>
                <li><a href="#"><i class="fa-solid fa-comments"></i>Inquiries</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="filters-section">
            <form method="POST" class="filters-form">
                <div class="filter-group">
                    <label for="search">Search Transactions</label>
                    <input type="text" id="search" name="search" placeholder="Search by description..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label for="filter_type">Transaction Type</label>
                    <select id="filter_type" name="filter_type">
                        <option value="all" <?php echo $filterType === 'all' ? 'selected' : ''; ?>>All Types</option>
                        <option value="credit" <?php echo $filterType === 'credit' ? 'selected' : ''; ?>>Credits</option>
                        <option value="debit" <?php echo $filterType === 'debit' ? 'selected' : ''; ?>>Debits</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter_date">Date Range</label>
                    <select id="filter_date" name="filter_date">
                        <option value="all" <?php echo $filterDate === 'all' ? 'selected' : ''; ?>>All Time</option>
                        <option value="today" <?php echo $filterDate === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo $filterDate === 'week' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="month" <?php echo $filterDate === 'month' ? 'selected' : ''; ?>>Last 30 Days</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Filter
                </button>
            </form>
        </div>

        <div class="history-container">
            <div class="history-header">
                <h2>Credit History</h2>
                <div class="transaction-count">
                    <?php echo $result->num_rows; ?> transaction<?php echo $result->num_rows !== 1 ? 's' : ''; ?>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <ul class="transaction-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="transaction-item">
                            <div class="transaction-details">
                                <h3><?php echo htmlspecialchars($row['description']); ?></h3>
                                <p><?php echo date('M d, Y H:i', strtotime($row['transaction_date'])); ?></p>
                            </div>
                            <div class="transaction-amount <?php echo $row['transaction_type'] === 'credit' ? 'credit-amount' : 'debit-amount'; ?>">
                                <?php echo $row['transaction_type'] === 'credit' ? '+' : '-'; ?> ZAR <?php echo number_format($row['amount'], 2); ?>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No transactions found matching your criteria.</p>
                </div>
            <?php endif; ?>

            <?php $stmt->close(); ?>
        </div>
    </main>

    <footer>
        <div class="copyright">Copyright &copy; 2025 City of Tshwane. All rights reserved.</div>
    </footer>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }

        // Close sidebar when clicking outside
        window.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.menu-toggle');

            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>

</html>