<?php
require_once __DIR__ . "/../init.php";
ob_start();

$user = checkAuth(['Admin', 'HR']);
$role = $user->role ?? null;
$isAdmin = RoleHelper::isAdmin($role);
$isUser = RoleHelper::isUser($role);
$isHr = RoleHelper::isHr($role);
$userVisibility = new UserVisibility($conn);
$roleOptions = fetchRoles($conn);

// 1. Define your filters (Active users only)
$where = "u.user_status_id = ?";
$params = [1];
$types = "i";

// 2. RUN PAGINATION FIRST
$pagination = getPaginationData(
    $conn,
    "users u INNER JOIN user_profile up ON u.user_id = up.user_id",
    $_GET['limit'] ?? 25,
    $_GET['page'] ?? 1,
    $where,
    $params,
    $types
);

// 3. Extract pagination values
$limit = $pagination['limit'];
$offset = $pagination['offset'];
$totalPages = $pagination['totalPages'];
$totalRecords = $pagination['totalRecords'];
$page = $pagination['page'];

// 4. Fetch the users using dynamic limit and offset
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

<body class="bg-[#f8fafc] min-h-screen flex flex-col antialiased text-slate-900 pt-24">
    <div><?php include "templates/navbar.php"; ?>
    </div>

    <main class="flex-grow">
        <div class="container mx-auto p-6">
            <div id="validationBlock" class="fixed bottom-28 right-5 z-[200] flex flex-col gap-3 pointer-events-none">
                <div class="pointer-events-auto">
                    <?= showValidation() ?>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">User Management</h1>
                        <p class="text-sm text-slate-500 mt-1">Manage system access, roles, and member profiles.</p>
                    </div>
                    <button onclick="toggleAddModal(true)"
                        data-tooltip="Create a new user account with specific access roles"
                        class="hidden md:flex bg-blue-600 text-white px-5 py-1.5 rounded-xl h-10 w-auto font-semibold hover:bg-blue-700 transition-all items-center shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-plus mr-2 text-xs"></i>
                        Create New User
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto mb-6">
                    <div class="relative flex-grow md:flex-grow-0 md:min-w-[300px]"
                        data-tooltip="Search users by username">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
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
                                <tr style="background-color: #3b82f6;">
                                    <th
                                        style="padding: 16px 24px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: white; border-bottom: 1px solid #2563eb;">
                                        User Details
                                    </th>

                                    <th
                                        style="padding: 16px 24px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: white; border-bottom: 1px solid #2563eb;">
                                        Username
                                    </th>

                                    <th
                                        style="padding: 16px 24px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: white; border-bottom: 1px solid #2563eb;">
                                        Access Level
                                    </th>

                                    <th
                                        style="padding: 16px 24px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: white; border-bottom: 1px solid #2563eb; text-align: right;">
                                        Actions
                                    </th>
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

                                            <!-- USER DETAILS -->
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
                                                            data-tooltip="View profile details"
                                                            class="text-sm font-semibold text-slate-900 hover:text-blue-600 transition-colors">
                                                            <?= $fullName ?>
                                                        </a>
                                                        <span class="text-medium text-slate-500">
                                                            <?= htmlspecialchars($user['email']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- USERNAME -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600">
                                                <span class="bg-slate-100 px-2 py-1 rounded text-[15px] font-mono">
                                                    <?= htmlspecialchars($user['username']) ?>
                                                </span>
                                            </td>

                                            <!-- ACCESS LEVEL -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php
                                                $role = strtolower($user['role_name']);

                                                switch ($role) {
                                                    case 'admin':

                                                        $style = "background-color: #fee2e2; color: #b91c1c; border-color: #fecaca;";
                                                        break;
                                                    case 'hr':

                                                        $style = "background-color: #f5f3ff; color: #6d28d9; border-color: #ddd6fe;";
                                                        break;
                                                    default:

                                                        $style = "background-color: #f0fdf4; color: #15803d; border-color: #dcfce7;";
                                                        break;
                                                }
                                                ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[13px] font-medium border"
                                                    style="<?= $style ?>">
                                                    <?= htmlspecialchars($user['role_name']) ?>
                                                </span>
                                            </td>

                                            <!-- ACTIONS -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div
                                                    class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-all duration-300">

                                                    <!-- VIEW -->
                                                    <a href="../public/user_profile.php?u=<?= $user_id ?>"
                                                        data-tooltip="View profile details" class="inline-flex items-center justify-center w-12 h-12 rounded-xl
                  bg-emerald-50 text-emerald-600 border border-emerald-100
                  hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:shadow-emerald-200
                  active:scale-95 transition-all duration-200">

                                                        <i class="fa-solid fa-user-shield text-lg"></i>
                                                    </a>

                                                    <!-- EDIT -->
                                                    <button
                                                        onclick="openEditUserModal('<?= $user_id ?>', '<?= addslashes($user['username']) ?>', '<?= addslashes($user['user_role_id']) ?>')"
                                                        data-tooltip="Edit account" class="hidden md:inline-flex items-center justify-center w-12 h-12 rounded-xl
                   bg-blue-50 text-blue-600 border border-blue-100
                   hover:bg-blue-600 hover:text-white hover:shadow-lg hover:shadow-blue-200
                   active:scale-95 transition-all duration-200">

                                                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                                                    </button>

                                                    <!-- ARCHIVE -->
                                                    <?php if (!$isSelf): ?>
                                                        <button onclick="openArchiveUserModal('<?= $user['user_id'] ?>')"
                                                            data-tooltip="Archive this account" class="inline-flex items-center justify-center w-12 h-12 rounded-xl
                       bg-red-50 text-red-600 border border-red-100
                       hover:bg-red-600 hover:text-white hover:shadow-lg hover:shadow-red-200
                       active:scale-95 transition-all duration-200">

                                                            <i class="fa-solid fa-box-archive text-lg"></i>
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
    </main>
    <div id="addUserModal"
        class="fixed inset-0 hidden items-center justify-center backdrop-blur-md bg-slate-900/60 z-[200] px-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-slate-200 transition-all duration-300 opacity-0 scale-95">

            <div class="bg-blue-500 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-[20px] text-white font-bold">Create New User</h3>
                    <p class="text-[15px] text-white text-slate-200 font-bold">Enter account and personal details.</p>

                </div>
                <button onclick="toggleAddModal(false)" class="text-slate-400 hover:text-slate-600 transition-colors">
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
                            <h4 class="text-[15px] font-bold uppercase tracking-widest text-slate-400">Account
                                Access</h4>
                        </div>
                        <!-- PROFILE PICTURE -->
                        <div class="flex flex-col items-center gap-4 w-64">
                            <div
                                class="relative w-32 h-32 overflow-hidden rounded-full border-2 border-slate-200 bg-slate-50">
                                <img id="preview-img"
                                    src="https://ui-avatars.com/api/?name=User&background=cbd5e1&color=fff"
                                    class="w-full h-full object-cover" alt="Preview">
                            </div>

                            <label class="cursor-pointer bg-white border border-slate-200 w-60 px-3 py-2 rounded-lg
                  text-[15px] font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm
                  flex items-center justify-center gap-2">
                                <i class="fa-solid fa-camera"></i>
                                <span>Profile Picture</span>
                                <input type="file" id="prof-pic-input" name="prof_pic" class="hidden" accept="image/*">
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[14px] font-semibold text-slate-700 mb-1">Username <span
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
                                <label class="block text-[14px] font-semibold text-slate-700 mb-1">
                                    Password <span class="text-red-500">*</span>
                                </label>


                                <div class="relative">
                                    <input type="password" id="password" name="password" data-required="true"
                                        data-error="Password is required."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">


                                    <button type="button" id="togglePassword"
                                        class="absolute inset-y-0 right-5 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <svg id="eyeIcon" xmlns="http://w3.org" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.67 8.5 7.652 6 12 6c4.348 0 8.33 2.5 9.964 5.678a1.012 1.012 0 0 1 0 .644C20.33 15.5 16.348 18 12 18c-4.348 0-8.33-2.5-9.964-5.678Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div>
                                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                </div>
                            </div>

                        </div>

                        <div>
                            <label class="block text-[14px] font-semibold text-slate-700 mb-1">Email Address<span
                                    class="text-red-500"> *</span></label>
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
                            <h4 class="text-[15px] font-bold uppercase tracking-widest text-slate-400">Personal
                                Details</h4>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[15px] font-semibold text-slate-700 mb-1">First
                                    Name</label>
                                <input type="text" name="fname" data-required="true"
                                    data-error="First Name is required."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <div>
                                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[15px] font-semibold text-slate-700 mb-1">Last Name</label>
                                <input type="text" name="lname" data-required="true" data-error="Last Name is required."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <div>
                                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[15px] font-semibold text-slate-700 mb-1">Middle
                                    Name</label>
                                <input type="text" name="mname" data-required="true"
                                    data-error="Middle Name is required."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <div>
                                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[15px] font-semibold text-slate-700 mb-1">Birthdate</label>
                                <input type="date" name="birthday" data-required="true"
                                    data-error="Birthdate is required."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <div>
                                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 items-end">

                            <!-- USER ROLE -->
                            <div>
                                <label class="block text-[15px] font-semibold text-slate-700 mb-1">
                                    User Role
                                </label>

                                <select name="user_role" data-required="true" data-error="User Role is required." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm
                   focus:ring-2 focus:ring-blue-500/20 outline-none appearance-none" style="color:#64748b;"
                                    onchange="this.style.color = this.value ? '#0f172a' : '#64748b'">

                                    <option value="" selected disabled hidden>
                                        Choose Role
                                    </option>

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
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end items-center gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="toggleAddModal(false)"
                        class="px-4 py-2 rounded-lg text-[15px] font-bold text-white bg-[#fb2424] hover:bg-[#c01c1c] rounded-[16px] transition-all duration-200">Cancel</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-[15px] font-bold shadow-md shadow-blue-100 transition-all">
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal"
        class="fixed inset-0 hidden items-center justify-center backdrop-blur-md bg-slate-900/60 z-[200] px-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-slate-100 opacity-0 scale-95 duration-300">
            <div class="bg-blue-500 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-[20px] font-bold text-white text-slate-900">Update Account</h3>
                    <p class="text-[15px] text-white">Update account credentials and system access.</p>
                </div>
                <button onclick="toggleEditModal(false)" class="text-slate-400 hover:text-slate-600 transition-colors">
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
                                <option value="<?= htmlspecialchars($option['user_role_id']) ?>"
                                    <?= ($option['role_name'] == $user['role_name']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($option['role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wider">
                        New Password
                    </label>

                    <div class="relative">
                        <input type="password" name="password" id="password_edit"
                            placeholder="Leave blank to keep current"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 pr-11 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400">

                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg id="toggleIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4 flex justify-end items-center gap-3">
                    <button type="button" onclick="toggleEditModal(false)" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white
           bg-[#fb2424] hover:bg-[#c01c1c] transition-all duration-200">
                        Cancel
                    </button>

                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white
           bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div id="tooltip"
        class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
    </div>
    <div class="mt-auto">
        <?php include "templates/footer.php"; ?>
    </div>

    <!-- Overlay -->
    <div id="archiveUserModal" class="fixed inset-0 z-[200] hidden items-center justify-center 
            bg-slate-900/70 backdrop-blur-sm 
            opacity-0 transition-all duration-300">


        <div id="archiveModalContent" class="bg-white w-full max-w-md mx-4 
                rounded-3xl shadow-2xl 
                transform scale-95 opacity-0 transition-all duration-300 
                overflow-hidden">


            <div class="px-6 pt-6 pb-4 text-center">
                <div class="mx-auto flex items-center justify-center w-14 h-14 
                        rounded-full  text-red-600 mb-4">
                    <i class="fa-solid fa-box-archive text-xl"></i>
                </div>

                <h3 class="text-xl font-semibold text-gray-900">
                    Archive User
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    This action will archive the selected user. You can restore them later.
                </p>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100"></div>

            <!-- Actions -->
            <form action="../controllers/archive_user.php" method="POST" class="px-6 py-5 flex gap-3">

                <input type="hidden" name="user_id" id="archiveUserId">

                <button type="button" onclick="closeArchiveUserModal()" class="flex-1 py-2.5 rounded-xl 
               border border-gray-300 
               bg-white text-gray-700 font-medium
               hover:bg-gray-50 hover:border-gray-400
               transition-all duration-200">
                    Cancel
                </button>

                <!-- Confirm -->
                <button type="submit" class="flex-1 py-2.5 rounded-xl 
                           bg-red-600 text-white font-semibold
                           shadow-md shadow-red-200
                           hover:bg-red-700 hover:shadow-lg hover:shadow-red-300
                           active:scale-[0.98]
                           transition-all duration-200">
                    Confirm
                </button>
            </form>
        </div>
    </div>
    <div id="toast-container" class="fixed top-5 right-5 z-[100] flex flex-col gap-3"></div>
</body>

<?php ob_end_flush(); ?>
<script src="js/removeNotification.js" defer></script>
<script src="js/user_list.js"></script>
<script src="js/tooltip.js"></script>
<script src="js/inputValidation.js" defer></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // 1. Form Validations
        initFormValidation("addUserForm");
        initFormValidation("EditUserForm");

        // 2. TOAST TRIGGER FROM SESSION

        <?php if (isset($_SESSION['validation'])): ?>
            const type = "<?php echo $_SESSION['validation']['type']; ?>";
            const message = "<?php echo $_SESSION['validation']['message']; ?>";


            if (typeof showToast === "function") {

                const duration = type === "error" ? 10000 : 7000;
                showToast(message, type, duration);
            }

            <?php unset($_SESSION['validation']); ?>
        <?php endif; ?>
    });
</script>


</html>