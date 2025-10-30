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

// Read the SQL file and extract MySQL statements for Credit Payment system
$sqlFilePath = "C:/xampp/htdocs/TshwaneBusMate/-- Tshwane Bus Routes Database.sql"; // Path to the SQL file from Credit_Payment folder
if (file_exists($sqlFilePath)) {
    $sqlContent = file_get_contents($sqlFilePath);

    // Find the start of the MySQL section
    $mysqlSectionStart = "-- CREDIT PAYMENT SYSTEM SQL CODE";
    $startPos = strpos($sqlContent, $mysqlSectionStart);

    if ($startPos !== false) {
        // Extract the MySQL part
        $mysqlSql = substr($sqlContent, $startPos);

        // Split into individual statements (basic parsing, assuming ; separates statements)
        $statements = array_filter(array_map('trim', explode(';', $mysqlSql)));

        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) { // Skip comments
                // Execute the statement
                if ($conn->query($statement) !== TRUE) {
                    // Log error but don't die to allow partial execution
                    error_log("Error executing SQL: " . $conn->error . " for statement: " . $statement);
                }
            }
        }
    } else {
        die("MySQL section not found in SQL file.");
    }
} else {
    die("SQL file not found: " . $sqlFilePath);
}

// Select the database (assuming it was created by the SQL above)
$conn->select_db($dbname);
