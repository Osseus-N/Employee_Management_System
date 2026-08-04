// REQUIRED FIELD
function isEmpty(value) {
    return value.trim() === "";
}

// EMAIL FORMAT
function validateEmail(email) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
}

// PASSWORD LENGTH
function validatePassword(password) {
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}

// PHONE NUMBER (11 digits)
function validatePhone(phone) {
    if (!phone) return true; // optional field
    const pattern = /^[0-9]{11}$/;
    if (!pattern.test(phone)) {
        alert("Phone number must be 11 digits.");
        return false;
    }
    return true;
}

// HOURLY RATE
function validateRate(rate) {
    if (rate === "" || isNaN(rate) || Number(rate) <= 0) {
        alert("Invalid hourly rate.");
        return false;
    }
    return true;
}

// LOGIN FORM
function validateLogin() {
    const email = document.getElementById("emailInput").value;
    const password = document.getElementById("passwordInput").value;

    if (isEmpty(email)) {
        alert("Email is required.");
        return false;
    }
    if (!validateEmail(email)) {
        alert("Invalid email address.");
        return false;
    }
    if (!validatePassword(password)) {
        return false;
    }
    return true;
}

// EMPLOYEE CREATE/EDIT FORM (admin dashboard)
function validateEmployee() {
    const firstname = document.getElementById("firstName").value;
    const lastname = document.getElementById("lastName").value;
    const position = document.getElementById("position").value;
    const rate = document.getElementById("hourlyRate").value;
    const contact = document.getElementById("contactNumber")
        ? document.getElementById("contactNumber").value
        : "";

    if (isEmpty(firstname)) {
        alert("First name is required.");
        return false;
    }
    if (isEmpty(lastname)) {
        alert("Last name is required.");
        return false;
    }
    if (isEmpty(position)) {
        alert("Position is required.");
        return false;
    }
    if (!validateRate(rate)) {
        return false;
    }
    if (!validatePhone(contact)) {
        return false;
    }
    return true;
}

//Confirmation lang para sa delete
function confirmDelete() {
    return confirm("Are you sure you want to delete this employee?");
}

// Dapat Number lang
function numbersOnly(event) {
    const key = event.key;
    if (!/[0-9]/.test(key) && key !== "Backspace" && key !== "Delete" && key !== "Tab") {
        event.preventDefault();
    }
}

// Dapat Letter lang
function lettersOnly(event) {
    const key = event.key;
    if (!/^[a-zA-Z ]$/.test(key) && key !== "Backspace" && key !== "Delete" && key !== "Tab") {
        event.preventDefault();
    }
}

// AUTO-ATTACH INPUT RESTRICTIONS ON WHATEVER PAGE IS LOADED
window.addEventListener("load", function () {
    const rate = document.getElementById("hourlyRate");
    if (rate) rate.addEventListener("keypress", numbersOnly);

    const firstname = document.getElementById("firstName");
    if (firstname) firstname.addEventListener("keypress", lettersOnly);

    const lastname = document.getElementById("lastName");
    if (lastname) lastname.addEventListener("keypress", lettersOnly);

    const contact = document.getElementById("contactNumber");
    if (contact) contact.addEventListener("keypress", numbersOnly);
});
