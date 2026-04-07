<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth();
$statusOptions = fetchStatus($conn);
$current_user_id = $userData->user_id;
$user_role = $userData->role;

$sql = "SELECT us.*, 
               st.status_desc, 
               updater.username AS updater_name,
               UPPER(u.username) AS username 
        FROM user_suggestions us
        LEFT JOIN status st ON us.status_id = st.status_id
        LEFT JOIN users updater ON us.suggestion_updated_by = updater.user_id
        LEFT JOIN users u ON us.user_id = u.user_id
        ORDER BY us.suggestion_created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$suggestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <title>Suggestions</title>
</head>

<body class="pt-24">
    <div>
        <?php include "templates/navbar.php"; ?>
    </div>


    <h2 class="text-2xl font-bold mb-4">System Reports</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-red-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Reporter</th>
                    <th class="px-4 py-2 text-left">Suggestion</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Updated by</th>
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

                        <td>
                            <select class="status-updater w-full border rounded-lg p-2"
                                data-report-id="<?= $sug['suggestion_id'] ?>">
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $sug['status_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['status_desc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-4 py-2 text-sm max-w-xs truncate">
                            <?php if ($sug['suggestion_updated_by']): ?>
                                Last updated by
                                <?= htmlspecialchars($sug['updater_name']) ?> <br>
                                on
                                <?= date('M d, Y', strtotime($sug['suggestion_updated_at'])) ?>
                            <?php else: ?>
                                No updates yet
                            <?php endif; ?>
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
<script>
    // 1. Capture the PHP session ID for the JS to use
    const currentUserId = "<?= $current_user_id ?>";

    document.querySelectorAll('.status-updater').forEach(select => {
        select.addEventListener('change', function () {
            const suggestionId = this.getAttribute('data-report-id');
            const statusId = this.value;

            this.style.opacity = '0.5';

            fetch('../controllers/quick_update_suggestion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                // 2. ADD updated_by TO THE BODY
                body: `suggestion_id=${suggestionId}&status_id=${statusId}&updated_by=${currentUserId}`
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

</html>