<?php
require 'db_payment.php';
require_once 'vendor/autoload.php';
require_once 'secrets.php';

// Start session for user data
session_start();

// Check if user is logged in - redirect to credit wallet if not
// Working progress........

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

    // If no errors, create Stripe checkout session
    if (empty($errors)) {
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://localhost/TshwaneBusMate/Public/Passenger/Credit_Payment';

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'zar',
                    'unit_amount' => $totalAmount * 100, // Amount in cents
                    'product_data' => [
                        'name' => $itemName,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.php',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.php',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
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
    </head>

    <body>

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
        <main>
            <div class="content-container">
                <h2>Payment Gateway</h2>

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

        <footer>
            <div class="copyright">
                Copyright &copy; 2025 City of Tshwane. All rights reserved.
            </div>
        </footer>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Confirm Payment</h3>
                </div>
                <div class="modal-body">
                    <pre id="confirmationMessage"></pre>
                </div>
                <div class="modal-footer">
                    <button id="confirmCancel" class="btn-cancel">Cancel</button>
                    <button id="confirmOk" class="btn-ok">Proceed</button>
                </div>
            </div>
        </div>

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
