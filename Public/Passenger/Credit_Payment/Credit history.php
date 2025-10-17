<?php
session_start();
include 'Database.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
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

        .back-button {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            transform: translateY(-50%) translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        main {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
        }

        .history-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .history-container h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: #27ae60;
            text-align: center;
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
        }

        .transaction-details h3 {
            color: #FFD700;
            margin-bottom: 5px;
        }

        .transaction-details p {
            color: #bbb;
            margin: 2px 0;
        }

        .transaction-amount {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .credit-amount {
            color: #27ae60;
        }

        .debit-amount {
            color: #e74c3c;
        }

        .empty-state {
            text-align: center;
            color: #bbb;
            font-size: 1.2rem;
            margin: 50px 0;
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
        <a href="Credit Wallet.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </header>

    <main>
        <div class="history-container">
            <h2>Credit History</h2>
            
            <?php
            // Fetch transaction history for the logged-in user
            $userId = 1; // This should be dynamically set based on session
            
            $query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<ul class="transaction-list">';
                
                while ($row = $result->fetch_assoc()) {
                    $amount = htmlspecialchars($row['amount']);
                    $type = htmlspecialchars($row['transaction_type']);
                    $date = htmlspecialchars($row['transaction_date']);
                    $description = htmlspecialchars($row['description']);
                    
                    $amountClass = ($type == 'credit') ? 'credit-amount' : 'debit-amount';
                    $amountPrefix = ($type == 'credit') ? '+' : '-';
                    
                    echo '<li class="transaction-item">';
                    echo '<div class="transaction-details">';
                    echo '<h3>' . $description . '</h3>';
                    echo '<p>' . date('M d, Y H:i', strtotime($date)) . '</p>';
                    echo '</div>';
                    echo '<div class="transaction-amount ' . $amountClass . '">';
                    echo $amountPrefix . ' ZAR ' . number_format($amount, 2);
                    echo '</div>';
                    echo '</li>';
                }
                
                echo '</ul>';
            } else {
                echo '<div class="empty-state">';
                echo '<i class="fas fa-history" style="font-size: 3rem; margin-bottom: 20px; color: #666;"></i>';
                echo '<p>No transactions found.</p>';
                echo '</div>';
            }
            
            $stmt->close();
            ?>
        </div>
    </main>

    <footer>
        <div class="copyright">Copyright &copy; 2025 City of Tshwane. All rights reserved.</div>
    </footer>
</body>
</html>
