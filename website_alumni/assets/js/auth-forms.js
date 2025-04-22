// Login and Register Form Enhancements

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const loginForm = document.querySelector('.login-form');
    const registerForm = document.querySelector('.register-form');
    
    // Add entrance animation to form elements
    animateFormElements();
    
    // Password strength indicator for registration
    setupPasswordStrength();
    
    // File upload enhancements
    setupFileUpload();
    
    // Form validation
    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', validateRegisterForm);
    }
});

// Animate form elements on page load
function animateFormElements() {
    const formElements = document.querySelectorAll('.form-group, button[type="submit"], .form-links a');
    
    formElements.forEach((element, index) => {
        // Add animation with sequential delay
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        // Delay each element slightly more than the previous one
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 + (index * 50));
    });
}

// Password strength indicator
function setupPasswordStrength() {
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmInput = document.querySelector('input[name="confirm_password"]');
    
    if (!passwordInput) return;
    
    // Create password strength indicator
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength';
    strengthIndicator.innerHTML = `
        <div class="strength-meter">
            <div class="strength-meter-fill"></div>
        </div>
        <span class="strength-text"></span>
    `;
    
    // Insert after password input
    passwordInput.parentNode.insertBefore(strengthIndicator, passwordInput.nextSibling);
    
    const strengthMeterFill = strengthIndicator.querySelector('.strength-meter-fill');
    const strengthText = strengthIndicator.querySelector('.strength-text');
    
    // Update on password input
    passwordInput.addEventListener('input', function() {
        const value = passwordInput.value;
        const strength = calculatePasswordStrength(value);
        
        // Update strength meter
        strengthMeterFill.style.width = `${strength.percent}%`;
        strengthMeterFill.style.backgroundColor = strength.color;
        strengthText.textContent = strength.text;
        strengthText.style.color = strength.color;
        
        // Check password match if confirm field has value
        if (confirmInput && confirmInput.value) {
            checkPasswordMatch(passwordInput.value, confirmInput.value);
        }
    });
    
    // Add password match checking
    if (confirmInput) {
        // Create match indicator
        const matchIndicator = document.createElement('div');
        matchIndicator.className = 'password-match';
        
        // Insert after confirm password input
        confirmInput.parentNode.insertBefore(matchIndicator, confirmInput.nextSibling);
        
        confirmInput.addEventListener('input', function() {
            checkPasswordMatch(passwordInput.value, confirmInput.value);
        });
    }
}

// Calculate password strength
function calculatePasswordStrength(password) {
    // Initial settings
    let strength = {
        percent: 0,
        color: '#ff4d4d',
        text: 'Très faible'
    };
    
    if (!password) return strength;
    
    // Basic checks
    let score = 0;
    
    // Length check
    if (password.length >= 8) score += 25;
    if (password.length >= 12) score += 15;
    
    // Complexity checks
    if (/[A-Z]/.test(password)) score += 15; // Uppercase
    if (/[a-z]/.test(password)) score += 10; // Lowercase
    if (/[0-9]/.test(password)) score += 15; // Numbers
    if (/[^A-Za-z0-9]/.test(password)) score += 20; // Special chars
    
    // Set data based on score
    if (score >= 80) {
        strength.percent = 100;
        strength.color = '#2ecc71';
        strength.text = 'Excellent';
    } else if (score >= 60) {
        strength.percent = 75;
        strength.color = '#27ae60';
        strength.text = 'Fort';
    } else if (score >= 40) {
        strength.percent = 50;
        strength.color = '#f39c12';
        strength.text = 'Moyen';
    } else if (score >= 20) {
        strength.percent = 25;
        strength.color = '#e67e22';
        strength.text = 'Faible';
    } else {
        strength.percent = 10;
        strength.color = '#ff4d4d';
        strength.text = 'Très faible';
    }
    
    return strength;
}

// Check if passwords match
function checkPasswordMatch(password, confirmPassword) {
    const matchIndicator = document.querySelector('.password-match');
    
    if (!matchIndicator) return;
    
    if (!password || !confirmPassword) {
        matchIndicator.textContent = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchIndicator.textContent = 'Les mots de passe correspondent';
        matchIndicator.style.color = '#2ecc71';
    } else {
        matchIndicator.textContent = 'Les mots de passe ne correspondent pas';
        matchIndicator.style.color = '#ff4d4d';
    }
}

// Enhance file upload
function setupFileUpload() {
    const fileInput = document.querySelector('input[type="file"]');
    
    if (!fileInput) return;
    
    // Create file name display
    const fileNameDisplay = document.createElement('div');
    fileNameDisplay.className = 'file-name-display';
    fileInput.parentNode.insertBefore(fileNameDisplay, fileInput.nextSibling);
    
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            const fileName = fileInput.files[0].name;
            fileNameDisplay.textContent = 'Fichier sélectionné: ' + fileName;
            fileNameDisplay.style.color = '#27ae60';
            
            // Check file type
            const fileType = fileInput.files[0].type;
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            
            if (!validTypes.includes(fileType)) {
                fileNameDisplay.textContent = 'Type de fichier non autorisé. Veuillez télécharger un PDF, JPEG ou PNG.';
                fileNameDisplay.style.color = '#ff4d4d';
            }
        } else {
            fileNameDisplay.textContent = '';
        }
    });
}

// Validate login form
function validateLoginForm(event) {
    const form = event.target;
    const username = form.querySelector('input[name="username"]');
    const password = form.querySelector('input[name="password"]');
    let isValid = true;
    
    // Reset previous error styles
    resetFormErrors(form);
    
    // Validate username
    if (!username.value.trim()) {
        addErrorToField(username, "Veuillez entrer votre nom d'utilisateur ou email");
        isValid = false;
    }
    
    // Validate password
    if (!password.value) {
        addErrorToField(password, "Veuillez entrer votre mot de passe");
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
        showFormError(form, "Veuillez corriger les erreurs ci-dessous");
    }
}

// Validate register form
function validateRegisterForm(event) {
    const form = event.target;
    const fullName = form.querySelector('input[name="full_name"]');
    const username = form.querySelector('input[name="username"]');
    const email = form.querySelector('input[name="email"]');
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');
    const file = form.querySelector('input[name="verification_document"]');
    let isValid = true;
    
    // Reset previous error styles
    resetFormErrors(form);
    
    // Validate full name
    if (!fullName.value.trim()) {
        addErrorToField(fullName, "Veuillez entrer votre nom complet");
        isValid = false;
    }
    
    // Validate username
    if (!username.value.trim()) {
        addErrorToField(username, "Veuillez choisir un nom d'utilisateur");
        isValid = false;
    } else if (username.value.length < 4) {
        addErrorToField(username, "Le nom d'utilisateur doit contenir au moins 4 caractères");
        isValid = false;
    }
    
    // Validate email
    if (!email.value.trim()) {
        addErrorToField(email, "Veuillez entrer votre adresse email");
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        addErrorToField(email, "Veuillez entrer une adresse email valide");
        isValid = false;
    }
    
    // Validate password
    if (!password.value) {
        addErrorToField(password, "Veuillez choisir un mot de passe");
        isValid = false;
    } else if (password.value.length < 8) {
        addErrorToField(password, "Le mot de passe doit contenir au moins 8 caractères");
        isValid = false;
    }
    
    // Validate confirm password
    if (!confirmPassword.value) {
        addErrorToField(confirmPassword, "Veuillez confirmer votre mot de passe");
        isValid = false;
    } else if (confirmPassword.value !== password.value) {
        addErrorToField(confirmPassword, "Les mots de passe ne correspondent pas");
        isValid = false;
    }
    
    // Validate file
    if (file && !file.files.length) {
        addErrorToField(file, "Veuillez télécharger un document de vérification");
        isValid = false;
    } else if (file && file.files.length) {
        const fileType = file.files[0].type;
        const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (!validTypes.includes(fileType)) {
            addErrorToField(file, "Type de fichier non autorisé. Veuillez télécharger un PDF, JPEG ou PNG.");
            isValid = false;
        }
    }
    
    if (!isValid) {
        event.preventDefault();
        showFormError(form, "Veuillez corriger les erreurs ci-dessous");
        
        // Shake the form to indicate errors
        form.closest('.register-container, .login-container').classList.add('shake');
        setTimeout(() => {
            form.closest('.register-container, .login-container').classList.remove('shake');
        }, 600);
    }
}

// Helper functions
function resetFormErrors(form) {
    // Remove error classes and messages
    form.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('has-error');
        const errorMessage = group.querySelector('.field-error');
        if (errorMessage) errorMessage.remove();
    });
    
    // Remove form error alert
    const formError = form.querySelector('.form-error-alert');
    if (formError) formError.remove();
}

function addErrorToField(field, message) {
    const formGroup = field.closest('.form-group');
    formGroup.classList.add('has-error');
    
    // Add error message
    const errorMessage = document.createElement('div');
    errorMessage.className = 'field-error';
    errorMessage.textContent = message;
    formGroup.appendChild(errorMessage);
    
    // Add shake animation to field
    field.classList.add('shake');
    setTimeout(() => {
        field.classList.remove('shake');
    }, 600);
}

function showFormError(form, message) {
    // Create form error alert
    const formError = document.createElement('div');
    formError.className = 'form-error-alert';
    formError.textContent = message;
    
    // Insert at top of form
    form.insertBefore(formError, form.firstChild);
    
    // Scroll to the top of the form
    window.scrollTo({
        top: form.offsetTop - 100,
        behavior: 'smooth'
    });
}

function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
} 