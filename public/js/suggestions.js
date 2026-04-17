let pendingStatusChange = null;


function toggleStatusModal(show) {
    const modal = document.getElementById("statusConfirmModal");
    const container = document.getElementById("statusConfirmContainer");
    if (!modal || !container) return;

    if (show) {
        modal.style.display = "flex"; 
        
        setTimeout(() => {
            container.style.opacity = "1";
            container.style.transform = "scale(1)";
        }, 10);
    } else {
        container.style.opacity = "0";
        container.style.transform = "scale(0.95)";
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
    }
}


const timer = setInterval(() => {
    timeLeft--;
    if (timeLeft > 0) {
        confirmBtn.innerText = `Yes (${timeLeft}s)`;
    } else {
        clearInterval(timer);
        confirmBtn.disabled = false;
        confirmBtn.innerText = "Yes, Change it";
        confirmBtn.style.opacity = "1"; 
        confirmBtn.style.cursor = "pointer";
    }
}, 1000);

                                                                                               
function toggleModal(show) {
    const modal = document.getElementById("projectModal");
    const backdrop = document.getElementById("projectModalBackdrop");
    const container = document.getElementById("projectModalContainer");
    if (!modal || !backdrop || !container) return;

    if (show) {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        document.body.style.overflow = "hidden";
        setTimeout(() => {
            backdrop.classList.replace("opacity-0", "opacity-100");
            container.classList.replace("opacity-0", "opacity-100");
            container.classList.replace("scale-95", "scale-100");
        }, 10);
    } else {
        backdrop.classList.replace("opacity-100", "opacity-0");
        container.classList.replace("opacity-100", "opacity-0");
        container.classList.replace("scale-100", "scale-95");
        setTimeout(() => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "auto";
        }, 300);
    }
}

/**
 * Main Logic
 */
document.addEventListener("DOMContentLoaded", () => {
    // 1. New Suggestion Form Submission
    const suggestionForm = document.getElementById('suggestionForm');
    if (suggestionForm) {
        suggestionForm.addEventListener('submit', function (e) {
            const submitBtn = document.getElementById('submitBtn');
            const loadingIcon = document.getElementById('loadingIcon');
            const btnText = document.getElementById('btnText');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            if (loadingIcon) loadingIcon.style.display = 'inline-block';
            if (btnText) btnText.innerText = "Sending...";
        });
    }

    // 2. Status Change Confirmation Logic
    const confirmBtn = document.getElementById('confirmStatusBtn');
    const cancelBtn = document.getElementById('cancelStatusBtn');
    const statusModal = document.getElementById('statusConfirmModal');

    // YES Button Click
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (!pendingStatusChange) return;
            const { select, suggestionId, statusId } = pendingStatusChange;
            
            // Close smoothly
            toggleStatusModal(false);
            executeStatusUpdate(select, suggestionId, statusId);
        });
    }

    // CANCEL Button Click
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            if (pendingStatusChange) {
                pendingStatusChange.select.value = pendingStatusChange.originalValue;
            }
            clearInterval(Number(statusModal.dataset.timerId));
            
            // Close smoothly
            toggleStatusModal(false);
            pendingStatusChange = null;
        });
    }
});

/**
 * Listen for Status Dropdown changes
 */
document.addEventListener("change", function (e) {
    if (!e.target.classList.contains("status-updater") || !e.isTrusted) return;

    const select = e.target;
    const modal = document.getElementById("statusConfirmModal");
    const confirmBtn = document.getElementById("confirmStatusBtn");

    if (!modal || !confirmBtn) return;

    const suggestionId = select.getAttribute("data-report-id");
    const statusId = select.value;
    const originalValue = select.getAttribute("data-last-value") || select.defaultValue;

    pendingStatusChange = {
        select,
        suggestionId,
        statusId,
        originalValue
    };

    // Open smoothly
    toggleStatusModal(true);

    let timeLeft = 3;
    confirmBtn.disabled = true;
    confirmBtn.innerText = `Yes (${timeLeft}s)`;

    if (modal.dataset.timerId) {
        clearInterval(Number(modal.dataset.timerId));
    }

    const timer = setInterval(() => {
        timeLeft--;

        if (timeLeft > 0) {
            confirmBtn.innerText = `Yes (${timeLeft}s)`;
        } else {
            clearInterval(timer);
            confirmBtn.disabled = false;
            confirmBtn.innerText = "Yes, Change it";
        }
    }, 1000);

    modal.dataset.timerId = timer;
});

function executeStatusUpdate(select, suggestionId, statusId) {
    select.style.opacity = "0.5";
    select.disabled = true;

    const params = new URLSearchParams();
    params.append("suggestion_id", suggestionId);
    params.append("status_id", statusId);
    params.append("updated_by", currentUserId);

    fetch("../controllers/quick_update_suggestion.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params,
    })
    .then(res => res.json())
    .then(data => {
        select.style.opacity = "1";
        select.disabled = false;
        if (data.success) {
            select.setAttribute('data-last-value', statusId);
            showToast('<i class="fas fa-check-circle mr-2"></i>Updated!', "bg-emerald-600");
            
            if (["3", "4"].includes(statusId)) {
                const card = select.closest(".group");
                if (card) {
                    card.style.opacity = "0";
                    setTimeout(() => card.remove(), 500);
                }
            }
        } else {
            showToast("Error: " + (data.error || "Update failed"), "bg-rose-600");
            select.value = pendingStatusChange.originalValue;
        }
    })
    .catch(() => {
        select.style.opacity = "1";
        select.disabled = false;
        select.value = pendingStatusChange.originalValue;
    });
}

/**
 * Modernized Toast Notification
 */
function showToast(message, bgColor) {
    const toast = document.createElement("div");
    // Added backdrop-blur and mobile-style rounded corners
    toast.className = `fixed top-24 right-5 ${bgColor} bg-opacity-90 backdrop-blur-md text-white px-8 py-4 rounded-[2rem] shadow-2xl z-[200] transition-all duration-300 transform translate-x-10 opacity-0 font-bold`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-10', 'opacity-0');
    }, 10);

    // Fade out and remove
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-10');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}