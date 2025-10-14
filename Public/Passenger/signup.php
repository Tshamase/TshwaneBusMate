<?php
session_start(); //Starts a session to store user data across different pages

$devMode = true; //If true, OTP will be shown on otp.php for testing purposes

//Initializes error message and input variables
$fullnameErr = $phoneErr = "";
$fullname = $phone = "";

//Enables error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Function to clean and sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

//Runs this block only when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Validates full name input
    if (empty($_POST["fullname"])) {
        $fullnameErr = "Full name is required";
    } else {
        $fullname = test_input($_POST["fullname"]);
        //Allows only letters, spaces, hyphens, and apostrophes
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fullname)) {
            $fullnameErr = "Only letters and white space allowed";
        }
    }

    //Validates phone number input
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        //Allows only digits, between 10 and 15 characters
        if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $phoneErr = "Invalid phone number format";
        }
    }

    //Proceeds only if there are no validation errors
    if (empty($fullnameErr) && empty($phoneErr)) {
        //Connects to the database named 'tshwanebusmate'
        $conn = new mysqli('localhost', 'root', '', "tshwanebusmate");

        //Stops execution if connection fails
        if ($conn->connect_error) die('Connection Failed: ' . $conn->connect_error);

        //Checks if the phone number already exists in the signup table
        $check = $conn->prepare("SELECT id FROM signup WHERE phoneNumber = ?");
        $check->bind_param("s", $phone);
        $check->execute();
        $check->store_result();

        //If phone number is already registered, show error
        if ($check->num_rows > 0) {
            $phoneErr = "This phone number is already registered.";
        } else {
            //Inserts new user into the signup table
            $stmt = $conn->prepare("INSERT INTO signup (fullname, phoneNumber) VALUES (?, ?)");
            $stmt->bind_param("ss", $fullname, $phone);
            $stmt->execute();
            $userId = $stmt->insert_id; // Get the ID of the newly inserted user
            $stmt->close();

            //Generates a 4-digit OTP to prevent XSS threats
            $otp = random_int(1000, 9999);

            //Hashes the OTP for security
            $otpHash = password_hash((string)$otp, PASSWORD_DEFAULT);

            //Sets OTP expiration time to 5 minutes from now
            $expiresAt = date('Y-m-d H:i:s', time() + 5 * 60);

            //Stores OTP details in the otp_codes table
            $otpStmt = $conn->prepare("INSERT INTO otp_codes (user_id, phone, otp_hash, expires_at) VALUES (?, ?, ?, ?)");
            $otpStmt->bind_param("isss", $userId, $phone, $otpHash, $expiresAt);
            $otpStmt->execute();

            //Saves user info in session for use on otp.php
            $_SESSION['pending_user_id'] = $userId;
            $_SESSION['pending_phone'] = $phone;

            //If dev mode is enabled, store the plain OTP in session for display
            if ($devMode) {
                $_SESSION['dev_otp'] = $otp;
            }

            //Logs the OTP for developer testing
            error_log("DEV OTP for $phone: $otp");

            //Closes the database connection
            $conn->close();

            //Redirects user to the OTP verification page
            header("Location: otp.php");
            exit();
        }

        //Closes the phone check query
        $check->close();

        //Closes the database connection
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic page setup -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TshwaneBusMate Signup</title>

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* Reset default spacing and set base font */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Page background and text color */
    body {
        background: linear-gradient(135deg, #1a2f3a, #0d1b24); /* Dark gradient background */
        color: #f0f0f0; /* Light text color */
        line-height: 1.6;
        min-height: 100vh;
    }

    /* Semi-transparent overlay for depth */
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.3);
    }

    /* Hamburger menu icon for sidebar toggle */
    .menu-toggle {
        position: fixed;
        top: 30px;
        left: 20px;
        font-size: 24px;
        color: #27ae60;
        cursor: pointer;
        z-index: 1000;
    }

    /* Sidebar container */
    .sidebar {
        position: fixed;
        top: 0;
        left: -220px; /* Hidden by default */
        width: 200px;
        height: 100vh;
        background: #0d1b24;
        padding: 30px 0;
        transition: left 0.3s ease;
        z-index: 999;
        border-right: 1px solid rgba(255,255,255,0.1);
    }

    /* Show sidebar when wrapper has 'active' class */
    .wrapper.active .sidebar { left: 0; }

    /* Sidebar title */
    .sidebar h2 {
        color: #FFD700;
        font-style: italic;
        text-align: center;
        margin-bottom: 30px;
    }

    /* Sidebar navigation list */
    .sidebar ul { list-style: none; }

    /* Sidebar list items */
    .sidebar ul li {
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.3s ease;
    }

    /* Sidebar links */
    .sidebar ul li a {
        color: #fafafc;
        text-decoration: none;
        font-size: 1rem;
        flex-grow: 1;
    }

    /* Hover effect for sidebar items */
    .sidebar ul li:hover { background: rgba(255,255,255,0.05); }
    .sidebar ul li:hover a { color: #27ae60; }

    /* Top header bar */
    nav.header-bar {
        width: 100%;
        height: 15vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 15px 40px;
        color: white;
        background: linear-gradient(135deg, #000, #0d1b24);
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        position: fixed;
        top: 0;
        z-index: 100;
    }

    /* Logo styling with gradient text */
    nav.header-bar .logo h1 {
        font-size: 2.2rem;
        font-weight: 800;
        background: linear-gradient(to right, #fff 51%, #FFD700 49%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Signup form container */
    .signup-card {
        max-width: 450px;
        margin: 160px auto; /* Centered with space from top */
        background: rgba(255, 255, 255, 0.05); /* Glassy background */
        backdrop-filter: blur(10px); /* Blur effect */
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        padding: 30px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* Signup title */
    .signup-card h2 {
        font-size: 28px;
        color: #27ae60;
        margin-bottom: 10px;
    }

    /* Subtitle text */
    .signup-card p.subtitle {
        font-size: 20px;
        color: #bbb;
        margin-bottom: 25px;
    }

    /* Form labels */
    .signup-card label {
        display: block;
        text-align: left;
        margin: 15px 0 8px;
        font-weight: bold;
        color: #f0f0f0;
    }

    /* Input fields */
    .signup-card input[type="text"],
    .signup-card input[type="tel"] {
        width: 100%;
        padding: 10px;
        border: 2px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
    }

    /* Submit button */
    .signup-card button {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        font-size: 18px;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    /* Button hover effect */
    .signup-card button:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, #2ecc71, #27ae60);
    }

    /* Link styling inside signup card */
    .signup-card p a {
        color: yellow;
        text-decoration: none;
        font-weight: bold;
    }

    /* Link hover effect */
    .signup-card p a:hover { text-decoration: underline; }

    /* Error message styling */
    .error {
        color: red;
        font-size: 1rem;
        margin-left: 4px;
        text-align: left;
    }
  </style>
</head>
<body>
  <!--Sidebar toggle icon-->
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="wrapper">
    <!--Sidebar navigation-->
    <div class="sidebar">
      <h2>TBM</h2>
      <ul>
        <li><a href="home.html"><i class="fa-solid fa-house"></i>  Home</a></li>
        <li><a href="#"><i class="fa-solid fa-bus"></i>  About</a></li>
        <li><a href="#"><i class="fa-solid fa-comments"></i>  Inquiries</a></li>
      </ul>
    </div>

    <!--Top header bar-->
    <nav class="header-bar">
      <div class="logo">
        <h1>TSHWANE<span>BUSMATE</span></h1>
      </div>
    </nav>

    <!--Signup form card-->
    <div class="signup-card">
      <h2>Sign Up</h2>
      <p class="subtitle">Create your TshwaneBusMate account</p>

      <!--Signup form-->
      <form method="POST" action="">
        <!--Full name input-->
        <label for="fullname">Full Name<span class="error">*</span></label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" placeholder="Enter full name" required />
        <span class="error"><?php echo $fullnameErr ?? ''; ?></span>

        <!--Phone number input-->
        <label for="phone">Phone Number<span class="error">*</span></label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Enter phone number" required pattern="[0-9]{10,15}" />
        <span class="error"><?php echo $phoneErr ?? ''; ?></span>

        <!--Submit button-->
        <button type="submit">Next</button>
      </form>

      <!--Terms and login links-->
      <br>
      <p>By clicking the button, you agree to our <br><a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></p><br>
      <p>Already have an account? <a href="login.php">Log In here</a></p>
    </div>
  </div>

  <!--Sidebar toggle script-->
  <script>
    function toggleSidebar() {
      document.querySelector('.wrapper').classList.toggle('active');
    }
  </script>
</body>
</html>

