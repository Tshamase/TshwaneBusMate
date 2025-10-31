function toggleFields() {
    const role = document.getElementById('role').value;
    const commonFields = document.getElementById('common-fields');
    const commuterField = document.getElementById('commuter-field');
    const driverField = document.getElementById('driver-field');
    const adminField = document.getElementById('admin-field');
    const passwordField = document.getElementById('password-field');

    if (role) {
        commonFields.style.display = 'block';
        commuterField.style.display = role === 'commuter' ? 'block' : 'none';
        driverField.style.display = role === 'driver' ? 'block' : 'none';
        adminField.style.display = role === 'admin' ? 'block' : 'none';
        passwordField.style.display = 'block';
    } else {
        commonFields.style.display = 'none';
        commuterField.style.display = 'none';
        driverField.style.display = 'none';
        adminField.style.display = 'none';
        passwordField.style.display = 'none';
    }
}

function validateForm() {
    let isValid = true;
    const errors = [];

    const role = document.getElementById('role').value;
    const fullname = document.getElementById('fullname').value.trim();
    const password = document.getElementById('password').value;
    const buscard = document.getElementById('buscard').value.trim();
    const driverid = document.getElementById('driverid').value.trim();
    const adminid = document.getElementById('adminid').value.trim();

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.remove());

    if (!role) {
        errors.push("Role is required.");
        isValid = false;
    }

    if (!fullname) {
        errors.push("Full name is required.");
        isValid = false;
    } else if (!/^[a-zA-Z ]+$/.test(fullname)) {
        errors.push("Full name can only contain letters and spaces.");
        isValid = false;
    } else if (fullname.length < 2) {
        errors.push("Full name must be at least 2 characters long.");
        isValid = false;
    }

    if (!password) {
        errors.push("Password is required.");
        isValid = false;
    } else if (password.length < 6) {
        errors.push("Password must be at least 6 characters long.");
        isValid = false;
    }

    // Role-specific validations
    if (role === 'commuter') {
        if (!buscard) {
            errors.push("Bus card number is required for commuters.");
            isValid = false;
        } else if (!/^[0-9]{16}$/.test(buscard)) {
            errors.push("Bus card number must be exactly 16 digits.");
            isValid = false;
        }
    } else if (role === 'driver') {
        if (!driverid) {
            errors.push("Driver ID is required for drivers.");
            isValid = false;
        } else if (!/^DRV[0-9]{3}$/.test(driverid)) {
            errors.push("Driver ID must be in format DRV followed by 3 digits (e.g., DRV001).");
            isValid = false;
        }
    } else if (role === 'admin') {
        if (!adminid) {
            errors.push("Admin ID is required for admins.");
            isValid = false;
        } else if (!/^ADM[0-9]{3}$/.test(adminid)) {
            errors.push("Admin ID must be in format ADM followed by 3 digits (e.g., ADM001).");
            isValid = false;
        }
    }

    if (!isValid) {
        // Display errors
        const form = document.querySelector('form');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.marginBottom = '10px';
        errorDiv.innerHTML = '<ul>' + errors.map(err => '<li>' + err + '</li>').join('') + '</ul>';
        form.insertBefore(errorDiv, form.firstChild);
    }

    return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('role').addEventListener('change', toggleFields);

    // Add form validation on submit
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
});
