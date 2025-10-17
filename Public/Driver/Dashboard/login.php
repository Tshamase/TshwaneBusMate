<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST["fullname"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT name, password FROM drivers WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["driver_id"] = $id;
            $_SESSION["driver_name"] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Driver ID not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
