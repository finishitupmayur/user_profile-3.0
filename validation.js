// assets/js/validation.js

// Registration form validation
function validateRegisterForm() {
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirm_password").value.trim();

    // Check for empty fields
    if (!name || !email || !password || !confirmPassword) {
        alert("❗ Please fill in all fields.");
        return false;
    }

    // Validate name (at least 2 characters)
    if (name.length < 2) {
        alert("❗ Name must be at least 2 characters long.");
        return false;
    }

    // Email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("❗ Please enter a valid email address.");
        return false;
    }

    // Password length validation
    if (password.length < 6) {
        alert("❗ Password must be at least 6 characters long.");
        return false;
    }

    // Password match validation
    if (password !== confirmPassword) {
        alert("❗ Passwords do not match.");
        return false;
    }

    return true;
}

// Login form validation
function validateLoginForm() {
    const email = document.getElementById("login_email").value.trim();
    const password = document.getElementById("login_password").value.trim();

    // Check for empty fields
    if (!email || !password) {
        alert("❗ Please enter both email and password.");
        return false;
    }

    // Email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("❗ Please enter a valid email address.");
        return false;
    }

    return true;
}


function validateTaskForm() {
    const title = document.getElementById('title').value.trim();
    
    if (!title) {
        alert('❗ Task title is required.');
        return false;
    }
    
    if (title.length > 255) {
        alert('❗ Task title must be less than 255 characters.');
        return false;
    }
    
    return true;
}


function validateProfileForm() {
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();

    if (!name || !email) {
        alert("❗ Name and email are required.");
        return false;
    }

    if (name.length < 2) {
        alert("❗ Name must be at least 2 characters long.");
        return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("❗ Please enter a valid email address.");
        return false;
    }

    return true;
}


function confirmDeleteTask(taskTitle) {
    return confirm(`Are you sure you want to delete the task "${taskTitle}"?\n\nThis action cannot be undone.`);
}

function confirmDeleteAccount() {
    return confirm("Are you sure you want to delete your account?\n\nThis will permanently delete:\n- Your profile\n- All your tasks\n- All your data\n\nThis action cannot be undone!");
}


document.addEventListener('DOMContentLoaded', function() {

    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (passwordField && confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
    }
});