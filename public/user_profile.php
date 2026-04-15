<?php
require_once __DIR__ . "/../init.php";
ob_start();

$userSession = checkAuth(); 
$role = $userSession->role; 
$user_id = $userSession->user_id; 

$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);

if (isset($_GET['u'])) {
    $user = $_GET['u'];
} else {
    $user = $user_id;
}

try {
   
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
unset($queryParams['page']); 


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

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">

     <?php include "templates/navbar.php"; ?>
     <div class="h-20"></div>

    <div class="flex-1 flex items-center justify-center px-6 lg:px-8">
    <div class="max-w-6xl w-full">
        

       <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full h-full py-10">

<div 
    class="rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative max-w-md mx-auto w-full h-[450px] lg:h-[600px] self-start flex flex-col"
    style="background: linear-gradient(135deg, #833ab4 0%, #e1306c 50%, #fcb045 100%);"
>
        
        

  
        <div class="px-6 py-10 text-center relative bg-transparent flex-1 flex flex-col justify-center items-center">

            <div class="flex justify-center -mt-16 mb-4 w-full">
                <div class="relative inline-block">
                    <img src="/public/img/prof_pic/<?= htmlspecialchars($userData['user_prof'] ?? 'default.png') ?>"
                        class="w-32 h-32 rounded-full object-cover border-[5px] border-white shadow-md bg-white">

                    <div class="absolute bottom-2 right-2 w-5 h-5 bg-green-500 border-[3px] border-white rounded-full"></div>
                </div>
            </div>

            <?php
            $firstName = ucfirst($userData['user_first_name'] ?? '');
            $middleInitial = !empty($userData['user_middle_name']) ? strtoupper(substr($userData['user_middle_name'], 0, 1)) . '.' : '';
            $lastName = ucfirst($userData['user_last_name'] ?? '');
            $fullName = htmlspecialchars(trim("$firstName $middleInitial $lastName"));
            ?>

            <h2 class="text-xl font-bold text-slate-800"><?= $fullName ?></h2>
            <p class="text-sm text-black-500 font-medium mt-1">
                @<?= htmlspecialchars($userData['username'] ?? 'user') ?>
            </p>

            <div class="mt-4">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    <?= htmlspecialchars($userData['role_name'] ?? 'User') ?>
                </span>
            </div>

            <div class="mt-8 pt-6 flex justify-center w-full"> 
    <button
        data-tooltip="Edit profile information"
        class="relative group w-fit flex items-center justify-center gap-2 px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50 hover:text-blue-600 hover:border-blue-300 transition-all duration-200"
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
        <i class="fa-solid fa-pen-to-square"></i> Edit Profile
    </button>
</div>

        </div>
    </div>

    <div class="lg:col-span-2 self-start space-y-6 max-h-[80vh] overflow-y-auto pr-2">

    
    <div 
        class="p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 transition-shadow hover:shadow-md"
        style="background: linear-gradient(to right, #ffffff, #ffffff);"
    >
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-black/50">
            <div class="w-12 h-12 flex items-center justify-center bg-blue-50 rounded-xl text-blue-600">
                <i class="fa-solid fa-user text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Personal Information</h3>
                <p class="text-sm text-black-500">Your basic profile details</p>
            </div>
        </div>

        
        <div class="grid sm:grid-cols-3 gap-4">

        
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white/60 border border-slate-200 shadow-sm hover:shadow-md transition">
                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <p class="text-[15px] text-slate-500">First Name</p>
                    <p class="font-semibold text-slate-800">
                        <?= htmlspecialchars($userData['user_first_name'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>

           
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white/60 border border-slate-200 shadow-sm hover:shadow-md transition">
                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-circle-user"></i>
                </div>
                <div>
                    <p class="text-[15px] text-slate-500">Middle Name</p>
                    <p class="font-semibold text-slate-800">
                        <?= htmlspecialchars($userData['user_middle_name'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>

            
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white/60 border border-slate-200 shadow-sm hover:shadow-md transition">
                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <p class="text-[15px] text-slate-500">Last Name</p>
                    <p class="font-semibold text-slate-800">
                        <?= htmlspecialchars($userData['user_last_name'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>

        </div>

       
        <div class="mt-6 flex items-center gap-3 p-4 rounded-xl bg-white/60 border border-slate-200 shadow-sm">
            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-green-100 text-green-600">
                <i class="fa-solid fa-calendar"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500">Birthdate</p>
                <p class="font-semibold text-slate-800">
                    <?= !empty($userData['user_dob']) ? date('F j, Y', strtotime($userData['user_dob'])) : 'Not set' ?>
                </p>
            </div>
        </div>

    </div>

    
    <div 
        class="p-6 sm:p-10 rounded-2xl shadow-sm border border-slate-200 transition-shadow hover:shadow-md"
        style="background: linear-gradient(to right, #ffffff, #ffffff);"
    >
        <div class="flex items-center gap-4 mb-4 pb-4 border-b border-black/50">
            <div class="w-12 h-12 flex items-center justify-center bg-purple-50 rounded-xl text-purple-600">
                <i class="fa-solid fa-gear text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Account Details</h3>
                <p class="text-sm text-black-500">System credentials and contact</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 lg:gap-6">

         
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white/60 border border-slate-200 shadow-sm">
                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-200 text-slate-600">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Username</p>
                    <p class="font-semibold text-slate-800">
                        <?= htmlspecialchars($userData['username'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>

            <!-- Email -->
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white/60 border border-slate-200 shadow-sm">
                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Email Address</p>
                    <p class="font-semibold text-slate-800">
                        <?= htmlspecialchars($userData['email'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

    <div id="editUserModal"
        class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm items-center justify-center transition-all duration-300 ease-out font-sans hidden">

        <div id="editUserModalContent"
            class="bg-white w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[90vh] rounded-2xl overflow-hidden">

            <div
                class="px-8 py-5 flex justify-between items-center bg-white z-10 shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Edit User Profile</h2>
                    <p class="text-[15px] text-slate-500 mt-0.5">Update personal details and account settings</p>
                </div>
                <button onclick="closeEditUserModal()"
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scroll px-8 pt-8 pb-4">
                <form id="editUserForm" method="POST" action="../controllers/edit_user_profile.php"
                    class="space-y-8" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" id="editUserId">

                    <div class="flex flex-col sm:flex-row gap-8">
                        <div class="shrink-0 flex flex-col items-center sm:items-start gap-3">
                            <div class="relative group">
                                <div id="editUserPicture"
                                    class="w-32 h-32 rounded-full bg-slate-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden object-cover">
                                    <span class="text-slate-400 text-xs font-medium">No Image</span>
                                </div>
                                <label for="user_image"
                                    class="absolute bottom-0 right-0 p-2 text-white bg-blue-600 rounded-full cursor-pointer hover:bg-blue-700 shadow-lg transition-transform hover:scale-105 border-2 border-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                                <input type="file" id="user_image" name="user_image" accept="image/*"
                                    class="hidden">
                            </div>
                            <p class="text-[15px] text-slate-400 font-medium text-center w-32">Allowed: JPG, PNG
                            </p>
                        </div>

                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserFirstName"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">First
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="editUserFirstName"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-black rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. Juan">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <div class="flex justify-between items-center mb-1.5">
                                    <label for="editUserMiddleName"
                                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Middle
                                        Name</label>
                                </div>
                                <input type="text" name="middle_name" id="editUserMiddleName"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all disabled:opacity-50"
                                    placeholder="Middle Name">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserLastName"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Last
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="editUserLastName"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. Dela Cruz">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="editUserBirthdate"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Birthday
                                    <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date" id="editUserBirthdate"
                                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-ntPurple focus:ring-1 focus:ring-ntPurple transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="editUserName"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                                <input type="text" name="username" id="editUserName"
                                    class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-semibold text-slate-700">
                            </div>

                            <div>
                                <label for="edit_user_role"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">User
                                    Role</label>
                                <select name="user_role" id="edit_user_role"
                                    class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all cursor-pointer">
                                    <option value="" disabled selected>Select a role</option>
                                    <?php foreach ($roleOptions as $option): ?>
                                        <option value="<?= htmlspecialchars($option['user_role_id']) ?>">
                                            <?= htmlspecialchars($option['role_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-span-1 sm:col-span-2">
                                <label for="editUserEmail"
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email
                                    Address</label>
                                <input type="email" name="email" id="editUserEmail"
                                    class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
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
    <div id="tooltip"
    class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
</div>
</body>
<script src="js/user_profile.js"></script>
<script src="js/tooltip.js"></script>

</html>
