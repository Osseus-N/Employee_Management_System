async function loadAttendanceHeatmap(empId, containerId = "attendanceHeatmap") {
    try {
        const response = await fetch(`../../server/attendance/attendanceController.php?action=getEmployeeAttendance&emp_id=${empId}`);
        const result = await response.json();

        if (!result.success || !result.data || result.data.length === 0) {
            document.getElementById(containerId).innerHTML = "<p class='text-muted'>No attendance records found.</p>";
            return;
        }

        const attendanceList = result.data;

        const attendanceMap = {};
        const dates = [];

        attendanceList.forEach(item => {
            attendanceMap[item.date] = item.status.toLowerCase();
            dates.push(new Date(item.date));
        });

        const minDate = new Date(Math.min(...dates));
        const maxDate = new Date(Math.max(...dates));

        const gridContainer = document.getElementById(containerId);
        gridContainer.innerHTML = ""; // Clear existing contents

        let currentDate = new Date(minDate);

        while (currentDate <= maxDate) {
            const dateStr = currentDate.toISOString().split('T')[0];
            const status = attendanceMap[dateStr] || "no-record";

            // Create individual square/box
            const box = document.createElement("div");
            box.className = `attendance-box status-${status}`;

            box.title = `${dateStr}: ${status.toUpperCase()}`;

            gridContainer.appendChild(box);

            currentDate.setDate(currentDate.getDate() + 1);
        }

    } catch (error) {
        console.error("Error loading attendance heatmap:", error);
        document.getElementById(containerId).innerHTML = "<p class='text-danger'>Failed to load attendance grid.</p>";
    }


}
