<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../middleware/auth_middleware.php";
//require_once __DIR__ . "/../functions/auth_admin.php";
require_once __DIR__ . "/../functions/fetch_user_role.php";
require_once __DIR__ . "/../functions/user_visibility.php";
require_once __DIR__ . '/../helper/generalValidationMessage.php';

session_start();
$user = checkAuth('Admin');
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
    <title>Users</title>
</head>

<body>
    <div
        class="bg-white border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] rounded-2xl overflow-hidden flex flex-col">

        <!-- Main User List -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 sticky top-0 z-10 backdrop-blur-sm">
                    <tr class="border-b border-gray-100">
                        <!-- Headers -->
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-left">
                            Full Name</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center">
                            Username</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center">
                            Email</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center">
                            Role</th>
                        <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center w-32">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-gray-50">
                    <?php
                    // Initial Load (Server Side Rendering)
                    if (!empty($users)):
                        foreach ($users as $user):
                            // Re-use logic for consistency
                            $user_id = $user['user_id'];
                            $middleInitial = '';
                            $firstName = !empty($user['user_first_name']) ? ucfirst($user['user_first_name']) : '';
                            if (!empty($user['user_middle_name'])) {
                                $middleInitial = strtoupper(substr($user['user_middle_name'], 0, 1)) . '.';
                            }
                            $lastName = !empty($user['user_last_name']) ? ucfirst($user['user_last_name']) : '';
                            $fullName = htmlspecialchars(trim("$firstName $middleInitial $lastName"));
                            $defaultImagePath = '../public/img/prof_pic/default.png';
                            $userImage = $user['profile_image'] ?? $defaultImagePath;
                            // Assume $current_session_user_id is available from yourauth middleware
                            $isSelf = (isset($_SESSION['user_id']) && $user['user_id'] == $_SESSION['user_id']);
                            ?>
                            <tr>
                                <!-- Full Name + Avatar -->
                                <td class="p-4 whitespace-nowrap">
                                    <a href="../public/user_profile.php?u=<?= htmlspecialchars($user['user_id']) ?>"
                                        class="flex items-center gap-3 group-hover:opacity-100">
                                        <div
                                            class="h-8 w-8 rounded-full border border-white shadow-sm flex items-center justify-center text-xs font-bold <?= ($userImage === $defaultImagePath) ? 'bg-gradient-to-br from-cyan-100 to-blue-100 text-cyan-700' : '' ?>">
                                            <?php if ($userImage === $defaultImagePath):
                                                echo strtoupper(substr($firstName, 0, 1));
                                            else: ?>
                                                <img src="<?= htmlspecialchars($userImage) ?>" alt="Profile"
                                                    class="h-full w-full rounded-full object-cover">
                                            <?php endif; ?>
                                        </div>
                                        <span
                                            class="text-sm font-bold text-gray-700 group-hover:text-purple-700 transition-colors"><?= $fullName ?></span>
                                    </a>
                                </td>
                                <td class="p-4 text-center"><span
                                        class="px-2 py-1 bg-gray-50 text-gray-600 rounded text-xs font-mono"><?= htmlspecialchars($user['username']) ?></span>
                                </td>
                                <td class="p-4 text-center text-sm text-gray-600">
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>
                                <td class="p-4 text-center"><span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100"><?= htmlspecialchars($user['role_name']) ?></span>
                                </td>

                                <!-- Actions -->
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
                                        <?php else: ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
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

    <!-- Add Modal -->
    <h1> User list with user addition(modal)</h1>
    <div class="hidden">
        <form action="/../controllers/add_user.php" method="POST" enctype="multipart/form-data">

            <!-- credentials -->
            <div>
                <!-- Username -->
                <label for="username">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="username" placeholder="Username" required>

                <!-- Password -->
                <label for="password">Password <span class="text-yellow-900">*</span></label>
                <input type="password" name="password" id="password" placeholder="******" required>

                <!-- Email -->
                <label for="email">Email<span class="text-yellow-900">*</span></label>
                <input type="email" name="email" id="email" placeholder="Email" required>

                <!-- User Role -->
                <label for="user_role">User Role</label>
                <select name="user_role" id="user_role">
                    <option value="" disabled selected>Select a role</option>
                    <?php foreach ($roleOptions as $option): ?>
                        <?php if ($option['user_role_id'] >= ['user_role_id']): ?>
                            <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                <?= htmlspecialchars($option['role_name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Personal Info -->
            <div>
                <!-- Full Name -->
                <div>

                    <!-- First Name -->
                    <label for="first_name">First Name<span class="text-red-500">*</span></label>
                    <input type="text" name="fname" id="fname" placeholder="First Name" required>

                    <!-- Middle Name -->
                    <label for="middle_name">Middle Name</label>
                    <input type="text" name="mname" id="mname" placeholder="Middle Name">

                    <!-- Last Name -->
                    <label for="last_name">Last Name<span class="text-red-500">*</span></label>
                    <input type="text" name="lname" id="lname" placeholder="Last Name" required>

                    <!-- Birthday -->
                    <label for="birthday">Birthday <span class="text-red-500">*</span></label>
                    <input type="date" name="birthday" id="birthday" required>

                    <!-- Profile Pic -->
                    <label for="prof_pic">Profile Picture</label>
                    <input type="file" name="prof_pic" id="prof_pic">
                </div>

                <!-- Submit button -->
                <div>
                    <button type="submit"> Add User</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Edit user -->
    <h1>Edit user</h1>
    <div id="editUserModal" class="hidden">
        <div id="editUserModalContent">
            <form id="EditUserForm" action="../controllers/edit_user.php" method="POST">
                <!-- Hidden to get user_id -->
                <input type="hidden" name="user_id" id="editUserId">
                <div>
                    <!--Reset Password Not working for now-->
                    <a href="">Reset Password</a>
                    <!-- Username -->
                    <a href=""></a>
                    <label for=" username_edit">Username</label>
                    <input type="text" name="username" id="username_edit" placeholder="Edit Username">
                    <!-- Email -->
                    <label for="email_edit">Email</label>
                    <input type="email" name="email" id="email_edit" placeholder="*****">
                    <!-- Role -->
                    <label for="user_role_edit">User Role</label>
                    <select name="user_role" id="user_role_edit">
                        <option value="" disabled selected>Select a role</option>
                        <?php foreach ($roleOptions as $option): ?>
                            <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                <?= htmlspecialchars($option['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>
            </form>
            <!--Submit Button-->
            <button type="submit" form="EditUserForm">Update Changes</button>
        </div>
    </div>


    <!-- Archive user -->
    <div>

    </div>
</body>
<script src="js/user_list.js"></script>

</html>