const PAYROLL_SELF_API = "../../../server/api/payroll.php";

//Para lang sa status
function payStatusBadge(status) {
    const colors = {
        Pending: "bg-yellow-100 text-yellow-700",
        Paid: "bg-green-100 text-green-700",
        Cancelled: "bg-red-100 text-red-700"
    };
    return `<span class="px-2 py-1 rounded-full text-xs font-semibold ${colors[status] || ""}">${status}</span>`;
}

