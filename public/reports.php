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
                <thead class="bg-gray-100">
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
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold 
                                    <?= $report['severity'] == 'Critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= ucfirst($report['severity']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($report['report_desc']) ?>">
                                <?= htmlspecialchars($report['report_desc']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <select class="status-updater w-full border rounded-lg p-1 text-sm bg-white"
                                    data-report-id="<?= $report['report_id'] ?>">
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
            class="hidden fixed inset-0 z-[150] flex items-center justify-center p-6 backdrop-blur-sm">
            <div class="absolute inset-0 bg-black/40" onclick="closeAddModal()"></div>

            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden z-10 flex flex-col min-h-[600px]">

                <div class="bg-blue-600 px-5 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white">Report a Problem</h2>
                    <button onclick="closeAddModal()" class="text-white/80 hover:text-white transition-all">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="../controllers/add_report.php" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-6 flex-grow overflow-y-auto">

                    <div class="space-y-5">
                        <div>
                            <label for="cat_id" class="block text-sm font-bold text-gray-700 mb-1.5">Category</label>
                            <select name="cat_id" id="cat_id"
                                class="w-full border border-gray-300 rounded-xl p-4 focus:ring-4 focus:ring-blue-500 outline-none text-sm transition-all"
                                required>
                                <option value="" disabled selected>-- Select Category --</option>
                                <?php foreach ($categoryOptions as $cat): ?>
                                    <option value="<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['category']) ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>

                        <div class="mb-4">
                            <label for="mod_id" class="block text-sm font-medium text-gray-700">Module</label>
                            <select name="mod_id" id="mod_id" class="w-full border rounded-lg p-2" data-required="true"
                                data-error="Module is required.">
                                <option value="" disabled selected>-- Select Module --</option>
                                <?php foreach ($moduleOptions as $mod): ?>
                                    <option value="<?= $mod['mod_id'] ?>"><?= htmlspecialchars($mod['module']) ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>

                        <div class="mb-4">
                            <label for="sev_id" class="block text-sm font-medium text-gray-700">Severity Level</label>
                            <select name="sev_id" id="sev_id" class="w-full border rounded-lg p-2" data-required="true"
                                data-error="Severity Level is required.">
                                <option value="" disabled selected>-- Select Severity --</option>
                                <?php foreach ($severityOptions as $sev): ?>
                                    <option value="<?= $sev['sev_id'] ?>"><?= htmlspecialchars($sev['sev_desc']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>

                        <div>
                            <label for="rep_desc">Describe What Happened</label>
                            <input type="text" name="rep_desc" id="rep_desc" placeholder="Anyare?" data-required="true"
                                data-error="Description is required.">
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6">
                        <button type="button" onclick="closeAddModal()"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 text-sm bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-md transition-all active:scale-95">
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="editModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center p-6 backdrop-blur-sm">
            <div class="absolute inset-0 bg-black/40" onclick="closeModal()"></div>

            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden z-10 flex flex-col min-h-[600px]">

                <div class="bg-blue-600 px-5 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white">Edit Report</h2>
                    <button onclick="closeModal()" class="text-white/80 hover:text-white transition-all">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form id="editForm" action="../controllers/edit_reports.php" method="POST"
                    class="p-6 space-y-6 flex-grow overflow-y-auto">
                    <input type="hidden" name="report_id" id="edit_report_id">

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Category</label>
                            <select name="cat_id" id="edit_cat_id"
                                class="w-full border border-gray-300 rounded-xl p-4 focus:ring-4 focus:ring-blue-500 outline-none text-sm"
                                data-required="true" data-error="Category is required.">
                                <option value="other">Other</option>
                                <?php foreach ($categoryOptions as $c): ?>
                                    <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['category']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Module</label>
                            <select name="mod_id" id="edit_mod_id"
                                class="w-full border border-gray-300 rounded-xl p-4 focus:ring-4 focus:ring-blue-500 outline-none text-sm"
                                data-required="true" data-error="Module is required.">
                                <option value="other">Other</option>
                                <?php foreach ($moduleOptions as $m): ?>
                                    <option value="<?= $m['mod_id'] ?>"><?= htmlspecialchars($m['module']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Severity</label>
                            <select name="sev_id" id="edit_sev_id"
                                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                data-required="true" data-error="Severity is required.">
                                <?php foreach ($severityOptions as $s): ?>
                                    <option value="<?= $s['sev_id'] ?>"><?= htmlspecialchars($s['sev_desc']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Description</label>
                            <textarea name="report_desc" id="edit_desc" rows="5"
                                class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 outline-none text-sm resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6">
                        <button type="button" onclick="closeModal()"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 text-sm bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-md transition-all active:scale-95">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php ob_end_flush(); ?>

        <script src="js/removeNotification.js" defer></script>
        <script>
            const currentUserId = "<?= $current_user_id ?>";
            const editModal = document.getElementById('editModal');
            const addModal = document.getElementById('addReportModal');

            // Functions to control modals
            function openModal() { editModal.classList.remove('hidden'); }
            function closeModal() { editModal.classList.add('hidden'); }

            function openAddModal() { addModal.classList.remove('hidden'); }
            function closeAddModal() { addModal.classList.add('hidden'); }

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    closeAddModal();
                }
            });
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="report_desc" id="edit_desc" rows="4"
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        data-required="true" data-error="Description is required.">
                    </textarea>
                    <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                </div >

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow-md transition-colors">
                        Save Changes
                    </button>
                </div>
            </form >
        </div >
    </div >

</body >
                <?php ob_end_flush(); ?>
                < script src = "js/removeNotification.js" defer ></script>

        <script>
    // 1. Capture the PHP session ID for the JS to use
    const currentUserId = "<?= $current_user_id ?>";
            const editModal = document.getElementById('editModal');

            // --- MODAL FUNCTIONS ---
            function openModal() {
                editModal.classList.remove('hidden');
            }

            function closeModal() {
                editModal.classList.add('hidden');
            }

            // Close modal if user hits 'Esc' key
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

            document.querySelectorAll('.edit-report-btn').forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('edit_report_id').value = this.getAttribute('data-id');
                    document.getElementById('edit_cat_id').value = this.getAttribute('data-cat');
                    document.getElementById('edit_mod_id').value = this.getAttribute('data-mod');
                    document.getElementById('edit_sev_id').value = this.getAttribute('data-sev');
                    document.getElementById('edit_desc').value = this.getAttribute('data-desc');
                    openModal();
                });
            });

            document.querySelectorAll('.status-updater').forEach(select => {
                select.addEventListener('change', function () {
                    const reportId = this.getAttribute('data-report-id');
                    const statusId = this.value;
                    this.style.opacity = '0.5';

                    fetch('../controllers/quick_update_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `report_id=${reportId}&status_id=${statusId}&updated_by=${currentUserId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            this.style.opacity = '1';
                            if (data.success) {
                                const toast = document.createElement('div');
                                toast.className = "fixed top-24 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-[110]";
                                toast.innerHTML = "Status updated successfully!";
                                document.body.appendChild(toast);
                                setTimeout(() => toast.remove(), 3000);

                                if (parseInt(statusId) === 3 || parseInt(statusId) === 4) {
                                    this.closest('tr').remove();
                                }
                            } else {
                                alert('Update failed: ' + (data.error || 'Unknown error'));
                                location.reload();
                            }
                        });
                });
            });
        </script>
        <script src="js/reports.js"></script>
        <script src="js/inputValidation.js" defer></script>
        <script>document.addEventListener("DOMContentLoaded", () => {
                initFormValidation("addReportForm"),
                    initFormValidation("editForm");
            });</script>


</html>