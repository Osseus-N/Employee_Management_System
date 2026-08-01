document.addEventListener("DOMContentLoaded", initializeDashboard);

function initializeDashboard() {

    updateCurrentDate();
    bindDashboardEvents();
    showSection("employee");
    refreshDashboard();

}

function bindDashboardEvents() {

    document.getElementById("btnRefresh")
        ?.addEventListener("click", refreshDashboard);

    document.getElementById("btnLogout")
        ?.addEventListener("click", logout);

    document.getElementById("btnProfile")
        ?.addEventListener("click", openProfile);

    document.getElementById("tabEmployees")
        ?.addEventListener("click", () => showSection("employee"));

    document.getElementById("tabAttendance")
        ?.addEventListener("click", () => showSection("attendance"));

    document.getElementById("tabPayroll")
        ?.addEventListener("click", () => showSection("payroll"));

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

async function refreshDashboard() {

    await loadEmployees();
    await loadAttendance();
    await loadPayroll();

}

async function logout() {

    if (!confirm("Are you sure wanna logout zir?")) return;

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

function showSection(section) {

    document.getElementById("employeeSection").classList.add("hidden");
    document.getElementById("attendanceSection").classList.add("hidden");
    document.getElementById("payrollSection").classList.add("hidden");

    document.getElementById(section + "Section").classList.remove("hidden");

    setActiveTab({
        employee: "tabEmployees",
        attendance: "tabAttendance",
        payroll: "tabPayroll"
    }[section]);

}

function setActiveTab(activeId) {

    document.querySelectorAll(".tab-btn").forEach(button => {

        button.classList.remove("border-blue-600", "text-blue-600");

    });

    document.getElementById(activeId)
        ?.classList.add("border-blue-600", "text-blue-600");

}