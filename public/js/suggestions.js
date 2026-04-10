function toggleModal(show) {
  const modal = document.getElementById("projectModal");
  const backdrop = document.getElementById("projectModalBackdrop");
  const container = document.getElementById("projectModalContainer");

  if (!modal || !backdrop || !container) return;

  if (show) {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.style.overflow = "hidden";

    // Small delay ensures the browser registers the 'hidden' removal before animating
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

// Global Backdrop Click Listener
window.addEventListener("click", (e) => {
  if (e.target.id === "projectModalBackdrop") toggleModal(false);
});

/**
 * Status Updater (Event Delegation Pattern)
 * Listens for changes on any .status-updater inside the document
 */
document.addEventListener("change", function (e) {
  if (!e.target.classList.contains("status-updater")) return;

  const select = e.target;
  const suggestionId = select.getAttribute("data-report-id");
  const statusId = select.value;

  // Safety check for PHP-injected global
  if (typeof currentUserId === "undefined") {
    showToast("Error: User session not found. Please refresh.", "bg-amber-600");
    return;
  }

  // Visual feedback for 'Processing'
  select.style.opacity = "0.5";
  select.disabled = true;

  const params = new URLSearchParams();
  params.append("suggestion_id", suggestionId);
  params.append("status_id", statusId);
  params.append("updated_by", currentUserId);

  fetch("../controllers/quick_update_suggestion.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: params,
  })
    .then((res) => res.json())
    .then((data) => {
      select.style.opacity = "1";
      select.disabled = false;

      if (data.success) {
        showToast('<i class="fas fa-check-circle mr-2"></i>Suggestion updated!', "bg-green-600");

        // Remove card for 'Completed' (3) or 'Rejected' (4)
        if (["3", "4"].includes(statusId)) {
          const card = select.closest(".group");
          if (card) {
            card.style.transition = "all 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
            card.style.opacity = "0";
            card.style.transform = "translateY(20px) scale(0.95)";

            setTimeout(() => {
              card.remove();

              // Only reload if the ENTIRE grid is now empty
              const remainingCards = document.querySelectorAll(".group");
              if (remainingCards.length === 0) {
                location.reload();
              }
            }, 500);
          }
        }
      } else {
        showToast("Error: " + (data.error || "Update failed"), "bg-red-600");
      }
    })
    .catch((err) => {
      select.style.opacity = "1";
      select.disabled = false;
      console.error("Fetch Error:", err);
      showToast("Network error. Try again.", "bg-red-600");
    });
});

/**
 * Utility: UI Toast
 */
function showToast(message, bgColor) {
  const toast = document.createElement("div");
  toast.className = `fixed top-24 right-5 ${bgColor} text-white px-6 py-3 rounded-2xl shadow-2xl z-[200] transition-all duration-300 transform translate-x-10 opacity-0`;
  toast.innerHTML = message;
  document.body.appendChild(toast);

  // Trigger Slide-in
  requestAnimationFrame(() => {
    toast.classList.remove("translate-x-10", "opacity-0");
  });

  setTimeout(() => {
    toast.classList.add("translate-x-10", "opacity-0");
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}
