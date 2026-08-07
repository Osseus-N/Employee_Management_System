const ATTENDANCE_SELF_API = "/employee_management_system/index.php?action=markPresent";

document.addEventListener("DOMContentLoaded", function () {
    const btnMarkPresent = document.getElementById("btnMarkPresent");
    if (!btnMarkPresent) return;
    btnMarkPresent.addEventListener("click", markPresent);
});

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
