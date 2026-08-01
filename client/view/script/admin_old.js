//PANG FETCH



let employees = [];
let selectedID = null;

document.addEventListener("DOMContentLoaded", initializeAdminDashboard);

function initializeAdminDashboard() {

    initializeButtons();

    updateCurrentDate();

    loadEmployees();

    refreshDashboard();

}



function showEmptyEmployeeTable(show) {

    const emptyState = document.getElementById("employeeEmptyState");
    const table = document.querySelector("#employeeSection table");

    if (!emptyState || !table) return;

    if (show) {
        emptyState.classList.remove("hidden");
        table.classList.add("hidden");
    } else {
        emptyState.classList.add("hidden");
        table.classList.remove("hidden");
    }

}

function createStatusBadge(status) {

    switch (status) {

        case "Active":
            return `<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">${status}</span>`;

        case "Inactive":
            return `<span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">${status}</span>`;

        case "Terminated":
            return `<span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">${status}</span>`;

        default:
            return `<span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">${status}</span>`;
    }

}

//LOAD PAGE
window.onload = function () {
    loadEmployees();
    const search = document.getElementById("searchEmployee");
    if (search) {
        search.addEventListener("keyup", searchEmployee);
    }
    const refresh = document.getElementById("btnRefresh");
    if (refresh) {
        refresh.onclick = loadEmployees;
    }
};

const employeeForm = document.getElementById("employeeForm");

if (employeeForm) {

    employeeForm.addEventListener("submit", function (e) {

        e.preventDefault();

    });

}

function updateCurrentDate() {

    const currentDate = document.getElementById("currentDate");

    if (!currentDate) return;

    const now = new Date();

    currentDate.textContent = now.toLocaleDateString("en-PH", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    });

}

//PANG LOAD NG EMPLOYEE


//TABLE DISPLAY

//SEARCH FOR EMPLOYEE

//EMPLOYEE SELECTING

//FOR CLEANING FORM
function clearForm() {
    selectedID = null;
    document.getElementById("employeeForm").reset();
    document.getElementById("male").checked = true;
    document.getElementById("status").value = "Active";
}

//ADD EMPLOYEE

// SAVE
const saveBtn = document.getElementById("btnSave");

if (saveBtn) {
    saveBtn.addEventListener("click", function () {
        if (selectedID == null) {
            addEmployee();
        } else {
            updateEmployee();
        }
    });
}

//CANCEL

const cancelBtn = document.getElementById("btnCancel");
if (cancelBtn) {
    cancelBtn.addEventListener("click", function () {
        clearForm();
    });
}

// PAYROLL
const payrollTable = document.getElementById("payrollTableBody");


function checkUserRole() {

    const userRole = sessionStorage.getItem("role") || localStorage.getItem("role");
    const adminBtn = document.getElementById("adminReturnBtn");

    if (userRole && userRole.toLowerCase() === "admin") {
        adminBtn.classList.remove("d-none");
    }
}
//PAY OF EMPLOYEE

async function payEmployee(id, month, year) {
    try {
        const response = await fetch(payrollAPI, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                emp_id: id,
                month: month,
                year: year
            })
        });

        const result = await response.json();
        alert(result.message);
        loadPayroll();

    } catch (error) {
        console.log(error);
        alert("Unable to process payroll.");
    }
}

//ATTENDANCE

//COUNT FOR DASHBOARD

function updateDashboard() {
    const total = employees.length;
    const active = employees.filter(emp =>
        emp.emp_status == "Active").length;
    const inactive = employees.filter(emp =>
        emp.emp_status == "Inactive").length;
    const terminated = employees.filter(emp =>
        emp.emp_status == "Terminated").length;
    if (document.getElementById("totalEmployees"))
        document.getElementById("totalEmployees").innerHTML = total;

    if (document.getElementById("activeEmployees"))
        document.getElementById("activeEmployees").innerHTML = active;

    if (document.getElementById("inactiveEmployees"))
        document.getElementById("inactiveEmployees").innerHTML = inactive;

    if (document.getElementById("terminatedEmployees"))
        document.getElementById("terminatedEmployees").innerHTML = terminated;
}

//LOGOUT
async function logout() {
    if (!confirm("Logout?")) return;
    try {
        await fetch("../../server/login/loginController.php", {
            method: "DELETE"
        });
    } catch (error) {
        console.log(error);
    }
    window.location.href =
        "../../login/login.html";
}

//REFRESH OF DASHBOARD
async function refreshDashboard() {
    await loadEmployees();
    updateDashboard();
    loadAttendance();
    loadPayroll();
}

//ATOMATIC LOAD

window.addEventListener("load", function () {
    refreshDashboard();
});