//REQUIRED FIELD
function isEmpty(value) {
    return value.trim() === "";
}

//VALIDATION FOR EMAIL
function validateEmail(email) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);

}

//VALIDATION FOR PASS
function validatePassword(password) {
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}

//PHONE NUMBER
function validatePhone(phone) {
    const pattern = /^[0-9]{11}$/;

    if (!pattern.test(phone)) {
        alert("Phone number must be 11 digits.");
        return false;
    }
    return true;
}

//HOURLY RATE
function validateRate(rate) {
    if (rate == "" || isNaN(rate) || Number(rate) <= 0) {
        alert("Invalid hourly rate.");
        return false;
    }
    return true;
}

//LOGIN FORM
function validateLogin() {

    const email =
        document.getElementById("emailInput").value;

    const password =
        document.getElementById("passwordInput").value;

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

//FORM OF EMPLOYEE
function validateEmployee() {
    const firstname =
        document.getElementById("firstname").value;

    const lastname =
        document.getElementById("lastname").value;

    const position =
        document.getElementById("position").value;

    const rate =
        document.getElementById("hourly_rate").value;

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
    return true;
}

//PROFILE FORM
function validateProfile() {
    return validateEmployee();
}

//SEARCH VALIDATION
function validateSearch(value) {
    if (value.trim() == "") {
        return false;
    }
    return true;
}

//DATE VALIDATION
function validateDate(date) {
    if (date == "") {
        alert("Please select a date.");
        return false;
    }
    return true;
}

//DELETE CONFIRMATION
function confirmDelete() {

    return confirm("Are you sure you want to delete this employee?");

}

//CLEANING FORM
function clearInputs() {
    const inputs =
        document.querySelectorAll("input");
    inputs.forEach(input => {
        if (input.type != "button" &&
            input.type != "submit") {
            input.value = "";
        }
    });
}

//PANG NUMBER
function numbersOnly(event) {
    const key = event.key;
    if (!/[0-9]/.test(key) &&
        key !== "Backspace" &&
        key !== "Delete") {
        event.preventDefault();
    }
}

//PANG LETTER
function lettersOnly(event) {
    const key = event.key;
    if (!/^[a-zA-Z ]$/.test(key) &&
        key !== "Backspace" &&
        key !== "Delete") {
        event.preventDefault();
    }
}

//AUTOMATIC EVENT
window.addEventListener("load", function () {
    const rate =
        document.getElementById("hourly_rate");
    if (rate) {
        rate.addEventListener("keypress", numbersOnly);
    }
    const firstname =
        document.getElementById("firstname");
    if (firstname) {
        firstname.addEventListener("keypress", lettersOnly);
    }
    const lastname =
        document.getElementById("lastname");
    if (lastname) {
        lastname.addEventListener("keypress", lettersOnly);
    }
});