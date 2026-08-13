const MONTH_NAMES = [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
];

const currentPath = window.location.pathname;
const isLoginPage =
    currentPath === "/employee_management_system" ||
    currentPath === "/employee_management_system/login";

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

    if (
        document.getElementById("attendanceCalendar") ||
        document.getElementById("employeeTableBody")
    ) {
        loadDashboard();
    }

    const profileForm = document.getElementById("profileForm");

    if (profileForm) {
        profileForm.addEventListener("submit", saveProfile);
    }

    setupLogoutModal();
});

function showAdminReturnIfApplicable() {
    const role = sessionStorage.getItem("role");
    const adminReturnBtn = document.getElementById("adminReturnBtn");

    if (role === "admin" && adminReturnBtn) {
        adminReturnBtn.classList.remove("hidden");
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

        const { user, attendance, payroll } = result.data;

        if (user) {
            const setVal = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value ?? "";
                }
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

        renderCalendar(
            document.getElementById("attendanceCalendar"),
            attendanceMap
        );

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
        table.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5 text-gray-500">
                    No payroll records yet.
                </td>
            </tr>
        `;
        return;
    }

    payroll.forEach(pay => {
        const row = document.createElement("tr");
        row.className = "border-b border-gray-200 hover:bg-gray-50";

        row.innerHTML = `
            <td class="px-3 py-3">
                ${pay.payroll_start_date} → ${pay.payroll_end_date}
            </td>
            <td class="px-3 py-3">
                ${Number(pay.pay_total_days)}
            </td>
            <td class="px-3 py-3">
                ₱${Number(pay.pay_amount).toFixed(2)}
            </td>
            <td class="px-3 py-3">
                ${payStatusBadge(pay.pay_status)}
            </td>
        `;

        table.appendChild(row);
    });
}

function payStatusBadge(status) {
    const isPaid = status?.toLowerCase() === "paid";
    const bgClass = isPaid ? "bg-green-600 text-white" : "bg-yellow-400 text-gray-900";

    return `
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${bgClass}">
            ${status ?? "Pending"}
        </span>
    `;
}

function renderCalendar(container, attendanceMap) {
    if (!container) return;

    container.innerHTML = "";

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const year = today.getFullYear();
    const month = today.getMonth();

    const header = document.createElement("div");
    header.className = "text-base font-semibold text-gray-900 mb-3";
    header.textContent = `${MONTH_NAMES[month]} ${year}`;
    container.appendChild(header);

    const weekdayRow = document.createElement("div");
    weekdayRow.className = "grid grid-cols-7 gap-2 w-fit mb-2";

    ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"].forEach(day => {
        const label = document.createElement("span");
        label.className = "w-9 h-7 flex items-center justify-center text-xs text-gray-600";
        label.textContent = day;
        weekdayRow.appendChild(label);
    });

    container.appendChild(weekdayRow);

    const grid = document.createElement("div");
    grid.className = "grid grid-cols-7 gap-2 w-fit";

    const firstDayOfWeek = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let b = 0; b < firstDayOfWeek; b++) {
        const blankCell = document.createElement("span");
        blankCell.className = "w-9 h-9";
        grid.appendChild(blankCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
        const date = new Date(year, month, day);
        const dayOfWeek = date.getDay();

        const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
        const isFuture = date > today;

        const cell = document.createElement("span");
        cell.className = "w-9 h-9 flex items-center justify-center";
        cell.title = dateStr;

        const dayBox = document.createElement("span");
        dayBox.textContent = day;
        dayBox.className = "w-9 h-9 flex items-center justify-center rounded-md text-xs";

        cell.appendChild(dayBox);

        if (isFuture) {
            dayBox.className = "w-9 h-9 flex items-center justify-center rounded-md bg-gray-200 text-gray-700";
        } else if (attendanceMap[dateStr]) {
            dayBox.className = "w-9 h-9 flex items-center justify-center rounded-md bg-green-600 text-white";
            cell.title += " — Present";
        } else if (isWeekend) {
            dayBox.className = "w-9 h-9 flex items-center justify-center rounded-md bg-yellow-500 text-gray-900";
            cell.title += " — Weekend";
        } else {
            dayBox.className = "w-9 h-9 flex items-center justify-center rounded-md bg-red-500 text-white";
            cell.title += " — Absent";
        }

        grid.appendChild(cell);
    }

    const totalCells = firstDayOfWeek + daysInMonth;
    const trailingBlanks = (7 - (totalCells % 7)) % 7;

    for (let t = 0; t < trailingBlanks; t++) {
        const blankCell = document.createElement("span");
        blankCell.className = "w-9 h-9";
        grid.appendChild(blankCell);
    }

    container.appendChild(grid);
}

async function saveProfile(event) {
    event.preventDefault();

    const firstname = document.getElementById("empFirstName")?.value || "";
    const lastname = document.getElementById("empLastName")?.value || "";
    const contact = document.getElementById("empContact")?.value ||
        document.getElementById("empPhone")?.value || "";
    const email = document.getElementById("empEmail")?.value || "";
    const address = document.getElementById("empAddress")?.value || "";

    try {
        const isValid = validateProfileForm(
            firstname,
            lastname,
            email,
            contact,
            address
        );

        if (isValid === false) return;

        const payload = {
            emp_firstname: firstname,
            emp_lastname: lastname,
            emp_contact_number: contact,
            emp_address: address,
            acc_email: email
        };

        const response = await fetch(`${EMPLOYEE_API}/update`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
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

function setupLogoutModal() {
    const logoutBtn = document.getElementById("btnLogout");
    const logoutModal = document.getElementById("logoutModal");
    const closeBtn = document.getElementById("closeLogoutModal");
    const cancelBtn = document.getElementById("cancelLogoutBtn");
    const confirmBtn = document.getElementById("confirmLogoutBtn");

    if (!logoutBtn || !logoutModal) return;

    function openModal() {
        logoutModal.classList.remove("hidden");
        logoutModal.classList.add("flex");
    }

    function closeModal() {
        logoutModal.classList.remove("flex");
        logoutModal.classList.add("hidden");
    }

    logoutBtn.addEventListener("click", openModal);

    if (closeBtn) closeBtn.addEventListener("click", closeModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModal);

    if (confirmBtn) {
        confirmBtn.addEventListener("click", function () {
            sessionStorage.clear();
            window.location.href = "/employee_management_system/logout";
        });
    }

    logoutModal.addEventListener("click", function (event) {
        if (event.target === logoutModal) {
            closeModal();
        }
    });
}