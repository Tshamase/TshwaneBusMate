<!--Email verification page HTML structure-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - TshwaneBusMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <!--Inline styles for verification card-->
    <style>
        .verify-card {
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

        .verify-card h2 {
            font-size: 28px;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .verify-card p {
            font-size: 16px;
            color: #bbb;
            margin-bottom: 25px;
        }

        .code-input {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .code-input input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .code-input input:focus {
            border-color: #27ae60;
            outline: none;
        }

        .resend-link {
            color: #27ae60;
            text-decoration: none;
            font-size: 14px;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .error {
            color: #ff4444;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!--Header bar with logo-->
    <nav class="header-bar">
        <div class="logo">
            <h1>TSHWANE<span>BUSMATE</span></h1>
        </div>
    </nav>

    <!--Main verification card-->
    <div class="verify-card">
    <h2>Verify Your Email</h2>
    <p>We've sent a 4-digit code to your email address. Please enter it below to complete your registration.</p>

    <!--Test code display for development-->
    <?php
    session_start();
    if (isset($_SESSION['pending_verification'])) {
        echo "<div id='testCodeBlock' style='background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid rgba(39, 174, 96, 0.3);'>";
        echo "<p style='color: #27ae60; font-weight: bold; margin: 0;'>For Testing: Verification Code</p>";
        echo "<p id='testCode' style='color: #fff; font-size: 24px; font-weight: bold; letter-spacing: 3px; margin: 5px 0;'>" . $_SESSION['pending_verification']['code'] . "</p>";
        echo "<p style='color: #ccc; font-size: 12px; margin: 0;'>Remove this in production</p>";
        echo "</div>";
    }
    ?>

    <!--Verification form-->
    <form id="verifyForm" method="POST" action="verify_code.php">
        <div class="code-input">
            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
        </div>
        <input type="hidden" name="verification_code" id="verificationCode">
        <button type="submit">Verify Email</button>
    </form>

    <!--Timer and resend link-->
    <p id="timer">Code expires in: <span>3:00</span></p>
    <a href="#" class="resend-link" id="resendLink" style="display: none;">Send New Code</a>

    <!--Error message display-->
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    </div>

    <!--JavaScript for form interaction-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.code-input input');
            const form = document.getElementById('verifyForm');
            const hiddenInput = document.getElementById('verificationCode');
            const timerSpan = document.querySelector('#timer span');
            const resendLink = document.getElementById('resendLink');

            //Auto-focus next input field
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            //Update hidden input whenever inputs change
            function updateHiddenInput() {
                const code = Array.from(inputs).map(input => input.value).join('');
                hiddenInput.value = code;
            }

            inputs.forEach(input => {
                input.addEventListener('input', updateHiddenInput);
            });

            //Handle form submission - ensure code is complete
            form.addEventListener('submit', function(e) {
                const code = hiddenInput.value;
                if (code.length !== 4) {
                    e.preventDefault();
                    alert('Please enter all 4 digits of the verification code.');
                }
            });

            //Countdown timer functionality
            let timeLeft = 3 * 60; //3 minutes in seconds
            const timer = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerSpan.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timerSpan.parentElement.style.display = 'none';
                    resendLink.style.display = 'inline';
                }
            }, 1000);

            //Handle resend link click with AJAX
            resendLink.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=resend_code'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        //Reset timer and show success message
                        timeLeft = 3 * 60;
                        timerSpan.parentElement.style.display = 'block';
                        resendLink.style.display = 'none';
                        //Update test code display
                        const testCodeElement = document.getElementById('testCode');
                        if (testCodeElement && data.newCode) {
                            testCodeElement.textContent = data.newCode;
                        }
                        //Show success message
                        const successMsg = document.createElement('p');
                        successMsg.style.color = '#27ae60';
                        successMsg.style.marginTop = '10px';
                        successMsg.textContent = 'New verification code sent!';
                        resendLink.parentNode.appendChild(successMsg);
                        setTimeout(() => successMsg.remove(), 3000);
                    } else {
                        //Show error message
                        const errorMsg = document.createElement('p');
                        errorMsg.style.color = '#ff4444';
                        errorMsg.style.marginTop = '10px';
                        errorMsg.textContent = data.message || 'Failed to send new code. Please try again.';
                        resendLink.parentNode.appendChild(errorMsg);
                        setTimeout(() => errorMsg.remove(), 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMsg = document.createElement('p');
                    errorMsg.style.color = '#ff4444';
                    errorMsg.style.marginTop = '10px';
                    errorMsg.textContent = 'Failed to send new code. Please try again.';
                    resendLink.parentNode.appendChild(errorMsg);
                    setTimeout(() => errorMsg.remove(), 3000);
                });
            });
        });
    </script>
</body>
</html>
