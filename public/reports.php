<?php
session_start();
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../middleware/auth_middleware.php";
require_once __DIR__ . "/../functions/fetch_report_options.php";

$categoryOptions = fetchAllFromTable($conn, 'category');
$moduleOptions = fetchAllFromTable($conn, 'module');
$severityOptions = fetchAllFromTable($conn, 'severity');
$userData = checkAuth();
$current_user_id = $userData->user_id;
$user_role = $userData->role;

// SQL Logic: Admins see EVERYTHING, Users see only THEIR reports
if ($user_role === 'Admin') {
    $sql = "SELECT r.*, c.category, m.module, s.severity, st.status_name, u.username 
            FROM report r
            JOIN category c ON r.cat_id = c.cat_id
            JOIN module m ON r.mod_id = m.mod_id
            JOIN severity s ON r.sev_id = s.sev_id
            JOIN status st ON r.status_id = st.status_id
            JOIN user u ON r.user_id = u.user_id
            ORDER BY r.report_created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT r.*, c.category, m.module, s.severity, st.status_name, u.username 
            FROM report r
            JOIN category c ON r.cat_id = c.cat_id
            JOIN module m ON r.mod_id = m.mod_id
            JOIN severity s ON r.sev_id = s.sev_id
            JOIN status st ON r.status_id = st.status_id
            JOIN user u ON r.user_id = u.user_id
            WHERE r.user_id = ?
            ORDER BY r.report_created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $current_user_id);
}

$stmt->execute();
$reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
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
                            <td class="px-4 py-2 font-mono text-blue-600">
                                <?= $report['ref_num'] ?>
                            </td>
                            <td class="px-4 py-2">
                                <?= htmlspecialchars($report['username']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-xs text-gray-500 block">
                                    <?= $report['category'] ?>
                                </span>
                                <?= $report['module'] ?>
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold 
                            <?= $report['severity'] == 'Critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= $report['severity'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate">
                                <?= htmlspecialchars($report['report_desc']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="italic text-gray-600">
                                    <?= $report['status_name'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <?php if ($report['report_img']): ?>
                                    <a href="/uploads/<?= $report['report_img'] ?>" target="_blank"
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
                    <select name="cat_id" id="cat_id" class="w-full border rounded-lg p-2">
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
                    <select name="mod_id" id="mod_id" class="w-full border rounded-lg p-2">
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
                    <select name="sev_id" id="sev_id" class="w-full border rounded-lg p-2">
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
                    <input type="text" name="rep_desc" id="rep_desc" placeholder="Anyare?">
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