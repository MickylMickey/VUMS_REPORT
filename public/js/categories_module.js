// Grab the elements
const editModal = document.getElementById("editCategoryModal");
const editModalContent = document.getElementById("categoryModalContent");
const editCategoryId = document.getElementById("edit_cat_id");
const editCategoryName = document.getElementById("edit_category_input");
const editCategoryDesc = document.getElementById("edit_cat_desc_input");

function openEditCategoryModal(id, name, desc) {
  // Check if elements exist to avoid console errors
  if (!editCategoryId || !editCategoryName || !editCategoryDesc) return;

  // Set values using the variables defined above
  editCategoryId.value = id;
  editCategoryName.value = name;
  editCategoryDesc.value = desc;

  // Show wrapper
  editModal.classList.remove("hidden");
  editModal.classList.add("flex");

  // Animation trigger
  setTimeout(() => {
    editModalContent.classList.remove("opacity-0", "scale-95");
    editModalContent.classList.add("opacity-100", "scale-100");
  }, 10);
}

// 1. Grab Module-specific elements
const editModuleModal = document.getElementById("editModuleModal");
const editModuleContent = document.getElementById("moduleModalContent");

const inputModuleId = document.getElementById("edit_module_id");
const inputModuleName = document.getElementById("edit_module_name_input");
const inputModuleDesc = document.getElementById("edit_module_desc_input");

/**
 * Opens the Module Edit Modal
 */
function openEditModuleModal(id, name, desc) {
  if (!editModuleModal || !inputModuleId) return;

  // Populate fields
  inputModuleId.value = id;
  inputModuleName.value = name;
  inputModuleDesc.value = desc;

  // Show modal wrapper
  editModuleModal.classList.remove("hidden");
  editModuleModal.classList.add("flex");

  // Trigger animation
  setTimeout(() => {
    editModuleContent.classList.remove("opacity-0", "scale-95");
    editModuleContent.classList.add("opacity-100", "scale-100");
  }, 10);
}

/**
 * Closes the Module Edit Modal
 */
function closeEditModuleModal() {
  if (!editModuleModal) return;

  editModuleContent.classList.remove("opacity-100", "scale-100");
  editModuleContent.classList.add("opacity-0", "scale-95");

  setTimeout(() => {
    editModuleModal.classList.add("hidden");
    editModuleModal.classList.remove("flex");
  }, 300);
}

// Close Module Modal when clicking on the dark background
if (editModuleModal) {
  editModuleModal.addEventListener("click", (event) => {
    if (event.target === editModuleModal) {
      closeEditModuleModal();
    }
  });
}

// Renamed to match the specific action
function closeEditCategoryModal() {
  editModalContent.classList.remove("opacity-100", "scale-100");
  editModalContent.classList.add("opacity-0", "scale-95");

  setTimeout(() => {
    editModal.classList.add("hidden");
    editModal.classList.remove("flex");
  }, 300);
}

// Click outside to close
if (editModal) {
  editModal.addEventListener("click", (event) => {
    // Only close if the background (editModal) was clicked, not the content box
    if (event.target === editModal) closeEditCategoryModal();
  });
}
