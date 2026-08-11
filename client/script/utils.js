function showConfirmation(title, message, onConfirmCallback) {
    document.getElementById("confirmModalTitle").textContent = title;
    document.getElementById("confirmModalBody").textContent = message;
    pendingAction = onConfirmCallback;
    confirmationModalInstance.show();
}

function executeConfirmedAction() {
    if (typeof pendingAction === "function") {
        pendingAction();
    }
    confirmationModalInstance.hide();
    pendingAction = null;
}

