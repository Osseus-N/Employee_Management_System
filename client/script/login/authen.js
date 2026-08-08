const LOGIN_API = '/employee_management_system/login';

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("loginForm")?.addEventListener("submit", login);
});
async function login(event) {
    event.preventDefault();

    if (typeof validateLogin === 'function' && !validateLogin()) return;

    const email = document.getElementById("emailInput").value.trim();
    const password = document.getElementById("passwordInput").value;
    const errorEl = document.getElementById("loginError");

    try {
        const response = await fetch(LOGIN_API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            if (errorEl) {
                errorEl.textContent = result.message || "Invalid email or password.";
                errorEl.classList.remove("hidden");
            } else {
                alert(result.message || "Invalid email or password.");
            }
            return;
        }

        const role = result.data.role;
        const user = result.data.user;

        sessionStorage.setItem("role", role);
        sessionStorage.setItem("emp_id", user.emp_id);
        sessionStorage.setItem("user", user.emp_firstname);


        if (role === "admin") {
            window.location.replace("/employee_management_system/admin");
        } else{
            window.location.replace("/employee_management_system/employee");
        }

    } catch (error) {
        console.error("Login request failed:", error);
        if (errorEl) {
            errorEl.textContent = "Cannot connect to server. Open DevTools > Console for details.";
            errorEl.classList.remove("hidden");
        } else {
            alert("Cannot connect to server. Open DevTools > Console for details.");
        }
    }
}