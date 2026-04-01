<?php
require_once __DIR__ . "/../init.php";

ob_start();


$userData = checkAuth();
$current_user_id = $userData->user_id;
$user_role = $userData->role;

$sql = "SELECT us.*, 
               st.status_desc, 
               UPPER(u.username) AS username 
        FROM user_suggestions us
        JOIN status st ON us.status_id = st.status_id
        JOIN users u ON us.user_id = u.user_id
        ORDER BY us.suggestion_created_at DESC"; // Sorting by newest suggestion first

$stmt = $conn->prepare($sql);
$stmt->execute();
$suggestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare($sql);
$stmt->execute();
$reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <title>Suggestions</title>
</head>

<body>


    <h2 class="text-2xl font-bold mb-4">System Reports</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-red-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Reporter</th>
                    <th class="px-4 py-2 text-left">Suggestion</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Image</th>
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

                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <?= htmlspecialchars($sug['status_desc']) ?>
                            </span>
                        </td>

                        <td class="px-4 py-2">
                            <?php if (!empty($sug['suggestion_img'])): ?>
                                <a href="uploads/suggestions/<?= htmlspecialchars($sug['suggestion_img']) ?>" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 underline text-xs">
                                    View Attachment
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs italic">No Image</span>
                            <?php endif; ?>
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
    <!-- Add Suggestions -->
    <div>
        <h1>Add Suggestions</h1>
        <div>
            <form action="../controllers/add_suggestions.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="suggestion_desc">Describe your Suggestion</label>
                    <input type="text" name="suggestion_desc" id="suggestion_desc" placeholder="EDI WAG!!!" required>
                    <label for="suggestion_img">Upload Image Here:</label>
                    <input type="file" name="suggestion_img" id="suggestion_img">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Ipasa mo na</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>