let pendingStatusChange = null;
let timeLeft = 0;
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
  const confirmBtn = document.getElementById("confirmStatusBtn");
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
  const suggestionForm = document.getElementById("suggestionForm");
  if (suggestionForm) {
    suggestionForm.addEventListener("submit", function (e) {
      const submitBtn = document.getElementById("submitBtn");
      const loadingIcon = document.getElementById("loadingIcon");
      const btnText = document.getElementById("btnText");

      submitBtn.disabled = true;
      submitBtn.classList.add("opacity-70", "cursor-not-allowed");
      if (loadingIcon) loadingIcon.style.display = "inline-block";
      if (btnText) btnText.innerText = "Sending...";
    });
  }

  // 2. Status Change Confirmation Logic
  const confirmBtn = document.getElementById("confirmStatusBtn");
  const cancelBtn = document.getElementById("cancelStatusBtn");
  const statusModal = document.getElementById("statusConfirmModal");

  // YES Button Click
  if (confirmBtn) {
    confirmBtn.addEventListener("click", () => {
      if (!pendingStatusChange) return;
      const { select, suggestionId, statusId } = pendingStatusChange;

      // Close smoothly
      toggleStatusModal(false);
      executeStatusUpdate(select, suggestionId, statusId);
    });
  }

  // CANCEL Button Click
  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
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
    originalValue,
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
    .then((res) => res.json())
    .then((data) => {
      select.style.opacity = "1";
      select.disabled = false;

      if (data.success) {
        select.setAttribute("data-last-value", statusId);

        // GAMITIN ANG 'success' PARA MAG-MATCH SA COLOR LOGIC
        showToast('<i class="fas fa-check-circle mr-2"></i>Status updated successfully!', "success");

        if (["3", "4"].includes(statusId)) {
          const card = select.closest(".group");
          if (card) {
            card.style.transition = "all 0.5s ease";
            card.style.opacity = "0";
            card.style.transform = "translateX(20px)";
            setTimeout(() => card.remove(), 500);
          }
        }
      } else {
        // GAMITIN ANG 'error' PARA MAG-RED
        showToast('<i class="fas fa-exclamation-circle mr-2"></i>Error: ' + (data.error || "Update failed"), "error");
        select.value = pendingStatusChange.originalValue;
      }
    })
    .catch(() => {
      select.style.opacity = "1";
      select.disabled = false;
      if (pendingStatusChange) select.value = pendingStatusChange.originalValue;
      showToast('<i class="fas fa-wifi-slash mr-2"></i>Connection error', "error");
    });
}

/**
 * Modernized Toast Notification
 */
function showToast(message, type = "success") {
  const toast = document.createElement("div");

  // Premium Design Styles
  toast.style.cssText = `
        position: fixed;
        bottom: 100px; /* Itinaas para hindi matakpan */
        right: 30px;
        padding: 1.25rem 2.5rem;
        border-radius: 1.5rem;
        color: white;
        font-weight: 800;
        z-index: 100000;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateY(100px);
        opacity: 0;
    `;

  // Color Logic (Walang extra emojis dito dahil nagpasa na tayo ng FontAwesome icon sa taas)
  if (type === "success") {
    toast.style.backgroundColor = "#059669"; // Emerald Green
  } else {
    toast.style.backgroundColor = "#e11d48"; // Rose/Red
  }

  toast.innerHTML = message;
  document.body.appendChild(toast);

  // Animation: Slide Up & Fade In
  setTimeout(() => {
    toast.style.transform = "translateY(0)";
    toast.style.opacity = "1";
  }, 10);

  // Auto Remove after 4 seconds
  setTimeout(() => {
    toast.style.transform = "translateY(100px)";
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 500);
  }, 5000);
}
