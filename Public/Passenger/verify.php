<?php
session_start(); //Starts session to access stored user data

//Only allow POST requests to this script
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: otp.php"); //Redirects if not POST
    exit();
}

//Makes sure user came from the signup flow
if (!isset($_SESSION['pending_user_id'])) {
    header("Location: signup1.php"); //Redirects if session is missing
    exit();
}

//Gets user ID from session
$userId = (int)$_SESSION['pending_user_id'];

//Collects and sanitizes the 4 OTP digits from form
$digits = [];
for ($i = 1; $i <= 4; $i++) {
    $val = isset($_POST["digit$i"]) ? $_POST["digit$i"] : ''; //Gets each digit
    $digits[] = preg_replace('/\D/', '', $val); //Remove non-digit characters
}
$entered = implode('', $digits); //Combines digits into one string

//Checks if OTP is exactly 4 digits
if (strlen($entered) !== 4) {
    $_SESSION['otp_error'] = "Please enter the 4-digit code.";
    header("Location: otp.php");
    exit();
}

//Connects to the database
$conn = new mysqli('localhost', 'root', '', 'tshwanebusmate');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

//Gets the most recent OTP entry for this user
$sql = "SELECT id, otp_hash, expires_at, attempts, used
        FROM otp_codes
        WHERE user_id = ?
        ORDER BY id DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) { $conn->close(); die("Prepare failed: " . $conn->error); }
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$otpRow = $result->fetch_assoc(); //Fetches OTP data
$stmt->close();

//If no OTP found, ask user to resend
if (!$otpRow) {
    $_SESSION['otp_error'] = "No OTP found. Please resend a new code.";
    $conn->close();
    header("Location: otp.php");
    exit();
}

//If OTP was already used, block reuse
if ((int)$otpRow['used'] === 1) {
    $_SESSION['otp_error'] = "This OTP is already used. Please resend.";
    $conn->close();
    header("Location: otp.php");
    exit();
}

//If OTP is expired, ask for a new one
if (strtotime($otpRow['expires_at']) < time()) {
    $_SESSION['otp_error'] = "OTP expired. Please resend.";
    $conn->close();
    header("Location: otp.php");
    exit();
}

//If too many failed attempts, block further tries
if ((int)$otpRow['attempts'] >= 5) {
    $_SESSION['otp_error'] = "Too many attempts. Please resend a new code.";
    $conn->close();
    header("Location: otp.php");
    exit();
}

//Verifies entered code against hashed OTP
$valid = password_verify($entered, $otpRow['otp_hash']);

if ($valid) {
    //Begins transaction to update OTP and user status together
    $conn->begin_transaction();

    try {
        //Marks OTP as used
        $upd = $conn->prepare("UPDATE otp_codes SET used = 1 WHERE id = ?");
        if (!$upd) throw new Exception("Prepare failed: " . $conn->error);
        $upd->bind_param("i", $otpRow['id']);
        if (!$upd->execute()) throw new Exception("Execute failed: " . $upd->error);
        $upd->close();

        //Marks user as verified and set verification timestamp
        $uv = $conn->prepare("UPDATE signup SET is_verified = 1, verified_at = NOW() WHERE id = ?");
        if (!$uv) throw new Exception("Prepare failed: " . $conn->error);
        $uv->bind_param("i", $userId);
        if (!$uv->execute()) throw new Exception("Execute failed: " . $uv->error);
        $uv->close();

        $conn->commit(); //Commits both updates

        //Clears session data used during signup
        unset($_SESSION['pending_user_id'], $_SESSION['pending_phone']);
        $_SESSION['flash_success'] = "Your phone number has been verified. You can now log in.";

        $conn->close();
        header("Location: Registration Confirmation.html"); //Redirects to home after sign up is complete
        exit();
    } catch (Exception $e) {
        $conn->rollback(); //Undo changes if something fails
        $_SESSION['otp_error'] = "Something went wrong. Please try again.";
        $conn->close();
        header("Location: otp.php");
        exit();
    }
} else {
    //If OTP is incorrect, increase attempt count
    $inc = $conn->prepare("UPDATE otp_codes SET attempts = attempts + 1 WHERE id = ?");
    if ($inc) {
        $inc->bind_param("i", $otpRow['id']);
        $inc->execute();
        $inc->close();
    }
    $conn->close();

    //Show error and redirect back to OTP page
    $_SESSION['otp_error'] = "Incorrect code. Try again.";
    header("Location: otp.php");
    exit();
}
?>
