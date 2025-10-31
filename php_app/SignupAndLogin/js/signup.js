//signup form validation
document.addEventListener('DOMContentLoaded', function() {
    //form elements
    const form = document.getElementById('signupForm');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const fullnameError = document.getElementById('fullnameError');
    const emailError = document.getElementById('emailError');

    //url error handling
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error') === 'validation') {
        const generalError = document.getElementById('generalError');
        if (urlParams.get('fullname_error')) {
            fullnameError.textContent = urlParams.get('fullname_error');
            fullnameInput.value = urlParams.get('fullname') || '';
        }
        if (urlParams.get('email_error')) {
            //email error display
            emailError.textContent = urlParams.get('email_error');
            emailInput.value = urlParams.get('email') || '';
        }
        //url cleanup
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Show general error if present
    const generalErrorDiv = document.getElementById('generalError');
    if (generalErrorDiv && generalErrorDiv.textContent.trim()) {
        generalErrorDiv.style.display = 'block';
    }

    //form submit validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        //error reset
        fullnameError.textContent = '';
        emailError.textContent = '';
        const generalError = document.getElementById('generalError');
        if (generalError) {
            generalError.style.display = 'none';
            generalError.textContent = '';
        }

        // Add loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Processing...';
        submitButton.disabled = true;

        //fullname validation
        const fullname = fullnameInput.value.trim();
        if (!fullname) {
            fullnameError.textContent = 'Full name is required';
            isValid = false;
        } else if (!/^[a-zA-Z ]*$/.test(fullname)) {
            fullnameError.textContent = 'Only letters and white space allowed';
            isValid = false;
        } else if (fullname.length < 3) {
            fullnameError.textContent = 'Full name must be at least 3 characters long';
            isValid = false;
        } else if (!/\s/.test(fullname)) {
            fullnameError.textContent = 'Please enter both first and last name';
            isValid = false;
        }

        //email validation
        const email = emailInput.value.trim();
        if (!email) {
            emailError.textContent = 'Email is required';
            isValid = false;
        } else if (!/^[a-zA-Z0-9._%+-]+@(gmail\.com|outlook\.com)$/.test(email)) {
            emailError.textContent = 'email domain must be outlook.com or gmail.com';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        } else {
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Sending verification email...';
            submitBtn.disabled = true;
        }
    });

    //real-time fullname validation
    fullnameInput.addEventListener('input', function() {
        const fullname = this.value.trim();
        if (!fullname) {
            fullnameError.textContent = 'Full name is required';
        } else if (!/^[a-zA-Z ]*$/.test(fullname)) {
            fullnameError.textContent = 'Only letters and white space allowed';
        } else if (fullname.length < 3) {
            fullnameError.textContent = 'Full name must be at least 3 characters long';
        } else if (!/\s/.test(fullname)) {
            fullnameError.textContent = 'Please enter both first and last name';
        } else {
            fullnameError.textContent = '';
        }
    });

    //real-time email validation
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        if (!email) {
            emailError.textContent = 'Email is required';
        } else if (!/^[a-zA-Z0-9._%+-]+@(gmail\.com|outlook\.com)$/.test(email)) {
            emailError.textContent = 'email domain must be outlook.com or gmail.com';
        } else {
            emailError.textContent = '';
        }
    });
});
