/**
 * MODAL ELEMENTS
 */
// Add User
const addModal = document.getElementById("addUserModal");
const addContainer = document.getElementById("addUserModalContent") || addModal?.querySelector(".bg-white");


const editModal = document.getElementById("editUserModal");
const editContainer = document.getElementById("editUserModalContent") || editModal?.querySelector(".bg-white");


const archiveModal = document.getElementById("archiveUserModal");
const archiveContainer = document.getElementById("archiveModalContent") || archiveModal?.querySelector(".bg-white");
const archiveModalContent = document.getElementById("archiveModalContent");
const archiveUserIdField = document.getElementById("archiveUserId");
const archiveUserIdInput = document.getElementById("archiveUserId");

/**
 * TOGGLE FUNCTIONS
 */

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
        setTimeout(() => { archiveModal.classList.replace("flex", "hidden"); }, 300);
    }
}

/**
 * OPEN MODAL FUNCTIONS
 */

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

/**
 * GLOBAL CLICK-TO-CLOSE
 */
window.addEventListener("click", (e) => {
    if (e.target === addModal) toggleAddModal(false);
    if (e.target === editModal) toggleEditModal(false);
    if (e.target === archiveModal) toggleArchiveModal(false);
});

/**
 * USER LIST FILTERING
 */
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
    // Pinalitan natin ito para tumugma sa function mo sa taas
    if (typeof toggleEditModal === "function") toggleEditModal(false);
    if (typeof closeArchiveUserModal === "function") closeArchiveUserModal();
  }
});