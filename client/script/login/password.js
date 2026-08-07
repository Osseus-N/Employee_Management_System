document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordInput = document.getElementById("passwordInput");
    const toggleIcon = document.getElementById("toggleIcon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
    }
});