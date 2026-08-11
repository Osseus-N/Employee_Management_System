const MONTH_NAMES = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

const currentPath = window.location.pathname;
const isLoginPage = currentPath === "/employee_management_system" || currentPath === "/employee_management_system/login";

if (!sessionStorage.getItem("role") && !isLoginPage) {
    window.location.href = "/employee_management_system/logout";
}
document.addEventListener("DOMContentLoaded", function () {

    const name = sessionStorage.getItem("user");
    const welcomeGreeting = document.getElementById("welcomeGreeting");

    if (name && welcomeGreeting) {
        welcomeGreeting.textContent = "Welcome, " + name;
    }
    showAdminReturnIfApplicable();

    if (document.getElementById("attendanceCalendar") || document.getElementById("employeeTableBody")) {
        console.log("hello")
        loadDashboard();
    }

    // Form submission handlers
    const profileForm = document.getElementById("profileForm");
    if (profileForm) profileForm.addEventListener("submit", saveProfile);

    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) logoutBtn.addEventListener("click", logout);
});

function showAdminReturnIfApplicable() {
    const role = sessionStorage.getItem("role");
    const adminReturnBtn = document.getElementById("adminReturnBtn");
    if (role === "admin" && adminReturnBtn) {
        adminReturnBtn.classList.remove("d-none");
    }
}

async function loadDashboard() {
    try {
        const response = await fetch(`${EMPLOYEE_API}/data`, {
            method: "GET",
            credentials: "same-origin"
        });

        if (response.status === 401 || response.status === 403) {
            sessionStorage.clear();
            window.location.href = "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            alert(result.message || "Failed to load dashboard data.");
            return;
        }

        const {user, attendance, payroll} = result.data;

        if (user) {
            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val ?? "";
            };

            setVal("empFirstName", user.emp_firstname);
            setVal("empLastName", user.emp_lastname);
            setVal("empEmail", user.email);
            setVal("empPhone", user.emp_contact_number);
            setVal("empContact", user.emp_contact_number);
            setVal("empAddress", user.emp_address);
        }

        const attendanceMap = {};
        if (Array.isArray(attendance)) {
            attendance.forEach(att => {
                if (att.att_work_date) {
                    attendanceMap[att.att_work_date] = att;
                }
            });
        }

        renderCalendar(document.getElementById("attendanceCalendar"), attendanceMap);
        renderPayrollTable(payroll);

    } catch (error) {
        console.error("Dashboard Load Error:", error);
        alert("Cannot connect to server.");
    }
}

    function renderPayrollTable(payroll) {
        const table = document.getElementById("payrollTableBody");
        if (!table) return;

        table.innerHTML = "";

        if (!payroll || payroll.length === 0) {
            table.innerHTML = "<tr><td colspan='4' class='text-center py-3 text-muted'>No payroll records yet.</td></tr>";
            return;
        }

        payroll.forEach(pay => {
            const row = document.createElement("tr");
            row.innerHTML = `
            <td class="px-3 py-2">${pay.payroll_start_date} → ${pay.payroll_end_date}</td>
            <td class="px-3 py-2">${Number(pay.pay_total_days)}</td>
            <td class="px-3 py-2">₱${Number(pay.pay_amount).toFixed(2)}</td>
            <td class="px-3 py-2">${payStatusBadge(pay.pay_status)}</td>
        `;
            table.appendChild(row);
        });
    }

    function renderCalendar(container, attendanceMap) {
        if (!container) return;
        container.innerHTML = "";

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const year = today.getFullYear();
        const month = today.getMonth();

        const header = document.createElement("div");
        header.className = "attendance-month-header";
        header.textContent = `${MONTH_NAMES[month]} ${year}`;
        container.appendChild(header);

        const weekdayRow = document.createElement("div");
        weekdayRow.className = "attendance-grid attendance-weekdays";
        ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"].forEach(day => {
            const label = document.createElement("span");
            label.className = "attendance-weekday-label";
            label.textContent = day;
            weekdayRow.appendChild(label);
        });
        container.appendChild(weekdayRow);

        const grid = document.createElement("div");
        grid.className = "attendance-grid";

        const firstDayOfWeek = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let b = 0; b < firstDayOfWeek; b++) {
            const blankCell = document.createElement("span");
            blankCell.className = "attendance-cell blank";
            grid.appendChild(blankCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const date = new Date(year, month, day);
            const dayOfWeek = date.getDay();
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
            const isFuture = date > today;

            const cell = document.createElement("span");
            cell.title = dateStr;

            const dayBox = document.createElement("span");
            dayBox.className = "attendance-day-box";
            dayBox.textContent = day;
            cell.appendChild(dayBox);

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

            grid.appendChild(cell);
        }

        const totalCells = firstDayOfWeek + daysInMonth;
        const trailingBlanks = (7 - (totalCells % 7)) % 7;
        for (let t = 0; t < trailingBlanks; t++) {
            const blankCell = document.createElement("span");
            blankCell.className = "attendance-cell blank";
            grid.appendChild(blankCell);
        }

        container.appendChild(grid);
    }

    function payStatusBadge(status) {
        const isPaid = status?.toLowerCase() === "paid";
        const bgClass = isPaid ? "bg-success text-white" : "bg-warning text-dark";
        return `<span class="badge ${bgClass}">${status ?? 'Pending'}</span>`;
    }

    async function saveProfile(event) {
        event.preventDefault();

        const firstname = document.getElementById("empFirstName")?.value || "";
        const lastname = document.getElementById("empLastName")?.value || "";
        const contact = document.getElementById("empContact")?.value || document.getElementById("empPhone")?.value || "";
        const email = document.getElementById("empEmail")?.value || "";
        const address = document.getElementById("empAddress")?.value || "";

        try {

            $isValid = validateProfileForm(firstname, lastname, email, contact, address)

            if ($isValid === false) {
                return;
            }

            const payload = {
                emp_firstname: firstname,
                emp_lastname: lastname,
                emp_contact_number: contact,
                emp_address: address,
                acc_email: email
            };


            const response = await fetch(`${EMPLOYEE_API}/update`, {
                method: "PUT",
                headers: {"Content-Type": "application/json"},
                credentials: "same-origin",
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            alert(result.message);
        } catch (error) {
            console.error(error);
            alert("Unable to update profile.");
        }
    }



