<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TshwaneBusMate Login</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="wrapper">
    <div class="sidebar">
      <h2>TBM</h2>
      <ul>
        <li><a href="home.html"><i class="fa-solid fa-house"></i>  Home</a></li>
        <li><a href="#"><i class="fa-solid fa-bus"></i>  About</a></li>
      </ul>
    </div>

    <nav class="header-bar">
      <div class="logo">
        <h1>TSHWANE<span>BUSMATE</span></h1>
      </div>
    </nav>

    <div class="login-wrapper">
      <form action="auth.php" method="post">
        <h2>Login</h2>
        <div class="subtitle"><p>Please enter the required details below</p></div>

        <!--Display login errors if any exist in session-->
        <?php
        session_start();
        if (isset($_SESSION['login_errors'])) {
            echo '<div class="error-message" style="color: red; margin-bottom: 10px;"><ul>';
            foreach ($_SESSION['login_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['login_errors']);
        }
        ?>

        <label for="role">Role<span class="error">*</span></label>
        <select id="role" name="role" required>
          <option value="">Select Role</option>
          <option value="commuter">Commuter</option>
          <option value="driver">Driver</option>
          <option value="admin">Admin</option>
        </select>

        <div id="common-fields" class="conditional-field">
          <label for="fullname">Full Name<span class="error">*</span></label>
          <input type="text" id="fullname" name="fullname" placeholder="Enter full name" required />
        </div>

        <div id="commuter-field" class="conditional-field">
          <label for="buscard">Bus Card no<span class="error">*</span></label>
          <input type="text" id="buscard" name="buscard" placeholder="Enter card number" />
        </div>

        <div id="driver-field" class="conditional-field">
          <label for="driverid">Driver-ID<span class="error">*</span></label>
          <input type="text" id="driverid" name="driverid" placeholder="Enter driver ID" />
        </div>

        <div id="admin-field" class="conditional-field">
          <label for="adminid">Admin-ID<span class="error">*</span></label>
          <input type="text" id="adminid" name="adminid" placeholder="Enter admin ID" />
        </div>

        <div id="password-field" class="conditional-field">
          <label for="password">Password<span class="error">*</span></label>
          <input type="password" id="password" name="password" placeholder="Enter password" />
        </div>

        <div class="remember-container">
          <input type="checkbox" id="remember" name="remember" />
          <label for="remember">Remember me</label>
        </div>

        <button type="submit">Login</button>
        <div class="lastline"><p>Don't have an account? <a href="signup.php">Sign up here</a></p></div>
      </form>
    </div>
  </div>

  <script src="js/sidebar.js"></script>
  <script src="js/login.js"></script>
</body>
</html>
