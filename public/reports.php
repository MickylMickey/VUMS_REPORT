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

$reports = $visibility->getVisibleReports($current_user_id, $user_role);

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

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">System Reports</h2>
            <button onclick="openAddModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i>New Report
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow mb-12">
            <table class="min-w-full table-auto">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Ref Number</th>
                        <th class="px-4 py-2 text-left">Reporter</th>
                        <th class="px-4 py-2 text-left">Category/Module</th>
                        <th class="px-4 py-2 text-left">Severity</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Updated by</th>
                        <th class="px-4 py-2 text-left">Image</th>
                        <?php if ($isAdmin): ?>
                            <th class="px-4 py-2 text-left">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($reports as $report): ?>
                        <?php
                        // Define the class inside the loop for EACH specific report
                        $sev = strtolower($report['severity'] ?? 'low');
                        $badgeMap = [
                            'critical' => 'badge-critical',
                            'high' => 'badge-high',
                            'medium' => 'badge-medium',
                            'low' => 'badge-low'
                        ];
                        $badgeClass = $badgeMap[$sev] ?? 'badge-low';
                        ?>
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($report['ref_num']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <?= htmlspecialchars($report['reporter_name']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-xs text-gray-400 block uppercase font-semibold">
                                    <?= $report['cat_id'] ? htmlspecialchars($report['cat_desc']) : 'Other' ?>
                                </span>
                                <span class="text-sm">
                                    <?= $report['mod_id'] ? htmlspecialchars($report['mod_desc']) : 'Other' ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs font-bold 
<?= $report['severity'] == 'Critical' ? 'bg-red-100 text-red-700' :
            ($report['severity'] == 'High' ? 'bg-orange-100 text-orange-700' :
                ($report['severity'] == 'Medium' ? 'bg-yellow-100 text-yellow-700' :
                    'bg-green-100 text-green-700')) ?>">
                                    <?= htmlspecialchars($report['severity']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($report['report_desc']) ?>">
                                <?= htmlspecialchars($report['report_desc']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <select class="status-updater w-full border rounded-lg p-1 text-sm bg-white"
                                    data-report-id="<?= $report['report_id'] ?>" data-user-id="<?= $current_user_id ?>">
                                    <?php foreach ($statusOptions as $status): ?>
                                        <option value="<?= $status['status_id'] ?>"
                                            <?= $status['status_id'] == $report['status_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($status['status_desc']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                <?php if ($report['updated_by']): ?>
                                    <span class="font-medium text-gray-700">By
                                        <?= htmlspecialchars($report['updater_name']) ?></span><br>
                                    <?= date('M d, Y', strtotime($report['report_updated_at'])) ?>
                                <?php else: ?>
                                    <span class="italic text-gray-400">No updates yet</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <?php if ($report['report_img']): ?>
                                    <a href="uploads/<?= $report['report_img'] ?>" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 underline text-xs">View Image</a>
                                <?php else: ?>
                                    <span class="text-gray-300 text-xs italic">N/A</span>
                                <?php endif; ?>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td class="px-4 py-2">
                                    <button
                                        class="edit-report-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors"
                                        data-id="<?= $report['report_id'] ?>" data-cat="<?= $report['cat_id'] ?? 'other' ?>"
                                        data-mod="<?= $report['mod_id'] ?? 'other' ?>" data-sev="<?= $report['sev_id'] ?>"
                                        data-desc="<?= htmlspecialchars($report['report_desc'], ENT_QUOTES) ?>">
                                        Edit
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                        <p class="text-blue-100 text-xs text-white ">Tell us what's going wrong.</p>
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
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled selected>Select...</option>
                                <?php foreach ($categoryOptions as $cat): ?>
                                    <option value="<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['category']) ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[13px] font-semibold text-slate-600 ml-1">Module</label>
                            <select name="mod_id" id="mod_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled selected>Select...</option>
                                <?php foreach ($moduleOptions as $mod): ?>
                                    <option value="<?= $mod['mod_id'] ?>"><?= htmlspecialchars($mod['module']) ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] uppercase tracking-wider font-bold text-slate-400 ml-1">
                            Severity Level
                        </label>

                        <div class="flex gap-2">
                            <?php foreach ($severityOptions as $s): ?>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="sev_id" value="<?= $s['sev_id'] ?>" class="hidden peer"
                                        required>

                                    <div class="
                    py-2 text-center text-[12px] font-semibold rounded-xl border
                    border-slate-200 bg-white text-slate-500
                    transition-all duration-200
                    hover:border-blue-300 hover:text-blue-600
                    peer-checked:border-blue-500 peer-checked:text-blue-600 peer-checked:bg-blue-50
                ">
                                        <?= htmlspecialchars($s['sev_desc']) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="rep_desc" class="text-[13px] font-semibold text-slate-600 ml-1">What
                            happened?</label>
                        <textarea name="rep_desc" id="rep_desc" rows="3" placeholder="Briefly describe the issue..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all resize-none"></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button type="button" onclick="closeAddModal()"
                            class="flex-1 px-4 py-3 text-sm font-bold text-slate-500 rounded-2xl hover:bg-slate-100 transition-colors">Discard</button>
                        <button type="submit"
                            class="flex-[2] px-4 py-3 text-sm bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all active:scale-95">Submit
                            Report</button>
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

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-slate-600 ml-1">Severity</label>
                        <select name="sev_id" id="edit_sev_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                                    <?php foreach ($severityOptions as $s): ?>
                                <option value="<?= $s['sev_id'] ?>"><?= htmlspecialchars($s['sev_desc']) ?></option>
                                    <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-slate-600 ml-1">Description</label>
                        <textarea name="report_desc" id="edit_desc" rows="4"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all resize-none placeholder:text-slate-400"></textarea>
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

</body>
<?php ob_end_flush(); ?>
<script src="js/removeNotification.js" defer></script>
<script src="js/reports.js"></script>
<script src="js/inputValidation.js" defer></script>
<script>document.addEventListener("DOMContentLoaded", () => {
        initFormValidation("addReportForm"),
            initFormValidation("editForm");
    });</script>


</html>