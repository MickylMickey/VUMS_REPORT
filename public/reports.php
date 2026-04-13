<?php
require_once __DIR__ . "/../init.php";

ob_start();

$userData = checkAuth();
$categoryOptions = fetchAllFromTable($conn, 'category');
$moduleOptions = fetchAllFromTable($conn, 'module');
$severityOptions = fetchAllFromTable($conn, 'severity');
$statusOptions = fetchStatus($conn);
$visibility = new BugVisibility($conn);
$current_user_id = $userData->user_id;
$user_role = $userData->role;
$isAdmin = (isset($userData->role) && $userData->role === 'Admin');

// 1. Define your filters first
$where = "r.status_id != ?";
$params = [0];
$types = "i";

// 2. RUN PAGINATION FIRST to generate $limit and $offset
$pagination = getPaginationData(
    $conn,
    "report r", // Use the alias 'u' to match your $where clause
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
$reports = $visibility->getVisibleReports($current_user_id, $user_role, $limit, $offset);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Reports</title>
</head>

<body class="pt-24 bg-gray-50">
    <div>
        <?php include "templates/navbar.php"; ?>
        <div id="validationBlock" class="fixed top-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6 max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">System Reports</h2>
                <p class="text-slate-500 text-sm">Monitor and manage system issues and status updates.</p>
            </div>

            <button onclick="openAddModal()"
                class="hidden md:flex bg-blue-600 text-white px-5 py-1.5 rounded-xl h-10 w-auto font-semibold hover:bg-blue-700 transition-all items-center shadow-lg shadow-blue-200"
                data-tooltip="Add New Report">
                <i class="fa-solid fa-plus mr-2"></i>New Report
            </button>
        </div>

        <div
            class="bg-blue-500 p-4 rounded-t-2xl border-x border-t border-slate-100 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex w-full max-w-xs relative" data-tooltip="Search Reports">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Search by Ref Number or Reporter. . ."
                    class="w-full pl-11 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
            </div>

            <div class="flex gap-2">
                <select id="categoryFilter"
                    class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer"
                    data-tooltip="Filter by Category">
                    <option value="">All Categories</option>
                    <?php foreach ($categoryOptions as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['cat_id']) ?>"><?= htmlspecialchars($cat['category']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="moduleFilter"
                    class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer"
                    data-tooltip="Filter by Module">
                    <option value="">All Modules</option>
                    <?php foreach ($moduleOptions as $mod): ?>
                        <option value="<?= htmlspecialchars($mod['mod_id']) ?>"><?= htmlspecialchars($mod['module']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="severityFilter"
                    class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer"
                    data-tooltip="Filter by Severity">
                    <option value="">All Severities</option>
                    <?php foreach ($severityOptions as $sev): ?>
                        <option value="<?= htmlspecialchars($sev['sev_id']) ?>"><?= htmlspecialchars($sev['sev_desc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button id="resetBtn"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl transition-all"
                    data-tooltip="Reset Filters">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>


        <div class="overflow-x-auto bg-white rounded-b-2xl shadow-sm border border-slate-100">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Ref Number</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Reporter</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Category & Module</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Severity</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Description</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">
                            Image</th>
                        <?php if ($isAdmin): ?>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="reportsTableBody" class="divide-y divide-slate-100">
                    <?php foreach ($reports as $report): ?>
                        <tr class="report-row hover:bg-blue-50/30 transition-colors group"
                            data-ref="<?= htmlspecialchars($report['ref_num']) ?>"
                            data-reporter="<?= htmlspecialchars($report['reporter_name']) ?>"
                            data-desc="<?= htmlspecialchars($report['report_desc']) ?>"
                            data-cat="<?= $report['cat_id'] ?? 'other' ?>" data-mod="<?= $report['mod_id'] ?? 'other' ?>"
                            data-sev="<?= $report['sev_id'] ?? 'other' ?>">

                            <td class="px-6 py-4">
                                <span
                                    class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-medium uppercase">
                                    <?= htmlspecialchars($report['ref_num']) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-700">
                                    <?= htmlspecialchars($report['reporter_name']) ?>
                                </div>
                                <div class="text-[11px] text-slate-400">System User</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-blue-500 font-bold uppercase tracking-tight">
                                        <?= $report['cat_id'] ? htmlspecialchars($report['cat_desc']) : 'Other' ?>
                                    </span>
                                    <span class="text-sm text-slate-600 font-medium">
                                        <?= $report['mod_id'] ? htmlspecialchars($report['mod_desc']) : 'Other' ?>
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <?php
                                $sevColor = match (strtolower($report['severity'])) {
                                    'critical' => 'bg-red-50 text-red-600 border-red-100',
                                    'high' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    'medium' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    default => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                };
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= $sevColor ?>">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    <?= htmlspecialchars($report['severity']) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-500 max-w-[200px] truncate"
                                    title="<?= htmlspecialchars($report['report_desc']) ?>">
                                    <?= htmlspecialchars($report['report_desc']) ?>
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <select
                                    class="status-updater bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 text-xs font-semibold text-slate-600 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all cursor-pointer"
                                    data-report-id="<?= $report['report_id'] ?>" data-user-id="<?= $current_user_id ?>">
                                    <?php foreach ($statusOptions as $status): ?>
                                        <option value="<?= $status['status_id'] ?>"
                                            <?= $status['status_id'] == $report['status_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($status['status_desc']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <?php if ($report['report_img']): ?>
                                    <a href="uploads/<?= $report['report_img'] ?>" target="_blank"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-600 hover:text-white transition-all">
                                        <i class="fa-solid fa-image text-xs"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-300 text-[15px] italic">No image</span>
                                <?php endif; ?>
                            </td>

                            <?php if ($isAdmin): ?>
                                <td class="px-6 py-4 text-right">
                                    <button 
    class="edit-report-btn hidden md:flex bg-blue-600 text-white px-5 py-1.5 rounded-xl h-10 w-auto font-semibold text-sm hover:bg-blue-700 transition-all items-center shadow-lg shadow-blue-200"
    data-id="<?= $report['report_id'] ?>" 
    data-cat="<?= $report['cat_id'] ?? 'other' ?>"
    data-mod="<?= $report['mod_id'] ?? 'other' ?>" 
    data-sev="<?= $report['sev_id'] ?>"
    data-desc="<?= htmlspecialchars($report['report_desc'], ENT_QUOTES) ?>">
    <i class="fa-solid fa-pen-to-square mr-2"></i>
    Edit
</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <tbody id="reportsTableBody" class="divide-y divide-slate-100">
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="100%" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-4xl mb-4 opacity-20"></i>
                                <p class="text-sm font-medium">No reports found matching your criteria</p>
                                <p class="text-xs">Try adjusting your filters or search term</p>
                            </div>
                        </td>
                    </tr>

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

    <div id="addReportModal"
        class="hidden fixed inset-0 z-[250] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">
        <div id="addModalBackdrop" class="absolute inset-0 bg-slate-900/60 opacity-0 transition-opacity duration-300"
            onclick="closeAddModal()"></div>

        <div id="addModalContainer"
            class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 flex flex-col transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <div class="bg-blue-500 px-6 py-5 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Report a Problem</h2>
                    <p class="text-blue-100 text-xs text-white ">Tell us what's going wrong.</p>
                </div>
                <button onclick="closeAddModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="addReportForm" action="../controllers/add_report.php" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-4 overflow-y-auto max-h-[80vh]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-slate-600 ml-1">Category</label>
                        <select name="cat_id" id="cat_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer"
                            data-required="true" data-error="Category is required.">
                            <option value="" disabled selected>Select...</option>
                            <?php foreach ($categoryOptions as $cat): ?>
                                <option value="<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['category']) ?></option>
                            <?php endforeach; ?>
                            <option value="other">Other</option>
                        </select>
                        <div>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-slate-600 ml-1">Module</label>
                        <select name="mod_id" id="mod_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer"
                            data-required="true" data-error="Module is required.">
                            <option value="" disabled selected>Select...</option>
                            <?php foreach ($moduleOptions as $mod): ?>
                                <option value="<?= $mod['mod_id'] ?>"><?= htmlspecialchars($mod['module']) ?></option>
                            <?php endforeach; ?>
                            <option value="other">Other</option>
                        </select>
                        <div>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] uppercase tracking-wider font-bold text-slate-400 ml-1">
                        Severity Level
                    </label>
                    <div class="severity-group">
                        <div class="flex gap-2">
                            <?php foreach ($severityOptions as $s): ?>
                                <label class="flex-1 cursor-pointer relative group">
                                    <input type="radio" name="sev_id" value="<?= $s['sev_id'] ?>" class="hidden peer"
                                        data-required="true">

                                    <div
                                        class="py-2 text-center text-[12px] font-semibold rounded-xl border
                                        border-slate-200 bg-white text-slate-500
                                        transition-all duration-200
                                        hover:border-blue-300 hover:text-blue-600
                                        peer-checked:border-blue-500 peer-checked:text-blue-600 peer-checked:bg-blue-50">
                                        <?= htmlspecialchars($s['sev_desc']) ?>
                                    </div>

                                    <div class="absolute -top-1 -right-1 opacity-0 scale-50 
                                                peer-checked:opacity-100 peer-checked:scale-100 
                                                transition-all duration-300 pointer-events-none">
                                        <div
                                            class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-sm">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p id="severity-error" class="error-message hidden text-red-600 text-sm mt-2">
                            Severity level is required.
                        </p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="rep_desc" class="text-[13px] font-semibold text-slate-600 ml-1">What
                        happened?</label>
                    <textarea name="rep_desc" id="rep_desc" rows="3" placeholder="Briefly describe the issue... "
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all resize-none"
                        data-required="true" data-error="Description is required."></textarea>
                    <div>
                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[13px] font-semibold text-slate-600 ml-1">Attach Image (Optional)</label>
                    <input type="file" name="rep_img" accept="image/*"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-sm outline-none transition-all">
                </div>

                <div style="display: flex; align-items: center; gap: 12px; padding-top: 16px;">
                    <button type="button" onclick="closeAddModal()"
                        style="flex: 1; padding: 12px 16px; font-size: 14px; font-weight: bold; background-color: #10b981; color: white; border: none; border-radius: 16px; cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#059669'"
                        onmouseout="this.style.backgroundColor='#10b981'">
                        Discard
                    </button>

                    <button type="submit"
                        style="flex: 2; padding: 12px 16px; font-size: 14px; font-weight: bold; background-color: #2563eb; color: white; border: none; border-radius: 16px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                        onmouseout="this.style.backgroundColor='#2563eb'"
                        onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal"
        class="hidden fixed inset-0 z-[150] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">

        <div id="editModalBackdrop" class="absolute inset-0 bg-slate-900/60 opacity-0 transition-opacity duration-300"
            onclick="closeEditModal()"></div>

        <div id="editModalContainer"
            class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 flex flex-col transform scale-95 opacity-0 transition-all duration-300 ease-out">

            <div class="bg-blue-600 px-6 py-5 flex justify-between items-center text-white">
                <div>
                    <h2 class="text-xl font-bold tracking-tight">Edit Report Details</h2>
                    <p class="text-blue-100 text-xs mt-0.5">Update the classification or description.</p>
                </div>
                <button onclick="closeEditModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="editForm" action="../controllers/edit_reports.php" method="POST" class="p-6 space-y-5">
                <input type="hidden" name="report_id" id="edit_report_id">

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-slate-600 ml-1">Category</label>
                        <div class="relative">
                            <select name="cat_id" id="edit_cat_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                                <option value="other">Other</option>
                                <?php foreach ($categoryOptions as $c): ?>
                                    <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['category']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                        <div>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-slate-600 ml-1">Module</label>
                        <div class="relative">
                            <select name="mod_id" id="edit_mod_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                                <option value="other">Other</option>
                                <?php foreach ($moduleOptions as $m): ?>
                                    <option value="<?= $m['mod_id'] ?>"><?= htmlspecialchars($m['module']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[13px] font-semibold text-slate-600 ml-1">Severity Level</label>

                    <div class="flex gap-2">
                        <?php foreach ($severityOptions as $s): ?>
                            <label class="flex-1 cursor-pointer relative group">
                                <input type="radio" name="sev_id" value="<?= $s['sev_id'] ?>"
                                    class="absolute opacity-0 w-0 h-0 peer" required>

                                <div class="py-2 text-center text-[12px] font-semibold rounded-xl border
                                border-slate-200 bg-white text-slate-500
                                transition-all duration-200
                                hover:border-blue-300 hover:text-blue-600
                                peer-checked:border-blue-50 peer-checked:text-blue-600 peer-checked:bg-blue-50 
                                peer-focus:ring-2 peer-focus:ring-blue-500/20">
                                    <?= htmlspecialchars($s['sev_desc']) ?>
                                </div>

                                <div class="absolute -top-1 -right-1 opacity-0 scale-50 
                                        peer-checked:opacity-100 peer-checked:scale-100 
                                        transition-all duration-300 pointer-events-none">
                                    <div
                                        class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-sm">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[13px] font-semibold text-slate-600 ml-1">Description</label>
                    <textarea name="report_desc" id="edit_desc" rows="4"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all resize-none placeholder:text-slate-400"
                        data-required="true" data-error="Description is required."></textarea>
                    <div>
                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-3 text-sm font-bold text-slate-500 rounded-2xl hover:bg-slate-100 transition-colors">
                        Discard
                    </button>
                    <button type="submit"
                        class="flex-[2] px-4 py-3 text-sm bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
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
<script src="js/reports.js"></script>
<script src="js/tooltip.js"></script>
<script src="js/inputValidation.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        initFormValidation("addReportForm"),
            initFormValidation("editForm");
    });
</script>


</html>