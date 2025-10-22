<?php
// payment_gateway.php - Builds PayFast form for payment processing

require 'PayFastIntegration.php';
require 'db_payment.php';

// Start session for user data
session_start();

// Check if user is logged in - redirect to credit wallet if not
if (!isset($_SESSION['user'])) {
    header("Location: credit_wallet.html");
    exit();
}

// Function to sanitize input
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables
$errors = [];
$totalAmount = 0;
$action = '';
$itemName = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action
    $action = sanitize_input($_POST['action'] ?? '');
    if (empty($action)) {
        $errors[] = "Please select an action.";
    }

    // Calculate total amount and item name
    if ($action === 'reload') {
        $reloadAmount = floatval($_POST['reloadAmount'] ?? 0);
        if ($reloadAmount < 11) {
            $errors[] = "Reload amount must be at least ZAR 11.00.";
        } else {
            $totalAmount = $reloadAmount;
            $itemName = "Credit Reload - ZAR " . number_format($totalAmount, 2);
        }
    } elseif ($action === 'purchase') {
        $products = $_POST['product'] ?? [];
        $productNames = [];
        foreach ($products as $product) {
            $value = floatval($product);
            if ($value > 0) {
                $totalAmount += $value;
                // Map value to product name
                $productMap = [
                    20 => 'Connector 20',
                    60 => 'Connector 60',
                    80 => 'Connector 80',
                    100 => 'Connector 100',
                    150 => 'Connector 150',
                    200 => 'Connector 200',
                    350 => 'Connector 350',
                    500 => 'Connector 500'
                ];
                if (isset($productMap[$value])) {
                    $productNames[] = $productMap[$value];
                }
            }
        }
        if ($totalAmount <= 0) {
            $errors[] = "Please select at least one product.";
        } else {
            $itemName = implode(', ', $productNames);
        }
    }

    // If no errors, build PayFast form
    if (empty($errors)) {
        // PayFast configuration (use sandbox credentials)
        $config = [
            'merchant_id' => '10042793',            // sandbox merchant id
            'merchant_key' => 'h84dovz8djy3j',      // sandbox merchant key
            'passphrase'   => '',                    // optional passphrase
            'sandbox'      => true
        ];

        $pf = new PayFastIntegration($config);

        // Generate unique payment ID
        $m_payment_id = 'order_' . time() . '_' . rand(1000, 9999);

        // Store order in database
        $userId = 1; // Assuming single user for now
        $order_id = uniqid('order_');

        $insertOrder = "INSERT INTO orders (order_id, m_payment_id, user_id, amount, item_name, transaction_type) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertOrder);
        $stmt->bind_param("ssidss", $order_id, $m_payment_id, $userId, $totalAmount, $itemName, $action);
        $stmt->execute();
        $stmt->close();

        // Build PayFast payload
        $payload = [
            'amount' => $totalAmount,
            'item_name' => $itemName,
            'm_payment_id' => $m_payment_id,
            'name_first' => 'Test',
            'name_last' => 'User',
            'email_address' => 'customer@example.com', // Changed to avoid same account payment error
            'return_url' => 'http://localhost/TshwaneBusMate/Public/Passenger/Credit_Payment/success.php',
            'cancel_url' => 'http://localhost/TshwaneBusMate/Public/Passenger/Credit_Payment/cancel.php',
            'notify_url' => 'http://localhost/TshwaneBusMate/Public/Passenger/Credit_Payment/notify.php', // Use ngrok URL in production
        ];

        // Build form data with signature
        $formData = $pf->buildFormData($payload);

        // Generate and output the redirect form with auto-submit
        echo $pf->generateRedirectForm($formData, 'Proceed to PayFast');
        echo '<script>document.getElementById("pf_payment_form").submit();</script>';
        exit();
    }
}

// If GET request or errors, display the form
if ($_SERVER["REQUEST_METHOD"] == "GET" || !empty($errors)) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>TshwaneBusMate - Payment Gateway</title>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="payment_gateway.css" />
        <style>
            /* Menu toggle icon */
            .menu-toggle {
                position: fixed;
                top: 15px;
                left: 15px;
                font-size: 20px;
                color: #27ae60;
                cursor: pointer;
                z-index: 1000;
            }

            /* Sidebar navigation menu */
            .sidebar {
                position: fixed;
                top: 0;
                left: -220px;
                /* Hidden by default */
                width: 180px;
                height: 100vh;
                background: #0d1b24;
                padding: 20px 0;
                transition: left 0.3s ease;
                /* Smooth transition */
                z-index: 999;
                border-right: 1px solid rgba(255, 255, 255, 0.1);
            }

            /* Show sidebar when active */
            .sidebar.active {
                left: 0;
            }

            /* Sidebar title */
            .sidebar h2 {
                color: #ffd700;
                /* Title color */
                font-style: italic;
                text-align: center;
                margin-bottom: 15px;
                font-size: 1.2rem;
            }

            /* Sidebar list styling */
            .sidebar ul {
                list-style: none;
            }

            .sidebar ul li {
                padding: 8px 12px;
                /* Padding for list items */
                display: flex;
                align-items: center;
                gap: 8px;
                /* Space between icon and text */
                transition: background 0.3s ease;
                font-size: 0.9rem;
            }

            /* Sidebar links */
            .sidebar ul li a {
                color: #fafafc;
                /* Link color */
                text-decoration: none;
                /* Remove underline */
                flex-grow: 1;
                /* Allow link to grow */
            }

            /* Hover effect for sidebar items */
            .sidebar ul li:hover {
                background: rgba(255, 255, 255, 0.05);
            }

            .sidebar ul li:hover a {
                color: #27ae60;
            }

            /* Package list styling */
            #packageList {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .packageItem {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .packageItem select {
                flex: 1;
            }

            .btn-remove {
                flex-shrink: 0;
            }
        </style>
    </head>

    <body>
        <!-- Page header -->
        <header>
            <div class="header-container">
                <div class="logo">
                    <h1>TshwaneBusMate</h1>
                </div>
                <button
                    class="back-button"
                    onclick="window.location.href='credit_wallet.html'">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>
        </header>

        <!-- Menu toggle icon 
        <div class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>-->

        <!-- Sidebar navigation menu -->
        <div class="sidebar">
            <h2>TBM</h2>
            <ul>
                <li>
                    <a href="../home.html"><i class="fa-solid fa-house"></i>Home</a>
                </li>
                <li>
                    <a href="#"><i class="fa-solid fa-bus"></i>About</a>
                </li>
                <li>
                    <a href="../routes and tracking.html"><i class="fa-solid fa-map-location"></i>Bus Routes</a>
                </li>
                <li>
                    <a href="credit_wallet.html"><i class="fa-solid fa-id-card-clip"></i>Bus Card</a>
                </li>
                <li>
                    <a href="#"><i class="fa-solid fa-comments"></i>Inquiries</a>
                </li>
            </ul>
        </div>

        <main>
            <!-- Left side: form for choosing action -->
            <div class="content-container">
                <h2>Payment Gateway</h2>

                <!-- Pass errors from query string to JavaScript for notification display -->
                <script>
                    <?php
                    if (isset($_GET['errors']) && !empty($_GET['errors'])) {
                        $errors = json_decode($_GET['errors'], true);
                        echo 'window.paymentErrors = ' . json_encode($errors) . ';';
                    } else {
                        echo 'window.paymentErrors = null;';
                    }
                    ?>
                </script>

                <form id="invoicePayment" method="post" action="payment_gateway.php">
                    <div class="form-group">
                        <label for="actionSelect">Choose action</label>
                        <select id="actionSelect" name="action" required>
                            <option value="" disabled selected>-- Select an option --</option>
                            <option value="reload">Reload credits (Add to balance)</option>
                            <option value="purchase">Purchase product</option>
                        </select>
                    </div>

                    <!-- Reload fields -->
                    <div id="reloadFields" class="hidden">
                        <div class="form-group">
                            <label for="reloadAmount">Reload amount (ZAR)</label>
                            <input
                                id="reloadAmount"
                                name="reloadAmount"
                                type="number"
                                min="11"
                                max="3000"
                                step="0.01"
                                placeholder="Enter amount" />
                        </div>
                    </div>

                    <!-- Purchase fields -->
                    <div id="purchaseFields" class="hidden">
                        <div class="form-group">
                            <label>Select product(s)</label>
                            <div id="packageList">
                                <div class="packageItem">
                                    <select name="product[]" required>
                                        <option value="" disabled selected>
                                            -- Choose product --
                                        </option>
                                        <option value="20">Connector 20 - ZAR 20.00</option>
                                        <option value="60">Connector 60 - ZAR 60.00</option>
                                        <option value="80">Connector 80 - ZAR 80.00</option>
                                        <option value="100">Connector 100 - ZAR 100.00</option>
                                        <option value="150">Connector 150 - ZAR 150.00</option>
                                        <option value="200">Connector 200 - ZAR 200.00</option>
                                        <option value="350">Connector 350 - ZAR 350.00</option>
                                        <option value="500">Connector 500 - ZAR 500.00</option>
                                    </select>
                                    <button type="button" class="btn-remove">Remove</button>
                                </div>
                            </div>
                            <div class="package-controls">
                                <button type="button" id="addPackageBtn" class="btn-add">
                                    Add package
                                </button>
                                <small style="color: #aaa">You may add up to 3 packages in total.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Summary -->
                    <div style="margin-top: 30px">
                        <a href="#" id="toggleInvoiceLink" class="toggle-link hidden">View invoice</a>
                        <div id="invoiceSummary" class="invoice hidden">
                            <h3>Invoice</h3>
                            <ul id="invoiceItems"></ul>
                            <div class="total">
                                <span>Total</span><span id="invoiceTotal">ZAR 0.00</span>
                            </div>
                            <div id="invoicePaying" class="hidden"></div>
                        </div>
                        <button
                            id="proceedPay"
                            class="btn-proceed"
                            style="margin-top: 30px">
                            Proceed to Pay
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <div class="copyright">
                Copyright &copy; 2025 City of Tshwane. All rights reserved.
            </div>
        </footer>

        <!-- Notification Dialog -->
        <div id="notificationDialog" class="notification-dialog">
            <div class="notification-content">
                <span class="notification-close">&times;</span>
                <div class="notification-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="notification-message"></div>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                document.querySelector(".sidebar").classList.toggle("active");
            }

            // Close sidebar when clicking outside
            window.addEventListener("click", function(e) {
                const sidebar = document.querySelector(".sidebar");
                const toggle = document.querySelector(".menu-toggle");

                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove("active");
                }
            });
        </script>

        <script src="payment_gateway.js"></script>
    </body>

    </html>
<?php
}
