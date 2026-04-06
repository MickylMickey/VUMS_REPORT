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

// (Check if your object uses 'user_id' or 'id', and 'role' or 'role_id')
$reports = $visibility->getVisibleReports($current_user_id, $user_role);

// 2. You still have $user_role from your middleware to use in the HTML 
// (e.g., to decide who gets to see the "Edit" or "Change Status" buttons)
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

<body>
    <!--List of reports here -->
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">System Reports</h2>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
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
                            <th>Action</th>
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
    </div>
    <!-- Add Report -->
    <h1>Report Problem</h1>
    <div>
        <div>
            <form action="../controllers/add_report.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="cat_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="cat_id" id="cat_id" class="w-full border rounded-lg p-2" required>
                        <option value="" disabled selected>-- Select Category --</option>
                        <option value="Other">Other (Specify in Description)</option>
                        <?php foreach ($categoryOptions as $cat): ?>
                            <option value="<?= $cat['cat_id'] ?>">
                                <?= htmlspecialchars($cat['category']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="mod_id" class="block text-sm font-medium text-gray-700">Module</label>
                    <select name="mod_id" id="mod_id" class="w-full border rounded-lg p-2" required>
                        <option value="" disabled selected>-- Select Module --</option>
                        <option value="Other">Other (Specify in Description)</option>
                        <?php foreach ($moduleOptions as $mod): ?>
                            <option value="<?= $mod['mod_id'] ?>">
                                <?= htmlspecialchars($mod['module']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="sev_id" class="block text-sm font-medium text-gray-700">Severity Level</label>
                    <select name="sev_id" id="sev_id" class="w-full border rounded-lg p-2" required>
                        <option value="" disabled selected>-- Select Severity --</option>
                        <?php foreach ($severityOptions as $sev): ?>
                            <option value="<?= $sev['sev_id'] ?>">
                                <?= htmlspecialchars($sev['sev_desc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="rep_desc">Describe What Happened</label>
                    <input type="text" name="rep_desc" id="rep_desc" placeholder="Anyare?" required>
                </div>
                <div>
                    <label for="rep_img">Report Image</label>
                    <input type="file" name="rep_img" id="rep_img">
                </div>

                <button type="submit" class="bg-blue-600 text-black px-4 py-2 rounded">Submit Report</button>

            </form>
        </div>
    </div>
    <!--Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl mb-4">Edit Report</h2>
            <form id="editForm" action="../controllers/edit_reports.php" method="POST">
                <input type="hidden" name="report_id" id="edit_report_id">

                <label>Category</label>
                <select name="cat_id" id="edit_cat_id" class="w-full border p-2 mb-3">
                    <option value="other">Other</option>
                    <?php foreach ($categoryOptions as $c): ?>
                        <option value="<?= $c['cat_id'] ?>">
                            <?= $c['category'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Module</label>
                <select name="mod_id" id="edit_mod_id" class="w-full border p-2 mb-3">
                    <option value="other">Other</option>
                    <?php foreach ($moduleOptions as $m): ?>
                        <option value="<?= $m['mod_id'] ?>">
                            <?= $m['module'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Severity</label>
                <select name="sev_id" id="edit_sev_id" class="w-full border p-2 mb-3">
                    <option value="other">Other</option>
                    <?php foreach ($severityOptions as $s): ?>
                        <option value="<?= $s['sev_id'] ?>">
                            <?= $s['sev_desc'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Description</label>
                <textarea name="report_desc" id="edit_desc" class="w-full border p-2 mb-3"></textarea>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-600 text-blue px-4 py-2 rounded">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
    // 1. Capture the PHP session ID for the JS to use
    const currentUserId = "<?= $current_user_id ?>";

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
                // 2. ADD updated_by TO THE BODY
                body: `report_id=${reportId}&status_id=${statusId}&updated_by=${currentUserId}`
            })
                .then(response => {
                    if (!response.ok) throw new Error('Server error');
                    return response.json();
                })
                .then(data => {
                    this.style.opacity = '1';

                    if (data.success) {
                        console.log('Update successful');

                        // Check if the status is 3 (Completed) or 4 (Cancelled)
                        // We use parseInt to make sure we are comparing numbers
                        const selectedStatus = parseInt(statusId);

                        if (selectedStatus === 3 || selectedStatus === 4) {
                            // Find the closest Table Row (tr) and remove it with a nice fade-out
                            const row = this.closest('tr');

                            row.style.transition = 'all 0.5s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';

                            setTimeout(() => {
                                row.remove();
                                // Optional: Show a message if the table is now empty
                                checkIfTableEmpty();
                            }, 500);
                        }
                    } else {
                        alert('Update failed: ' + (data.error || 'Unknown error'));
                        // Optional: Reset the dropdown to previous value on failure
                        location.reload();
                    }
                })
                .catch(error => {
                    this.style.opacity = '1';
                    console.error('Error:', error);
                    alert('Connection error. Check console.');
                });
        });
    });
</script>
<script src="js/reports.js"></script>

</html>