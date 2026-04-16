const currentUserId = "<?= $current_user_id ?>";
const editModal = document.getElementById("editModal");
const editContainer = document.getElementById("editModalContainer");
const editBackdrop = document.getElementById("editModalBackdrop");
const addModal = document.getElementById("addReportModal");
const addReportForm = document.getElementById("addReportForm");
const addContainer = document.getElementById("addModalContainer");
const addBackdrop = document.getElementById("addModalBackdrop");


function openModal() {
  editModal.classList.remove("hidden");
}
function closeModal() {
  editModal.classList.add("hidden");
}
function closeAddModal() {
  addModal.classList.add("hidden");
}


window.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeModal();
    closeAddModal();
  }
});


document.querySelectorAll(".status-updater").forEach((select) => {
  select.addEventListener("change", function () {
    const reportId = this.getAttribute("data-report-id");
    const statusId = this.value;
    const userId = this.getAttribute("data-user-id");

  
    this.style.opacity = "0.5";
    this.disabled = true;

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
        this.style.opacity = "1";
        this.disabled = false; 

        if (data.success) {
          showToast(
            '<i class="fas fa-check-circle mr-2"></i>Status updated successfully!',
            "bg-green-500/80 text-white",
          );

          

          const statusToRemove = ["3", "4"];

          if (statusToRemove.includes(statusId)) {
            const row = this.closest("tr");

            // Start the fade out
            row.style.transition = "all 0.5s ease";
            row.style.opacity = "0";
            row.style.transform = "translateX(20px)";

            // Remove from DOM after animation finishes
            setTimeout(() => {
              row.remove();
            }, 500);
          }
          // --- NEW ANIMATION LOGIC END ---
        } else {
          showToast("Update failed: " + data.error, "bg-red-500");
          location.reload();
        }
      })
      .catch((err) => {
        console.error("Fetch Error:", err);
        this.disabled = false;
        this.style.opacity = "1";
      });
  });
});

// Helper function for toast notifications
function showToast(message, bgColor) {
  const toast = document.createElement("div");
  toast.className = `fixed top-24 right-5 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-[110] transition-all duration-300`;
  toast.innerHTML = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// 2. Animated Control Functions
function openAddModal() {
  // Show the hidden wrapper first
  addModal.classList.remove("hidden");

  // Use a 10ms delay to allow the browser to register the 'hidden' removal
  // before applying the transition classes
  setTimeout(() => {
    addContainer.classList.remove("scale-95", "opacity-0");
    addContainer.classList.add("scale-100", "opacity-100");
    addBackdrop.classList.remove("opacity-0");
    addBackdrop.classList.add("opacity-100");
  }, 10);
}

function closeAddModal() {
  // Reverse the animation first
  addContainer.classList.remove("scale-100", "opacity-100");
  addContainer.classList.add("scale-95", "opacity-0");
  addBackdrop.classList.remove("opacity-100");
  addBackdrop.classList.add("opacity-0");

  // Wait for the 300ms transition to finish before adding 'hidden' back
  setTimeout(() => {
    addModal.classList.add("hidden");
  }, 300);
}

// Update your Escape key listener to use the animated functions
window.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeAddModal();
    // If you have an edit modal, apply the same logic there
    if (typeof closeEditModal === "function") closeEditModal();
  }
});

function openEditModal() {
  // 1. Show the hidden wrapper
  editModal.classList.remove("hidden");

  // 2. Trigger animation (after 10ms to let browser register removal of hidden)
  setTimeout(() => {
    editContainer.classList.remove("scale-95", "opacity-0");
    editContainer.classList.add("scale-100", "opacity-100");
    editBackdrop.classList.remove("opacity-0");
    editBackdrop.classList.add("opacity-100");
  }, 10);
}

function closeEditModal() {
  // 1. Reverse the animation
  editContainer.classList.remove("scale-100", "opacity-100");
  editContainer.classList.add("scale-95", "opacity-0");
  editBackdrop.classList.remove("opacity-100");
  editBackdrop.classList.add("opacity-0");

  // 2. Hide after the transition finishes (300ms)
  setTimeout(() => {
    editModal.classList.add("hidden");
  }, 300);
}

document.querySelectorAll(".edit-report-btn").forEach((button) => {
  button.addEventListener("click", function () {
    // Populate standard fields
    document.getElementById("edit_report_id").value =
      this.getAttribute("data-id");
    document.getElementById("edit_cat_id").value =
      this.getAttribute("data-cat");
    document.getElementById("edit_mod_id").value =
      this.getAttribute("data-mod");
    document.getElementById("edit_desc").value = this.getAttribute("data-desc");

    // Handle the Severity Radios
    const severityId = this.getAttribute("data-sev");

    // Find the radio button inside the Edit Modal that matches the ID
    const radioToSelect = editModal.querySelector(
      `input[name="sev_id"][value="${severityId}"]`,
    );

    if (radioToSelect) {
      radioToSelect.checked = true;
    } else {
      // Fallback: If no match found, uncheck all to prevent stale data
      editModal
        .querySelectorAll('input[name="sev_id"]')
        .forEach((r) => (r.checked = false));
    }

    openEditModal();
  });
});
// 5. Add Report Form Submission
// Locate this section in your reports.js
// 5. Add Report Form Submission
if (addReportForm) {
  addReportForm.addEventListener("submit", function (e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const severityOptions = this.querySelectorAll('input[name="sev_id"]');
    const severityError = document.getElementById("severity-error");
    const fileInput = document.getElementById('rep_img_input');

    // 1. Check Severity (Existing logic)
    const isChecked = Array.from(severityOptions).some((r) => r.checked);
    if (!isChecked) {
      e.preventDefault();
      if (severityError) severityError.classList.remove("hidden");
      return false;
    }

    // 2. NEW: Check File Size (25MB Limit)
    if (fileInput.files.length > 0) {
      const maxSize = 25 * 1024 * 1024; // 25MB in bytes
      const fileSize = fileInput.files[0].size;

      if (fileSize > maxSize) {
        e.preventDefault();
        // Gumamit tayo ng alert o kaya yung existing showToast function mo
        showToast(
          '<i class="fas fa-exclamation-triangle mr-2"></i>Masyadong malaki ang file! Max 25MB lang.',
          "bg-red-500 text-white"
        );
        return false;
      }
    }

    // 3. UI Loading State (Existing logic)
    submitBtn.style.pointerEvents = "none";
    submitBtn.classList.add("opacity-70", "cursor-not-allowed");
    submitBtn.innerHTML =
      '<i class="fas fa-circle-notch fa-spin mr-2"></i>Sending...';

    // Submit the form
    const form = this;
    setTimeout(() => {
      form.submit();
    }, 100);
  });
}

//6. Filtering Logic
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput"); // Ensure your input has this ID
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
      // 1. Get all data
      const ref = row.getAttribute("data-ref").toLowerCase();
      const reporter = row.getAttribute("data-reporter").toLowerCase();
      const desc = row.getAttribute("data-desc").toLowerCase();
      const catId = row.getAttribute("data-cat");
      const modId = row.getAttribute("data-mod");
      const sevId = row.getAttribute("data-sev");

      // 2. Search Logic (Ref OR Reporter OR Description)
      const matchesSearch =
        searchTerm === "" ||
        ref.includes(searchTerm) ||
        reporter.includes(searchTerm) ||
        desc.includes(searchTerm);

      // 3. Filter Logic
      const matchesCat = catVal === "" || catId === catVal;
      const matchesMod = modVal === "" || modId === modVal;
      const matchesSev = sevVal === "" || sevId === sevVal;

      // 4. Combine everything
      if (matchesSearch && matchesCat && matchesMod && matchesSev) {
        row.classList.remove("hidden"); // Using Tailwind's 'hidden' class
        visibleCount++;
      } else {
        row.classList.add("hidden");
      }
    });

    // 5. Handle "No Results" display
    if (visibleCount === 0) {
      noResultsRow.classList.remove("hidden");
    } else {
      noResultsRow.classList.add("hidden");
    }
  }

  // Listeners
  searchInput.addEventListener("input", applyFilters);
  categoryFilter.addEventListener("change", applyFilters);
  moduleFilter.addEventListener("change", applyFilters);
  severityFilter.addEventListener("change", applyFilters);

  resetBtn.addEventListener("click", () => {
    searchInput.value = "";
    categoryFilter.value = "";
    moduleFilter.value = "";
    severityFilter.value = "";
    applyFilters();
  });
});
