const currentUserId = "<?= $current_user_id ?>";


let pendingStatusChange = null;


const editModal = document.getElementById("editModal");
const editContainer = document.getElementById("editModalContainer");
const editBackdrop = document.getElementById("editModalBackdrop");
const addModal = document.getElementById("addReportModal");
const addReportForm = document.getElementById("addReportForm");
const addContainer = document.getElementById("addModalContainer");
const addBackdrop = document.getElementById("addModalBackdrop");

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

document.addEventListener("change", function (e) {
  if (e.target.classList.contains("status-updater") && e.isTrusted) {
    const select = e.target;
    const reportId = select.getAttribute("data-report-id");
    const statusId = select.value;
    const userId = select.getAttribute("data-user-id");
    const originalValue = select.getAttribute("data-last-value") || select.defaultValue;

    pendingStatusChange = { select, reportId, statusId, userId, originalValue };

    const confirmBtn = document.getElementById("confirmStatusBtn");
    const modal = document.getElementById("statusConfirmModal");

    toggleStatusModal(true);

    let timeLeft = 3;
    confirmBtn.disabled = true;
    confirmBtn.style.opacity = "0.6";
    confirmBtn.innerText = `Yes (${timeLeft}s)`;

    if (modal.dataset.timerId) clearInterval(Number(modal.dataset.timerId));

    const timer = setInterval(() => {
      timeLeft--;
      if (timeLeft > 0) {
        confirmBtn.innerText = `Yes (${timeLeft}s)`;
      } else {
        clearInterval(timer);
        confirmBtn.disabled = false;
        confirmBtn.style.opacity = "1";
        confirmBtn.innerText = "Yes, Change it";
      }
    }, 1000);

    modal.dataset.timerId = timer;
  }
});

function executeStatusUpdate() {
  if (!pendingStatusChange) return;
  const { select, reportId, statusId, userId, originalValue } = pendingStatusChange;

  select.style.opacity = "0.5";
  select.disabled = true;

  fetch("../controllers/quick_update_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: `report_id=${reportId}&status_id=${statusId}&updated_by=${userId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      select.style.opacity = "1";
      select.disabled = false;

      if (data.success) {
        select.setAttribute("data-last-value", statusId);
        showToast('<i class="fas fa-check-circle mr-2"></i>Status updated successfully!', "success");

    
        const statusToRemove = ["3", "4"];
        if (statusToRemove.includes(statusId)) {
          const row = select.closest("tr") || select.closest(".report-row");
          if (row) {
            row.style.transition = "all 0.5s ease";
            row.style.opacity = "0";
            row.style.transform = "translateX(20px)";
            setTimeout(() => row.remove(), 500);
          }
        }
      } else {
        showToast("Update failed: " + data.error, "error");
        select.value = originalValue;
      }
    })
    .catch((err) => {
      console.error("Fetch Error:", err);
      select.disabled = false;
      select.style.opacity = "1";
      select.value = originalValue;
    });
}

document.addEventListener("DOMContentLoaded", () => {

  document.getElementById("confirmStatusBtn")?.addEventListener("click", () => {
    toggleStatusModal(false);
    executeStatusUpdate();
  });


  document.getElementById("cancelStatusBtn")?.addEventListener("click", () => {
    if (pendingStatusChange) {
      pendingStatusChange.select.value = pendingStatusChange.originalValue;
    }
    const modal = document.getElementById("statusConfirmModal");
    if (modal.dataset.timerId) clearInterval(Number(modal.dataset.timerId));
    toggleStatusModal(false);
    pendingStatusChange = null;
  });

  const searchInput = document.getElementById("searchInput");
  const categoryFilter = document.getElementById("categoryFilter");
  const moduleFilter = document.getElementById("moduleFilter");
  const severityFilter = document.getElementById("severityFilter");
  const resetBtn = document.getElementById("resetBtn");
  const rows = document.querySelectorAll(".report-row");
  const noResultsRow = document.getElementById("noResultsRow");

  function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const catVal = categoryFilter.value;
    const modVal = moduleFilter.value;
    const sevVal = severityFilter.value;
    let visibleCount = 0;

    rows.forEach((row) => {
      const ref = row.getAttribute("data-ref").toLowerCase();
      const reporter = row.getAttribute("data-reporter").toLowerCase();
      const desc = row.getAttribute("data-desc").toLowerCase();
      const catId = row.getAttribute("data-cat");
      const modId = row.getAttribute("data-mod");
      const sevId = row.getAttribute("data-sev");

      const matchesSearch = searchTerm === "" || ref.includes(searchTerm) || reporter.includes(searchTerm) || desc.includes(searchTerm);
      const matchesCat = catVal === "" || catId === catVal;
      const matchesMod = modVal === "" || modId === modVal;
      const matchesSev = sevVal === "" || sevId === sevVal;

      if (matchesSearch && matchesCat && matchesMod && matchesSev) {
        row.classList.remove("hidden");
        visibleCount++;
      } else {
        row.classList.add("hidden");
      }
    });
    if (noResultsRow) noResultsRow.classList.toggle("hidden", visibleCount !== 0);
  }

  searchInput?.addEventListener("input", applyFilters);
  categoryFilter?.addEventListener("change", applyFilters);
  moduleFilter?.addEventListener("change", applyFilters);
  severityFilter?.addEventListener("change", applyFilters);
  resetBtn?.addEventListener("click", () => {
    searchInput.value = "";
    categoryFilter.value = "";
    moduleFilter.value = "";
    severityFilter.value = "";
    applyFilters();
  });
});

function showToast(message, type = "success") {
  const toast = document.createElement("div");

  toast.style.cssText = `
        position: fixed;
        bottom: 100px; 
        right: 30px;
        padding: 1.25rem 2.5rem;
        border-radius: 1.5rem;
        color: white;
        font-weight: 800;
        z-index: 100000; /* PINAKAMATAAS NA LAYER */
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateY(100px);
        opacity: 0;
    `;

  if (type === "success") {
    toast.style.backgroundColor = "#059669"; // Emerald Green
    toast.innerHTML = message;
  } else {
    toast.style.backgroundColor = "#e11d48";
    toast.innerHTML = message;
  }

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.transform = "translateY(0)";
    toast.style.opacity = "1";
  }, 100);

  setTimeout(() => {
    toast.style.transform = "translateY(100px)";
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 500);
  }, 5000);
}


function openAddModal() {
  addModal.classList.remove("hidden");
  setTimeout(() => {
    addContainer.classList.remove("scale-95", "opacity-0");
    addContainer.classList.add("scale-100", "opacity-100");
    addBackdrop.classList.remove("opacity-0");
    addBackdrop.classList.add("opacity-100");
  }, 10);
}

function closeAddModal() {
  addContainer.classList.remove("scale-100", "opacity-100");
  addContainer.classList.add("scale-95", "opacity-0");
  addBackdrop.classList.remove("opacity-100");
  addBackdrop.classList.add("opacity-0");
  setTimeout(() => addModal.classList.add("hidden"), 300);
}

function openEditModal() {
  editModal.classList.remove("hidden");
  setTimeout(() => {
    editContainer.classList.remove("scale-95", "opacity-0");
    editContainer.classList.add("scale-100", "opacity-100");
    editBackdrop.classList.remove("opacity-0");
    editBackdrop.classList.add("opacity-100");
  }, 10);
}

function closeEditModal() {
  
  editContainer.classList.remove("scale-100", "opacity-100");
  editContainer.classList.add("scale-95", "opacity-0");
  editBackdrop.classList.remove("opacity-100");
  editBackdrop.classList.add("opacity-0");

  
  const editForm = document.getElementById("editForm");
  const fileLabel = document.getElementById("edit_file_name_label");

  if (editForm) {
    editForm.reset(); 
  }

  if (fileLabel) {
    
    fileLabel.innerText = "Click to upload new media...";
    fileLabel.classList.add('text-slate-400');
    fileLabel.classList.remove('text-blue-600', 'font-bold');
  }


  setTimeout(() => {
    editModal.classList.add("hidden");
  }, 300);
}


document.querySelectorAll(".edit-report-btn").forEach((button) => {
  button.addEventListener("click", function () {
    document.getElementById("edit_report_id").value = this.getAttribute("data-id");
    document.getElementById("edit_cat_id").value = this.getAttribute("data-cat");
    document.getElementById("edit_mod_id").value = this.getAttribute("data-mod");
    document.getElementById("edit_desc").value = this.getAttribute("data-desc");

    const severityId = this.getAttribute("data-sev");
    const radioToSelect = editModal.querySelector(`input[name="sev_id"][value="${severityId}"]`);
    if (radioToSelect) radioToSelect.checked = true;

    openEditModal();
  });
});

if (addReportForm) {
  addReportForm.addEventListener("submit", function (e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const severityOptions = this.querySelectorAll('input[name="sev_id"]');
    const fileInput = document.getElementById("rep_img_input");

    const isChecked = Array.from(severityOptions).some((r) => r.checked);
    if (!isChecked) {
      e.preventDefault();
      document.getElementById("severity-error")?.classList.remove("hidden");
      return false;
    }

    if (fileInput?.files.length > 0) {
      if (fileInput.files[0].size > 25 * 1024 * 1024) {
        e.preventDefault();
        showToast('<i class="fas fa-exclamation-triangle mr-2"></i>Max 25MB only!', "bg-red-500 text-white");
        return false;
      }
    }

    submitBtn.style.pointerEvents = "none";
    submitBtn.classList.add("opacity-70", "cursor-not-allowed");
    submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Sending...';

    const form = this;
    setTimeout(() => form.submit(), 100);
  });
}

window.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeAddModal();
    closeEditModal();
    toggleStatusModal(false);
  }
});
document.querySelectorAll(".remind-btn").forEach((button) => {
  button.addEventListener("click", function () {
    const reportId = this.getAttribute("data-id");
    const icon = this.querySelector("i");

    this.classList.add("opacity-50", "cursor-not-allowed");
    icon.className = "fa-solid fa-spinner fa-spin";

    fetch("../controllers/remind_admin_handler.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `report_id=${reportId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          icon.className = "fa-solid fa-check";
        } else {
          alert(data.message);
          icon.className = "fa-solid fa-bell";
          this.classList.remove("opacity-50", "cursor-not-allowed");
        }
      });
  });
});



let aiTimeout;

async function fetchSuggestions(text) {
  const badge = document.getElementById("ai-status-badge");
  const dot = document.getElementById("ai-dot");
  const statusText = document.getElementById("ai-status-text");

  
  if (text.length < 5) {
    badge.className =
      "flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-[11px] font-bold text-slate-400 transition-all duration-300 border border-transparent";
    statusText.innerText = "AI Ready";
    return;
  }

  badge.classList.add("ai-active");
  badge.classList.remove("ai-success");
  statusText.innerText = "AI is thinking...";

  
  clearTimeout(aiTimeout);
  aiTimeout = setTimeout(async () => {
    try {
      
      const response = await fetch(`../functions/get_suggestions.php?text=${encodeURIComponent(text)}`);
      const data = await response.json();

      if (data && !data.error) {
      
        badge.classList.remove("ai-active");
        badge.classList.add("ai-success");
        statusText.innerText = "Suggestions applied!";

        applyAiSuggestions(data);

        setTimeout(() => {
          badge.classList.remove("ai-success");
          statusText.innerText = "AI Ready";
        }, 3000);
      }
    } catch (err) {
      statusText.innerText = "AI Offline";
      badge.classList.remove("ai-active");
    }
  }, 800);
}
function applyAiSuggestions(data) {
  
  const catSelect = document.getElementById("cat_id");
  if (data.category) {
    catSelect.value = data.category;
   
    catSelect.dispatchEvent(new Event("change"));
  }
  const modSelect = document.getElementById("mod_id");
  if (data.module) {
    modSelect.value = data.module;
    modSelect.dispatchEvent(new Event("change"));
  }

  if (data.severity) {
    const severityRadio = document.querySelector(`input[name="sev_id"][value="${data.severity}"]`);
    if (severityRadio) {
      severityRadio.checked = true;
      
      severityRadio.dispatchEvent(new Event("change"));
    }
  }
  console.log("AI Suggestions Applied:", data);
}
function openViewModal(data) {
  const modal = document.getElementById("viewModal");
  const backdrop = document.getElementById("viewModalBackdrop");
  const container = document.getElementById("viewModalContainer");
  
  const imgElement = document.getElementById("view_attachment");
  const videoElement = document.getElementById("view_video_attachment");
  const placeholder = document.getElementById("no_media_placeholder");

  
  document.getElementById("view_category").innerText = data.category;
  document.getElementById("view_module").innerText = data.module;
  document.getElementById("view_desc").innerText = data.description;

  const sevBadge = document.getElementById("view_severity");
  sevBadge.innerText = data.severity;
  
  const filename = data.media ? data.media.toString().trim() : "";
  
  imgElement.style.display = "none";
  videoElement.style.display = "none";
  placeholder.style.display = "block";

  if (filename !== "") {
    const videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
    const fileExt = filename.split('.').pop().toLowerCase();
    const isVideo = videoExtensions.includes(fileExt);

    if (isVideo) {
      videoElement.src = "Videos/" + filename; 
      videoElement.style.display = "block";
      videoElement.load();
      placeholder.style.display = "none";
    } else {
      imgElement.src = "uploads/" + filename;
      imgElement.style.display = "block";
      placeholder.style.display = "none";
    }
  }
  
  modal.style.display = "flex";
  setTimeout(() => {
    backdrop.style.opacity = "1";
    container.style.opacity = "1";
    container.style.transform = "scale(1)";
  }, 10);
}

function closeViewModal() {
  const modal = document.getElementById("viewModal");
  const backdrop = document.getElementById("viewModalBackdrop");
  const container = document.getElementById("viewModalContainer");

  backdrop.style.opacity = "0";
  container.style.opacity = "0";
  container.style.transform = "scale(0.95)";

  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}

function updateEditFileLabel(input) {
    const label = document.getElementById('edit_file_name_label');
    if (input.files && input.files[0]) {
        label.innerText = input.files[0].name;
        label.classList.remove('text-slate-400');
        label.classList.add('text-blue-600', 'font-bold');
    } else {
        label.innerText = "Click to upload new media...";
        label.classList.add('text-slate-400');
        label.classList.remove('text-blue-600', 'font-bold');
    }
}