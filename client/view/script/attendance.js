const attendanceAPI = "../../../server/attendance/attendanceController.php";
let attendances = [];

document.addEventListener("DOMContentLoaded", initializeAttendance);

function initializeAttendance() {
    bindAttendanceEvents();
    loadAttendance();
}

function bindAttendanceEvents() {

    document.getElementById("btnRefreshAttendance")
        ?.addEventListener("click", loadAttendance);

    document.getElementById("attendanceSearch")
        ?.addEventListener("input", searchAttendance);

    document.getElementById("attendanceDateFilter")
        ?.addEventListener("change", filterAttendance);

}

async function loadAttendance() {

    try {

        const response = await fetch(attendanceAPI);

        const result = await response.json();

        if (!result.success) {
            attendances = [];
            renderAttendance([]);
            return;
        }
        attendances = result.data;
        renderAttendance(attendances);
    }
    catch (error) {
        console.error(error);

    }

}

function renderAttendance(data) {
    const table = document.getElementById("attendanceTableBody");
    table.innerHTML = "";

    if (!data.length) {
        table.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-6">
                    No attendance records found.
                </td>
            </tr>
        `;
        return;

    }

    data.forEach(record =>
        table.insertAdjacentHTML("beforeend", createAttendanceRow(record))
    );

}

function createAttendanceRow(record) {

    return `
    <tr class="border-b hover:bg-slate-50">
        <td class="p-3">${record.att_id}</td>
        <td class="p-3">${record.emp_firstname} ${record.emp_lastname}</td>
        <td class="p-3">${record.att_date}</td>
        <td class="p-3">${record.time_in ?? "-"}</td>
        <td class="p-3">${record.time_out ?? "-"}</td>
        <td class="p-3">${record.hours_worked ?? 0}</td>
        <td class="p-3">${record.att_status}</td>
    </tr>
    `;

}

function searchAttendance() {

    const keyword = document.getElementById("attendanceSearch")
        .value
        .toLowerCase();

    const filtered = attendances.filter(record =>
        `${record.emp_firstname} ${record.emp_lastname}`
            .toLowerCase()
            .includes(keyword)
    );
    renderAttendance(filtered);
}

function filterAttendance() {
    const date = document.getElementById("attendanceDateFilter").value;

    if (!date) {
        renderAttendance(attendances);
        return;

    }
    renderAttendance(
        attendances.filter(record => record.att_date === date)
    );

}