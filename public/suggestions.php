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
    <style>
        #projectModal {
            display: none;

            background-color: rgba(0, 0, 0, 0) !important;

            z-index: 9999 !important;
        }

        #projectModal.flex {
            display: flex;
            animation: fadeInModal 0.3s ease-out forwards;
        }

        @keyframes fadeInModal {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #projectModal.flex>div {
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="pt-24 relative min-h-screen">
    <div>
        <?php include "templates/navbar.php"; ?>
        <div id="validationBlock" class="fixed top-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
    </div>



    <div class="px-6 ">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">System Reports</h2>
            <button onclick="toggleModal(true)"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition flex items-center shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Suggestion
            </button>
        </div>

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
                                    <a href="uploads/suggestions/<?= htmlspecialchars($sug['suggestion_img']) ?>"
                                        target="_blank" class="text-blue-500 hover:text-blue-700 underline text-xs">
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
    </div>

    <div class="fixed bottom-10 right-10 z-[90]">
        <button onclick="toggleModal(true)"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-2xl transition-transform hover:scale-105 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Suggestion
        </button>
    </div>

    <div id="projectModal" class="fixed inset-0 hidden items-center justify-center backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold">New Suggestion</h3>
                <button onclick="toggleModal(false)"
                    class="text-white hover:text-gray-200 text-3xl leading-none">&times;</button>
            </div>

            <form action="../controllers/add_suggestions.php" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-4">
                <div>
                    <label for="suggestion_desc" class="block text-sm font-medium text-gray-700 mb-1">Describe your
                        Suggestion</label>
                    <textarea name="suggestion_desc" id="suggestion_desc" rows="4"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none"
                        placeholder="EDI WAG!!!" required></textarea>
                </div>

                <div>
                    <label for="suggestion_img" class="block text-sm font-medium text-gray-700 mb-1">Upload Image
                        Here:</label>
                    <input type="file" name="suggestion_img" id="suggestion_img"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="toggleModal(false)"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-lg">
                        Ipasa mo na
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
<?php ob_end_flush(); ?>

<script>
    const currentUserId = "<?= $current_user_id ?>";

    function toggleModal(show) {
        const modal = document.getElementById('projectModal');
        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    window.onclick = function (event) {
        const modal = document.getElementById('projectModal');
        if (event.target == modal) {
            toggleModal(false);
        }
    }

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

                        const statusToast = document.createElement('div');
                        statusToast.className = "fixed top-24 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-[110] transition-all duration-500";
                        statusToast.innerHTML = "Status updated successfully!";
                        document.body.appendChild(statusToast);

                        setTimeout(() => {
                            statusToast.style.opacity = '0';
                            statusToast.style.transform = 'translateY(-20px)';
                            setTimeout(() => statusToast.remove(), 500);
                        }, 3000);

                        const selectedStatus = parseInt(statusId);

                        if (selectedStatus === 3 || selectedStatus === 4) {
                            const row = this.closest('tr');
                            row.style.transition = 'all 0.5s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';

                            setTimeout(() => {
                                row.remove();
                                if (typeof checkIfTableEmpty === 'function') {
                                    checkIfTableEmpty();
                                }
                            }, 500);
                        }
                    } else {
                        alert('Update failed: ' + (data.error || 'Unknown error'));
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
<script src="js/removeNotification.js" defer></script>

</html>