const ADMIN_API = "/employee_management_system/admin/employees";
const PAYROLL_API = "/employee_management_system/payroll/self";

let employees = [];
let selectedID = null;
let pendingAction = null;

let collapseFormInstance = null;
let confirmationModalInstance = null;
let payModalInstance = null;

document.addEventListener("DOMContentLoaded", function () {
    collapseFormInstance = new bootstrap.Collapse(document.getElementById("createEmployeeForm"), { toggle: false });
    confirmationModalInstance = new bootstrap.Modal(document.getElementById("confirmationModal"));
    payModalInstance = new bootstrap.Modal(document.getElementById("payEmployeeModal"));

    loadEmployees();

    document.getElementById("searchInput").addEventListener("keyup", searchEmployee);
    document.getElementById("employeeForm").addEventListener("submit", handleFormSubmit);
    document.getElementById("btnResetForm").addEventListener("click", resetEmployeeForm);
    document.getElementById("btnCancelForm").addEventListener("click", hideEmployeeForm);
    document.getElementById("btnToggleCreateForm").addEventListener("click", prepareCreateForm);
    document.getElementById("payEmployeeForm").addEventListener("submit", handlePayrollSubmit);
    document.getElementById("btnConfirmAction").addEventListener("click", executeConfirmedAction);

    const btnMyProfile = document.getElementById("btnMyProfile");
    if (btnMyProfile) {
        btnMyProfile.addEventListener("click", () => {
            window.location.href = "/employee_management_system/employee";
        });
    }
});

async function loadEmployees() {
    try {
        const response = await fetch(ADMIN_API, { credentials: "same-origin" });
        const result = await response.json();

        if (result.success) {
            employees = result.data;
            displayEmployees(employees);
            populatePayrollDropdown();
        } else {
            alert(result.message || "Failed to load employees.");
        }
    } catch (error) {
        console.error("Error loading employees:", error);
    }
}


function displayEmployees(data) {
    const table = document.getElementById("employeeTableBody");
    table.innerHTML = "";

    if (!data || data.length === 0) {
        table.innerHTML = "<tr><td colspan='13' class='text-center py-4 text-muted'>No employees found</td></tr>";
        return;
    }

    data.forEach(emp => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="fw-bold">${emp.emp_id}</td>
            <td>${escapeHtml(emp.emp_firstname)}</td>
            <td>${escapeHtml(emp.emp_lastname)}</td>
            <td>${emp.emp_date_of_birth || "—"}</td>
            <td>${escapeHtml(emp.acc_email || emp.email || "—")}</td>
            <td>${escapeHtml(emp.emp_contact_number || "—")}</td>
            <td>${escapeHtml(emp.emp_gender)}</td>
            <td><span class="badge bg-secondary">${escapeHtml(emp.emp_position)}</span></td>
            <td>₱${Number(emp.emp_hourly_rate).toFixed(2)}</td>
            <td>${statusBadge(emp.emp_status)}</td>
            <td>${escapeHtml(emp.emp_address || "—")}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary" onclick="selectEmployee(${emp.emp_id})">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-danger" onclick="promptDeleteEmployee(${emp.emp_id})">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </td>
        `;
        table.appendChild(row);
    });
}

function statusBadge(status) {
    const colors = {
        Active: "bg-success",
        Inactive: "bg-warning text-dark",
        Terminated: "bg-danger"
    };
    return `<span class="badge ${colors[status] || "bg-secondary"}">${status}</span>`;
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str ?? "";
    return div.innerHTML;
}

function searchEmployee() {
    const keyword = document.getElementById("searchInput").value.toLowerCase();
    const filtered = employees.filter(emp =>
        emp.emp_firstname?.toLowerCase().includes(keyword) ||
        emp.emp_lastname?.toLowerCase().includes(keyword) ||
        emp.emp_position?.toLowerCase().includes(keyword) ||
        emp.emp_status?.toLowerCase().includes(keyword)
    );
    displayEmployees(filtered);
}

// 2. FORM SWITCHING & PRE-POPULATION (PUT METHOD PREPARATION)
function prepareCreateForm() {
    selectedID = null;
    document.getElementById("formTitle").textContent = "Add New Employee";
    document.getElementById("btnSubmitForm").textContent = "Submit";
    document.getElementById("passwordContainer").style.display = "block";
    document.getElementById("password").required = true;
    document.getElementById("employeeForm").reset();
}

function selectEmployee(id) {
    const emp = employees.find(e => e.emp_id == id);
    if (!emp) return;

    selectedID = id;
    document.getElementById("formTitle").textContent = `Edit Employee (ID: ${emp.emp_id})`;
    document.getElementById("btnSubmitForm").textContent = "Update Employee";

    // Pre-populate fields
    populateFormFields(emp);

    // Hide password field for PUT mode
    document.getElementById("passwordContainer").style.display = "none";
    document.getElementById("password").required = false;

    collapseFormInstance.show();
}

function populateFormFields(emp) {
    document.getElementById("firstName").value = emp.emp_firstname || "";
    document.getElementById("lastName").value = emp.emp_lastname || "";
    document.getElementById("dob").value = emp.emp_date_of_birth || "";
    document.getElementById("email").value = emp.acc_email || emp.email || "";
    document.getElementById("contactNumber").value = emp.emp_contact_number || "";
    document.getElementById("position").value = emp.emp_position || "Employee";
    document.getElementById("hourlyRate").value = emp.emp_hourly_rate || 0;
    document.getElementById("status").value = emp.emp_status || "Active";
    document.getElementById("address").value = emp.emp_address || "";

    if (emp.emp_gender === "Female") {
        document.getElementById("female").checked = true;
    } else {
        document.getElementById("male").checked = true;
    }
}

// Reset button restores existing info if editing, or clears form if adding
function resetEmployeeForm() {
    if (selectedID !== null) {
        const emp = employees.find(e => e.emp_id == selectedID);
        if (emp) populateFormFields(emp);
    } else {
        document.getElementById("employeeForm").reset();
    }
}

function hideEmployeeForm() {
    selectedID = null;
    document.getElementById("employeeForm").reset();
    collapseFormInstance.hide();
}

// 3. FORM SUBMISSION (POST FOR CREATE, PUT FOR UPDATE WITH CONFIRMATION)
function handleFormSubmit(event) {
    event.preventDefault();
    const isEdit = selectedID !== null;
    const actionText = isEdit ? "update this employee's details" : "add this new employee";

    showConfirmation(
        isEdit ? "Confirm Update" : "Confirm Addition",
        `Are you sure you want to ${actionText}?`,
        () => isEdit ? updateEmployee() : addEmployee()
    );
}

function collectFormData() {
    return {
        emp_firstname: document.getElementById("firstName").value,
        emp_lastname: document.getElementById("lastName").value,
        emp_gender: document.querySelector('input[name="gender"]:checked').value,
        emp_date_of_birth: document.getElementById("dob").value,
        emp_contact_number: document.getElementById("contactNumber").value,
        emp_position: document.getElementById("position").value,
        emp_hourly_rate: document.getElementById("hourlyRate").value,
        emp_status: document.getElementById("status").value,
        emp_address: document.getElementById("address").value
    };
}

async function addEmployee() {
    const payload = {
        ...collectFormData(),
        acc_email: document.getElementById("email").value,
        acc_password: document.getElementById("password").value
    };

    try {
        const response = await fetch(ADMIN_API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.success) {
            hideEmployeeForm();
            loadEmployees();
        } else {
            alert(result.message || "Failed to add employee.");
        }
    } catch (error) {
        console.error("Add error:", error);
    }
}

async function updateEmployee() {
    const payload = {
        emp_id: selectedID,
        ...collectFormData(),
        acc_email: document.getElementById("email").value
    };

    try {
        const response = await fetch(ADMIN_API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.success) {
            hideEmployeeForm();
            loadEmployees();
        } else {
            alert(result.message || "Failed to update employee.");
        }
    } catch (error) {
        console.error("Update error:", error);
    }
}

function promptDeleteEmployee(id) {
    showConfirmation(
        "Confirm Deletion",
        "Are you sure you want to delete this employee? This action cannot be undone.",
        () => deleteEmployee(id)
    );
}

async function deleteEmployee(id) {
    try {
        const response = await fetch(ADMIN_API, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ emp_id: id })
        });
        const result = await response.json();
        if (result.success) {
            loadEmployees();
        } else {
            alert(result.message || "Failed to delete employee.");
        }
    } catch (error) {
        console.error("Delete error:", error);
    }
}

function populatePayrollDropdown() {
    const select = document.getElementById("payrollEmpId");
    if (!select) return;
    select.innerHTML = employees
        .map(e => `<option value="${e.emp_id}">${escapeHtml(e.emp_firstname)} ${escapeHtml(e.emp_lastname)} (ID: ${e.emp_id})</option>`)
        .join("");
}

function handlePayrollSubmit(event) {
    event.preventDefault();
    const empId = document.getElementById("payrollEmpId").value;
    const emp = employees.find(e => e.emp_id == empId);
    const empName = emp ? `${emp.emp_firstname} ${emp.emp_lastname}` : `ID ${empId}`;

    showConfirmation(
        "Confirm Payroll",
        `Are you sure you want to process payroll for ${empName}?`,
        () => processPayroll()
    );
}

async function processPayroll() {
    const payload = {
        emp_id: document.getElementById("payrollEmpId").value,
        pay_period_start: document.getElementById("payPeriodStart").value,
        pay_period_end: document.getElementById("payPeriodEnd").value
    };

    try {
        const response = await fetch(PAYROLL_API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.success) {
            document.getElementById("payEmployeeForm").reset();
            payModalInstance.hide();
        } else {
            alert(result.message || "Failed to process payroll.");
        }
    } catch (error) {
        console.error("Payroll error:", error);
    }
}

function showConfirmation(title, message, onConfirmCallback) {
    document.getElementById("confirmModalTitle").textContent = title;
    document.getElementById("confirmModalBody").textContent = message;
    pendingAction = onConfirmCallback;
    confirmationModalInstance.show();
}

function executeConfirmedAction() {
    if (typeof pendingAction === "function") {
        pendingAction();
    }
    confirmationModalInstance.hide();
    pendingAction = null;
}