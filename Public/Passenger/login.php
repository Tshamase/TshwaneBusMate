<?php
session_start(); // Start session to store user data and error messages

// Function to sanitize user input (removes spaces and special characters)
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Check if form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize form inputs
    $fullname = sanitize($_POST['fullname'] ?? '');
    $buscard  = sanitize($_POST['buscard'] ?? '');

    // If fullname is missing, show error and redirect
    if (empty($fullname)) {
        $_SESSION['error'] = "Full Name is required.";
        header("Location: login.php");
        exit();
    }

    // Connect to MySQL database
    $conn = new mysqli("localhost", "root", "", "tshwanebusmate");

    // If connection fails, show error and redirect
    if ($conn->connect_error) {
        $_SESSION['error'] = "Database connection failed.";
        header("Location: login.php");
        exit();
    }

    // Prepare SQL to find user by fullname (case-insensitive)
    $stmt = $conn->prepare("SELECT * FROM signup WHERE LOWER(fullname) = LOWER(?)");
    if ($stmt === false) {
        $_SESSION['error'] = "SQL prepare failed.";
        $conn->close();
        header("Location: login.php");
        exit();
    }

    // Bind fullname to query and execute
    $stmt->bind_param("s", $fullname);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user found, log the login attempt
    if ($result && $result->num_rows === 1) {
        $log = $conn->prepare(
            "INSERT INTO login (username, buscardNum, verified, verified_at) 
             VALUES (?, ?, 1, NOW())"
        );
        if ($log === false) {
            $_SESSION['error'] = "SQL prepare failed.";
            $stmt->close();
            $conn->close();
            header("Location: login.php");
            exit();
        }

        // Save login info
        $log->bind_param("ss", $fullname, $buscard);
        $log->execute();
        $log->close();

        // Store user in session and redirect to dashboard
        $_SESSION['user'] = $fullname;

        $stmt->close();
        $conn->close();
        header("Location: loginhome.php");
        exit();
    } else {
        // If user not found, show error
        $_SESSION['error'] = "Invalid login credentials.";
        $stmt->close();
        $conn->close();
        header("Location: login.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TshwaneBusMate Login</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"></script>
  <style>
/*Appling consistent box-sizing and reset default margins/padding across all elements*/
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/*Setting body background gradient, text color, and base line height*/
body {
    background: linear-gradient(135deg, #1a2f3a, #0d1b24);
    color: #f0f0f0;
    line-height: 1.6;
    min-height: 100vh;
}

/*Adding a dark overlay to the entire screen for depth*/
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}

/*Styling for the menu toggle icon*/
.menu-toggle {
    position: fixed;
    top: 30px;
    left: 20px;
    font-size: 24px;
    color: #27ae60;
    cursor: pointer;
    z-index: 1000;
}

/*Sidebar hidden by default, slides in when active*/
.sidebar {
    position: fixed;
    top: 0;
    left: -220px;
    width: 200px;
    height: 100vh;
    background: #0d1b24;
    padding: 30px 0;
    transition: left 0.3s ease;
    z-index: 999;
    border-right: 1px solid rgba(255,255,255,0.1);
}

/*Showing sidebar when wrapper has 'active' class*/
.wrapper.active .sidebar {
    left: 0;
}

/*Sidebar title styling*/
.sidebar h2 {
    color: #FFD700;
    font-style: italic;
    text-align: center;
    margin-bottom: 30px;
}

/*Removing default list styling*/
.sidebar ul {
    list-style: none;
}

/*Styling each sidebar list item*/
.sidebar ul li {
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background 0.3s ease;
}

/*Styling sidebar links */
.sidebar ul li a {
    color: #fafafc;
    text-decoration: none;
    font-size: 1rem;
    flex-grow: 1;
}

/* Hover effect for sidebar items*/
.sidebar ul li:hover {
    background: rgba(255,255,255,0.05);
}
.sidebar ul li:hover a {
    color: #27ae60;
}

/*Header bar at the top of the page*/
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

/*Logo text styling with gradient fill*/
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

/*Login form container with glassmorphism effect*/
.login-wrapper {
    max-width: 450px;
    margin: 160px auto;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    padding: 30px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.1);
}

/*Login form heading*/
h2 {
    font-size: 28px;
    color: #27ae60;
    margin-bottom: 10px;
}

/*Subtitle text under heading*/
.subtitle p {
    font-size: 20px;
    color: #bbb;
    margin-bottom: 25px;
}

/*Label styling for form inputs*/
label {
    display: block;
    text-align: left;
    margin: 15px 0 8px;
    font-weight: bold;
    color: #f0f0f0;
}

/*Text input field styling*/
input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 2px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

/*Container for 'Remember Me' checkbox*/
.remember-container {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 15px;
}

/*Checkbox styling*/
.remember-container input[type="checkbox"] {
    transform: scale(1.2);
    cursor: pointer;
}

/*Label next to checkbox*/
.remember-container label {
    font-size: 16px;
    font-weight: normal;
    margin: 0;
    cursor: pointer;
}

/*Submit button styling*/
button {
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

/*Button hover effect*/
button:hover {
    transform: translateY(-3px);
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

/*Footer text below form*/
.lastline p {
    font-size: 15px;
    margin-top: 20px;
}

/*Link styling in footer*/
.lastline p a {
    color: yellow;
    text-decoration: none;
    font-weight: bold;
}

/*Link hover effect*/
.lastline p a:hover {
    text-decoration: underline;
}

/*Inline error message styling*/
.error {
    color: red;
    font-size: 1rem;
    margin-left: 4px;
    text-align: left;
}

/*Full-width error box styling*/
.error-message {
    background-color: rgba(255, 0, 0, 0.1);
    border: 1px solid rgba(255, 0, 0, 0.3);
    color: #ff6b6b;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}

  </style>
</head>
<body>
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>TBM</h2>
      <ul>
        <li><a href="home.html"><i class="fa-solid fa-house"></i>  Home</a></li>
        <li><a href="#"><i class="fa-solid fa-bus"></i>  About</a></li>
        <li><a href="#"><i class="fa-solid fa-comments"></i>  Inquiries</a></li>
      </ul>
    </div>

    <!-- Header Bar -->
    <nav class="header-bar">
      <div class="logo">
        <h1>TSHWANE<span>BUSMATE</span></h1>
      </div>
    </nav>

    <!-- Login Section -->
    <div class="login-wrapper">
      <form action="login.php" method="post">
        <h2>Login</h2>
        <div class="subtitle"><p>Please enter the required details below</p></div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <label for="fullname">Full Name<span class="error">*</span></label>
        <input type="text" id="fullname" name="fullname" placeholder="Enter full name" required />

        <label for="buscard">Bus Card no<span class="error">*</span></label>
        <input type="text" id="buscard" name="buscard" placeholder="Enter card number" required />

        <div class="remember-container">
          <input type="checkbox" id="remember" name="remember" />
          <label for="remember">Remember me</label>
        </div>

        <button type="submit">Login</button>
        <div class="lastline"><p>Don't have an account? <a href="signup.php">Sign up here</a></p></div>
      </form>
    </div>
  </div>

  <script>
  //function toggles the sidebar visibility by adding/removing the 'active' class on the wrapper
  function toggleSidebar() {
    document.querySelector('.wrapper').classList.toggle('active');
  }
</script>

</body>
</html>
