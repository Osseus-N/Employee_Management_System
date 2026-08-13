let employees = [];
let accounts = [];
let selectedID = null;

document.addEventListener("DOMContentLoaded", () => {
    loadEmployees();

    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", searchEmployee);
    }

    const employeeForm = document.getElementById("employeeForm");
    if (employeeForm) {
        employeeForm.addEventListener("submit", handleFormSubmit);
    }

    const btnResetForm = document.getElementById("btnResetForm");
    if (btnResetForm) {
        btnResetForm.addEventListener("click", resetEmployeeForm);
    }

    const btnCancelForm = document.getElementById("btnCancelForm")
    if(btnCancelForm){
        btnCancelForm.addEventListener("click", hideEmployeeForm)
    }
    const btnToggleCreateForm = document.getElementById("btnToggleCreateForm");
    if (btnToggleCreateForm) {
        btnToggleCreateForm.addEventListener("click", prepareCreateForm);
    }

    const btnMyProfile = document.getElementById("btnMyProfile");
    if (btnMyProfile) {
        btnMyProfile.addEventListener("click", () => {
            window.location.href = "/employee_management_system/employee";
        });
    }

    const payrollDropdownBtn = document.getElementById("payrollDropdownBtn");
    const payrollDropdown = document.getElementById("payrollDropdown");

    if (payrollDropdownBtn && payrollDropdown) {
        payrollDropdownBtn.addEventListener("click", (event) => {
            event.stopPropagation();
            payrollDropdown.classList.toggle("hidden");
        });

        document.addEventListener("click", (event) => {
            if (
                !payrollDropdown.contains(event.target) &&
                !payrollDropdownBtn.contains(event.target)
            ) {
                payrollDropdown.classList.add("hidden");
            }
        });
    }
});

async function loadEmployees() {
    try {
        const response = await fetch(ADMIN_API, {
            credentials: "same-origin"
        });

        if (response.status === 401 || response.status === 403) {
            window.location.href = "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            alert(result.message || "Failed to load employees.");
            return;
        }

        employees = result.data?.employees || [];
        accounts = result.data?.accounts || [];

        displayEmployees();
    } catch (error) {
        console.error("Error loading employees:", error);
        alert("Unable to connect to the server.");
    }
}

function displayEmployees(data = employees) {
    const table = document.getElementById("employeeTableBody");
    if (!table) return;

    table.innerHTML = "";

    if (data.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">
                    No employees found
                </td>
            </tr>
        `;
        return;
    }

    data.forEach(emp => {
        const account = accounts.find(acc => acc.emp_id == emp.emp_id);
        const email = account?.acc_email || "—";
        const fullName = `${emp.emp_firstname || ""} ${emp.emp_lastname || ""}`.trim();

        const row = document.createElement("tr");
        row.className = "border-b border-gray-200 hover:bg-gray-50";

        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-900">
                ${escapeHtml(fullName)}
            </td>
            <td class="px-4 py-3 text-sm text-gray-700">
                ${escapeHtml(email)}
            </td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-md bg-gray-500 px-2 py-1 text-xs font-semibold text-white">
                    ${escapeHtml(emp.emp_position || "—")}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">
                ₱${Number(emp.emp_hourly_rate || 0).toFixed(2)}
            </td>
            <td class="px-4 py-3 text-sm text-gray-700">
                ${statusBadge(emp.emp_status)}
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="inline-flex items-center gap-1 rounded-md border border-blue-500 px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50" onclick="selectEmployee(${emp.emp_id})">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </button>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="inline-flex items-center gap-1 rounded-md border border-red-500 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50" onclick="promptDeleteEmployee(${emp.emp_id})">
                    <i class="bi bi-trash"></i>
                    Delete
                </button>
            </td>
        `;

        table.appendChild(row);
    });
}

function searchEmployee() {
    const input = document.getElementById("searchInput");
    if (!input) return;

    const keyword = input.value.toLowerCase().trim();

    const filtered = employees.filter(emp => {
        const firstName = emp.emp_firstname?.toLowerCase() || "";
        const lastName = emp.emp_lastname?.toLowerCase() || "";
        const position = emp.emp_position?.toLowerCase() || "";
        const status = emp.emp_status?.toLowerCase() || "";

        const account = accounts.find(acc => acc.emp_id == emp.emp_id);
        const email = account?.acc_email?.toLowerCase() || "";

        return (
            firstName.includes(keyword) ||
            lastName.includes(keyword) ||
            position.includes(keyword) ||
            status.includes(keyword) ||
            email.includes(keyword)
        );
    });

    displayEmployees(filtered);
}

function prepareCreateForm() {
    selectedID = null;

    const form = document.getElementById("employeeForm");
    if (form) form.reset();

    document.getElementById("formTitle").textContent = "Add New Employee";
    document.getElementById("btnSubmitForm").textContent = "Submit";

    const password = document.getElementById("password");
    if (password) {
        password.required = true;
        password.value = "";
    }

    const passwordHelp = document.getElementById("passwordHelp");
    if (passwordHelp) {
        passwordHelp.textContent = "Enter a password for the employee.";
    }

    const passwordContainer = document.getElementById("passwordContainer");
    if (passwordContainer) {
        passwordContainer.classList.remove("hidden");
    }

    showCreateEmployeeForm();
}

function selectEmployee(id) {
    const emp = employees.find(e => e.emp_id == id);

    if (!emp) {
        alert("Employee not found.");
        return;
    }

    const account = accounts.find(acc => acc.emp_id == emp.emp_id);

    selectedID = emp.emp_id;

    document.getElementById("formTitle").textContent = `Edit Employee (ID: ${emp.emp_id})`;
    document.getElementById("btnSubmitForm").textContent = "Update Employee";

    const password = document.getElementById("password");
    if (password) {
        password.required = false;
        password.value = "";
    }

    const passwordHelp = document.getElementById("passwordHelp");
    if (passwordHelp) {
        passwordHelp.textContent = "Leave blank to keep the current password.";
    }

    const passwordContainer = document.getElementById("passwordContainer");
    if (passwordContainer) {
        passwordContainer.classList.remove("hidden");
    }

    populateFormFields(emp, account);
    showCreateEmployeeForm();
}

function populateFormFields(emp, account) {
    document.getElementById("firstName").value = emp.emp_firstname || "";
    document.getElementById("lastName").value = emp.emp_lastname || "";
    document.getElementById("dob").value = emp.emp_date_of_birth || "";
    document.getElementById("email").value = account?.acc_email || "";
    document.getElementById("password").value = "";
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

function resetEmployeeForm() {
    if (selectedID !== null) {
        const emp = employees.find(e => e.emp_id == selectedID);
        if (emp) {
            const account = accounts.find(acc => acc.emp_id == emp.emp_id);
            populateFormFields(emp, account);
        }
    } else {
        const form = document.getElementById("employeeForm");
        if (form) form.reset();
    }
}

function showCreateEmployeeForm() {
    const form = document.getElementById("createEmployeeForm");
    if (form) {
        form.classList.remove("hidden");
    }
}

function hideEmployeeForm() {
    selectedID = null;

    const form = document.getElementById("employeeForm");
    if (form) form.reset();

    const container = document.getElementById("createEmployeeForm");
    if (container) {
        container.classList.add("hidden");
    }

}

function handleFormSubmit(event) {
    event.preventDefault();

    const isEdit = selectedID !== null;
    const actionText = isEdit ? "update this employee's details" : "add this new employee";

    const confirmed = confirm(`Are you sure you want to ${actionText}?`);
    if (!confirmed) return;

    if (isEdit) {
        updateEmployee();
    } else {
        addEmployee();
    }
}

function collectFormData() {
    const gender = document.querySelector('input[name="gender"]:checked');

    return {
        acc_email: document.getElementById("email").value.trim(),
        acc_password: document.getElementById("password").value,
        emp_firstname: document.getElementById("firstName").value.trim(),
        emp_lastname: document.getElementById("lastName").value.trim(),
        emp_gender: gender?.value || "Male",
        emp_date_of_birth: document.getElementById("dob").value,
        emp_contact_number: document.getElementById("contactNumber").value.trim(),
        emp_position: document.getElementById("position").value,
        emp_hourly_rate: document.getElementById("hourlyRate").value,
        emp_status: document.getElementById("status").value,
        emp_address: document.getElementById("address").value.trim()
    };
}

async function addEmployee() {
    const payload = {
        ...collectFormData(),

    };

    try {
        const response = await fetch(ADMIN_API, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });

        if (response.status === 401 || response.status === 403) {
            window.location.href = "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (result.success) {
            alert("Employee added successfully.");
            hideEmployeeForm();
            await loadEmployees();
        } else {
            alert(result.message || "Failed to add employee.");
        }
    } catch (error) {
        console.error("Add employee error:", error);
        alert("Unable to add employee.");
    }
}

async function updateEmployee() {
    if (selectedID === null) {
        alert("No employee selected.");
        return;
    }

    const password = document.getElementById("password")?.value.trim();

    const payload = {
        emp_id: selectedID,
        ...collectFormData(),
        acc_email: document.getElementById("email").value.trim()
    };

    if (password !== "") {
        payload.acc_password = password;
    }

    try {
        const response = await fetch(ADMIN_API, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });

        if (response.status === 401 || response.status === 403) {
            window.location.href = "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (result.success) {
            alert("Employee updated successfully.");
            hideEmployeeForm();
            await loadEmployees();
        } else {
            alert(result.message || "Failed to update employee.");
        }
    } catch (error) {
        console.error("Update employee error:", error);
        alert("Unable to update employee.");
    }
}

function promptDeleteEmployee(id) {
    const emp = employees.find(e => e.emp_id == id);

    if (!emp) {
        alert("Employee not found.");
        return;
    }

    const fullName = `${emp.emp_firstname || ""} ${emp.emp_lastname || ""}`.trim();

    const confirmed = confirm(
        `Are you sure you want to delete ${fullName}?\n\nThis action cannot be undone.`
    );

    if (!confirmed) return;

    deleteEmployee(id);
}

async function deleteEmployee(id) {
    try {
        const response = await fetch(ADMIN_API, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify({ emp_id: id })
        });

        if (response.status === 401 || response.status === 403) {
            window.location.href = "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (result.success) {
            alert("Employee deleted successfully.");
            await loadEmployees();
        } else {
            alert(result.message || "Failed to delete employee.");
        }
    } catch (error) {
        console.error("Delete employee error:", error);
        alert("Unable to delete employee.");
    }
}

function statusBadge(status) {
    const statusClasses = {
        Active: "bg-green-100 text-green-700",
        Inactive: "bg-yellow-100 text-yellow-700",
        Terminated: "bg-red-100 text-red-700"
    };

    const classes = statusClasses[status] || "bg-gray-100 text-gray-700";

    return `
        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${classes}">
            ${escapeHtml(status || "Unknown")}
        </span>
    `;
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str ?? "";
    return div.innerHTML;
}