<?php
// payment_gateway.php - Handles form submission from payment_gateway.html

// Start session if needed for user data
session_start();

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate card number using Luhn algorithm
function luhn_check($number) {
    $number = preg_replace('/\D/', '', $number);
    $sum = 0;
    $flip = 0;
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $digit = (int)$number[$i];
        if ($flip) {
            $digit *= 2;
            if ($digit > 9) $digit -= 9;
        }
        $sum += $digit;
        $flip = !$flip;
    }
    return $sum % 10 === 0 && strlen($number) >= 12 && strlen($number) <= 19;
}

// Function to validate expiry date
function valid_expiry($expiry) {
    if (!preg_match('/^\d{2}\/\d{2}$/', $expiry)) return false;
    list($mm, $yy) = explode('/', $expiry);
    $mm = (int)$mm;
    $yy = (int)$yy;
    if ($mm < 1 || $mm > 12) return false;
    $now = new DateTime();
    $currentYear = (int)$now->format('y');
    $currentMonth = (int)$now->format('m');
    if ($yy < $currentYear) return false;
    if ($yy == $currentYear && $mm < $currentMonth) return false;
    return true;
}

// Initialize variables
$errors = [];
$success = false;
$totalAmount = 0;
$action = '';
$paymentMethod = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action
    $action = sanitize_input($_POST['action'] ?? '');
    if (empty($action)) {
        $errors[] = "Please select an action.";
    }

    // Calculate total amount
    if ($action === 'reload') {
        $reloadAmount = floatval($_POST['reloadAmount'] ?? 0);
        if ($reloadAmount < 11) {
            $errors[] = "Reload amount must be at least ZAR 11.00.";
        } else {
            $totalAmount = $reloadAmount;
        }
    } elseif ($action === 'purchase') {
        $products = $_POST['product'] ?? [];
        foreach ($products as $product) {
            $value = floatval($product);
            if ($value > 0) {
                $totalAmount += $value;
            }
        }
        if ($totalAmount <= 0) {
            $errors[] = "Please select at least one product.";
        }
    }

    // Get payment method
    $paymentMethod = sanitize_input($_POST['paymentMethod'] ?? '');
    if (empty($paymentMethod)) {
        $errors[] = "Please select a payment method.";
    }

    // Validate payment details
    if ($paymentMethod === 'card') {
        $cardNumber = sanitize_input($_POST['cardNumber'] ?? '');
        $cardName = sanitize_input($_POST['cardName'] ?? '');
        $cardExpiry = sanitize_input($_POST['cardExpiry'] ?? '');
        $cardCvc = sanitize_input($_POST['cardCvc'] ?? '');

        if (empty($cardNumber) || !luhn_check($cardNumber)) {
            $errors[] = "Invalid card number.";
        }
        if (empty($cardName)) {
            $errors[] = "Cardholder name is required.";
        }
        if (empty($cardExpiry) || !valid_expiry($cardExpiry)) {
            $errors[] = "Invalid expiry date.";
        }
        if (empty($cardCvc) || !preg_match('/^\d{3,4}$/', $cardCvc)) {
            $errors[] = "Invalid CVC.";
        }
    } elseif ($paymentMethod === 'eft') {
        $bankName = sanitize_input($_POST['bankName'] ?? '');
        $accountNumber = sanitize_input($_POST['accountNumber'] ?? '');
        $accountName = sanitize_input($_POST['accountName'] ?? '');
        $branchCode = sanitize_input($_POST['branchCode'] ?? '');

        if (empty($bankName)) {
            $errors[] = "Bank name is required.";
        }
        if (empty($accountNumber) || !preg_match('/^\d{6,14}$/', $accountNumber)) {
            $errors[] = "Invalid account number.";
        }
        if (empty($accountName)) {
            $errors[] = "Account holder name is required.";
        }
        if (!empty($branchCode) && !preg_match('/^\d{3,6}$/', $branchCode)) {
            $errors[] = "Invalid branch code.";
        }
    }

    // If no errors, process payment (simulate)
    if (empty($errors)) {
        // Here you would integrate with actual payment gateway
        // For now, simulate success
        $success = true;

        // Store transaction details in session or database
        $_SESSION['payment_success'] = true;
        $_SESSION['payment_amount'] = $totalAmount;
        $_SESSION['payment_method'] = $paymentMethod;
        $_SESSION['payment_action'] = $action;

        // Redirect to success page
        header("Location: Credit Wallet.php?status=success");
        exit();
    }
}

// If not POST or errors, display form again (but since it's PHP, perhaps redirect back)
if (!$success) {
    // Redirect back with errors
    $_SESSION['payment_errors'] = $errors;
    header("Location: payment_gateway.html");
    exit();
}
?>
