//Login Checker
function LogInChecker() {
    'use strict';
    const form = document.getElementById('loginForm');
    if (!form) return; // Safety if called on wrong page

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
            // Redirect to admin dashboard on success
            window.location.href = "../admin/admin_view.html";
            // Reset form after redirect (optional, but we can do it)
            form.reset();
            form.classList.remove('was-validated');
            return;
        }
        form.classList.add('was-validated');
    }, false);
}

//Signup Checker
function SignUpChecker(){

}
function LogIn(){
    window.location.href = "admin/admin_view.html";
}