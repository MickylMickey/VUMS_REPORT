document.addEventListener("DOMContentLoaded", () => {
  // --- 1. ELEMENTS ---
  const bell = document.getElementById("notif-bell");
  const dropdown = document.getElementById("notif-dropdown");
  const wrapper = document.getElementById("notification-wrapper");
  const profileDropdown = document.getElementById("profileDropdown");
  const markBtn = document.getElementById("mark-all-read");

  // --- 2. DROPDOWN TOGGLE ---
  if (bell && dropdown) {
    bell.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("hidden");

      // Close profile dropdown if it's open to avoid overlap
      profileDropdown?.classList.add("hidden");
    });
  }

  // --- 3. CLOSE ON CLICK OUTSIDE ---
  document.addEventListener("click", (e) => {
    if (wrapper && !wrapper.contains(e.target)) {
      dropdown?.classList.add("hidden");
    }
  });

  // --- 4. MARK ALL AS READ ---
  if (markBtn) {
    markBtn.addEventListener("click", function (e) {
      e.preventDefault();

      const userId = this.getAttribute("data-userid");
      const role = this.getAttribute("data-role");

      // Disable button immediately to prevent double-clicking
      this.style.pointerEvents = "none";
      this.style.opacity = "0.5";

      fetch("../controllers/mark_notifications_read.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `user_id=${userId}&role=${role}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // 1. Remove the red notification dot/ping
            const dot = document.querySelector("#notif-bell span.flex");
            if (dot) dot.remove();

            // 2. Clear unread styling (blue backgrounds and borders)
            const notificationItems = document.querySelectorAll("#notif-dropdown div.p-4");
            notificationItems.forEach((item) => {
              item.classList.remove("bg-blue-50/20", "border-l-4", "border-l-blue-500");
            });

            // 3. Update button UI
            this.innerText = "All caught up!";
            console.log("Success: Notifications updated.");
          } else {
            console.error("Server error:", data.message);
            // Re-enable if it failed
            this.style.pointerEvents = "auto";
            this.style.opacity = "1";
          }
        })
        .catch((error) => {
          console.error("Fetch error:", error);
          this.style.pointerEvents = "auto";
          this.style.opacity = "1";
        });
    });
  } else {
    console.warn("Notification: 'mark-all-read' button not found on this page.");
  }
});
