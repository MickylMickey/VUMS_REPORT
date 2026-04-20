<?php
require_once __DIR__ . "/../init.php";

// Role Checks
$userData = checkAuth();
$user_id = $userData->user_id;
$user_role_id = $userData->role;
$isAdmin = RoleHelper::isAdmin($userData->role);
$isUser = RoleHelper::isUser($userData->role);
$isHr = RoleHelper::isHR($userData->role);

$pagination = getPaginationData(
    $conn,                     
    "notifications",                   
    $_GET['limit'] ?? 10,      
    $_GET['page'] ?? 1,       
    null,         
    null, // Parameter for WHERE condition (selected barangay)
    null                     
);

// Extract pagination values from the result array
$offset = $pagination['offset'];           // Starting point for SQL LIMIT query
$limit = $pagination['limit'];             // Number of records per page
$totalPages = $pagination['totalPages'];   // Total number of pages available
$totalRecords = $pagination['totalRecords']; // Total number of matching records
$page = $pagination['page'];

$notifications = fetchNotification($conn, $user_id, $role, $limit, $offset);
$roles = fetchRoles($conn);
var_dump($userData);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notification List</title>
    <link rel="stylesheet" href="./output.css">
</head>


<?php include "templates/navbar.php"; ?>
<div class="h-20"></div>

<div class="flex h-screen w-full overflow-hidden">
    <div class="flex-1 flex flex-col min-w-0">
        <div class="border-b border-gray-300 bg-white h-15 z-50">
            <?php include "templates/navbar.php"; ?>
        </div>

        <main class="flex-1 overflow-y-auto">
            <div class="px-8 py-6 space-y-6">
                <?php renderBreadcrumb(['Home' => 'dashboard.php', 'Notification' => 'user_notification.php']); ?>

                <div class="rounded-xl bg-gradient-to-r from-[#413072] to-[#6b56a3] py-8 px-6 text-white shadow-md">
                    <div class="flex flex-col">
                        <h1 class="font-bold text-3xl tracking-tight">Notification Inbox</h1>
                        <p class="text-indigo-100 mt-1">Review and manage your system updates and report logs.</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th
                                        class="p-4 text-xs uppercase font-bold tracking-wider text-left text-gray-500 w-[20%]">
                                        Sender</th>
                                    <th
                                        class="p-4 text-xs uppercase font-bold tracking-wider text-left text-gray-500 w-[55%]">
                                        Message</th>
                                    <th
                                        class="p-4 text-xs uppercase font-bold tracking-wider text-left text-gray-500 w-[15%]">
                                        Received</th>
                                    <th class="p-4 w-[10%]"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="notificationTableBody">
                                <?php if (empty($notifications)): ?>
                                    <tr>
                                        <td colspan="4" class="p-12 text-center text-gray-400 italic">No notifications
                                            found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notification):
                                        $isUnread = ($notification['is_read'] == 0);
                                        $rowClass = $isUnread ? "bg-white font-semibold" : "bg-gray-50/40 font-normal text-gray-600";
                                        ?>
                                        <tr onclick="openNotificationModal('<?= addslashes($notification['message']) ?>'); markAsRead('<?= $notification['id'] ?>');"
                                            class="cursor-pointer transition-all hover:bg-indigo-50/30 group <?= $rowClass ?>"
                                            id="notification-<?= $notification['id'] ?>">

                                            <td class="p-4 text-sm flex items-center gap-3">
                                                <div class="relative flex-shrink-0">
                                                    <img src="<?= htmlspecialchars($notification['sender_image'] ?? 'assets/images/default.jpg') ?>"
                                                        class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                                    <?php if ($isUnread): ?>
                                                        <span class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
                                                            <span
                                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                            <span
                                                                class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <span
                                                    class="truncate max-w-[150px]"><?= htmlspecialchars($notification['sender_name']) ?></span>
                                            </td>

                                            <td class="p-4 text-sm">
                                                <p class="truncate max-w-md"><?= htmlspecialchars($notification['message']) ?>
                                                </p>
                                            </td>

                                            <td class="p-4 text-xs text-gray-500 whitespace-nowrap">
                                                <?= formatRelativeTime($notification['created_at']) ?>
                                            </td>

                                            <td class="p-4 text-right">
                                                <button
                                                    onclick="event.stopPropagation(); deleteNotification('<?= $notification['id'] ?>')"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-gray-400 hover:text-red-500 focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?= $offset + 1 ?>
                                </span> to
                                <span class="font-medium">
                                    <?= min($offset + $limit, $totalRecords) ?>
                                </span> of
                                <span class="font-medium">
                                    <?= $totalRecords ?>
                                </span> notifications
                            </div>
                            <div class="flex gap-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                        Previous
                                    </a>
                                <?php else: ?>
                                    <button disabled
                                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                        Previous
                                    </button>
                                <?php endif; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                        Next
                                    </a>
                                <?php else: ?>
                                    <button disabled
                                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                        Next
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="js/notification/userRemoveNotification.js"></script>
<script>
    function markAsRead(id) {
        // Logic to hit your controller and update UI
        const row = document.getElementById(`notification-${id}`);
        row.classList.remove('font-semibold', 'bg-white');
        row.classList.add('bg-gray-50/40', 'font-normal', 'text-gray-600');
        const badge = row.querySelector('.animate-ping')?.parentElement;
        if (badge) badge.remove();
    }

    function deleteNotification(id) {
        if (confirm('Archive this notification?')) {
            // Call your userRemoveNotification.js logic here
            document.getElementById(`notification-${id}`).style.opacity = '0';
            setTimeout(() => document.getElementById(`notification-${id}`).remove(), 300);
        }
    }
</script>


</html>