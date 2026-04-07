<?php
require_once(__DIR__ . '/../../init.php');
ob_start();
$user = checkAuth();
$role = $user->role;
$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);
// Define the base folder where images are stored
$uploadPath = "/public/img/prof_pic/";

// Check if user has a profile and if the file actually exists
if (!empty($user->user_prof)) {
    $profilePic = $uploadPath . $user->user_prof;
} else {
    // Default UI Avatar if no profile pic exists
    $profilePic = "https://ui-avatars.com/api/?name=" . urlencode($user->username) . "&background=random";
}

?>

<link href="/public/dist/output.css" rel="stylesheet">

<nav class="fixed top-4 left-1/2 -translate-x-1/2 w-[90%] max-w-6xl z-50">
    <div
        class="bg-white/70 backdrop-blur-md border border-white/20 px-6 py-3 rounded-2xl shadow-lg flex items-center justify-between">
        <div class="text-xl font-bold text-gray-800">
            <img src="/public/img/images.jpg" alt="Logo" class="rounded-full w-15 h-15 inline-block mr-2">
        </div>

        <div class="hidden md:flex gap-14 text-md font-medium text-gray-600">
            <a href="<?= $isAdmin ? 'admin_dashboard.php' : 'user_dashboard.php' ?>"
                class="hover:text-black transition-colors">
                Dashboard
            </a>
            <a href="reports.php" class="hover:text-black transition-colors">Reports</a>
            <a href="suggestions.php" class="hover:text-black transition-colors">Suggestions</a>
            <a href="archive_report.php" class="hover:text-black transition-colors">Completed Tickets</a>
            <?php if ($isAdmin): ?>
                <a href="user_list.php" class="hover:text-black transition-colors">User List</a>
                <a href="categories_module.php" class="hover:text-black transition-colors">Modules</a>
            <?php endif; ?>
        </div>

        <div class="relative inline-block text-left">
            <button id="profileButton"
                class="flex items-center focus:outline-none hover:opacity-80 transition-opacity cursor-pointer">
                <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" img src="<?= $profilePic ?>"
                    alt="User Profile">
            </button>

            <div id="profileDropdown"
                class="hidden absolute center-0 transform -translate-x-1/2 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden transform origin-top-right transition-all">

                <div class="py-1">
                    <a href="user_profile.php"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-center">
                        Your Profile
                    </a>
                    <hr class="border-gray-100">
                    <a href="../controllers/logout_handler.php"
                        class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-center">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>



<script src="/public/js/navbar.js"></script>