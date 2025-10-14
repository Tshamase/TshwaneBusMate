<?php
session_start();

$devMode = true; // true = show OTP on otp.php, false = hide it

$fullnameErr = $phoneErr = "";
$fullname = $phone = "";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validating Full Name
    if (empty($_POST["fullname"])) {
        $fullnameErr = "Full name is required";
    } else {
        $fullname = test_input($_POST["fullname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fullname)) {
            $fullnameErr = "Only letters and white space allowed";
        }
    }

    // Validating Phone
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $phoneErr = "Invalid phone number format";
        }
    }

    if (empty($fullnameErr) && empty($phoneErr)) {
        $conn = new mysqli('localhost', 'root', '', "tshwanebusmate");

        if ($conn->connect_error) die('Connection Failed: ' . $conn->connect_error);

        // Checking duplicate phone
        $check = $conn->prepare("SELECT id FROM signup WHERE phoneNumber = ?");
        $check->bind_param("s", $phone);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $phoneErr = "This phone number is already registered.";
        } else {
            // Insert user
            $stmt = $conn->prepare("INSERT INTO signup (fullname, phoneNumber) VALUES (?, ?)");
            $stmt->bind_param("ss", $fullname, $phone);
            $stmt->execute();
            $userId = $stmt->insert_id;
            $stmt->close();

            // Generate and store OTP (hashed)
            $otp = random_int(1000, 9999);
            $otpHash = password_hash((string)$otp, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', time() + 5 * 60);

            $otpStmt = $conn->prepare("INSERT INTO otp_codes (user_id, phone, otp_hash, expires_at) VALUES (?, ?, ?, ?)");
            $otpStmt->bind_param("isss", $userId, $phone, $otpHash, $expiresAt);
            $otpStmt->execute();

            // Save session context
            $_SESSION['pending_user_id'] = $userId;
            $_SESSION['pending_phone'] = $phone;

            // DEV MODE: store OTP in session for display on otp.php
            if ($devMode) {
                $_SESSION['dev_otp'] = $otp;
            }

            // Log for dev purposes
            error_log("DEV OTP for $phone: $otp");

            $conn->close();
            header("Location: otp.php");
            exit();
        }

        $check->close();
        $conn->close();
    }
}
?>
