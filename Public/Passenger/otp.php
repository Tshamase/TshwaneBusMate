<?php
//Starts session to store user data
session_start();

//Checks if user came from signup page
if (!isset($_SESSION['pending_user_id'])) {
    //If not, sends them back to signup
    header("Location: signup1.php");
    exit();
}

//Get any OTP error message from session
$otpError = $_SESSION['otp_error'] ?? '';

//Remove the error message after showing it
unset($_SESSION['otp_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TBM OTP</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background: linear-gradient(135deg, #1a2f3a, #0d1b24);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #f0f0f0;
    }

    /*OTP form box*/
    .form {
      width: 320px;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 30px 20px;
      gap: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      border-radius: 15px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.1);
      animation: fadeIn 0.5s ease-out;
    }

    .title {
      font-size: 24px;
      font-weight: 700;
      letter-spacing: 1px;
      background-color: yellow;
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .text {
      font-size: 14px;
      color: #bbb;
      line-height: 1.5;
    }

    /*OTP input boxes*/
    .numcontainer {
      display: flex;
      gap: 12px;
      justify-content: center;
    }

    .numcontainer input {
      background: rgba(255, 255, 255, 0.9);
      width: 50px;
      height: 50px;
      text-align: center;
      border: 2px solid #ccc;
      border-radius: 10px;
      font-size: 20px;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .numcontainer input:focus {
      border-color: #27ae60;
      box-shadow: 0 0 10px rgba(39, 174, 96, 0.6);
      outline: none;
    }

    /*Submit button*/
    .submitBtn {
      width: 100%;
      height: 45px;
      border: none;
      background: linear-gradient(135deg, #27ae60, #2ecc71);
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      border-radius: 50px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .submitBtn:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg, #2ecc71, #27ae60);
    }

    /*Error message style*/
    .error {
      color: #ffdddd;
      background: rgba(102, 0, 0, 0.15);
      padding: 8px;
      border-radius: 6px;
      font-size: 14px;
    }

    /*Mobile screen adjustments*/
    @media screen and (max-width: 400px) {
      .form { width: 90%; }
      .numcontainer input {
        width: 40px;
        height: 40px;
        font-size: 18px;
      }
    }

    /*Fade-in animation*/
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <!--OTP form-->
  <form class="form" method="POST" action="verify.php">
    <span class="title">Enter OTP</span>
    <p class="text">We have sent a verification code on your mobile number</p>
    <!--PHP to show OTP error-->
    <?php if (!empty($otpError)): ?>
      <p class="error"><?php echo htmlspecialchars($otpError); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['dev_otp'])): ?>
    <p style="background:#ffeeba; color:#856404; padding:7px; border-radius:5px; font-weight:bold;">
       Dev Testing — OTP is <?php echo $_SESSION['dev_otp']; ?>
    </p>
    <?php endif; ?>

    <div class="numcontainer" id="otpInputs">
      <input type="text" name="digit1" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
      <input type="text" name="digit2" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
      <input type="text" name="digit3" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
      <input type="text" name="digit4" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
    </div>

    <button class="submitBtn" type="submit">Verify</button>
    <p class="text">Didn't receive an OTP? click here to <a href="resend.php" style="color: #ffe08a;">resend</a></p>
  </form>

  <script>
    //Collects all 4 digits of OTP
    const inputs = Array.from(document.querySelectorAll('#otpInputs input'));

    //Handles typing in each box
    inputs.forEach((inp, idx) => {
      inp.addEventListener('input', () => {
        inp.value = inp.value.replace(/\D/g, '').slice(0, 1); //Makes sure its only numbers
        if (inp.value && idx < inputs.length - 1) inputs[idx + 1].focus(); //Move to next container seamlessly
      });

      inp.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !inp.value && idx > 0) inputs[idx - 1].focus(); //Move back
      });
    });

    //Handles pasting OTP-no need to type individually if it can be pasted
    document.getElementById('otpInputs').addEventListener('paste', (e) => {
      const text = (e.clipboardData || window.clipboardData).getData('text')
        .replace(/\D/g, '')
        .slice(0, inputs.length);
      if (!text) return;
      e.preventDefault();
      inputs.forEach((inp, i) => inp.value = text[i] || '');
      const nextIndex = Math.min(text.length, inputs.length - 1);
      inputs[nextIndex].focus();
    });
  </script>
</body>
</html>
