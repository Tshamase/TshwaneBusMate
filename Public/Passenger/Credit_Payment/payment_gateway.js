(function () {
  // Formater for number as ZAR currency
  function formatZAR(value) {
    return "ZAR " + value.toFixed(2);
  }

  // Simple field error for feedback
  function showFieldError(el, msg) {
    if (!el) return;
    el.style.borderColor = "#e74c3c";
    var errorDiv = el.parentNode.querySelector(".error-message");
    if (errorDiv) {
      errorDiv.textContent = msg;
      errorDiv.style.display = "block";
    }
  }

  function clearFieldError(el) {
    if (!el) return;
    el.style.borderColor = "";
    var errorDiv = el.parentNode.querySelector(".error-message");
    if (errorDiv) {
      errorDiv.style.display = "none";
    }
  }

  // Elements we will use
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
      btn.style.display = "inline-block";
    });
    addPackageBtn.disabled = items.length >= 3;

    var packageCount = items.length;
    if (packageCount === 1) {
      addPackageBtn.style.display = "inline-block";
    } else if (packageCount >= 3) {
      addPackageBtn.style.display = "none";
    } else {
      addPackageBtn.style.display = "inline-block";
    }
  }

  addPackageBtn.addEventListener("click", function () {
    var currentCount = packageList.querySelectorAll(".packageItem").length;
    if (currentCount >= 3) return;
    var first = packageList.querySelector(".packageItem");
    var clone = first.cloneNode(true);
    var sel = clone.querySelector("select");
    sel.selectedIndex = 0;
    var removeBtn = clone.querySelector(".btn-remove");
    removeBtn.style.display = "";
    removeBtn.addEventListener("click", handleRemoveClick);
    packageList.appendChild(clone);
    updateRemoveButtons();
    attachSelectListeners();
    updateInvoice();
  });

  function handleRemoveClick(e) {
    var items = packageList.querySelectorAll(".packageItem");
    if (items.length === 1) {
      // Reset selection and action
      items[0].querySelector("select").selectedIndex = 0;
      actionSelect.value = "";
      actionSelect.dispatchEvent(new Event("change"));
    } else {
      e.target.closest(".packageItem").remove();
      // Reorder remaining items by reappending them to ensure DOM order
      var remainingItems = packageList.querySelectorAll(".packageItem");
      packageList.innerHTML = ""; // Clear the list
      remainingItems.forEach(function (item) {
        packageList.appendChild(item);
      });
      updateRemoveButtons();
      attachSelectListeners();
    }
    updateInvoice();
  }

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
    var firstRemove = packageList.querySelector(".btn-remove");
    if (firstRemove) {
      firstRemove.style.display = "inline-block";
      firstRemove.addEventListener("click", handleRemoveClick);
    }
    attachSelectListeners();
    updateRemoveButtons();
  })();

  // --- Invoice Summary ---
  function clearInvoiceList() {
    while (invoiceItems.firstChild)
      invoiceItems.removeChild(invoiceItems.firstChild);
  }

  function appendInvoiceItem(label, amount) {
    var li = document.createElement("li");
    li.innerHTML =
      "<span>" + label + "</span><span>" + formatZAR(amount) + "</span>";
    invoiceItems.appendChild(li);
  }

  function updateInvoice() {
    clearInvoiceList();

    var baseTotal = 0;
    var totalWithCharges = 0;
    var action = actionSelect.value;
    var totalDiscount = 0;
    var credits = 0;

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

      selects.forEach(function (s, i) {
        var val = Math.max(parseFloat(s.value) || 0, 0);
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

      if (hasValidSelection) {
        var purchaseCharges = baseTotal <= 60 ? 1.5 : baseTotal * 0.025;
        totalWithCharges = baseTotal - totalDiscount + purchaseCharges;

        appendInvoiceItem("Credits earning", credits);
        appendInvoiceItem("Charges applied", purchaseCharges);
      }
    }

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
    }

    invoicePaying.classList.add("hidden");
    invoicePaying.textContent = "";
  }

  // Calculates discount based on package value
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

  // Calculates credits based on package value
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
  toggleInvoiceLink.addEventListener("click", function (e) {
    e.preventDefault();
    invoiceSummary.classList.toggle("hidden");
  });

  // Update invoice on reload amount change
  reloadAmount.addEventListener("input", function () {
    clearFieldError(reloadAmount);
    updateInvoice();
  });

  // Notification dialouge
  function showNotification(message, type = "info") {
    var dialog = document.getElementById("notificationDialog");
    var icon = dialog.querySelector(".notification-icon");
    var messageEl = dialog.querySelector(".notification-message");

    // Reset classes
    icon.className = "notification-icon";
    icon.classList.add(type);

    // Set icon
    var iconMap = {
      success: "fas fa-check-circle",
      error: "fas fa-exclamation-triangle",
      warning: "fas fa-exclamation-circle",
      info: "fas fa-info-circle",
    };
    icon.innerHTML = '<i class="' + iconMap[type] + '"></i>';

    // Set message
    messageEl.textContent = message;

    // Show dialog
    dialog.classList.add("show");

    // Auto-hide after 5 seconds
    setTimeout(function () {
      dialog.classList.remove("show");
    }, 5000);
  }

  // Close notification on click
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("notification-close")) {
      var dialog = document.getElementById("notificationDialog");
      dialog.classList.remove("show");
    }
  });

  // Validation and submit
  proceedPay.addEventListener("click", function (e) {
    e.preventDefault();
    var firstInvalid = null;
    function markInvalid(el, msg) {
      if (!firstInvalid) firstInvalid = el;
      showFieldError(el, msg);
    }

    var action = actionSelect.value;
    if (!action) {
      showNotification(
        "Please select an action: reload or purchase.",
        "warning"
      );
      actionSelect.focus();
      return;
    }

    var totalValue = 0;
    var selects = packageList.querySelectorAll('select[name="product[]"]');
    if (action === "reload") {
      var amt = parseFloat(reloadAmount.value);
      totalValue = amt;
      if (!reloadAmount.value || isNaN(amt) || amt < 11) {
        markInvalid(reloadAmount, "Enter a valid amount (minimum ZAR 11.00).");
        showNotification(
          "Please enter a valid reload amount (minimum ZAR 11.00).",
          "error"
        );
        return;
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
        showNotification("Please choose at least one product.", "warning");
        return;
      }
    }

    if (totalValue <= 0) {
      showNotification(
        "Your total is ZAR 0.00. Please enter an amount or choose product(s).",
        "error"
      );
      return;
    }

    // Build confirmation message with selected options
    var confirmationMessage = "Please confirm your selection:\n\n";
    if (action === "reload") {
      confirmationMessage += "Action: Reload credits\n";
      confirmationMessage += "Amount: " + formatZAR(amt) + "\n";
    } else if (action === "purchase") {
      confirmationMessage += "Action: Purchase product(s)\n";
      selects.forEach(function (s, i) {
        var val = parseFloat(s.value) || 0;
        if (val > 0) {
          var label = s.options[s.selectedIndex]
            ? s.options[s.selectedIndex].text.replace(/\s+-\s+ZAR.*$/, "")
            : "Product " + (i + 1);
          confirmationMessage +=
            "Product " + (i + 1) + ": " + label + " - " + formatZAR(val) + "\n";
        }
      });
    }
    confirmationMessage +=
      "\nTotal: " +
      formatZAR(totalValue) +
      "\n\nDo you approve and want to proceed to pay?";

    // Show confirmation modal
    var modal = document.getElementById("confirmationModal");
    var messageEl = document.getElementById("confirmationMessage");
    messageEl.textContent = confirmationMessage;
    modal.classList.add("show");

    // Blur the main content
    document.getElementById("main-content").style.filter = "blur(5px)";

    // Handle modal buttons
    var confirmOk = document.getElementById("confirmOk");
    var confirmCancel = document.getElementById("confirmCancel");

    var handleConfirm = function () {
      modal.classList.remove("show");
      document.getElementById("main-content").style.filter = "";
      confirmOk.removeEventListener("click", handleConfirm);
      confirmCancel.removeEventListener("click", handleCancel);

      // Show processing notification
      showNotification("Processing your payment request...", "info");

      // Redirect to working.html instead of submitting form
      window.location.href = "working.html";
    };

    var handleCancel = function () {
      modal.classList.remove("show");
      document.getElementById("main-content").style.filter = "";
      confirmOk.removeEventListener("click", handleConfirm);
      confirmCancel.removeEventListener("click", handleCancel);
    };

    confirmOk.addEventListener("click", handleConfirm);
    confirmCancel.addEventListener("click", handleCancel);
  });

  // Display session errors on page load
  if (window.paymentErrors && window.paymentErrors.length > 0) {
    window.paymentErrors.forEach(function (error) {
      showNotification(error, "error");
    });
  }

  // FIXED: Ensure invoice updates on page load
  document.addEventListener("DOMContentLoaded", function () {
    updateInvoice();
  });
})();
