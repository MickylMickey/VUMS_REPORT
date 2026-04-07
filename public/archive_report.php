<?php
require_once __DIR__ . "/../init.php";
ob_start();
$userData = checkAuth();

$visibility = new reportArchiveVisibility($conn);
$current_user_id = $userData->user_id;
$user_role = $userData->role;
$isAdmin = (isset($userData->role) && $userData->role === 'Admin');

// (Check if your object uses 'user_id' or 'id', and 'role' or 'role_id')
$reportArchive = $visibility->getVisibleArchiveReports($current_user_id, $user_role);
$whereClause = $isAdmin ? "" : "WHERE sa.user_id = ?";

$sql = "SELECT sa.*, 
               st.status_desc, 
               updater.username AS updater_name,
               UPPER(u.username) AS username 
        FROM suggestion_archive sa
        LEFT JOIN status st ON sa.status_id = st.status_id
        LEFT JOIN users updater ON sa.suggestion_updated_by = updater.user_id
        LEFT JOIN users u ON sa.user_id = u.user_id
        $whereClause
        ORDER BY sa.suggestion_updated_at DESC"; // Sort by newest archived first

$stmt = $conn->prepare($sql);

if (!$isAdmin) {
    $stmt->bind_param("i", $current_user_id);
}

$stmt->execute();
$suggestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Archive</title>
</head>

<body class="pt-24">
    <div>
        <?php include "templates/navbar.php"; ?>
    </div>
    <!-- Report Archive -->
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
                        <th class="px-4 py-2 text-left">Date Created</th>
                        <th class="px-4 py-2 text-left">Date Archived</th>
                        <th class="px-4 py-2 text-left">Archived by</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($reportArchive as $archive): ?>
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($archive['ref_num']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <?= htmlspecialchars($archive['reporter_name']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-xs text-gray-400 block uppercase font-semibold">
                                    <?= $archive['cat_id'] ? htmlspecialchars($archive['cat_desc']) : 'Other' ?>
                                </span>
                                <span class="text-sm">
                                    <?= $archive['mod_id'] ? htmlspecialchars($archive['mod_desc']) : 'Other' ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold 
                                    <?= $archive['severity'] == 'Critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= ucfirst($archive['severity']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($archive['report_desc']) ?>">
                                <?= htmlspecialchars($archive['report_desc']) ?>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($archive['status_desc']) ?>">
                                <?= htmlspecialchars($archive['status_desc']) ?>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($archive['report_created_at']) ?>">
                                <?= htmlspecialchars($archive['report_created_at']) ?>
                            </td>
                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($archive['report_updated_at']) ?>">
                                <?= htmlspecialchars($archive['report_updated_at']) ?>
                            </td>

                            <td class="px-4 py-2 text-sm max-w-xs truncate"
                                title="<?= htmlspecialchars($archive['reporter_name']) ?>">
                                <?= htmlspecialchars($archive['reporter_name']) ?>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!--Suugestion Archive-->

    <h2 class="text-2xl font-bold mb-4">System Reports</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-red-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Reporter</th>
                    <th class="px-4 py-2 text-left">Suggestion</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Date Created</th>
                    <th class="px-4 py-2 text-left">Date Archived</th>
                    <th class="px-4 py-2 text-left">Archived by</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suggestions as $sug): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 font-semibold text-gray-700">
                            <?= htmlspecialchars($sug['username']) ?>
                        </td>

                        <td class="px-4 py-2 text-sm text-gray-600 max-w-xs">
                            <?= nl2br(htmlspecialchars($sug['suggestion_desc'])) ?>
                        </td>

                        <td class="px-4 py-2 font-semibold text-gray-700">
                            <?= htmlspecialchars($sug['status_desc']) ?>
                        </td>
                        <td class="px-4 py-2 font-semibold text-gray-700">
                            <?= htmlspecialchars($sug['suggestion_created_at']) ?>
                        </td>
                        <td class="px-4 py-2 font-semibold text-gray-700">
                            <?= htmlspecialchars($sug['suggestion_updated_at']) ?>
                        </td>
                        <td class="px-4 py-2 font-semibold text-gray-700">
                            <?= htmlspecialchars($sug['updater_name'] ?? 'Not updated yet') ?>
                        </td>

                    </tr>
                <?php endforeach; ?>

                <?php if (empty($suggestions)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            No suggestions found. Be the first to suggest something!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>