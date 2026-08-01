// ===============================
// PAYROLL MODULE
// ===============================

const payrollAPI = "../../../server/payroll/payrollController.php";

let payrolls = [];

document.addEventListener("DOMContentLoaded", initializePayroll);

function initializePayroll() {
    bindPayrollEvents();
    loadPayroll();
}

function bindPayrollEvents() {

    document.getElementById("btnGeneratePayroll")
        ?.addEventListener("click", generatePayroll);

    document.getElementById("btnRefreshPayroll")
        ?.addEventListener("click", loadPayroll);

    document.getElementById("payrollSearch")
        ?.addEventListener("input", searchPayroll);

    document.getElementById("payrollPeriodFilter")
        ?.addEventListener("change", filterPayroll);

}

async function loadPayroll() {

    try {

        const response = await fetch(payrollAPI);

        const result = await response.json();

        if (!result.success) {

            payrolls = [];

            renderPayroll([]);

            return;

        }

        payrolls = result.data;

        renderPayroll(payrolls);

    }

    catch (error) {

        console.error(error);

    }

}

function renderPayroll(data) {

    const table = document.getElementById("payrollTableBody");

    table.innerHTML = "";

    if (!data.length) {

        table.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-6">
                    No payroll records found.
                </td>
            </tr>
        `;

        return;

    }

    data.forEach(payroll =>
        table.insertAdjacentHTML("beforeend", createPayrollRow(payroll))
    );

}

function createPayrollRow(payroll) {

    return `
    <tr class="border-b hover:bg-slate-50">
        <td class="p-3">${payroll.pay_id}</td>
        <td class="p-3">${payroll.emp_firstname} ${payroll.emp_lastname}</td>
        <td class="p-3">${payroll.pay_period_start} - ${payroll.pay_period_end}</td>
        <td class="p-3">₱${payroll.gross_pay}</td>
        <td class="p-3">₱${payroll.net_pay}</td>
        <td class="p-3">${payroll.pay_status}</td>
        <td class="p-3 text-center">
            <button
                class="btn-pay text-green-600"
                data-id="${payroll.pay_id}">
                <i class="bi bi-cash-stack"></i>
            </button>
        </td>
    </tr>
    `;

}

function searchPayroll() {

    const keyword = document.getElementById("payrollSearch")
        .value
        .toLowerCase();

    const filtered = payrolls.filter(payroll =>
        `${payroll.emp_firstname} ${payroll.emp_lastname}`
            .toLowerCase()
            .includes(keyword)
    );

    renderPayroll(filtered);

}

function filterPayroll() {

    const month = document.getElementById("payrollPeriodFilter").value;

    if (!month) {

        renderPayroll(payrolls);

        return;

    }

    renderPayroll(
        payrolls.filter(payroll =>
            payroll.pay_period_start.startsWith(month)
        )
    );

}

async function generatePayroll() {

    if (!confirm("Generate payroll?")) return;

    try {

        const response = await fetch(payrollAPI, {

            method: "POST"

        });

        const result = await response.json();

        alert(result.message);

        loadPayroll();

    }

    catch (error) {

        console.error(error);

    }

}

async function payEmployee(id) {

    try {

        const response = await fetch(payrollAPI, {

            method: "PUT",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                pay_id: id
            })

        });

        const result = await response.json();

        alert(result.message);

        loadPayroll();

    }

    catch (error) {

        console.error(error);

    }

}

document.addEventListener("click", event => {

    const button = event.target.closest(".btn-pay");

    if (!button) return;

    payEmployee(button.dataset.id);

});