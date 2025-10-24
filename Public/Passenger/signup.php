<!--Signup page HTML structure-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TshwaneBusMate Signup</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/signup.css">
</head>
<body>
  <!--Sidebar toggle button-->
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="wrapper">
    <!--Navigation sidebar-->
    <div class="sidebar">
      <h2>TBM</h2>
      <ul>
        <li><a href="home.html"><i class="fa-solid fa-house"></i>  Home</a></li>
        <li><a href="#"><i class="fa-solid fa-bus"></i>  About</a></li>
      </ul>
    </div>

    <!--Header bar with logo-->
    <nav class="header-bar">
      <div class="logo">
        <h1>TSHWANE<span>BUSMATE</span></h1>
      </div>
    </nav>

    <!--Main signup form container-->
    <div class="signup-card">
      <h2>Sign Up</h2>
      <p class="subtitle">Create your TshwaneBusMate account</p>

      <!--General error display area-->
      <div id="generalError" class="general-error" style="display: none;"></div>

      <!--Signup form with validation-->
      <form method="POST" action="process_signup.php" id="signupForm" novalidate>
        <label for="fullname">Full Name<span class="error">*</span></label>
        <input type="text" id="fullname" name="fullname" placeholder="First Last" />
        <span class="error" id="fullnameError"></span>

        <label for="email">Email<span class="error">*</span></label>
        <input type="email" id="email" name="email" placeholder="example@domain.com" />
        <span class="error" id="emailError"></span>

        <button type="submit">Next</button>
      </form>
      <br>
      <!--Terms and login links-->
      <p>By clicking the button, you agree to our <br><a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></p><br>
      <p>Already have an account? <a href="login.html">Log In here</a></p>
    </div>
  </div>

  <!--JavaScript includes-->
  <script src="js/sidebar.js"></script>
  <script src="js/signup.js"></script>
</body>
</html>
