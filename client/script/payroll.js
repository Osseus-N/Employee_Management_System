let payrollSchedules = [];
let payModalInstance = null;
let scheduleManagerModalInstance = null;

document.addEventListener("DOMContentLoaded", function () {
    const payrollModal = document.getElementById("payrollModal");
    if (payrollModal) {
        payModalInstance = new bootstrap.Modal(payrollModal);
    }

    const scheduleManagerModal = document.getElementById("scheduleManagerModal");
    if (scheduleManagerModal) {
        scheduleManagerModalInstance = new bootstrap.Modal(scheduleManagerModal);
    }

    const payrollForm = document.getElementById("payrollForm");
    if (payrollForm) {
        payrollForm.addEventListener("submit", handlePayrollSubmit);
    }

    loadPayrollSchedules();
});

async function loadPayrollSchedules() {
    try {
        const response = await fetch(PAYROLL_SCHEDULE_API, {
            credentials: "same-origin"
        });
        const result = await response.json();

        if (result.success) {
            payrollSchedules = result.data || [];
            renderScheduleManager();
        } else {
            console.error(result.message || "Failed to load payroll schedules.");
        }
    } catch (error) {
        console.error("Payroll schedule error:", error);
    }
}

function openCreateScheduleModal() {
    const title = document.getElementById("payrollModalTitle");
    if (title) {
        title.innerHTML = `<i class="bi bi-calendar-plus me-2"></i>Create Payroll Schedule`;
    }

    renderScheduleForm();

    if (payModalInstance) {
        payModalInstance.show();
    }
}

function renderScheduleForm(schedule = null) {
    const container = document.getElementById("payrollFormContent");
    if (!container) return;

    const isEdit = schedule !== null;

    container.innerHTML = `
        <input type="hidden" id="scheduleId" value="${isEdit ? schedule.schedule_id : ""}">
        <div class="mb-3">
            <label for="payBeginning" class="form-label">Pay Beginning</label>
            <input type="date" class="form-control" id="payBeginning" value="${isEdit ? schedule.payroll_start_date : ""}" required>
        </div>
        <div class="mb-3">
            <label for="payEnd" class="form-label">Pay End</label>
            <input type="date" class="form-control" id="payEnd" value="${isEdit ? schedule.payroll_end_date : ""}" required>
        </div>
    `;
}

function openPayScheduleModal() {
    const unpaidSchedules = payrollSchedules.filter(
        schedule => schedule.status === "Open"
    );

    if (unpaidSchedules.length === 0) {
        alert("There are no unpaid payroll schedules.");
        return;
    }

    const container = document.getElementById("payrollFormContent");
    if (!container) return;

    const scheduleOpts = unpaidSchedules
        .map(schedule => `
            <option value="${schedule.schedule_id}">
                ${schedule.payroll_start_date} → ${schedule.payroll_end_date}
            </option>
        `)
        .join("");

    container.innerHTML = `
        <div class="mb-3">
            <label for="payrollScheduleId" class="form-label">Payroll Schedule</label>
            <select class="form-select" id="payrollScheduleId" required>
                <option value="">Select Unpaid Payroll Schedule</option>
                ${scheduleOpts}
            </select>
        </div>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            This will process payroll for <strong>ALL employees</strong> under the selected payroll schedule.
        </div>
    `;

    const title = document.getElementById("payrollModalTitle");
    if (title) {
        title.innerHTML = `<i class="bi bi-cash-stack me-2"></i>Pay All Employees`;
    }

    if (payModalInstance) {
        payModalInstance.show();
    }
}

function handlePayrollSubmit(event) {
    event.preventDefault();

    const scheduleId = document.getElementById("scheduleId")?.value;
    const payBeginning = document.getElementById("payBeginning")?.value;
    const payEnd = document.getElementById("payEnd")?.value;
    const payScheduleId = document.getElementById("payrollScheduleId")?.value;

    if (payScheduleId) {
        handlePayScheduleSubmit();
        return;
    }

    if (!payBeginning || !payEnd) {
        alert("Please select the payroll start and end dates.");
        return;
    }

    if (payBeginning > payEnd) {
        alert("Payroll start date cannot be later than the end date.");
        return;
    }

    if (scheduleId) {
        showConfirmation(
            "Update Payroll Schedule",
            `Update payroll schedule from ${payBeginning} to ${payEnd}?`,
            () => updatePayrollSchedule(scheduleId, payBeginning, payEnd)
        );
        return;
    }

    showConfirmation(
        "Create Payroll Schedule",
        `Create payroll schedule from ${payBeginning} to ${payEnd}?`,
        () => createPayrollSchedule(payBeginning, payEnd)
    );
}

async function createPayrollSchedule(payBeginning, payEnd) {
    const payload = {
        payroll_start_date: payBeginning,
        payroll_end_date: payEnd
    };

    try {
        const response = await fetch(PAYROLL_SCHEDULE_API, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            if (payModalInstance) {
                payModalInstance.hide();
            }
            await loadPayrollSchedules();
        } else {
            alert(result.message || "Failed to create payroll schedule.");
        }
    } catch (error) {
        console.error("Create schedule error:", error);
        alert("An error occurred while creating the payroll schedule.");
    }
}

async function handlePayScheduleSubmit() {
    const scheduleId = document.getElementById("payrollScheduleId")?.value;

    if (!scheduleId) {
        alert("Please select an unpaid payroll schedule.");
        return;
    }

    const schedule = payrollSchedules.find(
        schedule => schedule.schedule_id == scheduleId
    );

    if (!schedule) {
        alert("Payroll schedule not found.");
        return;
    }

    if (schedule.status !== "Open") {
        alert("This payroll schedule has already been processed.");
        return;
    }

    showConfirmation(
        "Process Payroll",
        `Are you sure you want to process payroll for ALL employees for ${schedule.payroll_start_date} to ${schedule.payroll_end_date}?`,
        () => processPayrollSchedule(scheduleId)
    );
}

async function processPayrollSchedule(scheduleId) {
    const payload = {
        schedule_id: scheduleId
    };

    try {
        const response = await fetch(`${PAYROLL_API}/pay`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            if (payModalInstance) {
                payModalInstance.hide();
            }
            await loadPayrollSchedules();
            alert("Payroll processed successfully.");
        } else {
            alert(result.message || "Failed to process payroll.");
        }
    } catch (error) {
        console.error("Payroll processing error:", error);
        alert("An error occurred while processing payroll.");
    }
}

function openScheduleManager() {
    renderScheduleManager();
    if (scheduleManagerModalInstance) {
        scheduleManagerModalInstance.show();
    }
}

function renderScheduleManager() {
    const container = document.getElementById("scheduleManagerContent");
    if (!container) return;

    if (payrollSchedules.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                No payroll schedules found.
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pay Beginning</th>
                        <th>Pay End</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${payrollSchedules
        .map(
            schedule => `
                        <tr>
                            <td>${schedule.schedule_id}</td>
                            <td>${schedule.payroll_start_date}</td>
                            <td>${schedule.payroll_end_date}</td>
                            <td>
                                <span class="badge ${getScheduleStatusClass(schedule.status)}">
                                    ${escapeHtml(schedule.status)}
                                </span>
                            </td>
                            <td>
                                ${
                schedule.status !== "Processed"
                    ? `
                                        <button type="button" class="btn btn-sm btn-warning me-1" onclick="editPayrollSchedule(${schedule.schedule_id})">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deletePayrollSchedule(${schedule.schedule_id})">
                                        <i class="bi bi-trash"></i> Delete
                                        </button>
                                    `: `
                                        <span class="text-muted">Processed</span>
                                    `}
                            </td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        </div>
    `;
}

function getScheduleStatusClass(status) {
    switch (status) {
        case "Unpaid":
            return "bg-warning text-dark";
        case "Processed":
            return "bg-success";
        default:
            return "bg-secondary";
    }
}

function editPayrollSchedule(scheduleId) {
    const schedule = payrollSchedules.find(
        schedule => schedule.schedule_id == scheduleId
    );

    if (!schedule) {
        alert("Payroll schedule not found.");
        return;
    }

    if (schedule.status === "Processed") {
        alert("Processed payroll schedules cannot be edited.");
        return;
    }

    const title = document.getElementById("payrollModalTitle");
    if (title) {
        title.innerHTML = `<i class="bi bi-pencil me-2"></i>Edit Payroll Schedule`;
    }

    renderScheduleForm(schedule);

    if (scheduleManagerModalInstance) {
        scheduleManagerModalInstance.hide();
    }

    if (payModalInstance) {
        payModalInstance.show();
    }
}

async function updatePayrollSchedule(scheduleId, payBeginning, payEnd) {
    const payload = {
        schedule_id: scheduleId,
        payroll_start_date: payBeginning,
        payroll_end_date: payEnd
    };

    try {
        const response = await fetch(PAYROLL_SCHEDULE_API, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            if (payModalInstance) {
                payModalInstance.hide();
            }
            await loadPayrollSchedules();
            if (scheduleManagerModalInstance) {
                scheduleManagerModalInstance.show();
            }
        } else {
            alert(result.message || "Failed to update payroll schedule.");
        }
    } catch (error) {
        console.error("Update schedule error:", error);
        alert("An error occurred while updating the payroll schedule.");
    }
}

async function deletePayrollSchedule(scheduleId) {
    const schedule = payrollSchedules.find(
        schedule => schedule.schedule_id == scheduleId
    );

    if (!schedule) {
        alert("Payroll schedule not found.");
        return;
    }

    if (schedule.status === "Processed") {
        alert("Processed payroll schedules cannot be deleted.");
        return;
    }

    showConfirmation(
        "Delete Payroll Schedule",
        `Are you sure you want to delete the payroll schedule ${schedule.payroll_start_date} to ${schedule.payroll_end_date}?`,
        async () => {
            try {
                const response = await fetch(
                    `${PAYROLL_SCHEDULE_API}?id=${scheduleId}`,
                    {
                        method: "DELETE",
                        credentials: "same-origin"
                    }
                );

                const result = await response.json();

                if (result.success) {
                    await loadPayrollSchedules();
                } else {
                    alert(result.message || "Failed to delete payroll schedule.");
                }
            } catch (error) {
                console.error("Delete schedule error:", error);
                alert("An error occurred while deleting the payroll schedule.");
            }
        }
    );
}