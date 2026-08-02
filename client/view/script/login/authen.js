// This file is only ever loaded from /login/login.html, so its relative
// path depth to the project root is fixed: one folder up.
const LOGIN_API = "../server/router/login.php";

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    if (!loginForm) return;
    loginForm.addEventListener("submit", login);
});

async function login(event) {
    event.preventDefault();

    if (!validateLogin()) return;

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

        if (!response.ok && response.status === 404) {
            throw new Error("404: login.php not found at " + LOGIN_API);
        }

        const result = await response.json();

        if (!result.success) {
            if (errorEl) {
                errorEl.textContent = result.message || "Invalid email or password.";
                errorEl.classList.remove("hidden");
            } else {
                alert(result.message || "Invalid email or password.");
            }
            return;
        }

        // Persist role/id client-side so the dashboards can tailor the UI.
        // The actual authorization check always happens server-side via the
        // PHP session, so this is just for showing/hiding UI elements.
        sessionStorage.setItem("role", result.data.role);
        sessionStorage.setItem("emp_id", result.data.emp_id);
        sessionStorage.setItem("firstname", result.data.firstname);

        if (result.data.role === "admin") {
            window.location.href = "../client/view/admin/admin_view.html";
        } else {
            window.location.href = "../client/view/employee/employee_view.html";
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
