<?php
require_once __DIR__ . "/../init.php";
ob_start();

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

<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    
    <div class="max-w-6xl mx-auto p-6 lg:p-8 mt-4">
        


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div style="background: linear-gradient(to right, #3b82f6, #ffffff);" class="rounded-2xl shadow-sm overflow-hidden min-h-[400px]">
                

    <div class="px-6 py-8 pb-6 relative">
                    
                  

                    <div class="px-6 py-10 pb-6 relative">
                        
                        <div class="flex justify-center -mt-16 mb-4">
                            <div class="relative">
                                <img src="/public/img/prof_pic/<?= htmlspecialchars($userData['user_prof'] ?? 'default.png') ?>" alt="Profile"
                                    class="w-32 h-32 rounded-full object-cover border-[5px] border-white shadow-md bg-white">
                                <div class="absolute bottom-2 right-2 w-5 h-5 bg-green-500 border-[3px] border-white rounded-full" title="Active"></div>
                            </div>
                        </div>

                        <div class="text-center mb-6">
                            <?php
                            $firstName = ucfirst($userData['user_first_name'] ?? '');
                            $middleInitial = !empty($userData['user_middle_name']) ? strtoupper(substr($userData['user_middle_name'], 0, 1)) . '.' : '';
                            $lastName = ucfirst($userData['user_last_name'] ?? '');
                            $fullName = htmlspecialchars(trim("$firstName $middleInitial $lastName"));
                            ?>
                            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight"><?= $fullName ?></h2>
                            <span class="inline-block mt-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-600 uppercase tracking-wider">
                                <?= htmlspecialchars($userData['role_name'] ?? 'User') ?>
                            </span>
                        </div>

                        <div class="flex justify-center">
                            <button class="inline-flex items-center justify-center gap-2 py-2 px-5 bg-white border border-slate-50 text-slate-700 font-semibold rounded-full text-[13px] hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm"
                                onclick="openEditUserModal(
                                    '<?= htmlspecialchars($userData['user_id'] ?? '') ?>', 
                                    '<?= htmlspecialchars($userData['username'] ?? '') ?>',
                                    '<?= htmlspecialchars($userData['user_role_id'] ?? '') ?>', 
                                    '<?= htmlspecialchars($userData['email'] ?? '') ?>',
                                    '<?= htmlspecialchars($userData['user_first_name'] ?? '') ?>',
                                    '<?= htmlspecialchars($userData['user_middle_name'] ?? '') ?>',
                                    '<?= htmlspecialchars($userData['user_last_name'] ?? '') ?>',
                                    '<?= htmlspecialchars($userData['user_dob'] ?? '') ?>', 
                                    '../public/img/prof_pic/<?= htmlspecialchars($userData['user_prof'] ?? 'default.png') ?>'
                                )">
                                
                                Edit Profile
                            </button>
                        </div>
                </div>
            </div>

           <div class="lg:col-span-2 space-y-6">

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 tracking-tight">Personal Information</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-12">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-50 text-blue-500 rounded-xl shrink-0">
                    <i class="fa-regular fa-id-card w-4 h-4 flex items-center justify-center"></i>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">Full Name</p>
                    <p class="font-semibold text-slate-800 text-base"><?= $fullName ?></p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="p-3 bg-pink-50 text-pink-500 rounded-xl shrink-0">
                    <i class="fa-solid fa-cake-candles w-4 h-4 flex items-center justify-center"></i>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">Birthdate</p>
                    <p class="font-semibold text-slate-800 text-base">
                        <?= !empty($userData['user_dob']) ? date('F j, Y', strtotime($userData['user_dob'])) : '<span class="text-slate-400 italic">Not set</span>' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 bg-purple-50 rounded-lg text-purple-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M11.828 2.25c-.916 0-1.699.663-1.85 1.567l-.091.549a3.375 3.375 0 0 1-2.423 2.423l-.549.091c-.904.15-1.567.934-1.567 1.85v.644c0 .916.663 1.699 1.567 1.85l.549.091a3.375 3.375 0 0 1 2.423 2.423l.091.549c.15.904.934 1.567 1.85 1.567h.644c.916 0 1.699-.663 1.85-1.567l.091-.549a3.375 3.375 0 0 1 2.423-2.423l.549-.091c.904-.15 1.567-.934 1.567-1.85v-.644c0-.916-.663-1.699-1.567-1.85l-.549-.091a3.375 3.375 0 0 1-2.423-2.423l-.091-.549a1.822 1.822 0 0 0-1.85-1.567h-.644Z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 tracking-tight">Account Details</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-12">
            
            <div class="flex items-start gap-4">
                <div class="p-3 bg-indigo-50 text-indigo-500 rounded-xl shrink-0">
                    <i class="fa-solid fa-at w-4 h-4 flex items-center justify-center"></i>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">Username</p>
                    <p class="font-semibold text-slate-800 text-base"><?= htmlspecialchars($userData['username'] ?? 'N/A') ?></p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="p-3 bg-sky-50 text-sky-500 rounded-xl shrink-0">
                    <i class="fa-regular fa-envelope w-4 h-4 flex items-center justify-center"></i>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">Email Address</p>
                    <p class="font-semibold text-slate-800 text-base"><?= htmlspecialchars($userData['email'] ?? 'N/A') ?></p>
                </div>
            </div>


        </div>
    </div>
</div>

            
    </div>

    <div id="editUserModal"
        class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm items-center justify-center transition-all duration-300 ease-out font-sans hidden">

        <div id="editUserModalContent"
            class="bg-white w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[90vh] rounded-2xl overflow-hidden">

            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-white z-10 shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Edit User Profile</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Update personal details and account settings</p>
                </div>
                <button onclick="closeEditUserModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scroll px-8 pt-8 pb-4">
                <form id="editUserForm" method="POST" action="../controllers/edit_user_profile.php" class="space-y-8" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" id="editUserId">

                    <div class="flex flex-col sm:flex-row gap-8">
                        <div class="shrink-0 flex flex-col items-center sm:items-start gap-3">
                            <div class="relative group">
                                <div id="editUserPicture" class="w-32 h-32 rounded-full bg-slate-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden object-cover">
                                    <span class="text-slate-400 text-xs font-medium">No Image</span>
                                </div>
                                <label for="user_image" class="absolute bottom-0 right-0 p-2 text-white bg-blue-600 rounded-full cursor-pointer hover:bg-blue-700 shadow-lg transition-transform hover:scale-105 border-2 border-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                                <input type="file" id="user_image" name="user_image" accept="image/*" class="hidden">
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium text-center w-32">Allowed: JPG, PNG</p>
                        </div>

                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserFirstName" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="editUserFirstName" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all" placeholder="e.g. Juan">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <div class="flex justify-between items-center mb-1.5">
                                    <label for="editUserMiddleName" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Middle Name</label>
                                </div>
                                <input type="text" name="middle_name" id="editUserMiddleName" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all disabled:opacity-50" placeholder="Middle Name">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserLastName" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="editUserLastName" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all" placeholder="e.g. Dela Cruz">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserBirthdate" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Birthday <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date" id="editUserBirthdate" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="editUserName" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                                <input type="text" name="username" id="editUserName" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-semibold text-slate-700">
                            </div>
                            
                            <div>
                                <label for="edit_user_role" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">User Role</label>
                                <select name="user_role" id="edit_user_role" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all cursor-pointer">
                                    <option value="" disabled selected>Select a role</option>
                                    <?php foreach ($roleOptions as $option): ?>
                                        <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                            <?= htmlspecialchars($option['role_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-span-1 sm:col-span-2">
                                <label for="editUserEmail" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                                <input type="email" name="email" id="editUserEmail" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-8 py-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 z-10 shrink-0">
                <button type="button" onclick="closeEditUserModal()"
                    class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-xl text-sm hover:bg-slate-100 hover:text-slate-900 transition-colors shadow-sm">
                    Cancel
                </button>
                <button type="submit" form="editUserForm"
                    class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 shadow-md shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                    Save Changes
                </button>
            </div>

        </div>
    </div>
    
    <?php ob_end_flush(); ?>
</body>
<script src="js/user_profile.js"></script>
</html>