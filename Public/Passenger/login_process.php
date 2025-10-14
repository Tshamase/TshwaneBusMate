<?php
//Start session to store user info
session_start();

//Clean input to make it safe
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

//Redirect to another page, with optional message
function redirectWithMessage($location, $message = null) {
    if ($message) {
        $_SESSION['error'] = $message; //Save message
    }
    header("Location: $location"); //Go to page
    exit(); //Stop script
}

//Only allow form submissions
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid access."); //Block direct access
}

//Get and clean user input
$fullname = sanitize($_POST['fullname'] ?? '');
$buscard = sanitize($_POST['buscard'] ?? '');

//Make sure both fields are filled
if (empty($fullname) || empty($buscard)) {
    redirectWithMessage("login.php", "Full Name and Bus Card number are required.");
}

//Connect to the database
$conn = new mysqli("localhost", "root", "", "tshwanebusmate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);// Stop if error
}

//Check if user exists
$stmt = $conn->prepare("SELECT * FROM signup WHERE fullname = ? AND buscardNum = ?");
$stmt->bind_param("ss", $fullname, $buscard);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) { //if we find 1 matching record, the if statement is granted
    //Save login info
    $log = $conn->prepare("INSERT INTO login (username, buscardNum, verified, verified_at) VALUES (?, ?, 1, NOW())");
    $log->bind_param("ss", $fullname, $buscard);
    $log->execute();
    $log->close();

    //Store user in session
    $_SESSION['user'] = $fullname;

    //Close and go to home page
    $stmt->close();
    $conn->close();
    redirectWithMessage("loginhome.php");
} else {
    //Close and go back to login with error
    $stmt->close();
    $conn->close();
    redirectWithMessage("login.php", "Invalid login credentials.");
}
?>
