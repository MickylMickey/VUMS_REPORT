// Note: Do NOT define currentUserId here if it's already defined in the PHP script tag
function toggleModal(show) {
  const modal = document.getElementById("projectModal");
  if (!modal) return;

  if (show) {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.style.overflow = "hidden"; // Prevent scrolling when open
  } else {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.style.overflow = "auto"; // Restore scrolling
  }
}

// Close modal when clicking the backdrop
window.onclick = function (event) {
  const modal = document.getElementById("projectModal");
  if (event.target === modal) {
    toggleModal(false);
  }
};

// Your Status Updater Logic (remains mostly the same)
document.querySelectorAll(".status-updater").forEach((select) => {
  select.addEventListener("change", function () {
    const suggestionId = this.getAttribute("data-report-id");
    const statusId = this.value;

    this.style.opacity = "0.5";

    // currentUserId must be defined globally in your PHP file
    fetch("../controllers/quick_update_suggestion.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: `suggestion_id=${suggestionId}&status_id=${statusId}&updated_by=${currentUserId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        this.style.opacity = "1";
        if (data.success) {
          showToast("Status updated successfully!");

          // Remove row logic
          if (parseInt(statusId) === 3 || parseInt(statusId) === 4) {
            const row = this.closest("tr");
            if (row) {
              row.style.transition = "all 0.5s ease";
              row.style.opacity = "0";
              row.style.transform = "translateX(20px)";
              setTimeout(() => row.remove(), 500);
            }
          }
        } else {
          alert("Update failed: " + (data.error || "Unknown error"));
        }
      })
      .catch((error) => {
        this.style.opacity = "1";
        console.error("Error:", error);
      });
  });
});

// Helper for the Toast to keep code clean
function showToast(message) {
  const statusToast = document.createElement("div");
  statusToast.className = "fixed top-24 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-[110] transition-all duration-500";
  statusToast.innerHTML = message;
  document.body.appendChild(statusToast);

  setTimeout(() => {
    statusToast.style.opacity = "0";
    statusToast.style.transform = "translateY(-20px)";
    setTimeout(() => statusToast.remove(), 500);
  }, 3000);
}
