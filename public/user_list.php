<?php
require_once __DIR__ . "/../init.php";
ob_start();

$user = checkAuth('Admin');
$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);

$where = "u.user_status_id != ?";
$params = [0]; // Example: exclude deleted users
$types = "i";
// Pagination
$pagination = getPaginationData(
    $conn,
    "users u INNER JOIN user_profile up ON u.user_id = up.user_id",
    $_GET['limit'] ?? 10,
    $_GET['page'] ?? 1,
    $where,
    $params,
    $types
);
// Extract pagination values
$offset = $pagination['offset'];
$limit = $pagination['limit'];
$totalPages = $pagination['totalPages'];
$totalRecords = $pagination['totalRecords'];
$page = $pagination['page'];

$userVisibility = new UserVisibility($conn);
$users = $userVisibility->getVisibleUsers(20, 0);
$roleOptions = fetchRoles($conn);
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

<body class="pt-24 bg-gray-50 min-h-screen">
    <div>
        <?php include "templates/navbar.php"; ?>
        <div id="validationBlock" class="fixed top-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
            <button onclick="toggleAddModal(true)"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i>New User
            </button>
        </div>

        <div class="bg-white border border-gray-100 shadow-md rounded-2xl overflow-hidden flex flex-col">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-blue-500 text-white">
                        <tr class="border-b border-gray-100">
                            <th class="p-5 text-xs font-semibold uppercase tracking-wider text-white-500 text-left">Full
                                Name</th>
                            <th class="p-5 text-xs font-semibold uppercase tracking-wider text-white-500 text-center">
                                Username</th>
                            <th class="p-5 text-xs font-semibold uppercase tracking-wider text-white-500 text-center">
                                Email</th>
                            <th class="p-5 text-xs font-semibold uppercase tracking-wider text-white-500 text-center">
                                Role</th>
                            <th
                                class="p-5 text-xs font-semibold uppercase tracking-wider text-white-500 text-center w-32">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody" class="divide-y divide-gray-50">
                        <?php
                        
                        if (!empty($users)):
                            foreach ($users as $user):
                                $user_id = $user['user_id'];
                                $middleInitial = '';
                                $firstName = !empty($user['user_first_name']) ? ucfirst($user['user_first_name']) : '';
                                if (!empty($user['user_middle_name'])) {
                                    $middleInitial = strtoupper(substr($user['user_middle_name'], 0, 1)) . '.';
                                }
                                $lastName = !empty($user['user_last_name']) ? ucfirst($user['user_last_name']) : '';
                                $fullName = htmlspecialchars(trim("$firstName $middleInitial $lastName"));
                                $uploadDir = 'img/prof_pic/';

                                $dbImage = !empty($user['user_prof']) ? $user['user_prof'] : '';

                                if (!empty($dbImage) && $dbImage !== 'default.png') {
                                    $userImage = $uploadDir . $dbImage;
                                } else {
                                    $userImage = $uploadDir . 'default.png';
                                }

                                $isDefault = (strpos($userImage, 'default.png') !== false);
                                $isSelf = (isset($_SESSION['user_id']) && $user['user_id'] == $_SESSION['user_id']);
                                ?>
                                <tr>
                                    <td class="p-4 whitespace-nowrap">
                                        <a href="../public/user_profile.php?u=<?= htmlspecialchars($user['user_id']) ?>"
                                            class="flex items-center gap-3 group">

                                            <div
                                                class="h-8 w-8 rounded-full border border-white shadow-sm flex items-center justify-center text-xs font-bold 
                                                <?= $isDefault ? 'bg-gradient-to-br from-cyan-100 to-blue-100 text-cyan-700' : '' ?>">
                                                <?php if ($isDefault): ?>
                                                    <?= strtoupper(substr($firstName, 0, 1)); ?>
                                                <?php else: ?>
                                                    <img src="<?= htmlspecialchars($userImage) ?>" alt="Profile"
                                                        class="h-full w-full rounded-full object-cover">
                                                <?php endif; ?>
                                            </div>

                                            <span
                                                class="text-sm font-bold text-gray-700 group-hover:text-purple-700 transition-colors">
                                                <?= $fullName ?>
                                            </span>
                                        </a>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 bg-gray-50 text-gray-600 rounded text-xs font-mono">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-center text-sm text-gray-600">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                            <?= htmlspecialchars($user['role_name']) ?>
                                        </span>
                                    </td>

                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <button
                                                onclick="openEditUserModal('<?= $user['user_id'] ?>', '<?= addslashes($user['username']) ?>', '<?= addslashes($user['email'] ?? '') ?>', '<?= $user['user_role_id'] ?>')"
                                                class="text-xs font-bold uppercase tracking-wider text-cyan-600 hover:text-cyan-800 transition-colors">
                                                Edit
                                            </button>

                                            <?php if (!$isSelf): ?>
                                                <button onclick="openArchiveUserModal('<?= $user['user_id'] ?>')"
                                                    class="text-xs font-bold uppercase tracking-wider text-red-500 hover:text-red-700 transition-colors">
                                                    Archive
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="h-12 w-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-300">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm">No users found.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addUserModal"
        class="fixed inset-0 hidden items-center justify-center backdrop-blur-sm bg-gray-900/40 z-[200] px-4 transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
            <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold">Add New User</h3>
                <button onclick="toggleAddModal(false)"
                    class="text-white hover:text-gray-200 text-3xl leading-none transition-colors">&times;</button>
            </div>

            <form action="/../controllers/add_user.php" method="POST" enctype="multipart/form-data" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800 border-b pb-2">Credentials</h4>

                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="username" id="username" placeholder="Username" required
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" placeholder="******" required
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" placeholder="Email" required
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label for="user_role" class="block text-sm font-medium text-gray-700 mb-1">User Role <span
                                    class="text-red-500">*</span></label>
                            <select name="user_role" id="user_role" required
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <option value="" disabled selected>Select a role</option>
                                <?php foreach ($roleOptions as $option): ?>
                                    <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                        <?= htmlspecialchars($option['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800 border-b pb-2">Personal Information</h4>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">First Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="fname" id="fname" placeholder="First Name" required
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label for="lname" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="lname" id="lname" placeholder="Last Name" required
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="mname" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="mname" id="mname" placeholder="Middle Name"
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1">Birthday <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="birthday" id="birthday" required
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label for="prof_pic" class="block text-sm font-medium text-gray-700 mb-1">Profile
                                Picture</label>
                            <input type="file" name="prof_pic" id="prof_pic"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="toggleAddModal(false)"
                        class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-md">
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal"
        class="hidden fixed inset-0 items-center justify-center backdrop-blur-sm bg-gray-900/40 z-[200] px-4">
        <div id="editUserModalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden p-6">
            <h3 class="text-xl font-bold mb-4">Edit User</h3>
            <form id="EditUserForm" action="../controllers/edit_user.php" method="POST" class="space-y-4">
                <input type="hidden" name="user_id" id="editUserId">

                <div>
                    <label for="username_edit" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="username_edit" placeholder="Edit Username"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label for="user_role_edit" class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                    <select name="user_role" id="user_role_edit"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="" disabled>Select a role</option>
                        <?php foreach ($roleOptions as $option): ?>
                            <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                <?= htmlspecialchars($option['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="password_edit" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password_edit"
                        placeholder="Leave blank to keep current password"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </form>

            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" form="EditUserForm"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-md">
                    Update Changes
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleAddModal(show) {
            const modal = document.getElementById('addUserModal');
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Close Add Modal if clicked outside
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('addUserModal');
            if (event.target === modal) {
                toggleAddModal(false);
            }
        });
    </script>
</body>
<?php ob_end_flush(); ?>
<script src="js/removeNotification.js" defer></script>
<script src="js/user_list.js"></script>

</html>