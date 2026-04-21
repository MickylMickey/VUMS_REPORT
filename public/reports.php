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
$isUser = RoleHelper::isUser($userData->role);
$isHr = RoleHelper::isHR($userData->role);


if ($isAdmin) {
    $where = "r.status_id != ?";
    $params = [0];
    $types = "i";
} else {
    $where = "r.status_id != ? AND r.user_id = ?";
    $params = [0, $current_user_id];
    $types = "is";
}


$pagination = getPaginationData(
    $conn,
    "report r",
    $_GET['limit'] ?? 25,
    $_GET['page'] ?? 1,
    $where,
    $params,
    $types
);


$limit = $pagination['limit'];
$offset = $pagination['offset'];
$totalPages = $pagination['totalPages'];
$totalRecords = $pagination['totalRecords'];
$page = $pagination['page'];


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

<body class="min-h-screen flex flex-col pt-24 bg-gray-50">
    <div>
        <?php include "templates/navbar.php"; ?>
        <div id="validationBlock" class="fixed bottom-28 right-5 z-[250] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
    </div>

    <main class="flex-grow">
        <div class="container mx-auto p-6 max-w-7xl">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">System Reports</h2>

                    <p class="text-slate-500 text-sm">Monitor and manage system issues and status updates.</p>
                </div>

                <button onclick="openAddModal()" data-tooltip="Report a new issue or bug in the system"
                    class="hidden md:flex bg-blue-600 text-white px-5 py-1.5 rounded-xl h-10 font-semibold hover:bg-blue-700 transition-all items-center shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-plus mr-2"></i>New Report

                </button>
            </div>

            <div
                class="bg-blue-500 p-4 rounded-t-2xl border-x border-t border-slate-100 flex flex-wrap gap-4 items-center justify-between">
                <div class="flex w-full max-w-xs relative"
                    data-tooltip="Search reports by reference number or reporter name">
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
                            <option value="<?= htmlspecialchars($cat['cat_id']) ?>">
                                <?= htmlspecialchars($cat['category']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="moduleFilter"
                        class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer"
                        data-tooltip="Filter by Module">
                        <option value="">All Modules</option>
                        <?php foreach ($moduleOptions as $mod): ?>
                            <option value="<?= htmlspecialchars($mod['mod_id']) ?>">
                                <?= htmlspecialchars($mod['module']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="severityFilter"
                        class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer"
                        data-tooltip="Filter by Severity">
                        <option value="">All Severities</option>
                        <?php foreach ($severityOptions as $sev): ?>
                            <option value="<?= htmlspecialchars($sev['sev_id']) ?>">
                                <?= htmlspecialchars($sev['sev_desc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button id="resetBtn"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl transition-all"
                        data-tooltip="Reset filters and search">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded-b-2xl shadow-sm border border-slate-100">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Ref Number</th>
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Reporter</th>
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Category & Module</th>
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Severity</th>
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-left text-[13px] font-bold text-black-400 uppercase tracking-wider text-center">
                                Image/Videos</th>
                            <th
                                class="px-6 py-4 text-right text-[13px] font-bold text-black-400 uppercase tracking-wider">
                                Action</th>

                        </tr>
                    </thead>
                    <tbody id="reportsTableBody" class="divide-y divide-slate-100">
                        <?php foreach ($reports as $report): ?>
                            <tr class="report-row hover:bg-blue-50/30 transition-colors group"
                                data-ref="<?= htmlspecialchars($report['ref_num']) ?>"
                                data-reporter="<?= htmlspecialchars($report['reporter_name']) ?>"
                                data-desc="<?= htmlspecialchars($report['report_desc']) ?>"
                                data-cat="<?= $report['cat_id'] ?? 'other' ?>"
                                data-mod="<?= $report['mod_id'] ?? 'other' ?>"
                                data-sev="<?= $report['sev_id'] ?? 'other' ?>">

                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-medium uppercase">
                                        <?= htmlspecialchars($report['ref_num']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-medium font-semibold text-slate-700">
                                        <?= htmlspecialchars($report['reporter_name']) ?>
                                    </div>
                                    <div class="text-[12px] text-slate-400">System User</div>
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
                                        class="status-updater bg-slate-50 border border-slate-200 rounded-lg px-2 py-2 text-xs font-semibold text-slate-600 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all cursor-pointer"
                                        data-report-id="<?= $report['report_id'] ?>" data-user-id="<?= $current_user_id ?>"
                                        data-last-value="<?= $report['status_id'] ?>">
                                        <?php foreach ($statusOptions as $status): ?>
                                            <option value="<?= $status['status_id'] ?>"
                                                <?= $report['status_id'] == $status['status_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($status['status_desc']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $mediaFile = isset($report['report_img']) ? trim($report['report_img']) : '';

                                    if (!empty($mediaFile)):
                                        $fileExt = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                        $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];


                                        $folderPath = in_array($fileExt, $videoExtensions) ? "Videos/" : "uploads/";
                                        $finalPath = $folderPath . $mediaFile;
                                        ?>
                                        <div class="flex items-center justify-center">
                                            <a href="<?= htmlspecialchars($finalPath) ?>" download
                                                class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-600 hover:text-white transition-all shadow-sm group">

                                                <?php if (in_array($fileExt, $videoExtensions)): ?>
                                                    <i class="fa-solid fa-file-video text-sm"></i>
                                                <?php else: ?>
                                                    <i class="fa-solid fa-file-image text-sm"></i>
                                                <?php endif; ?>

                                                <span class="text-xs font-bold uppercase">Download</span>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-300 text-[13px] italic">No media</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($isAdmin): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                     <button
                                      type="button"
                                      class="view-report-btn"
    onclick="openViewModal({
        category: '<?= htmlspecialchars($report['cat_desc'] ?? 'Other') ?>',
        module: '<?= htmlspecialchars($report['mod_desc'] ?? 'Other') ?>',
        severity: '<?= htmlspecialchars($report['severity']) ?>',
        description: `<?= addslashes($report['report_desc']) ?>`,
        image: '<?= $report['report_img'] ?? '' ?>' 
    })"
    style="display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb; color: #ffffff; width: 40px; height: 40px; border-radius: 12px; border: none; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); transition: all 0.2s; flex-shrink: 0;"
    data-tooltip="View Report Details">
    <i class="fa-solid fa-file-lines"></i>
</button>

                                        <?php if ($isAdmin): ?>
                                        <button
                                         class="edit-report-btn"
                                         style="display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb; color: #ffffff; padding: 0 20px; border-radius: 12px; height: 40px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); transition: all 0.2s;"
                                         data-tooltip="Edit Report Details" 
                                         data-id="<?= $report['report_id'] ?>"
                                         data-cat="<?= $report['cat_id'] ?? 'other' ?>"
                                         data-mod="<?= $report['mod_id'] ?? 'other' ?>" 
                                         data-sev="<?= $report['sev_id'] ?>"
                                         data-desc="<?= htmlspecialchars($report['report_desc'], ENT_QUOTES) ?>">
                                         <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($isUser || $isHr): ?>
                                        <button
                                            class="remind-btn hidden md:flex bg-green-600 text-white px-5 py-1.5 rounded-xl h-10 font-semibold text-sm hover:bg-green-700 transition-all items-center shadow-lg shadow-green-200"
                                            data-tooltip="Remind Admin" data-id="<?= $report['report_id'] ?>">
                                            <i class="fa-solid fa-bell"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                    </span> reports
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
            <div id="addModalBackdrop"
                class="absolute inset-0 bg-slate-900/60 opacity-0 transition-opacity duration-300"
                onclick="closeAddModal()"></div>

            <div id="addModalContainer"
                class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 flex flex-col transform scale-95 opacity-0 transition-all duration-300 ease-out">
                <div class="bg-blue-500 px-6 py-5 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Report a Problem</h2>
                        <p class="text-blue-100 text-medium text-white ">Tell us what's going wrong</p>
                    </div>
                    <button onclick="closeAddModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="addReportForm" action="../controllers/add_report.php" method="POST"
                    enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto max-h-[80vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[13px] font-semibold text-slate-600 ml-1">Category</label>
                            <select name="cat_id" id="cat_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer"
                                data-required="true" data-error="Category is required.">
                                <option value="" disabled selected>Select...</option>
                                <?php foreach ($categoryOptions as $cat): ?>
                                    <option value="<?= $cat['cat_id'] ?>"
                                        data-desc="<?= htmlspecialchars($cat['cat_desc'] ?? $cat['category']) ?>">
                                        <?= htmlspecialchars($cat['category']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="other" data-desc="Use this for issues not listed above.">Other...
                                </option>
                            </select>
                            <div id="cat-desc-panel"
                                class="hidden mt-2 p-3 bg-slate-50 border border-slate-200 rounded-xl animate-fade-in">
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-tight">Definition</p>
                                <p class="text-xs text-slate-600 leading-relaxed mt-1" id="cat-desc-text"></p>
                            </div>
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
                                    <option value="<?= $mod['mod_id'] ?>"
                                        data-desc="<?= htmlspecialchars($mod['mod_desc'] ?? $mod['module']) ?>">
                                        <?= htmlspecialchars($mod['module']) ?>
                                    </option>

                                <?php endforeach; ?>
                                <option value="other" data-desc="Use this for issues not listed above.">Other...
                                </option>
                            </select>
                            <div id="mod-desc-panel"
                                class="hidden mt-2 p-3 bg-slate-50 border border-slate-200 rounded-xl animate-fade-in">
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-tight">Scope</p>
                                <p class="text-xs text-slate-600 leading-relaxed mt-1" id="mod-desc-text"></p>
                            </div>
                            <div>
                                <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[15px] uppercase tracking-wider font-bold text-slate-400 ml-1">
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
                        <label for="rep_desc" class="text-[15px] font-semibold text-slate-600 ml-1">What
                            happened?</label>
                        <textarea name="rep_desc" id="rep_desc" rows="3" placeholder="Briefly describe the issue... "
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all resize-none"
                            data-required="true" data-error="Description is required."></textarea>
                        <div>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[20px] font-semibold text-slate-600 ml-1">Attach Image (Optional)</label>
                        <p class="text-[13px] text-slate-400 ml-1 mb-1">Tip: You can paste a screenshot directly into
                            the
                            description box!</p>

                        <input type="file" name="rep_img" id="rep_img_input" accept="image/*,video/*"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-sm outline-none transition-all">

                        <div id="paste-preview-container" class="hidden mt-4 relative inline-block">
                            <img id="paste-preview"
                                class="max-h-40 w-auto rounded-xl border-2 border-blue-100 shadow-md object-cover"
                                src=""><button id="clear-preview-btn" type="button" onclick="clearPastedImage()"
                                class="hidden absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 12px; padding-top: 16px;">
                        <button type="button" onclick="closeAddModal()"
                            style="flex: 1; padding: 12px 16px; font-size: 14px; font-weight: bold; background-color: #fb2424; color: white; border: none; border-radius: 16px; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#c01c1c'"
                            onmouseout="this.style.backgroundColor='#fb2424'">
                            Cancel
                        </button>

                        <button type="submit"
                            style="flex: 2; padding: 12px 16px; font-size: 14px; font-weight: bold; background-color: #2563eb; color: white; border: none; border-radius: 16px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#1d4ed8'"
                            onmouseout="this.style.backgroundColor='#2563eb'"
                            onmousedown="this.style.transform='scale(0.95)'"
                            onmouseup="this.style.transform='scale(1)'">
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="editModal"
            class="hidden fixed inset-0 z-[150] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">

            <div id="editModalBackdrop"
                class="absolute inset-0 bg-slate-900/60 opacity-0 transition-opacity duration-300"
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
                            <label class="text-[15px] font-semibold text-slate-600 ml-1">Category</label>
                            <div class="relative">
                                <select name="cat_id" id="edit_cat_id"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer"
                                    data-required="true" data-error="Category is required.">
                                    <option value="other" data-desc="Select this if the category is not listed.">Other
                                    </option>
                                    <?php foreach ($categoryOptions as $c): ?>
                                        <option value="<?= $c['cat_id'] ?>"
                                            data-desc="<?= htmlspecialchars($c['category_long_desc'] ?? $c['category']) ?>">
                                            <?= htmlspecialchars($c['category']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i
                                    class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                            <div id="edit_cat_desc_panel"
                                class="hidden mt-2 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-tight">Definition</p>
                                <p class="text-xs text-slate-600 leading-relaxed mt-1 edit-desc-text"></p>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[15px] font-semibold text-slate-600 ml-1">Module</label>
                            <div class="relative">
                                <select name="mod_id" id="edit_mod_id"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                                    <option value="other" data-desc="Select this if the module is not listed.">Other
                                    </option>
                                    <?php foreach ($moduleOptions as $m): ?>
                                        <option value="<?= $m['mod_id'] ?>"
                                            data-desc="<?= htmlspecialchars($m['module_long_desc'] ?? $m['module']) ?>">
                                            <?= htmlspecialchars($m['module']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i
                                    class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                            <div id="edit_mod_desc_panel"
                                class="hidden mt-2 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-tight">Scope</p>
                                <p class="text-xs text-slate-600 leading-relaxed mt-1 edit-desc-text"></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[15px] font-semibold text-slate-600 ml-1">Severity Level</label>

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
                            class="flex-1 px-4 py-3 text-sm font-bold text-white rounded-2xl bg-[#fb2424] hover:bg-[#c01c1c] rounded-[16px] transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-[2] px-4 py-3 text-sm bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>


     <div id="viewModal" 
     style="position: fixed; inset: 0; z-index: 150; display: none; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); transition: all 0.3s;">
    
    <div id="viewModalBackdrop" onclick="closeViewModal()" 
         style="position: absolute; inset: 0; background-color: rgba(15, 23, 42, 0.6); opacity: 0; transition: opacity 0.3s;"></div>

    <div id="viewModalContainer" 
         style="background-color: #ffffff; border-radius: 1.5rem; width: 100%; max-width: 32rem; max-height: 90vh; overflow: hidden; z-index: 10; display: flex; flex-direction: column; transform: scale(0.95); opacity: 0; transition: all 0.3s ease-out; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        
        <div style="background-color: #2563eb; padding: 1.25rem 1.5rem; color: #ffffff; flex-shrink: 0;">
            <h2 style="font-size: 20px; font-weight: 700; margin: 0;">Report Details</h2>
            <p style="color: #dbeafe; font-size: 15px; margin-top: 2px;">Reviewing submitted evidence and information.</p>
        </div>

        <div style="padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.25rem;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase;">Category</label>
                    <div id="view_category" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 14px; color: #1e293b;"></div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase;">Module</label>
                    <div id="view_module" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 14px; color: #1e293b;"></div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase;">Severity Level</label>
                <div id="view_severity" style="display: inline-flex; width: fit-content; padding: 6px 16px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid #e2e8f0;"></div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase;">Description</label>
                <div id="view_desc" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; font-size: 14px; color: #475569; line-height: 1.6; white-space: pre-wrap;"></div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase;">Evidence Attachment</label>
                <div id="view_img_container" style="background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 8px; display: flex; justify-content: center; align-items: center; min-height: 200px;">
                    <img id="view_attachment" src="" style="max-width: 100%; border-radius: 10px; display: none; cursor: zoom-in;" onclick="window.open(this.src, '_blank')">
                    <div id="no_img_placeholder" style="text-align: center; color: #94a3b8;">
                        <i class="fa-regular fa-image" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                        <span style="font-size: 12px;">No image uploaded</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 1.25rem; border-top: 1px solid #f1f5f9; background: #ffffff; flex-shrink: 0; display: flex; justify-content: center;">
    <button type="button" onclick="closeViewModal()"
        style="width: 50%; padding: 12px; font-size: 14px; font-weight: 700; color: #2563eb; border-radius: 12px; background-color: #eff6ff; border: 1px solid #dbeafe; cursor: pointer; transition: all 0.2s;"
        onmouseover="this.style.backgroundColor='#dbeafe'"
        onmouseout="this.style.backgroundColor='#eff6ff'">
        Close 
    </button>
</div>
    </div>
</div>

        <div id="tooltip"
            class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
        </div>

    </main>
    <div id="statusConfirmModal" class="hidden"
        style="position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 1rem; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">

        <div id="statusConfirmContainer"
            style="position: relative; background-color: rgba(255, 255, 255, 0.95); width: 100%; max-width: 420px; padding: 2.5rem; border-radius: 2.5rem; box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.3); transform: scale(0.95); opacity: 0; transition: all 0.3s ease-out; text-align: center;">

            <div
                style="margin: 0 auto 1.25rem; display: flex; height: 70px; width: 70px; align-items: center; justify-content: center; border-radius: 1.5rem; background: linear-gradient(135deg, #fffbeb 0%, #ffedd5 100%);">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 35px; width: 35px; color: #f59e0b;" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>

            <h3
                style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0; letter-spacing: -0.025em; line-height: 1.2;">
                Confirm Status Update
            </h3>

            <p
                style="color: #64748b; margin-top: 0.75rem; font-size: 1rem; font-weight: 500; line-height: 1.5; padding: 0 1rem;">
                Are you sure you want to update the status of this report?
            </p>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 2rem;">
                <button id="confirmStatusBtn" disabled
                    style="width: 100%; padding: 1rem; border-radius: 1.25rem; font-weight: 700; color: white; background-color: #2563eb; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); opacity: 0.6;">
                    Yes (3s)
                </button>

                <button id="cancelStatusBtn"
                    style="width: 100%; padding: 0.85rem; border-radius: 1.25rem; background-color: #fee2e2; color: #ef4444; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; border: 1px solid rgba(239, 68, 68, 0.1);">
                    Maybe Later
                </button>
            </div>
        </div>
    </div>
    <div class="mt-auto">
        <?php include "templates/footer.php"; ?>
    </div>
</body>
<?php ob_end_flush(); ?>
<script src="js/removeNotification.js" defer></script>
<script src="js/reports.js"></script>
<script src="js/tooltip.js"></script>
<script src="js/dropdown_helper.js"></script>
<script src="js/paste_image.js"></script>
<script src="js/inputValidation.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        initFormValidation("addReportForm"),
            initFormValidation("editForm");
    });
</script>


</html>