<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - TshwaneBusMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        header {
            background: linear-gradient(135deg, #000 0%, #0d1b24 100%);
            padding: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            position: sticky;
            top: 0;
            z-index: 100;
            text-align: center;
        }

        .logo h1 {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff 60%, #FFD700 40%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .cancel-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .cancel-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .cancel-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #e74c3c;
        }

        .cancel-container p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #f0f0f0;
        }

        .back-button {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
            margin-right: 15px;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .try-again-button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .try-again-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        footer {
            background: linear-gradient(135deg, #000, #0d1b24);
            padding: 20px 0;
            text-align: center;
            color: #aaa;
            margin-top: auto;
        }

        .copyright {
            margin-top: 0px;
            padding-top: 0px;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <h1>TshwaneBusMate</h1>
        </div>
    </header>

    <main>
        <div class="cancel-container">
            <div class="cancel-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2>Payment Cancelled</h2>
            <p>Your payment was cancelled. If you think this is an error, please try again or contact support.</p>
            <a href="Credit_Payment/credit_wallet.html" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Wallet
            </a>
            <a href="payment_gateway.html" class="try-again-button">
                <i class="fas fa-redo"></i> Try Again
            </a>
        </div>
    </main>

    <footer>
        <div class="copyright">Copyright &copy; 2025 City of Tshwane. All rights reserved.</div>
    </footer>
</body>

</html>