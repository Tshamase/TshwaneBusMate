// ============== Payment Page JS ==============

// Auto-executed function
(function () {
  // Helper: format number as ZAR currency string
  function formatZAR(value) {
    return "ZAR " + value.toFixed(2);
  }

  // Show error message for a field
  function showFieldError(el, msg) {
    if (!el) return;
    el.style.borderColor = "#e74c3c";
    var errorDiv = el.parentNode.querySelector(".error-message");
    if (errorDiv) {
      errorDiv.textContent = msg;
      errorDiv.style.display = "block";
    }
  }

  // Clear error message for a field
  function clearFieldError(el) {
    if (!el) return;
    el.style.borderColor = "";
    var errorDiv = el.parentNode.querySelector(".error-message");
    if (errorDiv) {
      errorDiv.style.display = "none";
    }
  }

  // --- Element references ---
  var actionSelect = document.getElementById("actionSelect");
  var reloadFields = document.getElementById("reloadFields");
  var reloadAmount = document.getElementById("reloadAmount");
  var purchaseFields = document.getElementById("purchaseFields");
  var packageList = document.getElementById("packageList");
  var addPackageBtn = document.getElementById("addPackageBtn");
  var toggleInvoiceLink = document.getElementById("toggleInvoiceLink");
  var invoiceSummary = document.getElementById("invoiceSummary");
  var invoiceItems = document.getElementById("invoiceItems");
  var invoiceTotal = document.getElementById("invoiceTotal");
  var invoicePaying = document.getElementById("invoicePaying");
  var proceedPay = document.getElementById("proceedPay");

  // --- Show/Hide fields when user picks action ---
  actionSelect.addEventListener("change", function () {
    var val = actionSelect.value;
    if (val === "reload") {
      reloadFields.classList.remove("hidden");
      purchaseFields.classList.add("hidden");
    } else if (val === "purchase") {
      reloadFields.classList.add("hidden");
      purchaseFields.classList.remove("hidden");
    } else {
      reloadFields.classList.add("hidden");
      purchaseFields.classList.add("hidden");
    }
    updateInvoice();
  });

  // --- Add / remove package items (max 3) ---
  function updateRemoveButtons() {
    var items = packageList.querySelectorAll(".packageItem");
    items.forEach(function (it, idx) {
      var btn = it.querySelector(".btn-remove");
      btn.style.display = idx === 0 ? "none" : "";
    });
    addPackageBtn.disabled = items.length >= 3;

    // Show/hide buttons based on package count
    var packageCount = items.length;
    if (packageCount === 1) {
      addPackageBtn.style.display = "inline-block";
    } else if (packageCount >= 3) {
      addPackageBtn.style.display = "none";
    } else {
      addPackageBtn.style.display = "inline-block";
    }
  }

  // Add package button handler (duplicates first package item)
  addPackageBtn.addEventListener("click", function () {
    var currentCount = packageList.querySelectorAll(".packageItem").length;
    if (currentCount >= 3) return;
    var first = packageList.querySelector(".packageItem");
    var clone = first.cloneNode(true);
    var sel = clone.querySelector("select");
    sel.selectedIndex = 0;
    var removeBtn = clone.querySelector(".btn-remove");
    removeBtn.style.display = "";
    removeBtn.addEventListener("click", function () {
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

  // Attach change listener to all product selects to update invoice
  function attachSelectListeners() {
    var selects = packageList.querySelectorAll('select[name="product[]"]');
    selects.forEach(function (s) {
      s.addEventListener("change", function () {
        clearFieldError(s);
        updateInvoice();
      });
    });
  }

  // Initialize package controls on page load
  (function initPackageControls() {
    var firstRemove = packageList.querySelector(".btn-remove");
    if (firstRemove) firstRemove.style.display = "none";
    attachSelectListeners();
    updateRemoveButtons();
  })();

  // --- Invoice Summary ---
  function clearInvoiceList() {
    while (invoiceItems.firstChild)
      invoiceItems.removeChild(invoiceItems.firstChild);
  }

  // Append an item to the invoice list with label and amount
  function appendInvoiceItem(label, amount) {
    var li = document.createElement("li");
    li.innerHTML =
      "<span>" + label + "</span><span>" + formatZAR(amount) + "</span>";
    invoiceItems.appendChild(li);
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
      discountRate = 0.2; // 20% discount
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

  // Update invoice based on current selections and amounts
  function updateInvoice() {
    clearInvoiceList();

    var baseTotal = 0;
    var totalWithCharges = 0;
    var action = actionSelect.value;
    var totalDiscount = 0;
    var credits = 0;

    // Check action and performs calculations accordingly
    if (action === "reload") {
      var amt = Math.max(parseFloat(reloadAmount.value) || 0, 0);
      if (amt > 0) {
        var charges = amt <= 60 ? 1.5 : amt * 0.025;
        var creditedAmount = amt - charges;

        appendInvoiceItem("Reload credits", creditedAmount);
        appendInvoiceItem("Charges applied", charges);
        baseTotal = creditedAmount;
        totalWithCharges = amt;
        credits = creditedAmount;
      }
    } else if (action === "purchase") {
      var selects = packageList.querySelectorAll('select[name="product[]"]');
      var hasValidSelection = false;

      // Process each selected package and calculate totals
      selects.forEach(function (s, i) {
        var val = parseFloat(s.value) || 0;
        if (val > 0) {
          hasValidSelection = true;
          var label = s.options[s.selectedIndex]
            ? s.options[s.selectedIndex].text.replace(/\s+-\s+ZAR.*$/, "")
            : "Product " + (i + 1);

          // Calculate discount and credits for this package
          var packageDiscount = calculatePackageDiscount(val);
          var packageCredits = calculatePackageCredits(val);
          totalDiscount += packageDiscount;
          credits += packageCredits;

          appendInvoiceItem(label, val);
          baseTotal += val;
        }
      });

      // If at least one valid package selected, calculate charges and final total
      if (hasValidSelection) {
        // Charge calculation for purchases matches reload logic; minimum reload amount is enforced in validation above.
        var purchaseCharges = baseTotal <= 60 ? 1.5 : baseTotal * 0.025;
        totalWithCharges = baseTotal - totalDiscount + purchaseCharges;

        appendInvoiceItem("Credits earning", credits);
        if (totalDiscount > 0) {
          appendInvoiceItem("Discount applied", -totalDiscount);
        }
        appendInvoiceItem("Charges applied", purchaseCharges);
      }
    }

    // totalWithoutCharges: base total minus discounts, before charges are applied
    var totalWithoutCharges = baseTotal - totalDiscount;
    // totalWithCharges: final amount including charges (displayed to user)
    var totalValue = baseTotal - totalDiscount;

    if (
      totalValue > 0 ||
      (action === "reload" && parseFloat(reloadAmount.value) > 0)
    ) {
      toggleInvoiceLink.classList.remove("hidden");
      invoiceSummary.classList.remove("hidden");
      invoiceTotal.textContent = formatZAR(totalWithCharges);
    } else {
      toggleInvoiceLink.classList.add("hidden");
      invoiceSummary.classList.add("hidden");
      invoicePaying.classList.add("hidden");
      invoicePaying.textContent = "";
    }
  }

  // Toggle invoice visibility
  toggleInvoiceLink.addEventListener("click", function (e) {
    e.preventDefault();
    invoiceSummary.classList.toggle("hidden");
  });

  // Update invoice on reload amount change
  reloadAmount.addEventListener("input", function () {
    clearFieldError(reloadAmount);
    updateInvoice();
  });

  // Validation and submit
  proceedPay.addEventListener("click", function () {
    var firstInvalid = null;
    function markInvalid(el, msg) {
      if (!firstInvalid) firstInvalid = el;
      showFieldError(el, msg);
    }
    // Clear all previous errors
    var action = actionSelect.value;
    if (!action) {
      alert("Please select an action: reload or purchase.");
      actionSelect.focus();
      return;
    }
    // Validate action-specific fields
    var totalValue = 0;
    var selects = packageList.querySelectorAll('select[name="product[]"]');
    if (action === "reload") {
      var amt = parseFloat(reloadAmount.value);
      totalValue = amt;
      if (!reloadAmount.value || isNaN(amt) || amt < 11) {
        markInvalid(reloadAmount, "Enter a valid amount (minimum ZAR 11).");
      }
    } else if (action === "purchase") {
      var any = false;
      selects.forEach(function (s) {
        var val = parseFloat(s.value) || 0;
        if (val > 0) {
          any = true;
          totalValue += val;
        } else {
          markInvalid(s, "Please choose a product.");
        }
      });
      if (!any) {
        alert("Please choose at least one product.");
        return;
      }
    }

    // Final total check
    if (totalValue <= 0) {
      alert(
        "Your total is ZAR 0.00. Please enter an amount or choose product(s)."
      );
      return;
    }

    // Focus on first invalid field if any
    if (firstInvalid) {
      firstInvalid.focus();
    }
  });

  // Initialize invoice on page load
  document.addEventListener("DOMContentLoaded", function () {
    updateInvoice();
  });
})();
