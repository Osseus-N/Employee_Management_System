const PAYROLL_SELF_API = "../../../server/router/payroll.php";

function payStatusBadge(status) {
    const colors = {
        Pending: "bg-yellow-100 text-yellow-700",
        Paid: "bg-green-100 text-green-700",
        Cancelled: "bg-red-100 text-red-700"
    };
    return `<span class="px-2 py-1 rounded-full text-xs font-semibold ${colors[status] || ""}">${status}</span>`;
}

async function loadMyPayroll() {
    const table = document.getElementById("payrollTableBody");
    if (!table) return;

    try {
        const response = await fetch(PAYROLL_SELF_API, { credentials: "same-origin" });
        const result = await response.json();
        table.innerHTML = "";

        if (!result.success || result.data.length === 0) {
            table.innerHTML = "<tr><td colspan='4' class='text-center py-6 text-gray-400'>No payroll records yet.</td></tr>";
            return;
        }

        result.data.forEach(pay => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="px-5 py-3">${pay.pay_period_start} → ${pay.pay_period_end}</td>
                <td class="px-5 py-3">${pay.pay_total_hours}</td>
                <td class="px-5 py-3">₱${Number(pay.pay_amount).toFixed(2)}</td>
                <td class="px-5 py-3">${payStatusBadge(pay.pay_status)}</td>
            `;
            table.appendChild(row);
        });
    } catch (error) {
        console.error(error);
    }
}
