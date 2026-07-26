//PANG FETCH
const employeeAPI = "../../server/employee/employeeController.php";
const attendanceAPI = "../../server/attendance/attendanceController.php";
const payrollAPI = "../../server/payroll/payrollController.php";

let employees = [];
let selectedID = null;

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

//PANG LOAD NG EMPLOYEE
async function loadEmployees() {
    try {
        const response = await fetch(employeeAPI);
        const result = await response.json();
        if (result.success) {
            employees = result.data;
            displayEmployees(employees);
            updateDashboard();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.log(error);
        alert("Cannot connect to server.");
    }
}

//TABLE DISPLAY
function displayEmployees(data) {
    const table = document.getElementById("employeeTableBody");
    if (!table) return;
    table.innerHTML = "";

    if (data.length == 0) {
        table.innerHTML =
            "<tr><td colspan='8'>No Employees Found</td></tr>";
        return;
    }

    data.forEach(emp => {
        table.innerHTML += `
        <tr>
            <td>${emp.emp_id}</td>
            <td>${emp.emp_firstname}</td>
            <td>${emp.emp_lastname}</td>
            <td>${emp.emp_gender}</td>
            <td>${emp.emp_position}</td>
            <td>${emp.emp_hourly_rate}</td>
            <td>${emp.emp_status}</td>
            <td>
                <button onclick="selectEmployee(${emp.emp_id})">Edit</button>
                <button onclick="deleteEmployee(${emp.emp_id})">Delete</button>
            </td>
        </tr>
        `;
    });
}

//SEARCH FOR EMPLOYEE
function searchEmployee() {
    const keyword = document
        .getElementById("searchEmployee")
        .value
        .toLowerCase();
    const filtered = employees.filter(emp =>
        emp.emp_firstname.toLowerCase().includes(keyword) ||
        emp.emp_lastname.toLowerCase().includes(keyword) ||
        emp.emp_position.toLowerCase().includes(keyword) ||
        emp.emp_status.toLowerCase().includes(keyword)
    );
    displayEmployees(filtered);
}

//EMPLOYEE SELECTING
function selectEmployee(id) {
    selectedID = id;
    const emp = employees.find(e => e.emp_id == id);
    if (!emp) return;
    document.getElementById("firstname").value =
        emp.emp_firstname;

    document.getElementById("lastname").value =
        emp.emp_lastname;

    document.getElementById("gender").value =
        emp.emp_gender;

    document.getElementById("position").value =
        emp.emp_position;

    document.getElementById("hourly_rate").value =
        emp.emp_hourly_rate;

    document.getElementById("status").value =
        emp.emp_status;
}

//FOR CLEANING FORM
function clearForm() {
    selectedID = null;

    document.getElementById("firstname").value = "";
    document.getElementById("lastname").value = "";
    document.getElementById("gender").value = "Male";
    document.getElementById("position").value = "";
    document.getElementById("hourly_rate").value = "";
    document.getElementById("status").value = "Active";
}


//ADD EMPLOYEE
async function addEmployee() {
    if (!validateEmployee()) return;
    const employee = {

        emp_firstname: document.getElementById("firstname").value,
        emp_lastname: document.getElementById("lastname").value,
        emp_gender: document.getElementById("gender").value,
        emp_position: document.getElementById("position").value,
        emp_hourly_rate: document.getElementById("hourly_rate").value,
        emp_status: document.getElementById("status").value

    };

    try {
        const response = await fetch(employeeAPI, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(employee)
        });

        const result = await response.json();
        alert(result.message);

        if (result.success) {
            clearForm();
            loadEmployees();
        }

    } catch (error) {
        console.log(error);
        alert("Unable to add employee.");
    }

}

//UPDATE EMPLOYEE
async function updateEmployee() {

    if (selectedID == null) {
        alert("Select an employee first.");
        return;
    }
    if (!validateEmployee()) return;
    const employee = {
        emp_id: selectedID,
        emp_firstname: document.getElementById("firstname").value,
        emp_lastname: document.getElementById("lastname").value,
        emp_gender: document.getElementById("gender").value,
        emp_position: document.getElementById("position").value,
        emp_hourly_rate: document.getElementById("hourly_rate").value,
        emp_status: document.getElementById("status").value
    };

    try {
        const response = await fetch(employeeAPI, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(employee)
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            clearForm();
            loadEmployees();
        }

    } catch (error) {
        console.log(error);
        alert("Unable to update employee.");
    }
}

//DELETE
async function deleteEmployee(id) {

    if (!confirm("Delete this employee?")) {
        return;
    }

    try {
        const response = await fetch(employeeAPI, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                emp_id: id
            })
        });

        const result = await response.json();
        alert(result.message);

        if (result.success) {
            loadEmployees();
        }
    } catch (error) {
        console.log(error);
        alert("Unable to delete employee.");
    }
}
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
async function loadPayroll() {

    if (!payrollTable) return;
    try {
        const response = await fetch(payrollAPI);
        const result = await response.json();
        payrollTable.innerHTML = "";
        if (!result.success) {
            payrollTable.innerHTML =
                "<tr><td colspan='6'>No payroll records.</td></tr>";
            return;
        }
        result.data.forEach(pay => {
            payrollTable.innerHTML += `

            <tr>
                <td>${pay.emp_id}</td>
                <td>${pay.pay_period_start}</td>
                <td>${pay.pay_period_end}</td>
                <td>${pay.pay_amount}</td>
                <td>${pay.pay_status}</td>
            </tr>
            `;
        });
    } catch (error) {
        console.log(error);
    }
}
document.addEventListener("DOMContentLoaded", function () {
    checkUserRole();
});

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
const attendanceTable =
    document.getElementById("attendanceTableBody");

async function loadAttendance() {
    if (!attendanceTable) return;
    try {
        const response =
            await fetch(attendanceAPI);

        const result =
            await response.json();

        attendanceTable.innerHTML = "";
        if (!result.success) {
            attendanceTable.innerHTML =
                "<tr><td colspan='6'>No attendance found.</td></tr>";
            return;
        }
        result.data.forEach(att => {
            attendanceTable.innerHTML += `
            <tr>
                <td>${att.emp_id}</td>
                <td>${att.att_work_date}</td>
                <td>${att.att_clock_in}</td>
                <td>${att.att_clock_out}</td>
                <td>${att.att_total_hours}</td>
            </tr>
            `;
        });
    } catch (error) {
        console.log(error);
    }
}
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