<?php
require_once(__DIR__ . '/../../init.php');
ob_start();

$user = checkAuth();
$role = $user->role; // We now know this contains 'admin', 'hr', or 'user'
$user_id = $user->user_id;

$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);
$isHr = RoleHelper::isHr($role);

// Define the base folder where images are stored
$uploadPath = "/public/img/prof_pic/";

// Check if user has a profile
if (!empty($user->user_prof)) {
    $profilePic = $uploadPath . $user->user_prof;
} else {
    $profilePic = "https://ui-avatars.com/api/?name=" . urlencode($user->username) . "&background=random";
}

// FIX: Pass $role into these functions so they know how to filter the SQL
$unreadCount = getUnreadCount($conn, $user_id, $role); 
$recentNotifications = fetchNotification($conn, $user_id, $role, 5, 0);
?>

<link href="/public/dist/output.css" rel="stylesheet">

<nav class="absolute top-4 left-1/2 -translate-x-1/2 w-[90%] max-w-6xl z-40">
    <div class="bg-white/70 backdrop-blur-md border border-white/20 px-6 py-3 rounded-2xl shadow-lg flex items-center justify-between">

        <div class="flex items-center">
            <img src="/public/img/images.jpg" alt="Logo" class="rounded-full w-12 h-12 mr-3">
        </div>

        <div class="hidden md:flex gap-10 text-sm font-medium text-gray-600">
            <a href="<?= $isAdmin ? 'admin_dashboard.php' : ($isHr ? 'hr_dashboard.php' : 'user_dashboard.php') ?>"
                class="hover:text-black transition-colors">Dashboard</a>

            <a href="reports.php" class="hover:text-black transition-colors">Reports</a>
            <a href="suggestions.php" class="hover:text-black transition-colors">Suggestions</a>
            <a href="archive_report.php" class="hover:text-black transition-colors">Completed Tickets</a>

            <?php if ($isAdmin): ?>
                <a href="categories_module.php" class="hover:text-black transition-colors">Modules</a>
            <?php endif; ?>

            <?php if ($isAdmin || $isHr): ?>
                <a href="user_list.php" class="hover:text-black transition-colors">User List</a>
                <a href="archive_list.php" class="hover:text-black transition-colors">Archived Users</a>
            <?php endif; ?>
        </div>

         <?php if ($isAdmin): ?>
        <div class="flex items-center gap-4">

            <div class="relative" id="notification-wrapper">
                <button id="notif-bell" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-full transition">
                    <i class="fa-solid fa-bell text-xl"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="absolute top-1 right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    <?php endif; ?>
                </button>

                <div id="notif-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center bg-gray-50/50">
                       <button id="mark-all-read" 
        data-userid="<?= $user_id ?>" data-role="<?= $role ?>"
                        class="text-[15px] text-blue-600 font-bold hover:underline">
                        Mark all as read
                    </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        <?php if (empty($recentNotifications)): ?>
                            <div class="p-8 text-center text-gray-400 text-[15px]">All caught up!</div>
                        <?php else: ?>
                            <?php foreach ($recentNotifications as $n): ?>
                                <div class="p-4 flex gap-3 hover:bg-blue-50/40 transition border-b <?= !$n['is_read'] ? 'bg-blue-50/20 border-l-4 border-l-blue-500' : '' ?>">
                                    <img src="<?= !empty($n['sender_image']) ? '/public/img/prof_pic/' . $n['sender_image'] : '/public/img/default.png' ?>"
                                         class="w-9 h-9 rounded-full object-cover">
                                    <div>
                                        <p class="text-xs text-gray-700"><?= htmlspecialchars($n['message']) ?></p>
                                        <span class="text-[10px] text-gray-400"><?= timeAgo($n['created_at']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
            <div class="relative">
                <button id="profileButton" class="flex items-center hover:opacity-80 transition">
                    <img class="h-11 w-11 rounded-full object-cover border-2 border-gray-200"
                        src="<?= $profilePic ?>" alt="User Profile">
                </button>
                <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border z-50 overflow-hidden">
                    <a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Your Profile</a>
                    <hr>
                    <a href="../controllers/logout_handler.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-center">Logout</a>
                </div>
            </div>

        </div>

    </div>
</nav>

<script src="/public/js/navbar.js"></script>
<script src="/public/js/notifications.js"></script>