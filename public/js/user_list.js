const addModal = document.getElementById("addUserModal");
const addContainer = document.getElementById("addUserModalContent") || addModal?.querySelector(".bg-white");
const editModal = document.getElementById("editUserModal");
const editContainer = document.getElementById("editUserModalContent") || editModal?.querySelector(".bg-white");
const archiveModal = document.getElementById("archiveUserModal");
const archiveContainer = document.getElementById("archiveModalContent") || archiveModal?.querySelector(".bg-white");
const archiveModalContent = document.getElementById("archiveModalContent");
const archiveUserIdField = document.getElementById("archiveUserId");
const archiveUserIdInput = document.getElementById("archiveUserId");

function toggleAddModal(show) {
  if (!addModal) return;
  if (show) {
    addModal.classList.remove("hidden");
    addModal.classList.add("flex");
    setTimeout(() => {
      addModal.classList.remove("opacity-0");
      addModal.classList.add("opacity-100");
      addContainer?.classList.remove("scale-95", "opacity-0");
      addContainer?.classList.add("scale-100", "opacity-100");
    }, 10);
  } else {
    addContainer?.classList.remove("scale-100", "opacity-100");
    addContainer?.classList.add("scale-95", "opacity-0");
    addModal.classList.remove("opacity-100");
    addModal.classList.add("opacity-0");
    setTimeout(() => {
      addModal.classList.add("hidden");
      addModal.classList.remove("flex");
    }, 300);
  }
}

function toggleEditModal(show) {
  if (!editModal) return;
  if (show) {
    editModal.classList.remove("hidden");
    editModal.classList.add("flex");
    setTimeout(() => {
      editModal.classList.remove("opacity-0");
      editModal.classList.add("opacity-100");
      editContainer?.classList.remove("scale-95", "opacity-0");
      editContainer?.classList.add("scale-100", "opacity-100");
    }, 10);
  } else {
    editContainer?.classList.remove("scale-100", "opacity-100");
    editContainer?.classList.add("scale-95", "opacity-0");
    editModal.classList.remove("opacity-100");
    editModal.classList.add("opacity-0");
    setTimeout(() => {
      editModal.classList.add("hidden");
      editModal.classList.remove("flex");
    }, 300);
  }
}

function toggleArchiveModal(show) {
  if (show) {
    archiveModal.classList.remove("hidden");
    archiveModal.classList.add("flex");
    setTimeout(() => {
      archiveModal.classList.remove("opacity-0");
      archiveModal.classList.add("opacity-100");
      archiveModalContent.classList.remove("opacity-0", "scale-95");
      archiveModalContent.classList.add("opacity-100", "scale-100");
    }, 10);
  } else {
    archiveModalContent.classList.add("opacity-0", "scale-95");
    archiveModal.classList.replace("opacity-100", "opacity-0");
    setTimeout(() => {
      archiveModal.classList.replace("flex", "hidden");
    }, 300);
  }
}

function openEditUserModal(id, username, roleId) {
  const idField = document.getElementById("editUserId");
  const userField = document.getElementById("username_edit");
  const roleField = document.getElementById("user_role_edit");
  const passField = document.getElementById("password_edit");

  if (idField) idField.value = id;
  if (userField) userField.value = username;
  if (roleField) roleField.value = roleId;
  if (passField) passField.value = "";

  toggleEditModal(true);
}

function openArchiveModal(id, username) {
  const idField = document.getElementById("archiveUserId");
  const displayUser = document.getElementById("archiveUserNameDisplay");

  if (idField) idField.value = id;
  if (displayUser) displayUser.textContent = username;

  toggleArchiveModal(true);
}

window.addEventListener("click", (e) => {
  if (e.target === addModal) toggleAddModal(false);
  if (e.target === editModal) toggleEditModal(false);
  if (e.target === archiveModal) toggleArchiveModal(false);
});

const searchInput = document.getElementById("searchInput");
const roleFilter = document.getElementById("roleFilter");
const resetBtn = document.getElementById("resetBtn");
const reportRows = document.querySelectorAll(".report-row");
const noResultsRow = document.getElementById("noResultsRow");

function applyUserFilters() {
  const searchTerm = searchInput?.value.toLowerCase().trim() || "";
  const roleVal = roleFilter?.value || "";
  let visibleCount = 0;
  reportRows.forEach((row) => {
    const username = (row.getAttribute("data-username") || "").toLowerCase();
    const roleID = row.getAttribute("data-role") || "";

    const matchesSearch = searchTerm === "" || username.includes(searchTerm);
    const matchesRole = roleVal === "" || roleID === roleVal;

    if (matchesSearch && matchesRole) {
      row.classList.remove("hidden");
      visibleCount++;
    } else {
      row.classList.add("hidden");
    }
  });

  if (noResultsRow) {
    if (visibleCount === 0) {
      noResultsRow.classList.remove("hidden");
    } else {
      noResultsRow.classList.add("hidden");
    }
  }
}

if (searchInput) searchInput.addEventListener("input", applyUserFilters);
if (roleFilter) roleFilter.addEventListener("change", applyUserFilters);

if (resetBtn) {
  resetBtn.addEventListener("click", () => {
    if (searchInput) searchInput.value = "";
    if (roleFilter) roleFilter.value = "";
    applyUserFilters();
  });
}

function openArchiveUserModal(id) {
  document.getElementById("archiveUserId").value = id;
  const modal = document.getElementById("archiveUserModal");
  const content = document.getElementById("archiveModalContent");

  modal.classList.remove("hidden");
  modal.classList.add("flex");
  setTimeout(() => {
    modal.classList.add("opacity-100");
    content.classList.remove("opacity-0", "scale-95");
    content.classList.add("opacity-100", "scale-100");
  }, 10);
}

function closeArchiveUserModal() {
  archiveModalContent.classList.add("opacity-0", "scale-95");
  archiveModal.classList.replace("opacity-100", "opacity-0");
  setTimeout(() => {
    archiveModal.classList.replace("flex", "hidden");
  }, 300);
}

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    if (typeof toggleEditModal === "function") toggleEditModal(false);
    if (typeof closeArchiveUserModal === "function") closeArchiveUserModal();
  }
});

//toggle password for user creation
const passwordInput = document.querySelector("#password");
const toggleButton = document.querySelector("#togglePassword");
const eyeIcon = document.querySelector("#eyeIcon");

toggleButton.addEventListener("click", function () {
  const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
  passwordInput.setAttribute("type", type);

  if (type === "text") {
    eyeIcon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
  } else {
    eyeIcon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.67 8.5 7.652 6 12 6c4.348 0 8.33 2.5 9.964 5.678a1.012 1.012 0 0 1 0 .644C20.33 15.5 16.348 18 12 18c-4.348 0-8.33-2.5-9.964-5.678Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
  }
});

//password toggle for edit modal profile
function togglePasswordVisibility() {
  const passwordInput = document.getElementById("password_edit");
  const icon = document.getElementById("toggleIcon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    // Optional: Change icon to "eye-slash"
    icon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
  } else {
    passwordInput.type = "password";
    // Change icon back to "eye"
    icon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
  }
}

const input = document.getElementById("prof-pic-input");
const preview = document.getElementById("preview-img");

input.addEventListener("change", function () {
  const file = this.files[0];

  if (file) {
    // Create a temporary URL for the selected file
    const reader = new FileReader();

    // When the file is read, set the img src to the result
    reader.onload = function (e) {
      preview.src = e.target.result;
    };

    reader.readAsDataURL(file);
  }
});
