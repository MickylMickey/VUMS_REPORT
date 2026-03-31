<?php
require_once __DIR__ . "/../init.php";
ob_start();
session_start();
checkAuth();
$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);

if (isset($_GET['u'])) {
    $user = $_GET['u'];
} else {
    $user = $user_id;
}

try {
    // Efficiently check if user exists
    $checkSql = "SELECT COUNT(*) as count FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count === 0) {
        setToast("User not found", "error");
        header("Location: ../public/user_list.php");
        exit;
    }

    // Proceed with loading the user's data
    $sql = "SELECT users.user_id, users.username, user_profile.email, user_role.role_name, users.user_role_id, 
    user_profile.user_first_name, user_profile.user_middle_name, user_profile.user_last_name, user_profile.user_prof,
    user_profile.user_dob, user_status.status_name
FROM users
LEFT JOIN user_role 
    ON user_role.user_role_id = users.user_role_id
LEFT JOIN user_status 
    ON user_status.user_status_id = users.user_status_id
LEFT JOIN user_profile 
    ON user_profile.user_id = users.user_id
    WHERE users.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}
$stmt->close();

$roleOptions = fetchRoles($conn);
$queryParams = $_GET;
unset($queryParams['page']); // Remove existing 'page' param if present

// Build base URL with other parameters
$baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
$baseQuery = http_build_query($queryParams);
$separator = $baseQuery ? '&' : '?';
$paginationBase = $baseUrl . ($baseQuery ? '?' . $baseQuery : '') . $separator;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>User Profile</title>
</head>

<body>
    <div class="flex gap-4">
        <!-- Profile Section Wrapper -->
        <div
            class="w-full max-w-sm bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden h-fit sticky top-24">

            <!-- Decorative Banner -->
            <div class="relative h-28 bg-gradient-to-r from-cyan-500 to-purple-600">
                <!-- Edit Button (Positioned Absolute Top Right) -->
                <button class="p-2 text-black bg-blue-600 rounded-lg transition-all group" onclick="openEditUserModal(
        '<?= htmlspecialchars($userData['user_id'] ?? '') ?>', 
                        '<?= htmlspecialchars($userData['username'] ?? '') ?>',
                        '<?= htmlspecialchars($userData['user_role_id'] ?? '') ?>', // Pass ID, not name
                        '<?= htmlspecialchars($userData['email'] ?? '') ?>',
                        '<?= htmlspecialchars($userData['user_first_name'] ?? '') ?>',
                        '<?= htmlspecialchars($userData['user_middle_name'] ?? '') ?>',
                        '<?= htmlspecialchars($userData['user_last_name'] ?? '') ?>',
                        '<?= htmlspecialchars($userData['user_dob'] ?? '') ?>', 
                        '../public/img/prof_pic/<?= htmlspecialchars($userData['user_prof'] ?? 'default.png') ?>'
                    )">
                    EDIT USER
                </button>
            </div>

            <!-- Profile Content -->
            <div class="px-6 pb-6 relative">

                <!-- Profile Image (Negative margin to overlap banner) -->
                <div class="flex justify-center -mt-14 mb-4">
                    <div class="relative">
                        <img src="/public/img/prof_pic/<?= htmlspecialchars($userData['user_prof']) ?>" alt="Profile"
                            class="w-28 h-28 rounded-full object-cover border-[4px] border-white shadow-md bg-white">
                        <!-- Status Indicator (Optional) -->
                        <div class="absolute bottom-2 right-2 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                        </div>
                    </div>
                </div>

                <!-- Name & Role -->
                <div class="text-center mb-6">
                    <?php
                    $firstName = ucfirst($userData['user_first_name']);
                    $middleInitial = !empty($userData['user_middle_name']) ? strtoupper(substr($userData['user_middle_name'], 0, 1)) . '.' : '';
                    $lastName = ucfirst($userData['user_last_name']);
                    $fullName = htmlspecialchars(trim("$firstName $middleInitial $lastName"));
                    ?>
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight"><?= $fullName ?></h2>
                    <span
                        class="inline-block mt-1 px-3 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 uppercase tracking-wide">
                        <?= htmlspecialchars($userData['role_name']) ?>
                    </span>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-100 mb-6"></div>

                <!-- Information List -->
                <div class="space-y-4">

                    <!-- Username -->
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-5">
                                <path fill-rule="evenodd"
                                    d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Username
                            </p>
                            <p class="text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($userData['username']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-5">
                                <path
                                    d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                                <path
                                    d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email
                                Address</p>
                            <p class="text-sm font-medium text-gray-700 truncate"
                                title="<?= htmlspecialchars($userData['email']) ?>">
                                <?= htmlspecialchars($userData['email']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Birthdate -->
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-5">
                                <path
                                    d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                                <path fill-rule="evenodd"
                                    d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Birthdate
                            </p>
                            <p class="text-sm font-medium text-gray-700">
                                <?= !empty($userData['user_dob']) ? date('F j, Y', strtotime($userData['user_dob'])) : "Not set" ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit User Modal -->
    <div id="editUserModal"
        class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm items-center justify-center transition-all duration-300 ease-out font-sans hidden">

        <!-- Modal Content Card -->
        <div id="editUserModalContent"
            class="bg-white w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[90vh] rounded-2xl overflow-hidden">

            <!-- 1. Header -->
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-white z-10 shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Edit User Profile</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Update personal details and account settings</p>
                </div>
            </div>

            <!-- 2. Scrollable Form Body -->
            <div class="flex-1 overflow-y-auto custom-scroll px-8 pt-8 pb-2">
                <form id="editUserForm" method="POST" action="../controllers/edit_user_profile.php" class="space-y-8"
                    enctype="multipart/form-data">
                    <input type="hidden" name="user_id" id="editUserId">

                    <!-- SECTION: Profile Image & Identity -->
                    <div class="flex flex-col sm:flex-row gap-8">
                        <!-- Image Upload -->
                        <div class="shrink-0 flex flex-col items-center sm:items-start gap-3">
                            <div class="relative group">
                                <div id="editUserPicture"
                                    class="w-32 h-32 rounded-full bg-slate-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden object-cover">
                                    <span class="text-slate-400 text-xs font-medium">No Image</span>
                                </div>
                                <label for="user_image"
                                    class="absolute bottom-0 right-0 p-2 text-white rounded-full cursor-pointer hover:bg-purple-700 shadow-lg transition-transform hover:scale-105 border-2 border-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                                <input type="file" id="user_image" name="user_image" accept="image/*">
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium text-center w-32">Allowed: JPG, PNG</p>
                        </div>

                        <!-- Name Fields -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- First Name -->
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserFirstName"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">First
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="editUserFirstName"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all"
                                    placeholder="e.g. Juan">
                            </div>
                            <!-- Middle Name -->
                            <div class="col-span-2 sm:col-span-1">
                                <div class="flex justify-between items-center mb-1.5">
                                    <label for="editUserMiddleName"
                                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Middle
                                        Name</label>
                                    <input type="text" name="middle_name" id="editUserMiddleName"
                                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                        placeholder="Middle Name">
                                </div>
                            </div>
                            <!-- Last Name -->
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserLastName"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Last
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="editUserLastName"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all"
                                    placeholder="e.g. Dela Cruz">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserBirthdate"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Last
                                    Name <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date" id="editUserBirthdate"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: Account Details (Card Style) -->
                    <div class=" bg-slate-50/80 p-5 rounded-2xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-ntCyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Username -->
                            <div>
                                <label for="editUserName"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                                <input type="text" name="username" id="editUserName"
                                    class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all font-semibold text-slate-700">
                            </div>
                        </div>
                        <div>
                            <label for="edit_user_role">User Role</label>
                            <select name="user_role" id="edit_user_role">
                                <option value="" disabled selected>Select a role</option>
                                <?php foreach ($roleOptions as $option): ?>
                                    <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                        <?= htmlspecialchars($option['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Email -->
                        <div class="col-span-1 sm:col-span-2">
                            <label for="editUserEmail"
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email
                                Address</label>
                            <input type="email" name="email" id="editUserEmail"
                                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all">
                        </div>
                    </div>

                </form>
            </div>

            <!-- 3. Sticky Footer Action Buttons -->
            <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 z-10 shrink-0">
                <button type="button" onclick="closeEditUserModal()"
                    class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl text-sm hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm">
                    Cancel
                </button>
                <button type="submit" form="editUserForm"
                    class="px-6 py-2.5 bg-ntPurple text-black font-semibold rounded-xl text-sm hover:bg-purple-700 shadow-md shadow-purple-200 transition-all transform hover:-translate-y-0.5">
                    Save Changes
                </button>
            </div>

        </div>
    </div>
    <?php ob_end_flush(); ?>
</body>
<script src="js/user_profile.js"></script>

</html>