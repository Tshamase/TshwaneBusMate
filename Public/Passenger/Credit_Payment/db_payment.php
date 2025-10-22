<?php
$servername = "localhost";
$username = "root";      // your MySQL username
$password = "";          // your MySQL password
$dbname = "payments_db";

// Connect to MySQL server (no database yet)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create the orders table for Payfast tracking
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    m_payment_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    item_name VARCHAR(255),
    payment_status VARCHAR(20) DEFAULT 'PENDING',
    transaction_type VARCHAR(20), -- 'reload' or 'purchase'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table 'orders': " . $conn->error);
}

// Create the transactions table for balance and history
$sql = "CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_type VARCHAR(20), -- 'credit' or 'debit'
    description VARCHAR(255),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    balance DECIMAL(10,2) DEFAULT 0.00,
    order_id VARCHAR(50), -- link to orders table
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id)
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table 'transactions': " . $conn->error);
}

// Insert initial balance record for user 1 if not exists
$sql = "INSERT IGNORE INTO transactions (id, user_id, balance) VALUES (1, 1, 0.00)";
if ($conn->query($sql) !== TRUE) {
    die("Error inserting initial balance: " . $conn->error);
}
