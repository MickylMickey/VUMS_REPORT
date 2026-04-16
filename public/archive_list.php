<?php
require_once __DIR__ . "/../init.php";
ob_start();

$user = checkAuth('Admin');
$role = $_SESSION['user_role_id'] ?? '';
$userVisibility = new UserVisibility($conn);


$where = "u.user_status_id = ?";
$params = [2];
$types = "i";


$pagination = getPaginationData(
    $conn,
    "users u INNER JOIN user_profile up ON u.user_id = up.user_id",
    $_GET['limit'] ?? 10,
    $_GET['page'] ?? 1,
    $where,
    $params,
    $types
);

$limit = $pagination['limit'];
$offset = $pagination['offset'];
$totalPages = $pagination['totalPages'];
$totalRecords = $pagination['totalRecords'];
$page = $pagination['page'];


$users = $userVisibility->getVisibleUsers($limit, $offset, 2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Archived Users</title>
</head>

<body class="pt-24 bg-slate-50">
    <?php include "templates/navbar.php"; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Archived Users</h1>
                <p class="text-sm text-gray-500">View and manage archived users</p>
            </div>

            <div class="relative w-48 sm:w-56 md:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input type="text" id="searchInput" data-tooltip="Search archived users by full name, username, or role"
                    placeholder="Search by Username or Name"
                    class="w-full pl-11 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all shadow-sm">
            </div>
            <div id="validationBlock" class="fixed bottom-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
                <div class="pointer-events-auto">
                            <?= showValidation() ?>
                </div>
            </div>
        </div>


        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50">
                        <tr class="bg-blue-500 border-b border-blue-600">
                            <th class="p-4 text-[15px] font-semibold uppercase text-white ">Full Name</th>
                            <th class="p-4 text-[15px] font-semibold uppercase text-white text-center">Username</th>
                            <th class="p-4 text-[15px] font-semibold uppercase text-white text-center">Role</th>
                            <th class="p-4 text-[15px] font-semibold uppercase text-white text-center">Status</th>
                            <th class="p-4 text-[15px] font-semibold uppercase text-white text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody" class="divide-y divide-gray-50">
                        <?php if (!empty($users)):
                            foreach ($users as $u):
                                $fullName = htmlspecialchars($u['user_first_name'] . ' ' . $u['user_last_name']);
                                $username = htmlspecialchars($u['username']);
                                $roleName = htmlspecialchars($u['role_name']);
                                ?>
                                <tr class="report-row hover:bg-gray-50/50 transition-colors"
                                    data-search="<?= strtolower($fullName . ' ' . $username . ' ' . $roleName) ?>">

                                    <td class="p-4 text-sm font-[13px] text-gray-700">
                                        <?= $fullName ?>
                                    </td>

                                    <td class="p-4 text-center text-[15px] font-mono text-gray-600">
                                        <?= $username ?>
                                    </td>

                                    <td class="p-5 text-center">
                                        <span
                                            class="px-2 py-1 <?= $u['role_name'] === 'Admin' ? 'bg-red-600 text-white border-red-700' : 'bg-blue-50 text-blue-700 border-blue-100' ?> rounded-full text-[13px] font-bold uppercase tracking-wider border">
                                            <?= $roleName ?>
                                        </span>
                                    </td>

                                    <td class="p-4 text-center">
                                        <span
                                            class="px-2 py-1 bg-amber-50 text-amber-700 rounded-full text-[12px] font-bold uppercase tracking-wider border border-amber-100">
                                            Archived
                                        </span>
                                    </td>

                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">

                                            <!-- VIEW -->
                                            <a href="../public/user_profile.php?u=<?= $u['user_id'] ?>"
                                                data-tooltip="View profile details" class="group inline-flex items-center justify-center w-12 h-12 rounded-xl
                  bg-emerald-50 text-emerald-600 border border-emerald-100
                  hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:shadow-emerald-200
                  active:scale-95 transition-all duration-200">

                                                <i
                                                    class="fa-solid fa-user-shield text-lg group-hover:scale-110 transition-transform"></i>
                                            </a>

                                            <!-- RESTORE -->
                                            <button
                                                onclick="openRestoreUserModal('<?= $u['user_id'] ?>', '<?= addslashes($u['username']) ?>')"
                                                data-tooltip="Restore this user" style="
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #cffafe;
        color: #0e7490;
        border: 1px solid #a5f3fc;
        transition: all 0.2s ease;
    " onmouseover="
        this.style.backgroundColor='#06b6d4';
        this.style.color='#ffffff';
        this.style.boxShadow='0 10px 20px rgba(34,211,238,0.4)';
    " onmouseout="
        this.style.backgroundColor='#cffafe';
        this.style.color='#0e7490';
        this.style.boxShadow='none';
    " onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'">

                                                <i class="fa-solid fa-rotate-left text-lg"></i>
                                            </button>

                                            <!-- DELETE -->
                                            <button
                                                onclick="openDeletePermanentModal('<?= $u['user_id'] ?>', '<?= addslashes($u['username']) ?>')"
                                                data-tooltip="Delete permanently" class="group inline-flex items-center justify-center w-12 h-12 rounded-xl
                   bg-red-50 text-red-600 border border-red-100
                   hover:bg-red-600 hover:text-white hover:shadow-lg hover:shadow-red-200
                   active:scale-95 transition-all duration-200">

                                                <i
                                                    class="fa-solid fa-trash-can text-lg group-hover:scale-110 transition-transform"></i>
                                            </button>

                                        </div>
                                    </td>
                                    </td>

                                <?php endforeach; ?>

                            <tr id="noResultsRow" class="hidden">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-3xl mb-2 block opacity-20"></i>
                                    No users match your search criteria.
                                </td>
                            </tr>

                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-500 italic">
                                    No archived users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="restoreUserModal"
        class="fixed inset-0 z-[200] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm opacity-0 transition-all duration-300">

        <div id="restoreModalContent"
            class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 text-center">

            <!-- ICON -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <div class="relative">
                    <i class="fa-solid fa-user text-green-700 text-2xl"></i>

                    <!-- refresh overlay -->
                    <i class="fa-solid fa-rotate-left text-green-500 text-sm absolute -bottom-1 -right-1"></i>
                </div>
            </div>

            <!-- TITLE -->
            <h3 class="text-xl font-bold text-gray-900">Restore User?</h3>

            <!-- DESCRIPTION -->
            <p class="text-sm text-gray-500 mt-2">
                Would you like to restore
                <span id="restoreUserNameDisplay" class="font-bold text-gray-800"></span>
                to the active user list? They will be able to login to the system again.
            </p>

            <!-- ACTIONS -->
            <form action="../controllers/restore_user.php" method="POST" class="mt-8 flex justify-center gap-3">

                <input type="hidden" name="user_id" id="restoreUserId">

                <!-- CANCEL (FIXED COLOR) -->
                <button type="button" onclick="toggleRestoreModal(false)" style="
            flex: 1;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 500;
            color: #374151;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
            cursor: pointer;
        " onmouseover="this.style.backgroundColor='#e5e7eb'" onmouseout="this.style.backgroundColor='#f3f4f6'">
                    Cancel
                </button>

                <!-- CONFIRM -->
                <button type="submit" class="flex-1 px-4 py-2.5 
                           bg-green-600 text-white 
                           rounded-xl font-medium
                           hover:bg-green-700 
                           shadow-lg shadow-green-200
                           transition-all">
                    Confirm Restore
                </button>
            </form>

        </div>
    </div>
    <div id="deletePermanentModal" class="fixed inset-0 z-[200] hidden items-center justify-center 
            bg-slate-900/80 backdrop-blur-md 
            opacity-0 transition-all duration-300">

        <!-- Modal -->
        <div id="deleteModalContent" class="bg-white w-full max-w-md mx-4 
                rounded-3xl shadow-2xl 
                transform scale-95 opacity-0 transition-all duration-300 
                overflow-hidden border border-red-100
                py-2">

            <!-- Header -->
            <div class="px-6 pt-7 pb-5 text-center">
                <div class="mx-auto flex items-center justify-center 
                        w-16 h-16 rounded-full 
                        bg-red-100 text-red-600 mb-5
                        shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="text-xl font-bold text-gray-900">
                    Delete Account Permanently
                </h3>

                <p class="text-[17px] text-gray-500 mt-2 leading-relaxed">
                    This will permanently delete
                    <span id="deleteUserNameDisplay" class="font-semibold text-red-600"></span>.
                    <br>
                    <span class="text-red-500 font-medium">
                        This action cannot be undone.
                    </span>
                </p>
            </div>

            <!-- Warning Bar -->
            <div
                class="bg-red-50 border-t border-b border-red-100 px-6 py-3 text-[15px] text-red-600 text-center font-medium">
                All user data will be permanently removed from the system.
            </div>

            <!-- Actions -->
            <form action="../controllers/delete_user_permanent.php" method="POST" class="px-6 py-5 flex flex-col gap-3">

                <input type="hidden" name="user_id" id="deleteUserId">

                <!-- TIMER -->
                <div class="text-center">
                    <p class="text-[18px] text-gray-500">
                        You can delete in
                        <span id="deleteTimer" class="font-semibold text-red-600 text-[18px]">5</span>s
                    </p>
                </div>

                <div class="flex gap-3">

                    <!-- Cancel -->
                    <button type="button" onclick="toggleDeleteModal(false)" class="flex-1 py-2.5 rounded-xl 
                               border border-gray-300 bg-white 
                               text-gray-700 font-medium
                               hover:bg-gray-50 hover:border-gray-400
                               transition-all duration-200">
                        Cancel
                    </button>

                    <!-- Delete -->
                    <button type="submit" id="confirmDeleteBtn" disabled style="
            background-color: #dc2626;
            color: white;
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 12px;
            width: 50%;
            transition: 0.2s;
            opacity: 1;
            cursor: not-allowed;
        " onmouseover="if(!this.disabled){this.style.backgroundColor='#b91c1c'}"
                        onmouseout="if(!this.disabled){this.style.backgroundColor='#dc2626'}">
                        Delete Permanently
                    </button>

                </div>
            </form>
        </div>
    </div>
    <div id="tooltip"
        class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
    </div>
    <script src="js/removeNotification.js" defer></script>
    <script>
        // --- SEARCH LOGIC (Based on user_list.php) ---
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.report-row');
            const noResultsRow = document.getElementById('noResultsRow');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase().trim();
                    let hasVisibleRows = false;

                    rows.forEach(row => {
                        const searchText = row.getAttribute('data-search') || "";
                        if (searchText.includes(searchTerm)) {
                            row.classList.remove('hidden');
                            hasVisibleRows = true;
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    if (hasVisibleRows) {
                        noResultsRow.classList.add('hidden');
                    } else {
                        noResultsRow.classList.remove('hidden');
                    }
                });
            }
        });

        // --- MODAL ELEMENTS ---
        const restoreModal = document.getElementById("restoreUserModal");
        const restoreContent = document.getElementById("restoreModalContent");
        const deleteModal = document.getElementById("deletePermanentModal");
        const deleteContent = document.getElementById("deleteModalContent");

        // --- TIMER STATE ---
        let deleteCountdownInterval = null;

        // --- RESTORE MODAL LOGIC ---
        function openRestoreUserModal(id, username) {
            document.getElementById("restoreUserId").value = id;
            document.getElementById("restoreUserNameDisplay").textContent = username;
            toggleRestoreModal(true);
        }

        function toggleRestoreModal(show) {
            if (show) {
                restoreModal.classList.remove("hidden");
                restoreModal.classList.add("flex");

                setTimeout(() => {
                    restoreModal.classList.add("opacity-100");
                    restoreContent.classList.remove("opacity-0", "scale-95");
                    restoreContent.classList.add("opacity-100", "scale-100");
                }, 10);
            } else {
                restoreContent.classList.add("opacity-0", "scale-95");
                restoreModal.classList.replace("opacity-100", "opacity-0");

                setTimeout(() => {
                    restoreModal.classList.replace("flex", "hidden");
                }, 300);
            }
        }

        // --- DELETE PERMANENT MODAL LOGIC ---
        function openDeletePermanentModal(id, username) {
            document.getElementById("deleteUserId").value = id;
            document.getElementById("deleteUserNameDisplay").textContent = username;
            toggleDeleteModal(true);
        }

        function startDeleteTimer() {
            let timeLeft = 5;

            const timerEl = document.getElementById("deleteTimer");
            const btn = document.getElementById("confirmDeleteBtn");

            clearInterval(deleteCountdownInterval);

            // 🔒 FULL LOCK STATE (constant blur for 5 seconds)
            btn.disabled = true;
            btn.style.cursor = "not-allowed";
            btn.style.opacity = "1";
            btn.style.filter = "blur(2px) brightness(0.9)";
            btn.style.transition = "0.3s ease";

            // show timer container
            const timerContainer = timerEl.closest("div");
            if (timerContainer) timerContainer.style.display = "block";

            timerEl.textContent = timeLeft;

            deleteCountdownInterval = setInterval(() => {
                timeLeft--;

                if (timeLeft > 0) {
                    timerEl.textContent = timeLeft;
                    // ❌ NO MORE FILTER CHANGES (important fix)
                } else {
                    clearInterval(deleteCountdownInterval);

                    // enable button
                    btn.disabled = false;
                    btn.style.cursor = "pointer";

                    // remove blur instantly
                    btn.style.filter = "none";

                    // hide timer
                    if (timerContainer) timerContainer.style.display = "none";
                }
            }, 1000);
        }

        function toggleDeleteModal(show) {
            if (show) {
                deleteModal.classList.remove("hidden");
                deleteModal.classList.add("flex");

                setTimeout(() => {
                    deleteModal.classList.add("opacity-100");
                    deleteContent.classList.remove("opacity-0", "scale-95");
                    deleteContent.classList.add("opacity-100", "scale-100");
                }, 10);

                // 🔥 START TIMER WHEN OPEN
                startDeleteTimer();

            } else {
                deleteContent.classList.add("opacity-0", "scale-95");
                deleteModal.classList.replace("opacity-100", "opacity-0");

                setTimeout(() => {
                    deleteModal.classList.replace("flex", "hidden");
                }, 300);

                // reset timer when closed
                clearInterval(deleteCountdownInterval);
            }
        }

        // --- CLOSE MODALS ON OVERLAY CLICK ---
        window.onclick = (e) => {
            if (e.target === restoreModal) toggleRestoreModal(false);
            if (e.target === deleteModal) toggleDeleteModal(false);
        }
    </script>
</body>
<script src="js/tooltip.js"></script>

</html>
<?php ob_end_flush(); ?>