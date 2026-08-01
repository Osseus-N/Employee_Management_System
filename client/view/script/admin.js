document.addEventListener("DOMContentLoaded", initializeDashboard);

function initializeDashboard() {
    updateCurrentDate();
    bindDashboardEvents();
    refreshDashboard();
}

function bindDashboardEvents() {

    document.getElementById("btnRefresh")
        ?.addEventListener("click", refreshDashboard);

    document.getElementById("btnLogout")
        ?.addEventListener("click", logout);

    document.getElementById("btnProfile")
        ?.addEventListener("click", openProfile);

}

function updateCurrentDate() {
    const currentDate = document.getElementById("currentDate");

    if (!currentDate) return;

    currentDate.textContent = new Date().toLocaleDateString("en-PH", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    });
}

function updateDashboardCards() {

    document.getElementById("totalEmployees").textContent =
        employees.length;

    document.getElementById("activeEmployees").textContent =
        employees.filter(emp => emp.emp_status === "Active").length;

    document.getElementById("inactiveEmployees").textContent =
        employees.filter(emp => emp.emp_status === "Inactive").length;

    document.getElementById("terminatedEmployees").textContent =
        employees.filter(emp => emp.emp_status === "Terminated").length;
}

async function refreshDashboard() {

    await loadEmployees();
    await loadAttendance();
    await loadPayroll();

    updateDashboard();
}

async function logout() {

    if (!confirm("Are you sure you want to logout?")) return;
    try {
        await fetch("../../server/login/loginController.php", {
            method: "DELETE"
        });
    } catch (error) {
        console.error(error);
    }
    window.location.href = "../../login/login.html";
}

function openProfile() {

    window.location.href = "../employee/employee_view.html";

}