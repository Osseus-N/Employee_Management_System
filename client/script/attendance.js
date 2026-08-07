const ATTENDANCE_SELF_API = "/employee_management_system/employee/action=markPresent";

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
