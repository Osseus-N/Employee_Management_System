const EMPLOYEE_API   = "/employee_management_system/employee";

if (!sessionStorage.getItem("role")) {
    window.location.href = "/employee_management_system/";
}

document.addEventListener("DOMContentLoaded", function () {
    const name = sessionStorage.getItem("firstname");
    if (name) document.getElementById("welcomeGreeting").textContent = "Welcome, " + name;

    if (sessionStorage.getItem("role") === "admin") {
        document.getElementById("adminReturnBtn").classList.remove("hidden");
    }

    loadDashboard();

    document.getElementById("profileForm").addEventListener("submit", saveProfile);

    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) logoutBtn.addEventListener("click", logout);
});

async function loadDashboard() {
    try {
        const response = await fetch(`${EMPLOYEE_API}/data`, {
            method: "GET",
            credentials: "same-origin"
        });
        const result = await response.json();

        if (!result.success) {
            alert(result.message);
            return;
        }

        const { user, attendance, payroll } = result.data;

        document.getElementById("empFirstName").value = user.emp_firstname;
        document.getElementById("empLastName").value = user.emp_lastname;
        document.getElementById("empContact").value = user.emp_contact_number ?? "";
        document.getElementById("empPosition").textContent = user.emp_position;
        document.getElementById("empStatus").textContent = user.emp_status;

        const attendanceMap = {};
        attendance.forEach(att => attendanceMap[attendance.att_work_date] = att);
        renderCalendar(document.getElementById("attendanceCalendar"), attendanceMap);

        renderPayrollTable(payroll);
    } catch (error) {
        console.error(error);
        alert("Cannot connect to server.");
    }
}
        const MONTH_NAMES = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        function renderPayrollTable(payroll) {
            const table = document.getElementById("payrollTableBody");
            if (!table) return;

            table.innerHTML = "";

            if (!payroll || payroll.length === 0) {
                table.innerHTML = "<tr><td colspan='4' class='text-center py-6 text-gray-400'>No payroll records yet.</td></tr>";
                return;
            }

            payroll.forEach(pay => {
                const row = document.createElement("tr");
                row.innerHTML = `
            <td class="px-5 py-3">${pay.pay_period_start} → ${pay.pay_period_end}</td>
            <td class="px-5 py-3">${pay.pay_total_hours}</td>
            <td class="px-5 py-3">₱${Number(pay.pay_amount).toFixed(2)}</td>
            <td class="px-5 py-3">${payStatusBadge(pay.pay_status)}</td>
        `;
                table.appendChild(row);
            });
        }

function renderCalendar(container, attendanceMap) {
    const year = new Date().getFullYear();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let month = 0; month < 12; month++) {
        const row = document.createElement("div");
        row.className = "attendance-row";

        const label = document.createElement("span");
        label.className = "attendance-month-label";
        label.textContent = MONTH_NAMES[month];
        row.appendChild(label);

        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dateStr = date.toISOString().split("T")[0]; // YYYY-MM-DD
            const dayOfWeek = date.getDay(); // 0 = Sun, 6 = Sat
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
            const isFuture = date > today;

            const cell = document.createElement("span");
            cell.title = dateStr;

            if (isFuture) {
                cell.className = "attendance-cell empty";
            } else if (attendanceMap[dateStr]) {
                cell.className = "attendance-cell present";
                cell.title += " — Present";
            } else if (isWeekend) {
                cell.className = "attendance-cell weekend";
                cell.title += " — Weekend";
            } else {
                cell.className = "attendance-cell absent";
                cell.title += " — Absent";
            }

            row.appendChild(cell);
        }

        container.appendChild(row);
    }
}
