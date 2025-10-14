<?php
include 'Database.php';
$PaymentUrl = 'Payment Gate.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Credit Wallet - TshwaneBusMate</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
      /* ===== Reset and Typography ===== */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }

      body {
        background: linear-gradient(135deg, #1a2f3a, #0d1b24);
        color: #f0f0f0;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      /* ===== Header ===== */
      header {
        background: linear-gradient(135deg, #000 0%, #0d1b24 100%);
        padding: 20px 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        position: sticky;
        top: 0;
        z-index: 100;
        text-align: center;
      }

      header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        background: linear-gradient(to right, #fff 60%, #FFD700 40%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
      }

      header p {
        font-size: 0.9rem;
        color: #bbb;
      }

      /* ===== Balance Section ===== */
      .balance {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        margin: 20px auto;
        width: 80%;
        max-width: 600px;
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
      }

      .balance h2 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #27ae60;
      }

      .balance p {
        font-size: 1rem;
        color: #e0e0e0;
      }

      /* ===== Main Card Section ===== */
      main {
        flex: 1;
        display: flex;
        align-items: top;
        justify-content: center;
        padding: 2rem;
      }

      .card-container {
        background-image: url(Images/684.jpg);
        background-size: cover;
        padding: 25px;
        width: 100%;
        max-width: 80%;
        border-radius: 28px;
        height: 250px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        color: black;
        position: relative;
      }

      .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
      }

      .card-details {
        position: absolute;
        bottom: 25px;
        left: 25px;
        right: 25px;
      }

      .card-number {
        font-size: 1.2rem;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
      }

      .card-name {
        font-size: 1rem;
        opacity: 0.9;
      }

      /* ===== Load History Section ===== */
      .load-history {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 2rem auto;
        width: 80%;
        gap: 50px;
      }

      .load-history h4 {
        margin-bottom: 10px;
        color: #27ae60;
      }

      .load, .history {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: bold;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
      }

      .load {
        background: #FFD700 url(Images/wallet.png) no-repeat center/cover;
        color: white;
      }

      .load:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        cursor: pointer;
      }

      .history {
        background: #FFD700 url(Images/document.png) no-repeat center/cover;
        color: #2b2b2b;
      }

      .history:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        cursor: pointer;
      }

      .icon-link {
        text-decoration: none;
        color: inherit;
      }

      /* ===== Footer ===== */
      footer {
        background: linear-gradient(135deg, #000, #0d1b24);
        padding: 20px 0;
        text-align: center;
        color: #f0f0f0;
        margin-top: 50px;
        font-size: 0.9rem;
      }

      /* ===== Responsive ===== */
      @media (max-width: 768px) {
        .load-history {
          flex-direction: column;
          gap: 30px;
        }

        .balance {
          width: 90%;
        }

        main {
          padding: 1rem;
        }
      }
    </style>
  </head>
  <body>
    <header>
      <h1>TshwaneBusMate</h1>
      <p>On the move for people</p>
    </header>

    <div class="balance">
      <h2>Total Balance:</h2>
      <p>
        Remaining Balance: 
        <?php
          $userId = 1; // Example user ID
          $query = "SELECT balance FROM transactions WHERE id = $userId";
          $result = mysqli_query($conn, $query);
          if ($result && $row = mysqli_fetch_assoc($result)) {
            echo htmlspecialchars($row['balance']) . " credits";
          } else {
            echo "Unable to fetch balance";
          }
        ?>
      </p>
    </div>

    <main>
      <div class="card-container">
        <div class="card-header"></div>
        <div class="card-details">
          <p>Card Number:</p>
          <div class="card-number">5576 9700 ****** 00</div>
          <p>Valid:</p>
          <div class="card-number">10/26</div>
          <div class="card-name">Ms/Mr ......</div>
        </div>
      </div>
    </main>

    <section class="load-history">
      <div style="text-align:center;">
        <h4>Load Credits</h4>
        <a class="icon-link" href="<?php echo $PaymentUrl; ?>">
          <div class="load"></div>
        </a>
      </div>

      <div style="text-align:center;">
        <h4>Load History</h4>
        <a class="icon-link" href="<?php echo $historyUrl; ?>">
          <div class="history"></div>
        </a>
      </div>
    </section>

    <footer>Copyright &copy; 2025 City of Tshwane. All rights reserved.</footer>
  </body>
</html>
