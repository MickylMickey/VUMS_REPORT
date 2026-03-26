// Edit User Modal
const editModal = document.getElementById("editUserModal");
const editModalContent = document.getElementById("editUserModalContent");
const editUserId = document.getElementById("editUserId");
const editUserName = document.getElementById("username_edit");
const editUserEmail = document.getElementById("email_edit");
const editUserRole = document.getElementById("user_role_edit");

function openEditUserModal(id, name, email, role) {
  editUserId.value = id;
  editUserName.value = name;
  editUserEmail.value = email;
  editUserRole.value = role;

  editModal.classList.remove("hidden");
  editModal.classList.add("flex");

  setTimeout(() => {
    editModalContent.classList.remove("opacity-0", "scale-95");
    editModalContent.classList.add("opacity-100", "scale-100");
  }, 10);
}

function closeEditUserModal() {
  editModalContent.classList.remove("opacity-100", "scale-100");
  editModalContent.classList.add("opacity-0", "scale-95");
  setTimeout(() => {
    editModal.classList.remove("flex");
    editModal.classList.add("hidden");
  }, 300);
}

if (editModal) {
  editModal.addEventListener("click", (event) => {
    if (event.target === editModal) closeEditUserModal();
  });
}

// Close filter dropdown on outside click
document.addEventListener("click", function (event) {
  const filter = document.getElementById("filter");
  const popup = document.getElementById("filterPopup");
  if (
    filter &&
    popup &&
    !popup.contains(event.target) &&
    !filter.contains(event.target)
  ) {
    filter.checked = false;
  }
});
