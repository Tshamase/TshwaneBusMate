<?php
session_start();
require_once 'config/db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errors = [];

$role = test_input($_POST['role'] ?? '');
$fullname = test_input($_POST['fullname'] ?? '');

if (empty($role)) {
    $errors[] = "Please select a role.";
}

if (empty($fullname)) {
    $errors[] = "Full name is required.";
}

$user_data = null;
$redirect_url = '';

switch ($role) {
    case 'commuter':
        $buscard = test_input($_POST['buscard'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($buscard)) {
            $errors[] = "Bus card number is required for commuters.";
        }
        if (empty($password)) {
            $errors[] = "Password is required for commuters.";
        }

        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("SELECT id, full_name, bus_card_number, password FROM logins WHERE full_name = ? AND bus_card_number = ? AND role = 'commuter'");
                $stmt->execute([$fullname, $buscard]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user_data || !password_verify($password, $user_data['password'])) {
                    $errors[] = "Invalid full name, bus card number, or password.";
                } else {
                    $redirect_url = 'home.html';
                }
            } catch(PDOException $e) {
                $errors[] = "Database error occurred. Please try again.";
                error_log("Commuter login error: " . $e->getMessage());
            }
        }
        break;

    case 'driver':
        $driverid = test_input($_POST['driverid'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($driverid)) {
            $errors[] = "Driver ID is required for drivers.";
        }
        if (empty($password)) {
            $errors[] = "Password is required for drivers.";
        }

        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("SELECT id, full_name, driver_id, password FROM logins WHERE driver_id = ? AND role = 'driver'");
                $stmt->execute([$driverid]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user_data || !password_verify($password, $user_data['password'])) {
                    $errors[] = "Invalid driver ID or password.";
                } else {
                    $redirect_url = 'Drivers Dashboard/dashboard.html';
                }
            } catch(PDOException $e) {
                $errors[] = "Database error occurred. Please try again.";
                error_log("Driver login error: " . $e->getMessage());
            }
        }
        break;

    case 'admin':
        $adminid = test_input($_POST['adminid'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($adminid)) {
            $errors[] = "Admin ID is required for admins.";
        }
        if (empty($password)) {
            $errors[] = "Password is required for admins.";
        }

        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("SELECT id, full_name, admin_id, password FROM logins WHERE admin_id = ? AND role = 'admin'");
                $stmt->execute([$adminid]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user_data || !password_verify($password, $user_data['password'])) {
                    $errors[] = "Invalid admin ID or password.";
                } else {
                    $redirect_url = 'Admin.html';
                }
            } catch(PDOException $e) {
                $errors[] = "Database error occurred. Please try again.";
                error_log("Admin login error: " . $e->getMessage());
            }
        }
        break;

    default:
        $errors[] = "Invalid role selected.";
        break;
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header('Location: login.php');
    exit();
}

if ($user_data) {
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['user_name'] = $user_data['full_name'];
    $_SESSION['user_role'] = $role;

    if (!empty($redirect_url)) {
        header("Location: $redirect_url");
        exit();
    }
}

$_SESSION['login_errors'] = ["An unexpected error occurred."];
header('Location: login.php');
exit();
?>
