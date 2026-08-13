let payrollSchedules = [];

document.addEventListener("DOMContentLoaded", () => {

    const payrollForm = document.getElementById("payrollForm");
    const btnClosePayrollModal = document.getElementById("btnClosePayrollModal");
    const btnCancelPayroll = document.getElementById("btnCancelPayroll");
    const btnCreateSchedule = document.getElementById("btnCreateSchedule");
    const btnPaySchedule = document.getElementById("btnPaySchedule");
    const btnManageSchedules = document.getElementById("btnManageSchedules");
    const btnCloseScheduleManager = document.getElementById("btnCloseScheduleManager");
    const btnConfirmAction = document.getElementById("btnConfirmAction");
    const btnCancelConfirmation = document.getElementById("btnCancelConfirmation");

    if (payrollForm) {
        payrollForm.addEventListener("submit", (event) => {
            handlePayrollSubmit(event);
        });
    }



    if(btnCloseScheduleManager){
        btnCloseScheduleManager.addEventListener("click", closeScheduleManager)
    }
    if (btnClosePayrollModal) {
        btnClosePayrollModal.addEventListener("click", closePayrollModal);
    }

    if (btnCancelPayroll) {
        btnCancelPayroll.addEventListener("click", closePayrollModal);
    }

    if (btnCreateSchedule) {
        btnCreateSchedule.addEventListener("click", openCreateScheduleModal);
    }

    if (btnPaySchedule) {
        btnPaySchedule.addEventListener("click", openPayScheduleModal);
    }

    if (btnManageSchedules) {
        btnManageSchedules.addEventListener("click", openScheduleManager);
    }

    if (btnConfirmAction) {
        btnConfirmAction.addEventListener("click", executeConfirmedAction);
    }

    if (btnCancelConfirmation) {
        btnCancelConfirmation.addEventListener("click", closeConfirmation);
    }

    loadPayrollSchedules();
});
async function loadPayrollSchedules() {
    try {
        const response = await fetch(PAYROLL_SCHEDULE_API, {
            credentials: "same-origin"
        });

        if (response.status === 401 || response.status === 403) {
            window.location.href =
                "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            console.error(
                result.message || "Failed to load payroll schedules."
            );
            return;
        }

        payrollSchedules = result.data || [];

        renderScheduleManager();

    } catch (error) {
        console.error("Payroll schedule error:", error);
        alert("Unable to load payroll schedules.");
    }
}

function openPayrollModal() {
    const modal = document.getElementById("payrollModal");

    if (!modal) return;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    modal.setAttribute("aria-hidden", "false");
}
function closeScheduleManager(){
    const modal = document.getElementById("scheduleManagerModal");

    if(!modal) return;

    modal.classList.add("hidden");
    modal.classList.remove("flex");
    modal.setAttribute("aria-hidden", "true");
}
function closePayrollModal() {
    const modal = document.getElementById("payrollModal");

    if (!modal) return;

    modal.classList.add("hidden");
    modal.classList.remove("flex");
    modal.setAttribute("aria-hidden", "true");

    clearPayrollForm();
}

function clearPayrollForm() {
    const container = document.getElementById("payrollFormContent");

    if (container) {
        container.innerHTML = "";
    }

    const title = document.getElementById("payrollModalTitle");

    if (title) {
        title.innerHTML = "Payroll";
    }
}

function openCreateScheduleModal() {
    const title = document.getElementById("payrollModalTitle");

    if (title) {
        title.innerHTML = `
            <i class="bi bi-calendar-plus mr-2"></i>
            Create Payroll Schedule
        `;
    }

    renderScheduleForm();
    openPayrollModal();
}

function renderScheduleForm(schedule = null) {
    const container = document.getElementById("payrollFormContent");

    if (!container) return;

    const isEdit = schedule !== null;

    container.innerHTML = `
        <input
            type="hidden"
            id="scheduleId"
            value="${isEdit ? schedule.schedule_id : ""}"
        >

        <div class="mb-4">
            <label
                for="payBeginning"
                class="mb-2 block text-sm font-medium text-gray-700"
            >
                Pay Beginning
            </label>

            <input
                type="date"
                id="payBeginning"
                value="${isEdit ? schedule.payroll_start_date : ""}"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-gray-500 focus:outline-none
                       focus:ring-2 focus:ring-gray-200"
            >
        </div>

        <div class="mb-4">
            <label
                for="payEnd"
                class="mb-2 block text-sm font-medium text-gray-700"
            >
                Pay End
            </label>

            <input
                type="date"
                id="payEnd"
                value="${isEdit ? schedule.payroll_end_date : ""}"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-gray-500 focus:outline-none
                       focus:ring-2 focus:ring-gray-200"
            >
        </div>
    `;
}
function openPayScheduleModal() {
    const openSchedules = payrollSchedules.filter(
        schedule => schedule.status === "Open"
    );

    if (openSchedules.length === 0) {
        alert("There are no unpaid payroll schedules.");
        return;
    }

    const container = document.getElementById("payrollFormContent");

    if (!container) return;

    const scheduleOptions = openSchedules
        .map(schedule => `
            <option value="${schedule.schedule_id}">
                ${escapeHtml(schedule.payroll_start_date)}
                →
                ${escapeHtml(schedule.payroll_end_date)}
            </option>
        `)
        .join("");

    container.innerHTML = `
        <div class="mb-4">
            <label
                for="payrollScheduleId"
                class="mb-2 block text-sm font-medium text-gray-700"
            >
                Payroll Schedule
            </label>

            <select
                id="payrollScheduleId"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                       focus:border-gray-500 focus:outline-none
                       focus:ring-2 focus:ring-gray-200"
            >
                <option value="">
                    Select Unpaid Payroll Schedule
                </option>

                ${scheduleOptions}
            </select>
        </div>

        <div
            class="rounded-lg border border-blue-200 bg-blue-50
                   p-4 text-sm text-blue-700"
        >
            <i class="bi bi-info-circle mr-2"></i>

            This will process payroll for
            <strong>ALL employees</strong>
            under the selected payroll schedule.
        </div>
    `;

    const title = document.getElementById("payrollModalTitle");

    if (title) {
        title.innerHTML = `
            <i class="bi bi-cash-stack mr-2"></i>
            Pay All Employees
        `;
    }

    openPayrollModal();
}
function handlePayrollSubmit(event) {
    event.preventDefault();

    const scheduleId =
        document.getElementById("scheduleId")?.value;

    const payBeginning =
        document.getElementById("payBeginning")?.value;

    const payEnd =
        document.getElementById("payEnd")?.value;

    const payScheduleId =
        document.getElementById("payrollScheduleId")?.value;

    // Paying an existing schedule
    if (payScheduleId) {
        handlePayScheduleSubmit();
        return;
    }

    // Creating or editing a schedule
    if (!payBeginning || !payEnd) {
        alert("Please select the payroll start and end dates.");
        return;
    }

    if (payBeginning > payEnd) {
        alert(
            "Payroll start date cannot be later than the end date."
        );
        return;
    }

    // Editing
    if (scheduleId) {
        showConfirmation(
            "Update Payroll Schedule",
            `Update payroll schedule from ${payBeginning} to ${payEnd}?`,
            () => {
                updatePayrollSchedule(
                    scheduleId,
                    payBeginning,
                    payEnd
                );
            }
        );

        return;
    }

    // Creating
    showConfirmation(
        "Create Payroll Schedule",
        `Create payroll schedule from ${payBeginning} to ${payEnd}?`,
        () => {
            createPayrollSchedule(
                payBeginning,
                payEnd
            );
        }
    );
}
async function createPayrollSchedule(
    payBeginning,
    payEnd
) {
    const payload = {
        payroll_start_date: payBeginning,
        payroll_end_date: payEnd
    };

    try {
        const response = await fetch(
            PAYROLL_SCHEDULE_API,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "same-origin",
                body: JSON.stringify(payload)
            }
        );

        if (response.status === 401 || response.status === 403) {
            window.location.href =
                "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            alert(
                result.message ||
                "Failed to create payroll schedule."
            );
            return;
        }

        closePayrollModal();

        await loadPayrollSchedules();

    } catch (error) {
        console.error(
            "Create schedule error:",
            error
        );

        alert(
            "An error occurred while creating the payroll schedule."
        );
    }
}
async function updatePayrollSchedule(
    scheduleId,
    payBeginning,
    payEnd
) {
    const payload = {
        schedule_id: scheduleId,
        payroll_start_date: payBeginning,
        payroll_end_date: payEnd
    };

    try {
        const response = await fetch(
            PAYROLL_SCHEDULE_API,
            {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "same-origin",
                body: JSON.stringify(payload)
            }
        );

        if (response.status === 401 || response.status === 403) {
            window.location.href =
                "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            alert(
                result.message ||
                "Failed to update payroll schedule."
            );
            return;
        }

        closePayrollModal();

        await loadPayrollSchedules();

        openScheduleManager();

    } catch (error) {
        console.error(
            "Update schedule error:",
            error
        );

        alert(
            "An error occurred while updating the payroll schedule."
        );
    }
}
async function handlePayScheduleSubmit() {
    const scheduleId =
        document.getElementById("payrollScheduleId")?.value;

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
        alert(
            "This payroll schedule has already been processed."
        );
        return;
    }

    showConfirmation(
        "Process Payroll",
        `Are you sure you want to process payroll for ALL employees for ${schedule.payroll_start_date} to ${schedule.payroll_end_date}?`,
        () => {
            processPayrollSchedule(scheduleId);
        }
    );
}
async function processPayrollSchedule(scheduleId) {
    const payload = {
        schedule_id: scheduleId
    };

    try {
        const response = await fetch(
            `${PAYROLL_API}/pay`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "same-origin",
                body: JSON.stringify(payload)
            }
        );

        if (response.status === 401 || response.status === 403) {
            window.location.href =
                "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            alert(
                result.message ||
                "Failed to process payroll."
            );
            return;
        }

        closePayrollModal();

        await loadPayrollSchedules();

        alert("Payroll processed successfully.");

    } catch (error) {
        console.error(
            "Payroll processing error:",
            error
        );

        alert(
            "An error occurred while processing payroll."
        );
    }
}
function openScheduleManager() {
    renderScheduleManager();

    const modal =
        document.getElementById("scheduleManagerModal");

    if (!modal) return;

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    modal.setAttribute("aria-hidden", "false");
}

function closeScheduleManager() {
    const modal =
        document.getElementById("scheduleManagerModal");

    if (!modal) return;

    modal.classList.add("hidden");
    modal.classList.remove("flex");

    modal.setAttribute("aria-hidden", "true");
}
function renderScheduleManager() {
    const container =
        document.getElementById("scheduleManagerContent");

    if (!container) return;

    if (payrollSchedules.length === 0) {
        container.innerHTML = `
            <div
                class="rounded-lg border border-blue-200
                       bg-blue-50 p-4 text-sm text-blue-700"
            >
                No payroll schedules found.
            </div>
        `;

        return;
    }

    container.innerHTML = `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">
                            ID
                        </th>

                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">
                            Pay Beginning
                        </th>

                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">
                            Pay End
                        </th>

                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">
                            Status
                        </th>

                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    ${payrollSchedules
        .map(renderScheduleRow)
        .join("")}

                </tbody>

            </table>
        </div>
    `;
}
function renderScheduleRow(schedule) {
    const isProcessed =
        schedule.status === "Processed";

    return `
        <tr class="hover:bg-gray-50">

            <td class="px-4 py-3 text-sm">
                ${schedule.schedule_id}
            </td>

            <td class="px-4 py-3 text-sm">
                ${escapeHtml(
        schedule.payroll_start_date
    )}
            </td>

            <td class="px-4 py-3 text-sm">
                ${escapeHtml(
        schedule.payroll_end_date
    )}
            </td>

            <td class="px-4 py-3">

                <span
                    class="inline-flex rounded-full
                           px-2.5 py-1 text-xs font-semibold
                           ${getScheduleStatusClass(
        schedule.status
    )}"
                >
                    ${escapeHtml(schedule.status)}
                </span>

            </td>

            <td class="px-4 py-3">

                ${
        isProcessed
            ? `
                            <span class="text-sm text-gray-400">
                                Processed
                            </span>
                        `
            : `
                            <button
                                type="button"
                                class="mr-1 inline-flex items-center gap-1
                                       rounded-md border border-blue-500
                                       px-3 py-1.5 text-sm text-blue-600
                                       hover:bg-blue-50"
                                onclick="editPayrollSchedule(
                                    ${schedule.schedule_id}
                                )"
                            >
                                <i class="bi bi-pencil"></i>
                                Edit
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-1
                                       rounded-md border border-red-500
                                       px-3 py-1.5 text-sm text-red-600
                                       hover:bg-red-50"
                                onclick="deletePayrollSchedule(
                                    ${schedule.schedule_id}
                                )"
                            >
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>
                        `
    }

            </td>

        </tr>
    `;
}
function getScheduleStatusClass(status) {
    switch (status) {
        case "Open":
            return "bg-yellow-100 text-yellow-700";

        case "Processed":
            return "bg-green-100 text-green-700";

        case "Cancelled":
            return "bg-red-100 text-red-700";

        default:
            return "bg-gray-100 text-gray-700";
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
        alert(
            "Processed payroll schedules cannot be edited."
        );
        return;
    }

    const title =
        document.getElementById("payrollModalTitle");

    if (title) {
        title.innerHTML = `
            <i class="bi bi-pencil mr-2"></i>
            Edit Payroll Schedule
        `;
    }

    renderScheduleForm(schedule);

    closeScheduleManager();

    openPayrollModal();
}
function deletePayrollSchedule(scheduleId) {
    const schedule = payrollSchedules.find(
        schedule => schedule.schedule_id == scheduleId
    );

    if (!schedule) {
        alert("Payroll schedule not found.");
        return;
    }

    if (schedule.status === "Processed") {
        alert(
            "Processed payroll schedules cannot be deleted."
        );
        return;
    }

    showConfirmation(
        "Delete Payroll Schedule",
        `Are you sure you want to delete the payroll schedule ${schedule.payroll_start_date} to ${schedule.payroll_end_date}?`,
        () => {
            executeDeletePayrollSchedule(scheduleId);
        }
    );
}
async function executeDeletePayrollSchedule(scheduleId) {
    try {
        const response = await fetch(
            `${PAYROLL_SCHEDULE_API}?id=${scheduleId}`,
            {
                method: "DELETE",
                credentials: "same-origin"
            }
        );

        if (response.status === 401 || response.status === 403) {
            window.location.href =
                "/employee_management_system/login";
            return;
        }

        const result = await response.json();

        if (!result.success) {
            alert(
                result.message ||
                "Failed to delete payroll schedule."
            );
            return;
        }

        await loadPayrollSchedules();

        renderScheduleManager();

    } catch (error) {
        console.error(
            "Delete schedule error:",
            error
        );

        alert(
            "An error occurred while deleting the payroll schedule."
        );
    }
}