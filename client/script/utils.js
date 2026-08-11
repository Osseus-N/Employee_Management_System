let pendingAction = null;

function showConfirmation(title, message, onConfirmCallback) {
    const modal = document.getElementById("confirmationModal");
    const titleElement = document.getElementById("confirmModalTitle");
    const bodyElement = document.getElementById("confirmModalBody");

    if (!modal) {
        console.error("Confirmation modal not found.");
        return;
    }

    if (titleElement) {
        titleElement.textContent = title;
    }

    if (bodyElement) {
        bodyElement.textContent = message;
    }

    pendingAction = onConfirmCallback;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    modal.setAttribute("aria-hidden", "false");
}

function executeConfirmedAction() {
    if (typeof pendingAction !== "function") {
        console.error("No pending action.");
        return;
    }

    const action = pendingAction;

    pendingAction = null;

    closeConfirmation();

    action();
}

function closeConfirmation() {
    const modal = document.getElementById("confirmationModal");

    if (!modal) return;

    modal.classList.add("hidden");
    modal.classList.remove("flex");
    modal.setAttribute("aria-hidden", "true");

    pendingAction = null;
}