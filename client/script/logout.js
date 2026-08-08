const LOGOUT_API = "/employee_management_system/logout";
document.addEventListener("DOMContentLoaded", function () {
    const confirmLogoutBtn = document.getElementById("btnLogout");
    confirmLogoutBtn.addEventListener("click", logout);
});

async function logout() {
    try {
        const response = await fetch(LOGOUT_API, {
            method: "GET",
            credentials: "same-origin"
        });
        const result = await response.json();

        sessionStorage.clear();

        if (result.success) {
            window.location.href = "/employee_management_system";
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error(error);
        alert("Unable to log out.");
    }
}