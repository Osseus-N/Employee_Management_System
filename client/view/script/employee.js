// ===============================
// EMPLOYEE MODULE
// ===============================

const employeeAPI = "../../../server/employee/employeeController.php";

let employees = [];
let selectedEmployee = null;

document.addEventListener("DOMContentLoaded", initializeEmployee);

function initializeEmployee() {

    bindEmployeeEvents();

    loadEmployees();

}
function bindEmployeeEvents() {

    document.getElementById("btnAddEmployee")
        ?.addEventListener("click", openEmployeeModal);

    document.getElementById("btnCloseModal")
        ?.addEventListener("click", closeEmployeeModal);

    document.getElementById("btnCancel")
        ?.addEventListener("click", closeEmployeeModal);

    document.getElementById("employeeForm")
        ?.addEventListener("submit", saveEmployee);

    document.getElementById("searchEmployee")
        ?.addEventListener("input", searchEmployee);

    document.getElementById("statusFilter")
        ?.addEventListener("change", filterEmployees);

}

async function loadEmployees() {

    try {

        const response = await fetch(employeeAPI);

        const result = await response.json();

        if (!result.success) {

            employees = [];

            renderEmployees([]);

            return;

        }

        employees = result.data;

        renderEmployees(employees);

    }

    catch (error) {

        console.error(error);

    }

}

function renderEmployees(data) {

    const table = document.getElementById("employeeTableBody");

    table.innerHTML = "";

    if (data.length === 0) {

        table.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-6">
                    No employees found.
                </td>
            </tr>
        `;

        return;

    }

    data.forEach(employee => {

        table.insertAdjacentHTML("beforeend", createEmployeeRow(employee));

    });

}

function createEmployeeRow(emp) {

    return `
    <tr class="border-b hover:bg-slate-50">

        <td class="text-center">
            <input type="checkbox" class="employee-check" value="${emp.emp_id}">
        </td>
        <td>${emp.emp_id}</td>
        <td>${emp.emp_firstname}</td>
        <td>${emp.emp_lastname}</td>
        <td>${emp.emp_gender}</td>
        <td>${emp.emp_date_of_birth}</td>
        <td>${emp.emp_contact_number ?? ""}</td>
        <td>${emp.emp_position}</td>
        <td>₱${emp.emp_hourly_rate}</td>
        <td>${createStatusBadge(emp.emp_status)}</td>
        <td class="text-center">

            <button
                class="btn-edit text-blue-600"
                data-id="${emp.emp_id}">

                <i class="bi bi-pencil-square"></i>

            </button>

            <button
                class="btn-delete text-red-600 ml-2"
                data-id="${emp.emp_id}">

                <i class="bi bi-trash"></i>

            </button>

        </td>

    </tr>
    `;

}

function searchEmployee() {

    const keyword = document.getElementById("searchEmployee").value.toLowerCase();

    const filtered = employees.filter(emp =>
        emp.emp_firstname.toLowerCase().includes(keyword) ||
        emp.emp_lastname.toLowerCase().includes(keyword) ||
        emp.emp_position.toLowerCase().includes(keyword)
    );

    renderEmployees(filtered);

}

function filterEmployees() {

    const status = document.getElementById("statusFilter").value;

    if (!status) {
        renderEmployees(employees);
        return;
    }

    renderEmployees(
        employees.filter(emp => emp.emp_status === status)
    );

}

function openEmployeeModal() {

    clearForm();

    document.getElementById("employeeModalTitle").textContent = "Add Employee";

    document.getElementById("employeeModal")
        .classList.remove("hidden");

    document.getElementById("employeeModal")
        .classList.add("flex");

}

function closeEmployeeModal() {

    document.getElementById("employeeModal")
        .classList.add("hidden");

    document.getElementById("employeeModal")
        .classList.remove("flex");

}

function closeEmployeeModal() {

    document.getElementById("employeeModal")
        .classList.add("hidden");

    document.getElementById("employeeModal")
        .classList.remove("flex");

}

function selectEmployee(id) {

    selectedEmployee = employees.find(emp => emp.emp_id == id);

    if (!selectedEmployee) return;

    firstName.value = selectedEmployee.emp_firstname;
    lastName.value = selectedEmployee.emp_lastname;
    dob.value = selectedEmployee.emp_date_of_birth;
    contactNumber.value = selectedEmployee.emp_contact_number;
    position.value = selectedEmployee.emp_position;
    hourlyRate.value = selectedEmployee.emp_hourly_rate;
    status.value = selectedEmployee.emp_status;

    document.getElementById(
        selectedEmployee.emp_gender.toLowerCase()
    ).checked = true;

    document.getElementById("employeeModalTitle").textContent =
        "Edit Employee";

    document.getElementById("employeeModal")
        .classList.remove("hidden");

    document.getElementById("employeeModal")
        .classList.add("flex");

}

async function saveEmployee(event) {

    event.preventDefault();

    if (selectedEmployee) {

        await updateEmployee();

    } else {

        await addEmployee();

    }

}

function getEmployeeData() {

    return {

        emp_firstname: firstName.value.trim(),
        emp_lastname: lastName.value.trim(),
        emp_date_of_birth: dob.value,
        emp_contact_number: contactNumber.value.trim(),
        emp_position: position.value.trim(),
        emp_hourly_rate: hourlyRate.value,
        emp_status: status.value,
        emp_gender: document.querySelector(
            "input[name='gender']:checked"
        ).value

    };

}

function clearForm() {

    selectedEmployee = null;

    employeeForm.reset();

    male.checked = true;

    status.value = "Active";

}

async function addEmployee() {

    try {

        const response = await fetch(employeeAPI, {

            method: "POST",

            headers: {

                "Content-Type": "application/json"

            },

            body: JSON.stringify(getEmployeeData())

        });

        const result = await response.json();

        alert(result.message);

        closeEmployeeModal();

        loadEmployees();

    }

    catch (error) {

        console.error(error);

    }

}

async function updateEmployee() {

    try {

        const response = await fetch(employeeAPI, {

            method: "PUT",

            headers: {

                "Content-Type": "application/json"

            },

            body: JSON.stringify({

                emp_id: selectedEmployee.emp_id,

                ...getEmployeeData()

            })

        });

        const result = await response.json();

        alert(result.message);

        closeEmployeeModal();

        loadEmployees();

    }

    catch (error) {

        console.error(error);

    }

}

async function deleteEmployee(id) {

    if (!confirm("Delete this employee?")) return;

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

        loadEmployees();

    }

    catch (error) {

        console.error(error);

    }

}

document.addEventListener("click", event => {

    const edit = event.target.closest(".btn-edit");

    if (edit) {

        selectEmployee(edit.dataset.id);

        return;

    }

    const del = event.target.closest(".btn-delete");

    if (del) {

        deleteEmployee(del.dataset.id);

    }

});

