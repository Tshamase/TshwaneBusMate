<?php
session_start();
$name = $_SESSION["fullname"] ?? "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tshwane Busses</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="ccc.css" />
</head>
<body>
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="wrapper">
  
    <div class="sidebar">
      <div class="close-btn" onclick="toggleSidebar()">✖</div>
      <h2>TBM</h2>
      <ul>
        <li><a href="help.html"><i class="fa-solid fa-house"></i>Home</a></li>
        <li><a href="#"><i class="fa-solid fa-bus"></i>About</a></li>
        <li><a href="routes and tracking.html"><i class="fa-solid fa-map-location"></i>Bus Routes</a></li>
        <li><a href="PayGateway.html"><i class="fa-solid fa-id-card-clip"></i>Bus Card</a></li>
        <li><a href="#"><i class="fa-solid fa-comments"></i>Inquiries</a></li>
      </ul>
    </div>

    <nav>
      <div class="logo">
        <h1>TSHWANE<span>BUSMATE</span></h1>
      </div>
      <div class="buttons">
        <div class="btn"><a href="login.html"><button>Login</button></a></div>
      </div>
    </nav>
<div class="container">

      <section class="Bus">
         <div class="dropdown">
      <input type="checkbox" id="cen">
      <label for="cen">Centurion</label>
      <div class="dropdown-content">
       
        <ul>
          <li><strong>Areas:</strong></li>
        <ul>
            <li>bus Routes</li>
           	<li><a href="Centurion.html">ROUTE 4 - Olievenhoutbos to Centurion via Highveld - Ecopark - Southdowns</a></li>
            <li>ROUTE 7 - Olievenhoutbos to Wierdapark - Eldoraine - Clubview A</li>
            <li>Route 9</li>
            <li>Valhalla</li>
            <li>Voortrekkerhoogte</li>
        </ul>
		</ul>
      </div>
    </div>

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

    
    <div class="dropdown">
      <input type="checkbox" id="pmo">
      <label for="pmo">Pretoria Moot</label>
      <div class="dropdown-content">
        <ul>
          <li><strong>Areas:</strong></li>
        <ul>
            <li><a href="Pretoria Moot.html">Moregloed 1</a></li>
            <li>Rietfontein21</li>
            <li>Villieria1_3</li>
            <li>Waverley 6</li>
            <li>Wonderboom South</li>
        </ul>
		<ul>
      </div>
    </div>

   
    <div class="dropdown">
      <input type="checkbox" id="pn">
      <label for="pn">Pretoria North</label>
      <div class="dropdown-content">
	    <ul>
          <li><strong>Areas:</strong></li>
        <ul>
			<li><a href="Pretoria North.html">Pretoria North 1</a></li>
			<li>Pretoria North 2</li>
			<li>Pretoria North 3</li>
			<li>Pretoria North 4</li>
			
            <li>Doornpoort 1</li>
			<li>Doornpoort 2</li>
			<li>Doornpoort 3</li>
			
            <li>Wonderboom 1</li>
			<li>Wonderboom 2</li>
			<li>Wonderboom 3</li>
			<li>Wonderboom 4</li>
			
            <li>The Orchards 1</li>
			<li>The Orchards 2</li>
			<li>The Orchards 3</li>
			
            <li>Theresa Park</li>
            <li>Mountainview 1</li>
			<li>Mountainview 2_4</li>
        </ul>
		</ul>
      </div>
    </div>

  
    <div class="dropdown">
      <input type="checkbox" id="pw">
      <label for="pw">Pretoria West</label>
      <div class="dropdown-content">
        <ul>
          <li><strong>Areas:</strong></li>
          <ul>
		  
			<li>WestPark 1</li>
			<li>WestPark 2</li>
			<li>WestPark 6</li>
			<li><a href="Pretoria West 2.html">WestPark 7</a></li>
			<li>WestPark 8</li>
			
            <li>Booysens</li>
            <li>Erasmia 2</li>
			<li>Erasmia 3</li>
            <li>Danville 1, 2–6</li>
			
            <li>Tuine 1</li>
			<li>Tuine 2</li>
          </ul>
        </ul>
      </div>
    </div>	

      </section>
       <section class="Text">
          <div class="content">
            <h1>Welcome!</h1>
            <h2>To <span>TshwaneBusMate</span></h2>
            <p>You have successfully signed up <?php echo htmlspecialchars($name); ?>!</p>
            <div class="main-btn"><button>About</button></div>
          </div>
        </section>

      <section class="map">
  <h1>Map</h1>
  <div class="map-wrapper">
    <iframe 
      src="https://embed.waze.com/iframe?zoom=14&lat=-26.2361&lon=28.3694&ct=livemap" 
      width="600" 
      height="450" 
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
      <p>Copyright &copy;2025; Designed by <span class="designer"> Tshwane</span></p>
    </div>
  </div>
</div>

  <script>
    function toggleSidebar() {
      document.querySelector('.wrapper').classList.toggle('active');
    }

    window.addEventListener('click', function(e) {
      const wrapper = document.querySelector('.wrapper');
      const sidebar = document.querySelector('.sidebar');
      const toggle = document.querySelector('.menu-toggle');

      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        wrapper.classList.remove('active');
      }
    });
  </script>
</body>
</html>