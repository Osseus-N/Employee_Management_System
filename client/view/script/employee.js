const EMPLOYEE_API = "../../../server/router/employee.php";
const LOGOUT_API = "../../../server/router/index.php";

// Security shit din
if (!sessionStorage.getItem("role")) {
    window.location.href = "../login/login.html";
}

async function logout() {
    if (!confirm("Logout?")) return;
    try {
        await fetch(LOGOUT_API, { method: "DELETE", credentials: "same-origin" });
    } catch (error) {
        console.error(error);
    }
    sessionStorage.clear();
    window.location.href = "../login/login.html";
}

document.addEventListener("DOMContentLoaded", function () {
    const name = sessionStorage.getItem("firstname");
    if (name) document.getElementById("welcomeGreeting").textContent = "Welcome, " + name;

    //Para makita din ng admin yung kanya
    if (sessionStorage.getItem("role") === "admin") {
        document.getElementById("adminReturnBtn").classList.remove("hidden");
    }

    loadProfile();
    loadMyAttendance();
    loadMyPayroll();

    document.getElementById("profileForm").addEventListener("submit", saveProfile);
    document.getElementById("btnMarkPresent").addEventListener("click", clockIn);
    document.getElementById("btnClockOut").addEventListener("click", clockOut);
});

//PROFILE
async function loadProfile() {
    try {
        const response = await fetch(EMPLOYEE_API, { credentials: "same-origin" });
        const result = await response.json();

        if (!result.success) {
            alert(result.message);
            return;
        }

        const emp = result.data;
        document.getElementById("empFirstName").value = emp.emp_firstname;
        document.getElementById("empLastName").value = emp.emp_lastname;
        document.getElementById("empContact").value = emp.emp_contact_number ?? "";
        document.getElementById("empPosition").textContent = emp.emp_position;
        document.getElementById("empStatus").textContent = emp.emp_status;
    } catch (error) {
        console.error(error);
        alert("Cannot connect to server.");
    }
}

//Save of profile
async function saveProfile(event) {
    event.preventDefault();

    const firstname = document.getElementById("empFirstName").value;
    const lastname = document.getElementById("empLastName").value;
    const contact = document.getElementById("empContact").value;

    if (isEmpty(firstname) || isEmpty(lastname)) {
        alert("First and last name are required.");
        return;
    }
    if (!validatePhone(contact)) return;

    try {
        const response = await fetch(EMPLOYEE_API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({
                emp_firstname: firstname,
                emp_lastname: lastname,
                emp_contact_number: contact
            })
        });
        const result = await response.json();
        alert(result.message);
    } catch (error) {
        console.error(error);
        alert("Unable to update profile.");
    }
}
