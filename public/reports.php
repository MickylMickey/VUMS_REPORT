<?php
require_once __DIR__ . "/../init.php";

ob_start();
session_start();

$userData = checkAuth();
$categoryOptions = fetchAllFromTable($conn, 'category');
$moduleOptions = fetchAllFromTable($conn, 'module');
$severityOptions = fetchAllFromTable($conn, 'severity');
$visibility = new BugVisibility($conn);
$current_user_id = $userData->user_id;
$user_role = $userData->role;

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
                        <th class="px-4 py-2 text-left">Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= $report['ref_num'] ?>
                            </td>
                            <td class="px-4 py-2">
                                <?= htmlspecialchars($report['username']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-xs text-gray-500 block">
                                    <?= $report['cat_desc'] ?>
                                </span>
                                <?= $report['mod_desc'] ?>
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold 
                            <?= $report['severity'] == 'Critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= ucfirst($report['severity']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate">
                                <?= htmlspecialchars($report['report_desc']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="italic text-gray-600">
                                    <?= $report['status_desc'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <?php if ($report['report_img']): ?>
                                    <a href="uploads/<?= $report['report_img'] ?>" target="_blank"
                                        class="text-blue-500 underline text-xs">View Image</a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">No Image</span>
                                <?php endif; ?>
                            </td>
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
                                <?= htmlspecialchars($sev['severity']) ?>
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
</body>

</html>