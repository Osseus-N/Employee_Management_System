const EMPLOYEE_API = "Employee_Management_System/employee/?action=edit";

if (!sessionStorage.getItem("role")) {
    window.location.href = "../view/login.html";
}

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
