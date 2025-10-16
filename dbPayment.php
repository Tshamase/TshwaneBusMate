<?php 
/* Database configuration:
    $servername = "localhost";
    $username = "root";
    $password = "";
    $databaseName = "dbtbms";

// Create connection without database selection:
    $conn = new mysqli($servername, $username, $password);

// Check Connection
    if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
    }

// Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . $databaseName;
    if ($conn->query($sql) === TRUE) {
        echo "<script> console.log('Database tshwanebusmate created successfully or already exists'); </script>";
    } else {
        echo "<script> console.log('Error creating database: " . $conn->error . "'); </script>";
    }

// Now connect to the newly created database
    $conn->select_db($databaseName);
    echo "<script> console.log('Connected to tshwanebusmate database successfully'); </script>";

// Create tables if they don't exist

// Create transactions table
    $transactionsTable = "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        transaction_type VARCHAR(50) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        description TEXT,
        transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'pending',
        reference_id VARCHAR(100) UNIQUE,
        FOREIGN KEY (email) REFERENCES signup(email) ON DELETE CASCADE
    )";
    
    if ($conn->query($transactionsTable) === TRUE) {
        echo "<script> console.log('Transactions table created successfully'); </script>";
    } else {
        echo "<script> console.log('Error creating transactions table: " . $conn->error . "'); </script>";
    }
*/
?>