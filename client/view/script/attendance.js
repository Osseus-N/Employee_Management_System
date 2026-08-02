const ATTENDANCE_SELF_API = "../../../server/router/attendance.php";

async function loadMyAttendance() {
    const table = document.getElementById("attendanceTableBody");
    if (!table) return;

    try {
        const response = await fetch(ATTENDANCE_SELF_API, { credentials: "same-origin" });
        const result = await response.json();
        table.innerHTML = "";

        if (!result.success || result.data.length === 0) {
            table.innerHTML = "<tr><td colspan='4' class='text-center py-6 text-gray-400'>No attendance records yet.</td></tr>";
            return;
        }

        result.data.forEach(att => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="px-4 py-3">${att.att_work_date}</td>
                <td class="px-4 py-3">${att.att_clock_in}</td>
                <td class="px-4 py-3">${att.att_clock_out ?? "—"}</td>
                <td class="px-4 py-3">${att.att_total_hours ?? "—"}</td>
            `;
            table.appendChild(row);
        });
    } catch (error) {
        console.error(error);
    }
}

async function clockIn() {
    try {
        const response = await fetch(ATTENDANCE_SELF_API, {
            method: "POST",
            credentials: "same-origin"
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) loadMyAttendance();
    } catch (error) {
        console.error(error);
        alert("Unable to clock in.");
    }
}

async function clockOut() {
    try {
        const response = await fetch(ATTENDANCE_SELF_API, {
            method: "PUT",
            credentials: "same-origin"
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) loadMyAttendance();
    } catch (error) {
        console.error(error);
        alert("Unable to clock out.");
    }
}
