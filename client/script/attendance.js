
document.addEventListener("DOMContentLoaded", function () {
    const btnMarkPresent = document.getElementById("btnMarkPresent");
    if (!btnMarkPresent) return;
    btnMarkPresent.addEventListener("click", markPresent);
});

async function loadMyAttendance() {
    const calendar = document.getElementById("attendanceCalendar");
    if (!calendar) return;

    try {
        const response = await fetch(ATTENDANCE_SELF_API, { credentials: "same-origin" });
        const result = await response.json();

        calendar.innerHTML = "";

        const attendanceMap = {};
        if (result.success) {
            result.data.forEach(att => {
                attendanceMap[att.att_work_date] = att;
            });
        }

        renderCalendar(calendar, attendanceMap);
    } catch (error) {
        console.error(error);
    }
}

async function markPresent() {
    try {
        const response = await fetch(ATTENDANCE_SELF_API, {
            method: "POST",
            credentials: "same-origin"
        });

        const result = await response.json();

        alert(result.message);

        if (result.success) {
            loadMyAttendance();
        }
    } catch (error) {
        console.error("Mark present request failed:", error);
        alert("Unable to mark attendance.");
    }
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
