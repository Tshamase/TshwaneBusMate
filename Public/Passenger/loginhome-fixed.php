<?php
session_start();
$name = $_SESSION["user"] ?? "fullname";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tshwane Busses</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    /* Reset and base styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #1a2f3a, #0d1b24);
        color: #f0f0f0;
        line-height: 1.2;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Header styles */
    .header-bar {
        width: 100%;
        height: 10vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px 20px;
        color: white;
        background: linear-gradient(135deg, #000, #0d1b24);
        box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        position: fixed;
        top: 0;
        z-index: 100;
    }

    .logo h1 {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(to right, #fff 51%, #FFD700 49%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Sidebar styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: -220px;
        width: 180px;
        height: 100vh;
        background: #0d1b24;
        padding: 20px 0;
        transition: left 0.3s ease;
        z-index: 999;
        border-right: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar.active { left: 0; }

    .sidebar h2 {
        color: #FFD700;
        font-style: italic;
        text-align: center;
        margin-bottom: 15px;
        font-size: 1.2rem;
    }

    .sidebar ul { list-style: none; }

    .sidebar ul li {
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
        font-size: 0.9rem;
    }

    .sidebar ul li a {
        color: #fafafc;
        text-decoration: none;
        flex-grow: 1;
    }

    .sidebar ul li:hover { background: rgba(255,255,255,0.05); }
    .sidebar ul li:hover a { color: #27ae60; }

    /* Menu toggle */
    .menu-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        font-size: 20px;
        color: #27ae60;
        cursor: pointer;
        z-index: 1000;
    }

    /* Main container */
    .main-container {
        margin-top: 10vh;
        min-height: 90vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 10px;
        padding: 10px;
    }

    .main-container > section {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        padding: 15px;
        background: rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow-y: auto;
    }

    .bus-section {
        grid-column: 1;
        grid-row: 1 / 3;
    }

    .text-section {
        grid-column: 2;
        grid-row: 1;
    }

    .map-section {
        grid-column: 2;
        grid-row: 2;
    }

    /* Content styles */
    .content {
        text-align: center;
        max-width: 100%;
    }

    .content h1 {
        font-size: 1.5rem;
        color: #27ae60;
        margin-bottom: 5px;
    }

    .content h2 {
        font-size: 1.2rem;
        color: #FFD700;
        margin-bottom: 8px;
    }

    .content h2 span {
        color: #27ae60;
    }

    .content p {
        font-size: 0.9rem;
        color: #ddd;
        margin: 5px 0;
    }

    .main-btn button {
        margin-top: 10px;
        padding: 8px 20px;
        font-size: 14px;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .main-btn button:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #2ecc71, #27ae60);
    }

    /* Dropdown styles */
    .dropdown {
        background: rgba(0, 0, 0, 0.2);
        margin-bottom: 8px;
        border-radius: 5px;
        overflow: hidden;
        width: 100%;
        max-width: 250px;
    }

    .dropdown input[type="checkbox"] { display: none; }

    .dropdown label {
        display: block;
        padding: 8px 12px;
        background: rgba(39, 174, 96, 0.8);
        color: #fff;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.85rem;
        transition: background 0.3s;
    }

    .dropdown label:hover { background: rgba(39,174,96,1); }

    .dropdown-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background: rgba(0, 0, 0, 0.1);
    }

    .dropdown input[type="checkbox"]:checked ~ .dropdown-content {
        max-height: 200px;
        padding: 8px 12px;
    }

    .dropdown-content ul {
        list-style: none;
        padding: 0;
    }

    .dropdown-content li {
        margin-bottom: 4px;
        color: #ddd;
        font-size: 0.8rem;
    }

    .dropdown-content a {
        color: #27ae60;
        text-decoration: none;
    }

    .dropdown-content a:hover {
        color: #FFD700;
    }

    /* Map styles */
    .map-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }

    .map-wrapper iframe {
        width: 100%;
        max-width: 300px;
        height: 200px;
        border-radius: 5px;
        border: none;
    }

    .map-text {
        max-width: 250px;
        font-size: 0.8rem;
        color: #ddd;
        text-align: center;
    }

    .map-text h2 {
        color: #FFD700;
        font-size: 1rem;
        margin-bottom: 5px;
    }

    /* Footer */
    .footer {
        background: linear-gradient(135deg, #000, #0d1b24);
        color: white;
        text-align: center;
        padding: 8px;
        font-size: 0.8rem;
    }

    .icons a {
        color: #27ae60;
        font-size: 16px;
        margin: 0 5px;
        transition: color 0.3s;
    }

    .icons a:hover { color: #FFD700; }

    /* Responsive */
    @media (max-width: 768px) {
        .main-container {
            grid-template-columns: 1fr;
            grid-template-rows: auto;
        }
        
        .bus-section {
            grid-column: 1;
            grid-row: auto;
        }
    }
  </style>
</head>
<body>

  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>

  <div class="sidebar">
    <h2>TBM</h2>
    <ul>
      <li><a href="home.html"><i class="fa-solid fa-house"></i>Home</a></li>
      <li><a href="#"><i class="fa-solid fa-bus"></i>About</a></li>
      <li><a href="routes and tracking.html"><i class="fa-solid fa-map-location"></i>Bus Routes</a></li>
      <li><a href="PayGateway.html"><i class="fa-solid fa-id-card-clip"></i>Bus Card</a></li>
      <li><a href="#"><i class="fa-solid fa-comments"></i>Inquiries</a></li>
    </ul>
  </div>

  <nav class="header-bar">
    <div class="logo">
      <h1>TSHWANE<span>BUSMATE</span></h1>
    </div>
  </nav>

  <div class="main-container">
    <section class="bus-section">
      <h1>Favourite Routes</h1>
      
      <div class="dropdown">
        <input type="checkbox" id="pc">
        <label for="pc">Pretoria Central</label>
        <div class="dropdown-content">
          <ul>
            <li><strong>Areas:</strong></li>
            <ul>
              <li><a href="Pretoria Central.html">Brooklyn 5_8</a></li>
              <li>Brooklyn 9</li>
              <li>Brooklyn 10</li>
              <li>Colbyn</li>
              <li>Muckleneuk7</li>
              <li>Sunnyside</li>
            </ul>
          </ul>
        </div>
      </div>

      <div class="dropdown">
        <input type="checkbox" id="pe">
        <label for="pe">Pretoria East</label>
        <div class="dropdown-content">
          <ul>
            <li><strong>Areas:</strong></li>
            <ul>
              <li><a href="Pretoria East.html">Defence Force</a></li>
              <li>Eastlynne 2</li>
              <li>Faerie Glen</li>
              <li>Garsfontein 1</li>
              <li>Garsfontein 2</li>
              <li>Garsfontein 3</li>
              <li>Innovation Hub</li>
              <li>Lynnwood Manor, Ridge</li>
              <li>Meyerspark</li>
              <li>Monumentpark 1</li>
              <li>Monumentpark 2</li>
              <li>Moreleta Park</li>
              <li>Murrayfield</li>
              <li>Queenswood</li>
              <li>Scientia</li>
              <li>Silverton 1</li>
              <li>Silverton 2</li>
              <li>Silverton 3</li>
              <li>Waterkloof Glen, Ridge</li>
              <li>Wilgers Wapadrand</li>
            </ul>
          </ul>
        </div>
      </div>
    </section>

    <section class="text-section">
        <div class="content">
            <h1>Welcome!</h1>
            <h2>To <span>TshwaneBusMate</span></h2>
            <p>You have successfully logged in <?php echo htmlspecialchars($name); ?>!</p>
            <div class="main-btn"><button>About</button></div>
        </div>
    </section>

    <section class="text-section">
        <div class="content">
            <h1>Welcome!</h1>
            <h2>To <span>TshwaneBusMate</span></h2>
            <p>You have successfully logged in <?php echo htmlspecialchars($name); ?>!</p>
            <div class="main-btn"><button>About</button></div>
        </div>
    </section>

    <section class="map-section">
        <h1>Map</h1>
        <div class="map-wrapper">
            <iframe 
                src="https://embed.waze.com/iframe?zoom=14&lat=-26.2361&lon=28.3694&ct=livemap" 
                width="300" 
                height="200"
                allowfullscreen>
            </iframe>
            <div class="map-text">
                <h2>Live Traffic View</h2>
                <p>Stay ahead of delays! Use our real-time map to track traffic and plan your bus journey with ease.</p>
            </div>
        </div>
    </section>
  </div>

  <div class="footer">
    <div class="icons">
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
      <a href="#"><i class="fa-brands fa-facebook"></i></a>
      <a href="#"><i class="fa-brands fa-square-instagram"></i></a>
    </div>
    <p>Copyright &copy;2025; Designed by <span class="designer">Tshwane</span></p>
  </div>

  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('active');
    }

    // Close sidebar when clicking outside
    window.addEventListener('click', function(e) {
      const sidebar = document.querySelector('.sidebar');
      const toggle = document.querySelector('.menu-toggle');
      
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('active');
      }
    });
  </script>
</body>
</html>
