<?php
require_once __DIR__ . "/../init.php";
ob_start();

$user = checkAuth('Admin');
$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);
$userVisibility = new UserVisibility($conn);
$roleOptions = fetchRoles($conn);


// 1. Define your filters first
$where = "u.user_status_id != ?";
$params = [0];
$types = "i";

// 2. RUN PAGINATION FIRST to generate $limit and $offset
$pagination = getPaginationData(
    $conn,
    "users u", // Use the alias 'u' to match your $where clause
    $_GET['limit'] ?? 10,
    $_GET['page'] ?? 1,
    $where,
    $params,
    $types
);

// 3. NOW you can extract these (this fixes the 'Undefined variable' warning)
$limit = $pagination['limit'];
$offset = $pagination['offset'];
$totalPages = $pagination['totalPages'];
$totalRecords = $pagination['totalRecords'];
$page = $pagination['page'];

// 4. FINALLY, fetch the users using those fresh variables
$users = $userVisibility->getVisibleUsers($limit, $offset);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Users</title>
</head>

<body class="bg-[#f8fafc] min-h-screen antialiased text-slate-900 pt-24">
    <div><?php include "templates/navbar.php"; ?>
    </div>
    <div id="validationBlock" class="fixed top-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
        <div class="pointer-events-auto">
            <?= showValidation() ?>
        </div>
    </div>


    <div class="container mx-auto p-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">User Management</h1>
                    <p class="text-sm text-slate-500 mt-1">Manage system access, roles, and member profiles.</p>
                </div>
                <button onclick="toggleAddModal(true)" data-tooltip="Create a new user account"
                    class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-all shadow-sm shadow-blue-200">
                    <i class="fa-solid fa-plus mr-2 text-xs"></i>
                    Create New User
                </button>
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto mb-6">
                <div class="relative flex-grow md:flex-grow-0 md:min-w-[300px]" data-tooltip="Search users by username">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchInput" placeholder="Search by Username"
                        class="w-full pl-11 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                </div>

                <select id="roleFilter" data-tooltip="Filter users by role"
                    class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer h-[40px]">
                    <option value="">All Roles</option>
                    <?php foreach ($roleOptions as $role): ?>
                        <option value="<?= htmlspecialchars($role['user_role_id']) ?>">
                            <?= htmlspecialchars($role['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button id="resetBtn"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl transition-all h-[40px] flex items-center justify-center"
                    title="Reset Filters">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>


            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th
                                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                    User Details</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                    Username</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                    Access Level</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200 text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (!empty($users)):
                                foreach ($users as $user):
                                    $user_id = $user['user_id'];
                                    $firstName = !empty($user['user_first_name']) ? ucfirst($user['user_first_name']) : '';
                                    $middleInitial = !empty($user['user_middle_name']) ? strtoupper(substr($user['user_middle_name'], 0, 1)) . '.' : '';
                                    $lastName = !empty($user['user_last_name']) ? ucfirst($user['user_last_name']) : '';
                                    $fullName = htmlspecialchars(trim("$firstName $middleInitial $lastName"));

                                    $userImage = (!empty($user['user_prof']) && $user['user_prof'] !== 'default.png')
                                        ? 'img/prof_pic/' . $user['user_prof']
                                        : 'img/prof_pic/default.png';

                                    $isDefault = (strpos($userImage, 'default.png') !== false);
                                    $isSelf = (isset($_SESSION['user_id']) && $user['user_id'] == $_SESSION['user_id']);
                                    ?>
                                    <tr class="report-row hover:bg-blue-50/30 transition-colors group"
                                        data-username="<?= htmlspecialchars($user['username']) ?>"
                                        data-role="<?= htmlspecialchars($user['user_role_id']) ?>">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-4">
                                                <div class="relative flex-shrink-0">
                                                    <?php if ($isDefault): ?>
                                                        <div
                                                            class="h-10 w-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm border border-blue-200">
                                                            <?= strtoupper(substr($firstName, 0, 1)); ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <img src="<?= htmlspecialchars($userImage) ?>"
                                                            class="h-10 w-10 rounded-xl object-cover border border-slate-200 shadow-sm">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <a href="../public/user_profile.php?u=<?= $user_id ?>"
                                                        data-tooltip="View user profile"
                                                        class="text-sm font-semibold text-slate-900 hover:text-blue-600 transition-colors">
                                                        <?= $fullName ?>
                                                    </a>
                                                    <span
                                                        class="text-xs text-slate-500"><?= htmlspecialchars($user['email']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600">
                                            <span
                                                class="bg-slate-100 px-2 py-1 rounded text-xs font-mono">@<?= htmlspecialchars($user['username']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                <?= htmlspecialchars($user['role_name']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div
                                                class="flex items-center justify-end gap-4 opacity-40 group-hover:opacity-100 transition-opacity">
                                                <button
                                                    onclick="openEditUserModal('<?= $user_id ?>', '<?= addslashes($user['username']) ?>', '<?= addslashes($user['email']) ?>')"
                                                    data-tooltip="Edit user details"
                                                    class="text-xs font-bold uppercase tracking-widest text-blue-500 hover:text-blue-700">
                                                    Edit
                                                </button>
                                                <?php if (!$isSelf): ?>
                                                    <button onclick="openArchiveUserModal('<?= $user_id ?>')"
                                                        data-tooltip="Archive this user"
                                                        class="text-xs font-bold uppercase tracking-widest text-red-500 hover:text-red-700">
                                                        Archive
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($users as $user): ?>
                            <?php endforeach; ?>

                            <tr id="noResultsRow" class="hidden">
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-3xl mb-2 block opacity-20"></i>
                                    No users match your search criteria.
                                </td>
                            </tr>
                        </tbody>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <p class="text-sm text-slate-500">
                        Showing <span class="font-medium text-slate-700">
                            <?= $offset + 1 ?>
                        </span>
                        to <span class="font-medium text-slate-700">
                            <?= min($offset + $limit, $totalRecords) ?>
                        </span>
                        of <span class="font-medium text-slate-700">
                            <?= $totalRecords ?>
                        </span> users
                    </p>

                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>"
                                class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Previous
                            </a>
                        <?php endif; ?>

                        <div class="hidden sm:flex gap-1">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>&limit=<?= $limit ?>"
                                    class="px-3 py-2 text-sm font-medium rounded-lg border transition-all <?= $i == $page ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>"
                                class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="addUserModal"
            class="fixed inset-0 hidden items-center justify-center backdrop-blur-md bg-slate-900/60 z-[200] px-4 transition-all">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-slate-200 transition-all duration-300 opacity-0 scale-95">

                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Create New User</h3>
                        <p class="text-[11px] text-slate-500">Enter account and personal details.</p>
                    </div>
                    <button onclick="toggleAddModal(false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="addUserForm" action="/../controllers/add_user.php" method="POST" enctype="multipart/form-data"
                    class="p-6">
                    <div class="space-y-6">

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Account
                                    Access</h4>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="col-span-1">
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Username <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="username" data-required="true"
                                        data-error="Username is required."
                                        data-check-url="/check-availability.php?field=username&value="
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Password <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password" data-required="true"
                                        data-error="Password is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Email Address <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" data-required="true" data-error="Email is required."
                                    data-check-url="/check-availability.php?field=email&value="
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                                <div>
                                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Personal
                                    Details</h4>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">First
                                        Name</label>
                                    <input type="text" name="fname" data-required="true"
                                        data-error="First Name is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Last Name</label>
                                    <input type="text" name="lname" data-required="true"
                                        data-error="Last Name is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Middle
                                        Name</label>
                                    <input type="text" name="mname" data-required="true"
                                        data-error="Middle Name is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Birthdate</label>
                                    <input type="date" name="birthday" data-required="true"
                                        data-error="Birthdate is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 items-end">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">User Role</label>
                                    <select name="user_role" data-required="true" data-error="User Role is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none appearance-none">
                                        <option value="" disabled selected>Select Role</option>
                                        <?php foreach ($roleOptions as $option): ?>
                                            <option value="<?= $option['user_role_id'] ?>">
                                                <?= $option['role_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div>
                                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                    </div>
                                </div>
                                <div>
                                    <label class="cursor-pointer bg-white border border-slate-200 w-full px-3 py-2 rounded-lg
                                        text-[11px] font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm flex
                                        items-center justify-center gap-2">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Profile Picture</span>
                                        <input type="file" name="prof_pic" class="hidden">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="toggleAddModal(false)"
                            class="px-4 py-2 rounded-lg text-xs font-bold text-white bg-[#fb2424] hover:bg-[#c01c1c] rounded-[16px] transition-all duration-200">Cancel</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow-md shadow-blue-100 transition-all">
                            Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="editUserModal"
            class="fixed inset-0 hidden items-center justify-center backdrop-blur-md bg-slate-900/60 z-[200] px-4 transition-all">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-slate-200 opacity-0 scale-95 duration-300">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Edit User Account</h3>
                        <p class="text-xs text-slate-500">Update account credentials and system access.</p>
                    </div>
                    <button onclick="toggleEditModal(false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="EditUserForm" action="../controllers/edit_user.php" method="POST" class="p-6 space-y-5">
                    <input type="hidden" name="user_id" id="editUserId">

                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">Username</label>
                        <input type="text" name="username" id="username_edit" data-required="true"
                            data-error="Username is required."
                            data-check-url="/check-availability.php?field=username&value=" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2
                            focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                        <div>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">User
                            Role</label>
                        <div class="relative">
                            <select name="user_role" id="user_role_edit"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none appearance-none cursor-pointer">
                                <option value="" disabled>Select a role</option>
                                <?php foreach ($roleOptions as $option): ?>
                                    <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                        <?= htmlspecialchars($option['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div
                                class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">New
                            Password</label>
                        <input type="password" name="password" id="password_edit"
                            placeholder="Leave blank to keep current"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400">
                    </div>

                    <div class="pt-4 flex justify-end items-center gap-3">
                        <button type="button" onclick="toggleEditModal(false)"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[#fb2424] hover:bg-[#c01c1c] rounded-[16px] transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div id="tooltip"
            class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
        </div>
</body>

<?php ob_end_flush(); ?>
<script src="js/removeNotification.js" defer></script>
<script src="js/user_list.js"></script>
<script src="js/tooltip.js"></script>
<script src="js/inputValidation.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        initFormValidation("addUserForm"),
            initFormValidation("EditUserForm");
    });
</script>

</html>