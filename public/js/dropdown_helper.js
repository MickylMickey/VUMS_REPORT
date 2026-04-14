document.addEventListener("DOMContentLoaded", function () {
  function setupDescToggle(selectId, panelId, textId) {
    const selectEl = document.getElementById(selectId);
    const panelEl = document.getElementById(panelId);
    const textEl = document.getElementById(textId);

    selectEl.addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const description = selectedOption.getAttribute("data-desc");

      if (description && description.trim() !== "") {
        textEl.textContent = description;
        panelEl.classList.remove("hidden");
        // Optional: Add a subtle slide-down animation
        panelEl.style.display = "block";
      } else {
        panelEl.classList.add("hidden");
      }
    });
  }

  // Initialize for both dropdowns
  setupDescToggle("cat_id", "cat-desc-panel", "cat-desc-text");
  setupDescToggle("mod_id", "mod-desc-panel", "mod-desc-text");
});

function handleEditDesc(selectId, panelId) {
  const selectEl = document.getElementById(selectId);
  const panelEl = document.getElementById(panelId);
  const textEl = panelEl.querySelector(".edit-desc-text");

  function update() {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    // Only show if there's a selected option and it's not the placeholder
    if (selectedOption && selectedOption.value !== "") {
      const desc = selectedOption.getAttribute("data-desc");
      if (desc) {
        textEl.textContent = desc;
        panelEl.classList.remove("hidden");
        return;
      }
    }
    panelEl.classList.add("hidden");
  }

  // Listen for changes
  selectEl.addEventListener("change", update);

  // Run once on load for Edit modals (in case data is pre-selected)
  update();
}

handleEditDesc("edit_cat_id", "edit_cat_desc_panel");
handleEditDesc("edit_mod_id", "edit_mod_desc_panel");
