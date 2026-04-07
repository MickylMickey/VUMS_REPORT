document.addEventListener("DOMContentLoaded", () => {
  const profileButton = document.getElementById("profileButton");
  const profileDropdown = document.getElementById("profileDropdown");

  // 1. Toggle visibility when clicking the button
  profileButton.addEventListener("click", (event) => {
    // Prevent the click from bubbling up to the window listener
    event.stopPropagation();
    profileDropdown.classList.toggle("hidden");
  });

  // 2. Close the menu if the user clicks anywhere else on the page
  window.addEventListener("click", (event) => {
    if (!profileDropdown.classList.contains("hidden")) {
      // If the click is NOT inside the dropdown or on the button, hide it
      if (!profileDropdown.contains(event.target) && !profileButton.contains(event.target)) {
        profileDropdown.classList.add("hidden");
      }
    }
  });
});
