const LOGOUT_API = "/employee_management_system/index.php?action=logout";
document.addEventListener("DOMContentLoaded", function () {
    const confirmLogoutBtn = document.getElementById("btnLogout");
    if (!confirmLogoutBtn) return;
    confirmLogoutBtn.addEventListener("click", logout);
});

async function logout() {
    try {
        await fetch(LOGOUT_API, {
            method: "DELETE",
            credentials: "same-origin"
        });
    } catch (error) {
        console.error("Logout request failed:", error);
    }

    sessionStorage.clear();
    window.location.href = "/employee_management_system/index.php";
}