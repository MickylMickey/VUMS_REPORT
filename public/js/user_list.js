// --- Modal Elements: Add User ---
const addModal = document.getElementById("addUserModal");
const addContainer = addModal?.querySelector(".bg-white");
const addBackdrop = addModal; // The wrapper acts as the backdrop

// --- Modal Elements: Edit User ---
const editModal = document.getElementById("editUserModal");
const editContainer = editModal?.querySelector(".bg-white");
const editBackdrop = editModal;

/**
 * ADD USER MODAL LOGIC
 */
function toggleAddModal(show) {
  if (show) {
    // 1. Show wrapper
    addModal.classList.remove("hidden");
    addModal.classList.add("flex");

    // 2. Animate elements in
    setTimeout(() => {
      addContainer.classList.remove("scale-95", "opacity-0");
      addContainer.classList.add("scale-100", "opacity-100");
      addBackdrop.classList.remove("opacity-0");
      addBackdrop.classList.add("opacity-100");
    }, 10);
  } else {
    // 1. Animate elements out
    addContainer.classList.remove("scale-100", "opacity-100");
    addContainer.classList.add("scale-95", "opacity-0");
    addBackdrop.classList.remove("opacity-100");
    addBackdrop.classList.add("opacity-0");

    // 2. Hide after transition (matches duration-300)
    setTimeout(() => {
      addModal.classList.add("hidden");
      addModal.classList.remove("flex");
    }, 300);
  }
}

/**
 * EDIT USER MODAL LOGIC
 */
function openEditUserModal(id, username, roleId) {
  // 1. Populate fields
  const idField = document.getElementById("editUserId");
  const userField = document.getElementById("username_edit");
  const roleField = document.getElementById("user_role_edit");
  const passField = document.getElementById("password_edit");

  if (idField) idField.value = id;
  if (userField) userField.value = username;
  if (roleField) {
    roleField.value = roleId;
  }
  if (passField) passField.value = "";
  // 2. Trigger the toggle
  toggleEditModal(true);
}

function toggleEditModal(show) {
  if (show) {
    editModal.classList.remove("hidden");
    editModal.classList.add("flex");

    setTimeout(() => {
      editContainer.classList.remove("scale-95", "opacity-0");
      editContainer.classList.add("scale-100", "opacity-100");
      editBackdrop.classList.remove("opacity-0");
      editBackdrop.classList.add("opacity-100");
    }, 10);
  } else {
    editContainer.classList.remove("scale-100", "opacity-100");
    editContainer.classList.add("scale-95", "opacity-0");
    editBackdrop.classList.remove("opacity-100");
    editBackdrop.classList.add("opacity-0");

    setTimeout(() => {
      editModal.classList.add("hidden");
      editModal.classList.remove("flex");
    }, 300);
  }
}

// Global Click-to-Close (Outside the modal box)
window.addEventListener("click", (e) => {
  if (e.target === addModal) toggleAddModal(false);
  if (e.target === editModal) toggleEditModal(false);
});

const resetBtn = document.getElementById("resetBtn");
const searchInput = document.getElementById("searchInput");
const roleFilter = document.getElementById("roleFilter");
const reportRows = document.querySelectorAll(".report-row");
const noResultsRow = document.getElementById("noResultsRow");

function applyUserFilters() {
  const searchTerm = searchInput.value.toLowerCase().trim();
  const roleVal = roleFilter.value;

  let visibleCount = 0;

  reportRows.forEach((row) => {
    const username = (row.getAttribute("data-username") || "").toLowerCase();
    const roleID = row.getAttribute("data-role");

    const matchesSearch = searchTerm === "" || username.includes(searchTerm);
    const matchesRole = roleVal === "" || roleID === roleVal;

    if (matchesSearch && matchesRole) {
      row.classList.remove("hidden");
      visibleCount++;
    } else {
      row.classList.add("hidden");
    }
  });

  // Show/hide "No Results"
  if (noResultsRow) {
    if (visibleCount === 0) {
      noResultsRow.classList.remove("hidden");
    } else {
      noResultsRow.classList.add("hidden");
    }
  }
}

// Event listeners
if (searchInput) searchInput.addEventListener("input", applyUserFilters);
if (roleFilter) roleFilter.addEventListener("change", applyUserFilters);

if (resetBtn) {
  resetBtn.addEventListener("click", () => {
    searchInput.value = "";
    roleFilter.value = "";
    applyUserFilters();
  });
}
