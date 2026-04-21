/**
 * GENERIC MODAL CONTROLS
 */

// Generic function
function openGenericModal(modalId, contentId) {
  const modal = document.getElementById(modalId);
  const content = document.getElementById(contentId);

  if (!modal || !content) return;

  modal.classList.remove("hidden");
  modal.classList.add("flex");

  setTimeout(() => {
    content.classList.remove("opacity-0", "scale-95");
    content.classList.add("opacity-100", "scale-100");
  }, 10);
}

function closeGenericModal(modalId, contentId) {
  const modal = document.getElementById(modalId);
  const content = document.getElementById(contentId);

  if (!modal || !content) return;

  // Reverse animation (Pop-out)
  content.classList.remove("opacity-100", "scale-100");
  content.classList.add("opacity-0", "scale-95");

  setTimeout(() => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  }, 300);
}

/**
 * SPECIFIC MODAL HANDLERS
 */

// --- CATEGORY ---
function openEditCategoryModal(id, name, desc) {
  document.getElementById("edit_cat_id").value = id;
  document.getElementById("edit_category_input").value = name;
  document.getElementById("edit_cat_desc_input").value = desc;

  openGenericModal("editCategoryModal", "categoryModalContent");
}

function closeEditCategoryModal() {
  closeGenericModal("editCategoryModal", "categoryModalContent");
}

// --- MODULE ---
function openEditModuleModal(id, name, desc) {
  document.getElementById("edit_module_id").value = id;
  document.getElementById("edit_module_name_input").value = name;
  document.getElementById("edit_module_desc_input").value = desc;

  openGenericModal("editModuleModal", "moduleModalContent");
}

function closeEditModuleModal() {
  closeGenericModal("editModuleModal", "moduleModalContent");
}

/**
 * CLICK OUTSIDE TO CLOSE
 */
const allModalIds = ["editCategoryModal", "editModuleModal", "addCategoryModal", "addModuleModal"];

allModalIds.forEach((id) => {
  const modal = document.getElementById(id);
  if (modal) {
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        const content = modal.querySelector('[id*="Content"]') || modal.querySelector('[id*="Container"]');
        if (content) closeGenericModal(id, content.id);
      }
    });
  }
});

/**
 * NEW: TABLE FILTERING LOGIC
 */
function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const filter = input.value.toUpperCase();
  const table = document.getElementById(tableId);
  const tr = table.getElementsByTagName("tr");

  for (let i = 1; i < tr.length; i++) {
    let matchFound = false;
    const tdArray = tr[i].getElementsByTagName("td");

    for (let j = 0; j < tdArray.length - 1; j++) {
      if (tdArray[j]) {
        const textValue = tdArray[j].textContent || tdArray[j].innerText;
        if (textValue.toUpperCase().indexOf(filter) > -1) {
          matchFound = true;
          break;
        }
      }
    }

    tr[i].style.display = matchFound ? "" : "none";
  }
}
