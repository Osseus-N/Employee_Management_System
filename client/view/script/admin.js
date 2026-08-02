const ADMIN_API = "../../../server/router/admin.php";
const ATTENDANCE_API = "../../../server/router/attendance.php";
const PAYROLL_API = "../../../server/router/payroll.php";
const LOGOUT_API = "../../../server/router/login.php";

let employees = [];
let selectedID = null;

// ---------- ACCESS GUARD ----------
if (sessionStorage.getItem("role") !== "admin") {
    window.location.href = "../../../login/login.html";
}

async function logout() {
    if (!confirm("Logout?")) return;
    try {
        await fetch(LOGOUT_API, { method: "DELETE", credentials: "same-origin" });
    } catch (error) {
        console.error(error);
    }
    sessionStorage.clear();
    window.location.href = "../../../login/login.html";
}

document.addEventListener("DOMContentLoaded", function () {
    const name = sessionStorage.getItem("firstname");
    if (name) document.getElementById("welcomeName").textContent = name;

    refreshDashboard();

    document.getElementById("searchEmployee").addEventListener("keyup", searchEmployee);
    document.getElementById("btnRefresh").addEventListener("click", refreshDashboard);
    document.getElementById("toggleEmployeeForm").addEventListener("click", showCreateForm);
    document.getElementById("btnSave").addEventListener("click", onSave);
    document.getElementById("btnCancel").addEventListener("click", hideForm);
    document.getElementById("payrollForm").addEventListener("submit", processPayroll);
});

// ---------- EMPLOYEES ----------
async function loadEmployees() {
    try {
        const response = await fetch(ADMIN_API, { credentials: "same-origin" });
        const result = await response.json();

        if (result.success) {
            employees = result.data;
            displayEmployees(employees);
            populatePayrollDropdown();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error(error);
        alert("Cannot connect to server.");
    }
}

function displayEmployees(data) {
    const table = document.getElementById("employeeTableBody");
    table.innerHTML = "";

    if (data.length === 0) {
        table.innerHTML = "<tr><td colspan='11' class='text-center py-6 text-gray-400'>No employees found</td></tr>";
        return;
    }

    data.forEach(emp => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="px-4 py-3">${escapeHtml(emp.emp_firstname)}</td>
            <td class="px-4 py-3">${escapeHtml(emp.emp_lastname)}</td>
            <td class="px-4 py-3">${emp.emp_date_of_birth}</td>
            <td class="px-4 py-3">${escapeHtml(emp.emp_contact_number ?? "")}</td>
            <td class="px-4 py-3">${escapeHtml(emp.emp_gender)}</td>
            <td class="px-4 py-3">${escapeHtml(emp.emp_position)}</td>
            <td class="px-4 py-3">₱${Number(emp.emp_hourly_rate).toFixed(2)}</td>
            <td class="px-4 py-3">${statusBadge(emp.emp_status)}</td>
            <td class="px-4 py-3 text-center text-gray-500 text-sm">Use "Process Payroll" below</td>
            <td class="px-4 py-3 text-center">
                <button class="text-blue-600 hover:underline" onclick="selectEmployee(${emp.emp_id})">Edit</button>
            </td>
            <td class="px-4 py-3 text-center">
                <button class="text-red-600 hover:underline" onclick="deleteEmployee(${emp.emp_id})">Delete</button>
            </td>
        `;
        table.appendChild(row);
    });
}

function statusBadge(status) {
    const colors = {
        Active: "bg-green-100 text-green-700",
        Inactive: "bg-yellow-100 text-yellow-700",
        Terminated: "bg-red-100 text-red-700"
    };
    return `<span class="px-2 py-1 rounded-full text-xs font-semibold ${colors[status] || ""}">${status}</span>`;
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str ?? "";
    return div.innerHTML;
}

function searchEmployee() {
    const keyword = document.getElementById("searchEmployee").value.toLowerCase();
    const filtered = employees.filter(emp =>
        emp.emp_firstname.toLowerCase().includes(keyword) ||
        emp.emp_lastname.toLowerCase().includes(keyword) ||
        emp.emp_position.toLowerCase().includes(keyword) ||
        emp.emp_status.toLowerCase().includes(keyword)
    );
    displayEmployees(filtered);
}

// ---------- CREATE / EDIT FORM ----------
function showCreateForm() {
    selectedID = null;
    document.getElementById("formTitle").textContent = "Create New Employee";
    document.getElementById("employeeForm").reset();
    document.getElementById("male").checked = true;
    document.getElementById("status").value = "Active";
    setLoginFieldsVisible(true);
    document.getElementById("createEmployeeForm").classList.remove("hidden");
}

function selectEmployee(id) {
    selectedID = id;
    const emp = employees.find(e => e.emp_id == id);
    if (!emp) return;

    document.getElementById("formTitle").textContent = "Edit Employee";
    document.getElementById("firstName").value = emp.emp_firstname;
    document.getElementById("lastName").value = emp.emp_lastname;
    document.getElementById("dob").value = emp.emp_date_of_birth;
    document.getElementById("contactNumber").value = emp.emp_contact_number ?? "";
    document.getElementById("position").value = emp.emp_position;
    document.getElementById("hourlyRate").value = emp.emp_hourly_rate;
    document.getElementById("status").value = emp.emp_status;

    if (emp.emp_gender === "Male") document.getElementById("male").checked = true;
    else if (emp.emp_gender === "Female") document.getElementById("female").checked = true;
    else document.getElementById("other").checked = true;

    setLoginFieldsVisible(false); // editing never touches login credentials here
    document.getElementById("createEmployeeForm").classList.remove("hidden");
}

function setLoginFieldsVisible(visible) {
    ["loginFieldsWrap", "passwordFieldWrap", "roleFieldWrap"].forEach(id => {
        document.getElementById(id).classList.toggle("hidden", !visible);
    });
}

function hideForm() {
    selectedID = null;
    document.getElementById("employeeForm").reset();
    document.getElementById("createEmployeeForm").classList.add("hidden");
}

function onSave() {
    if (selectedID === null) {
        addEmployee();
    } else {
        updateEmployee();
    }
}

function collectEmployeeFormData() {
    return {
        emp_firstname: document.getElementById("firstName").value,
        emp_lastname: document.getElementById("lastName").value,
        emp_gender: document.querySelector('input[name="gender"]:checked').value,
        emp_date_of_birth: document.getElementById("dob").value,
        emp_contact_number: document.getElementById("contactNumber").value,
        emp_position: document.getElementById("position").value,
        emp_hourly_rate: document.getElementById("hourlyRate").value,
        emp_status: document.getElementById("status").value
    };
}

async function addEmployee() {
    if (!validateEmployee()) return;

    const email = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value;
    const role = document.getElementById("loginRole").value;

    if (!email || !validateEmail(email)) {
        alert("A valid login email is required.");
        return;
    }
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return;
    }

    const payload = {
        ...collectEmployeeFormData(),
        acc_email: email,
        acc_password: password,
        acc_role: role
    };

    try {
        const response = await fetch(ADMIN_API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            hideForm();
            refreshDashboard();
        }
    } catch (error) {
        console.error(error);
        alert("Unable to add employee.");
    }
}

async function updateEmployee() {
    if (!validateEmployee()) return;

    const payload = { emp_id: selectedID, ...collectEmployeeFormData() };

    try {
        const response = await fetch(ADMIN_API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            hideForm();
            refreshDashboard();
        }
    } catch (error) {
        console.error(error);
        alert("Unable to update employee.");
    }
}

async function deleteEmployee(id) {
    if (!confirmDelete()) return;

    try {
        const response = await fetch(ADMIN_API, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ emp_id: id })
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) refreshDashboard();
    } catch (error) {
        console.error(error);
        alert("Unable to delete employee.");
    }
}

// ---------- ATTENDANCE (admin sees everyone) ----------
async function loadAttendance() {
    const table = document.getElementById("attendanceTableBody");
    try {
        const response = await fetch(ATTENDANCE_API, { credentials: "same-origin" });
        const result = await response.json();
        table.innerHTML = "";

        if (!result.success || result.data.length === 0) {
            table.innerHTML = "<tr><td colspan='5' class='text-center py-6 text-gray-400'>No attendance records.</td></tr>";
            return;
        }

        result.data.forEach(att => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="px-4 py-3">${escapeHtml(att.emp_firstname)} ${escapeHtml(att.emp_lastname)}</td>
                <td class="px-4 py-3">${att.att_work_date}</td>
                <td class="px-4 py-3">${att.att_clock_in}</td>
                <td class="px-4 py-3">${att.att_clock_out ?? "—"}</td>
                <td class="px-4 py-3">${att.att_total_hours ?? "—"}</td>
            `;
            table.appendChild(row);
        });
    } catch (error) {
        console.error(error);
    }
}

// ---------- PAYROLL ----------
function populatePayrollDropdown() {
    const select = document.getElementById("payrollEmpId");
    select.innerHTML = employees
        .map(e => `<option value="${e.emp_id}">${escapeHtml(e.emp_firstname)} ${escapeHtml(e.emp_lastname)}</option>`)
        .join("");
}

async function loadPayroll() {
    const table = document.getElementById("payrollTableBody");
    try {
        const response = await fetch(PAYROLL_API, { credentials: "same-origin" });
        const result = await response.json();
        table.innerHTML = "";

        if (!result.success || result.data.length === 0) {
            table.innerHTML = "<tr><td colspan='6' class='text-center py-6 text-gray-400'>No payroll records.</td></tr>";
            return;
        }

        result.data.forEach(pay => {
            const row = document.createElement("tr");
            const payAction = pay.pay_status === "Pending"
                ? `<button class="text-blue-600 hover:underline" onclick="markPaid(${pay.pay_id})">Mark Paid</button>`
                : `<span class="text-gray-400 text-sm">—</span>`;
            row.innerHTML = `
                <td class="px-4 py-3">${escapeHtml(pay.emp_firstname)} ${escapeHtml(pay.emp_lastname)}</td>
                <td class="px-4 py-3">${pay.pay_period_start} → ${pay.pay_period_end}</td>
                <td class="px-4 py-3">${pay.pay_total_hours}</td>
                <td class="px-4 py-3">₱${Number(pay.pay_amount).toFixed(2)}</td>
                <td class="px-4 py-3">${statusBadge(pay.pay_status)}</td>
                <td class="px-4 py-3 text-center">${payAction}</td>
            `;
            table.appendChild(row);
        });
    } catch (error) {
        console.error(error);
    }
}

async function processPayroll(event) {
    event.preventDefault();
    const empId = document.getElementById("payrollEmpId").value;
    const start = document.getElementById("payPeriodStart").value;
    const end = document.getElementById("payPeriodEnd").value;

    if (!empId || !start || !end) {
        alert("Please select an employee and a full date range.");
        return;
    }

    try {
        const response = await fetch(PAYROLL_API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ emp_id: empId, pay_period_start: start, pay_period_end: end })
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            document.getElementById("payrollForm").reset();
            loadPayroll();
        }
    } catch (error) {
        console.error(error);
        alert("Unable to process payroll.");
    }
}

async function markPaid(payId) {
    if (!confirm("Mark this payroll record as paid?")) return;
    try {
        const response = await fetch(PAYROLL_API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ pay_id: payId })
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) loadPayroll();
    } catch (error) {
        console.error(error);
        alert("Unable to update payroll.");
    }
}

// ---------- REFRESH ALL ----------
async function refreshDashboard() {
    await loadEmployees();
    loadAttendance();
    loadPayroll();
}
