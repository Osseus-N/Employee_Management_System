// PANG FETCH
const employeeAPI = "../../server/employee/employeeController.php";
const attendanceAPI = "../../server/attendance/attendanceController.php";
const payrollAPI = "../../server/payroll/payrollController.php";

let employee = null;
window.onload = function () {
    loadProfile();
};

//LOAD EMPLOYEE PROFILES
async function loadProfile() {
    try {
        const response = await fetch(employeeAPI);
        const result = await response.json();
        if (result.success) {
            employee = result.data;
            displayProfile(employee);
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.log(error);
        alert("Unable to load profile.");
    }
}

//DISPLAY PROFILE
function displayProfile(emp) {

    if (!emp) return;

    if (document.getElementById("emp_name"))
        document.getElementById("emp_name").innerHTML =
            emp.emp_firstname + " " + emp.emp_lastname;

    if (document.getElementById("emp_position"))
        document.getElementById("emp_position").innerHTML =
            emp.emp_position;

    if (document.getElementById("emp_rate"))
        document.getElementById("emp_rate").innerHTML =
            emp.emp_hourly_rate;

    if (document.getElementById("firstname"))
        document.getElementById("firstname").value =
            emp.emp_firstname;

    if (document.getElementById("lastname"))
        document.getElementById("lastname").value =
            emp.emp_lastname;

    if (document.getElementById("gender"))
        document.getElementById("gender").value =
            emp.emp_gender;

    if (document.getElementById("position"))
        document.getElementById("position").value =
            emp.emp_position;

    if (document.getElementById("hourly_rate"))
        document.getElementById("hourly_rate").value =
            emp.emp_hourly_rate;

}

//PROFILE UPDATE
async function updateProfile() {

    if (!validateProfile()) return;

    const data = {
        emp_firstname: document.getElementById("firstname").value,
        emp_lastname: document.getElementById("lastname").value,
        emp_gender: document.getElementById("gender").value,
        emp_position: document.getElementById("position").value,
        emp_hourly_rate: document.getElementById("hourly_rate").value
    };

    try {
        const response = await fetch(employeeAPI, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            loadProfile();
        }
    } catch (error) {
        console.log(error);
        alert("Unable to update profile.");
    }
}

//VALIDATION

//SAVE BUTTON
const saveButton = document.getElementById("btnSave");
if (saveButton) {
    saveButton.onclick = function () {
        updateProfile();
    };
}

//ATTENDANCE
async function loadAttendance() {
    const table = document.getElementById("attendanceTableBody");

    if (!table) return;
    try {
        const response = await fetch(attendanceAPI);
        const result = await response.json();
        table.innerHTML = "";
        if (!result.success) {
            table.innerHTML =
                "<tr><td colspan='5'>No attendance found.</td></tr>";
            return;
        }

        result.data.forEach(att => {
            table.innerHTML += `

            <tr>
                <td>${att.att_work_date}</td>
                <td>${att.att_clock_in ?? "-"}</td>
                <td>${att.att_clock_out ?? "-"}</td>
                <td>${att.att_total_hours ?? "0"}</td>
            </tr>
            `;
        });
    } catch (error) {
        console.log(error);
        alert("Unable to load attendance.");
    }
}

//CLOCKIN (PAPASOK)
async function clockIn() {
    try {
        const response = await fetch(attendanceAPI, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "clock_in"
            })
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            loadAttendance();
        }
    } catch (error) {
        console.log(error);
        alert("Clock In failed.");
    }
}

//CLOCKOUT(PAGUWI)
async function clockOut() {
    try {
        const response = await fetch(attendanceAPI, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "clock_out"
            })
        });

        const result = await response.json();
        alert(result.message);
        if (result.success) {
            loadAttendance();
        }
    } catch (error) {
        console.log(error);
        alert("Clock Out failed.");
    }
}

//BUTTON
const clockInBtn = document.getElementById("btnClockIn");
if (clockInBtn) {
    clockInBtn.onclick = function () {
        clockIn();
    };
}
const clockOutBtn = document.getElementById("btnClockOut");

if (clockOutBtn) {
    clockOutBtn.onclick = function () {
        clockOut();
    };
}

//DATE FOR TODAY
function showDate() {

    const today = new Date();
    const date = today.toLocaleDateString();

    if (document.getElementById("currentDate")) {
        document.getElementById("currentDate").innerHTML = date;
    }
}

//REFRESH OF ATTENDANCE
function refreshAttendance() {
    loadAttendance();
    showDate();
}
refreshAttendance();

//PAYROLL HISTORY
async function loadPayroll() {
    const table = document.getElementById("payrollTableBody");
    if (!table) return;
    try {
        const response = await fetch(payrollAPI);
        const result = await response.json();

        table.innerHTML = "";
        if (!result.success) {
            table.innerHTML =
                "<tr><td colspan='6'>No payroll records found.</td></tr>";
            return;
        }

        result.data.forEach(pay => {
            table.innerHTML += `

            <tr>
                <td>${pay.pay_period_start}</td>
                <td>${pay.pay_period_end}</td>
                <td>${pay.pay_total_hours}</td>
                <td>₱ ${pay.pay_amount}</td>
                <td>${pay.pay_status}</td>
            </tr>
            `;
        });

    } catch (error) {
        console.log(error);
        alert("Unable to load payroll.");
    }
}

//PAYSLIP DOWNLOAD (OPTIONAL)
function downloadPayslip() {
    alert("Download Payslip feature is not available yet.");
}

//WELCOME NOTIF(OPTIONAL LANG)
function welcomeEmployee() {
    if (!employee) return;
    const welcome = document.getElementById("welcome");
    if (welcome) {
        welcome.innerHTML =
            "Welcome, " +
            employee.emp_firstname +
            " " +
            employee.emp_lastname;
    }
}

//PANG REFRESH SA DASHBOARD
async function refreshDashboard() {
    await loadProfile();
    await loadAttendance();
    await loadPayroll();
    welcomeEmployee();
}

//LOGOUT
async function logout() {
    if (!confirm("Are you sure you want to logout?")) {
        return;
    }
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

//LOGOUT BUTTON
const logoutBtn = document.getElementById("btnLogout");
if (logoutBtn) {
    logoutBtn.onclick = function () {
        logout();
    };
}

//PANG DOWNLOAD (OPTIONAL)
const payslipBtn = document.getElementById("btnPayslip");
if (payslipBtn) {
    payslipBtn.onclick = function () {
        downloadPayslip();
    };
}

//PANG LOAD NG PAGE
window.addEventListener("load", function () {
    refreshDashboard();
});