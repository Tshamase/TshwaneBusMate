<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TshwaneBusMate - Payment Gateway</title>
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
            position: relative;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
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

        .back-button {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-left: 20px;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        main {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .content-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            flex: 1;
            min-width: 0;
        }

        .content-container h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: #27ae60;
            position: relative;
            padding-bottom: 15px;
        }

        .content-container h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, #27ae60, #FFD700);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
        }

        select, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #27ae60;
            box-shadow: 0 0 10px rgba(39, 174, 96, 0.3);
        }

        select option {
            background: #1a2f3a;
            color: #fff;
        }

        .package-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }

        .btn-add, .btn-remove {
            padding: 8px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-remove {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-add:hover, .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .invoice {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .invoice h3 {
            color: #FFD700;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .invoice ul {
            list-style: none;
            margin-bottom: 20px;
        }

        .invoice li {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .invoice .total {
            font-weight: 700;
            font-size: 1.3rem;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            color: #27ae60;
        }

        .payment-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 350px;
            flex-shrink: 0;
        }

        .payment-container h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #27ae60;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .payment-method input[type="radio"] {
            margin: 0;
        }

        .payment-details {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .payment-details.active {
            display: block;
        }

        .btn-proceed {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 30px;
        }

        .btn-proceed:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .btn-proceed:disabled {
            background: rgba(255, 255, 255, 0.1);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        footer {
            background: linear-gradient(135deg, #000, #0d1b24);
            padding: 20px 0 20px;
            text-align: center;
            margin-top: auto;
        }

        .copyright {
            margin-top: 0px;
            padding-top: 0px;
            /*border-top: 1px solid rgba(255, 255, 255, 0.1);*/
            color: #aaa;
        }

        @media (max-width: 768px) {
            main {
                flex-direction: column;
                padding: 1rem;
            }
            
            .payment-container {
                width: 100%;
                margin-top: 2rem;
            }
            
            .header-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .content-container, .payment-container {
                padding: 25px;
            }
        }

        .hidden {
            display: none !important;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
        }

        .toggle-link {
            color: #27ae60;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .toggle-link:hover {
            color: #2ecc71;
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
        <button class="back-button" onclick="window.location.href='Credit Wallet.php'">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>
  </header>

  <main>
    <!-- Left side: form for choosing action -->
    <div class="content-container">
    <h2>Payment Gateway</h2>

    <form id="invoicePayment" method="post">
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
          placeholder="Enter amount"
        />
      </div>
      </div>

      <!-- Purchase fields -->
      <div id="purchaseFields" class="hidden">
      <div class="form-group">
        <label>Select product(s)</label>
        <div id="packageList">
          <div class="packageItem">
            <select name="product[]" required>
              <option value="" disabled selected>-- Choose product --</option>
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
          <button type="button" id="addPackageBtn" class="btn-add">Add package</button>
          <small style="color: #aaa">You may add up to 3 packages</small>
        </div>
      </div>
      </div>

    <!-- Invoice Summary -->
      <div style="margin-top: 30px;">
      <a href="#" id="toggleInvoiceLink" class="toggle-link hidden">View invoice</a>
      <div id="invoiceSummary" class="invoice hidden">
        <h3>Invoice</h3>
        <ul id="invoiceItems"></ul>
        <div class="total">
        <span>Total</span><span id="invoiceTotal">ZAR 0.00</span>
        </div>
        <div id="invoicePaying" class="hidden"></div>
      </div>
      </div>
    </form>
    </div>

    <!-- Right side: payment method chooser -->
    <div class="payment-container">
    <h2>Payment Options</h2>
    <p style="margin-bottom: 20px; color: #aaa;">
      Choose a payment method and enter details to proceed.
    </p>

    <div class="payment-methods">
      <div class="payment-method">
        <input type="radio" name="paymentMethod" value="card" id="cardRadio">
        <label for="cardRadio">Card / Debit</label>
      </div>
      <div class="payment-method">
        <input type="radio" name="paymentMethod" value="eft" id="eftRadio">
        <label for="eftRadio">Bank Transfer (EFT)</label>
      </div>
    </div>

    <!-- Card details -->
    <div id="pm-card" class="payment-details">
      <div class="form-group">
        <label>Card number</label>
        <input id="cardNumber" type="text" inputmode="numeric" maxlength="19" placeholder="1234 5678 9012 3456">
        <div class="error-message" id="cardNumberError"></div>
      </div>
      <div class="form-group">
        <label>Name on card</label>
        <input id="cardName" type="text" placeholder="Cardholder name">
        <div class="error-message" id="cardNameError"></div>
      </div>
      <div style="display: flex; gap: 10px;">
        <div class="form-group" style="flex: 1;">
          <label>Expiry (MM/YY)</label>
          <input id="cardExpiry" type="text" maxlength="5" placeholder="MM/YY">
          <div class="error-message" id="cardExpiryError"></div>
        </div>
        <div class="form-group" style="width: 100px;">
          <label>CVC</label>
          <input id="cardCvc" type="text" maxlength="4" placeholder="123">
          <div class="error-message" id="cardCvcError"></div>
        </div>
      </div>
    </div>

    <!-- EFT details -->
    <div id="pm-eft" class="payment-details">
      <div class="form-group">
        <label>Bank</label>
        <select id="bankName" name="bank-name">
          <option value="" disabled selected>-- Choose option --</option>
          <option value="sd-bank">Standard Bank</option>
          <option value="ab-bank">Absa</option>
          <option value="fnb-bank">FNB</option>
          <option value="nd-bank">Nedbank</option>
          <option value="cp-bank">Capitec</option>
        </select>
        <div class="error-message" id="bankNameError"></div>
      </div>
      <div class="form-group">
        <label>Account number</label>
        <input id="accountNumber" type="text" maxlength="11" placeholder="1234567890">
        <div class="error-message" id="accountNumberError"></div>
      </div>
      <div class="form-group">
        <label>Account holder</label>
        <input id="accountName" type="text" maxlength="25" placeholder="Account holder name">
        <div class="error-message" id="accountNameError"></div>
      </div>
      <div class="form-group">
        <label>Branch code</label>
        <input id="branchCode" type="text" maxlength="6" placeholder="Branch code">
        <div class="error-message" id="branchCodeError"></div>
      </div>
      <small style="color: #aaa">You will be shown EFT instructions after submitting.</small>
    </div>

    <button id="proceedPay" class="btn-proceed">Proceed to Pay</button>
    </div>
  </main>

  <!-- Footer -->
  <footer>
      <div class="copyright">Copyright &copy; 2025 City of Tshwane. All rights reserved.</div>
  </footer>

<script>
  (function () {
    // Helper: format number as ZAR currency string
    function formatZAR(value) {
      return "ZAR " + value.toFixed(2);
    }

    // Simple field error helpers for feedback
    function showFieldError(el, msg) {
      if (!el) return;
      el.style.borderColor = '#e74c3c';
      var errorDiv = el.parentNode.querySelector('.error-message');
      if (errorDiv) {
        errorDiv.textContent = msg;
        errorDiv.style.display = 'block';
      }
    }
    
    function clearFieldError(el) {
      if (!el) return;
      el.style.borderColor = '';
      var errorDiv = el.parentNode.querySelector('.error-message');
      if (errorDiv) {
        errorDiv.style.display = 'none';
      }
    }

    // Luhn check for card numbers
    function luhnCheck(num) {
      var s = num.replace(/\D/g, '');
      var sum = 0, flip = 0;
      for (var i = s.length - 1; i >= 0; i--) {
        var d = parseInt(s.charAt(i), 10);
        if (flip) {
          d = d * 2;
          if (d > 9) d -= 9;
        }
        sum += d;
        flip = flip ^ 1;
      }
      return (sum % 10) === 0 && s.length >= 12 && s.length <= 19;
    }

    // Expiry MM/YY validation
    function validExpiry(v) {
      if (!/^\d{2}\/\d{2}$/.test(v)) return false;
      var parts = v.split('/');
      var mm = parseInt(parts[0], 10);
      var yy = parseInt(parts[1], 10);
      if (mm < 1 || mm > 12) return false;
      var now = new Date();
      var currentYear = now.getFullYear() % 100;
      var currentMonth = now.getMonth() + 1;
      if (yy < currentYear) return false;
      if (yy === currentYear && mm < currentMonth) return false;
      return true;
    }

    // Elements we will use
    var actionSelect = document.getElementById('actionSelect');
    var reloadFields = document.getElementById('reloadFields');
    var reloadAmount = document.getElementById('reloadAmount');
    var purchaseFields = document.getElementById('purchaseFields');
    var packageList = document.getElementById('packageList');
    var addPackageBtn = document.getElementById('addPackageBtn');
    var toggleInvoiceLink = document.getElementById('toggleInvoiceLink');
    var invoiceSummary = document.getElementById('invoiceSummary');
    var invoiceItems = document.getElementById('invoiceItems');
    var invoiceTotal = document.getElementById('invoiceTotal');
    var invoicePaying = document.getElementById('invoicePaying');

    var paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
    var pmCard = document.getElementById('pm-card');
    var pmEft = document.getElementById('pm-eft');
    var proceedPay = document.getElementById('proceedPay');

    // --- Show/Hide fields when user picks action ---
    actionSelect.addEventListener('change', function () {
      var val = actionSelect.value;
      if (val === 'reload') {
        reloadFields.classList.remove('hidden');
        purchaseFields.classList.add('hidden');
      } else if (val === 'purchase') {
        reloadFields.classList.add('hidden');
        purchaseFields.classList.remove('hidden');
      } else {
        reloadFields.classList.add('hidden');
        purchaseFields.classList.add('hidden');
      }
      updateInvoice();
    });

    // --- Add / remove package items (max 3) ---
    function updateRemoveButtons() {
      var items = packageList.querySelectorAll('.packageItem');
      items.forEach(function (it, idx) {
        var btn = it.querySelector('.btn-remove');
        btn.style.display = (idx === 0) ? 'none' : '';
      });
      addPackageBtn.disabled = items.length >= 3;
      
      // Show/hide buttons based on package count
      var packageCount = items.length;
      if (packageCount === 1) {
        addPackageBtn.style.display = 'inline-block';
      } else if (packageCount >= 3) {
        addPackageBtn.style.display = 'none';
      } else {
        addPackageBtn.style.display = 'inline-block';
      }
    }

    addPackageBtn.addEventListener('click', function () {
      var currentCount = packageList.querySelectorAll('.packageItem').length;
      if (currentCount >= 3) return;
      var first = packageList.querySelector('.packageItem');
      var clone = first.cloneNode(true);
      var sel = clone.querySelector('select');
      sel.selectedIndex = 0;
      var removeBtn = clone.querySelector('.btn-remove');
      removeBtn.style.display = '';
      removeBtn.addEventListener('click', function () {
        clone.remove();
        updateRemoveButtons();
        attachSelectListeners();
        updateInvoice();
      });
      packageList.appendChild(clone);
      updateRemoveButtons();
      attachSelectListeners();
      updateInvoice();
    });

    // Attach change listener to all product selects
    function attachSelectListeners() {
      var selects = packageList.querySelectorAll('select[name="product[]"]');
      selects.forEach(function (s) {
        s.onchange = function () {
          clearFieldError(s);
          updateInvoice();
        };
      });
    }
    
    (function initPackageControls() {
      var firstRemove = packageList.querySelector('.btn-remove');
      if (firstRemove) firstRemove.style.display = 'none';
      attachSelectListeners();
      updateRemoveButtons();
    })();

    // --- Invoice Summary ---
    function clearInvoiceList() {
      while (invoiceItems.firstChild) invoiceItems.removeChild(invoiceItems.firstChild);
    }

    function appendInvoiceItem(label, amount) {
      var li = document.createElement('li');
      li.innerHTML = '<span>' + label + '</span><span>' + formatZAR(amount) + '</span>';
      invoiceItems.appendChild(li);
    }

   function updateInvoice() {
    clearInvoiceList();
    
    var baseTotal = 0;
    var totalWithCharges = 0;
    var action = actionSelect.value;
    var totalDiscount = 0;
    var credits = 0;

    if (action === 'reload') {
        var amt = Math.max(parseFloat(reloadAmount.value) || 0, 0);
        if (amt > 0) {
            var charges = (amt <= 60) ? 1.50 : amt * 0.025;
            var creditedAmount = amt - charges;
            
            appendInvoiceItem('Reload credits', creditedAmount);
            appendInvoiceItem('Charges applied', charges);
            baseTotal = creditedAmount;
            totalWithCharges = amt;
            credits = creditedAmount;
        }
    } else if (action === 'purchase') {
        var selects = packageList.querySelectorAll('select[name="product[]"]');
        var hasValidSelection = false;
        
        selects.forEach(function (s, i) {
            var val = Math.max(parseFloat(s.value) || 0, 0);
            if (val > 0) {
                hasValidSelection = true;
                var label = s.options[s.selectedIndex] ? 
                            s.options[s.selectedIndex].text.replace(/\s+-\s+ZAR.*$/, '') : 
                            'Product ' + (i + 1);
                
                // Calculate discount and credits for this package
                var packageDiscount = calculatePackageDiscount(val);
                var packageCredits = calculatePackageCredits(val);
                totalDiscount += packageDiscount;
                credits += packageCredits;

                appendInvoiceItem(label, val);
                baseTotal += val;
            }
        });

        if (hasValidSelection) {
            var purchaseCharges = (baseTotal <= 60) ? 1.50 : baseTotal * 0.025;
            totalWithCharges = baseTotal - totalDiscount + purchaseCharges;
            
            appendInvoiceItem('Credits earning', credits);
            appendInvoiceItem('Charges applied', purchaseCharges);
        }
    }
    
    var totalValue = baseTotal - totalDiscount;
    
    if (totalValue > 0 || (action === 'reload' && parseFloat(reloadAmount.value) > 0)) {
        toggleInvoiceLink.classList.remove('hidden');
        invoiceSummary.classList.remove('hidden');
        invoiceTotal.textContent = formatZAR(totalWithCharges);
    } else {
        toggleInvoiceLink.classList.add('hidden');
        invoiceSummary.classList.add('hidden');
    }
    
    invoicePaying.classList.add('hidden');
    invoicePaying.textContent = '';
}

// Function to calculate discount based on package value
function calculatePackageDiscount(packageValue) {
    var discountRate = 0;
    
    if (packageValue === 20) {
        discountRate = 0; // 0% discount 
    } else if (packageValue === 60) {
        discountRate = 0; // 0% discount 
    } else if (packageValue === 80) {
        discountRate = 0.17; // 17% discount
    } else if (packageValue === 100) {
        discountRate = 0.18; // 18% discount
    } else if (packageValue === 150) {
        discountRate = 0.19; // 19% discount
    } else if (packageValue === 200) {
        discountRate = 0.20; // 20% discount
    } else if (packageValue === 350) {
        discountRate = 0.21; // 21% discount
    } else if (packageValue === 500) {
        discountRate = 0.22; // 22% discount
    }
    
    return packageValue * discountRate;
}

// Function to calculate credits based on package value
function calculatePackageCredits(packageValue) {
    if (packageValue === 20) {
        return 20;
    } else if (packageValue === 60) {
        return 60;
    } else if (packageValue === 80) {
        return 96;
    } else if (packageValue === 100) {
        return 122;
    } else if (packageValue === 150) {
        return 185;
    } else if (packageValue === 200) {
        return 250;
    } else if (packageValue === 350) {
        return 445;
    } else if (packageValue === 500) {
        return 640;
    }
    return 0;
}


    // Toggle invoice visibility
    toggleInvoiceLink.addEventListener('click', function (e) {
      e.preventDefault();
      invoiceSummary.classList.toggle('hidden');
    });

    // Update invoice on reload amount change
    reloadAmount.addEventListener('input', function () {
      clearFieldError(reloadAmount);
      updateInvoice();
    });

    // Payment method radio handling
    paymentRadios.forEach(function (r) {
      r.addEventListener('change', function () {
        if (r.value === 'card' && r.checked) {
          pmCard.classList.add('active');
          pmEft.classList.remove('active');
        } else if (r.value === 'eft' && r.checked) {
          pmCard.classList.remove('active');
          pmEft.classList.add('active');
        }
      });
    });

    // Clear errors on focus
    ['cardNumber','cardName','cardExpiry','cardCvc','bankName','accountNumber','accountName','branchCode'].forEach(function(id){
      var el = document.getElementById(id);
      if (el) el.addEventListener('focus', function(){ clearFieldError(el); });
    });

    // Validation and submit
    proceedPay.addEventListener('click', function () {
      var firstInvalid = null;
      function markInvalid(el, msg) { 
        if (!firstInvalid) firstInvalid = el; 
        showFieldError(el, msg); 
      }

      var action = actionSelect.value;
      if (!action) {
        alert('Please select an action: reload or purchase.');
        actionSelect.focus();
        return;
      }

      var totalValue = 0;
      var selects = packageList.querySelectorAll('select[name="product[]"]');
      if (action === 'reload') {
        var amt = parseFloat(reloadAmount.value);
        totalValue = amt;
        if (!reloadAmount.value || isNaN(amt) || amt < 11) {
          markInvalid(reloadAmount, 'Enter a valid amount (minimum ZAR 11.50).');
        }
      } else if (action === 'purchase') {
        var any = false;
        selects.forEach(function (s) {
          var val = parseFloat(s.value) || 0;
          if (val > 0) {
            any = true;
            totalValue += val;
          } else {
            markInvalid(s, 'Please choose a product.');
          }
        });
        if (!any) {
          alert('Please choose at least one product.');
          return;
        }
      }

      if (totalValue <= 0) {
        alert('Your total is ZAR 0.00. Please enter an amount or choose product(s).');
        return;
      }

      var chosenPayment = null;
      paymentRadios.forEach(function (r) {
        if (r.checked) chosenPayment = r.value;
      });
      if (!chosenPayment) {
        alert('Please choose a payment method (Card or EFT).');
        return;
      }

      // Validate card details
      if (chosenPayment === 'card') {
        var cnEl = document.getElementById('cardNumber');
        var cn = cnEl.value.trim();
        var cnmEl = document.getElementById('cardName');
        var cnm = cnmEl.value.trim();
        var ceEl = document.getElementById('cardExpiry');
        var ce = ceEl.value.trim();
        var ccEl = document.getElementById('cardCvc');
        var cc = ccEl.value.trim();

        if (!cn) markInvalid(cnEl, 'Card number required.');
        else if (!luhnCheck(cn)) markInvalid(cnEl, 'Invalid card number.');

        if (!cnm) markInvalid(cnmEl, 'Cardholder name required.');

        if (!ce) markInvalid(ceEl, 'Expiry required (MM/YY).');
        else if (!validExpiry(ce)) markInvalid(ceEl, 'Invalid or expired date.');

        if (!cc) markInvalid(ccEl, 'CVC required.');
        else if (!/^\d{3,4}$/.test(cc)) markInvalid(ccEl, 'CVC must be 3-4 digits.');

      } else { // EFT validations
        var bnEl = document.getElementById('bankName');
        var anEl = document.getElementById('accountNumber');
        var ahEl = document.getElementById('accountName');
        var bcEl = document.getElementById('branchCode');

        var bn = bnEl.value.trim();
        var an = anEl.value.trim();
        var ah = ahEl.value.trim();
        var bc = bcEl.value.trim();

        if (!bn || bn.length < 2) markInvalid(bnEl, 'Enter bank name.');
        if (!an || !/^\d{6,14}$/.test(an)) markInvalid(anEl, 'Account number should be 6-14 digits.');
        if (!ah || ah.length < 2) markInvalid(ahEl, 'Enter account holder name.');
        if (bc && !/^\d{3,6}$/.test(bc)) markInvalid(bcEl, 'Branch code should be 3-6 digits.');
      }

      if (firstInvalid) {
        firstInvalid.focus();
        return;
      }

      // Success handling
      if (chosenPayment === 'card') {
        alert('Payment successful!\n' + formatZAR(totalValue) + ' charged by Card.');
        setTimeout(function () {
          window.location.href = 'Credit Wallet.php';
        }, 500);
      } else {
        alert('EFT payment successful.');
        setTimeout(function () {
          window.location.href = 'Credit Wallet.php';
        }, 500);
      }
    });

    // FIXED: Ensure invoice updates on page load
    document.addEventListener('DOMContentLoaded', function() {
      updateInvoice();
    });
  })();
</script>
  </body>
</html>
