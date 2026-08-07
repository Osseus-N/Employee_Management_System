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
