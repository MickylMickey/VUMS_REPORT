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
               UPPER(u.username) AS username,
               up.user_prof AS reporter_profile_pic
        FROM user_suggestions us
        LEFT JOIN status st ON us.status_id = st.status_id
        LEFT JOIN users updater ON us.suggestion_updated_by = updater.user_id
        LEFT JOIN users u ON us.user_id = u.user_id
        LEFT JOIN user_profile up ON up.user_id = u.user_id
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Suggestions</title>
</head>

<body class="pt-24 relative min-h-screen flex flex-col">
    <div>
        <?php include "templates/navbar.php"; ?>
        <div id="validationBlock" class="fixed top-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
    </div>

    <div class="mt-10 px-6 pb-24 min-h-[75vh]">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">Community Suggestions</h2>
                <p class="text-slate-500 text-sm mt-1">Share your ideas to improve the system.</p>
            </div>
            <button onclick="toggleModal(true)"
                class="hidden md:flex bg-blue-600 text-white px-5 py-1.5 rounded-xl h-10 w-auto font-semibold hover:bg-blue-700 transition-all items-center shadow-lg shadow-blue-200"
                data-tooltip="Add a new suggestion">
                <i class="fa-solid fa-plus mr-2"></i>New Suggestion
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($suggestions as $sug):
                // Determine status colors
                $statusColor = match ((int) $sug['status_id']) {
                    1 => 'bg-amber-50 text-amber-700 border-amber-100',
                    2 => 'bg-blue-50 text-blue-700 border-blue-100',
                    3 => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    4 => 'bg-rose-50 text-rose-700 border-rose-100',
                    default => 'bg-slate-50 text-slate-600 border-slate-100'
                };
                ?>
                <div
                    class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group">
                    <div class="p-5 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm overflow-hidden border-2 border-slate-100">
                                <?php if (!empty($sug['reporter_profile_pic'])): ?>
                                    <img src="img/prof_pic/<?= htmlspecialchars($sug['reporter_profile_pic']) ?>"
                                        alt="<?= htmlspecialchars($sug['username']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= substr(htmlspecialchars($sug['username']), 0, 1) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 leading-none"><?= htmlspecialchars($sug['username']) ?>
                                </h3>
                                <span class="text-[15px] text-slate-400 mt-1 block">
                                    <?= date('M d, Y', strtotime($sug['suggestion_created_at'])) ?>
                                </span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[15px] font-bold border <?= $statusColor ?>">
                            <?= htmlspecialchars($sug['status_desc']) ?>
                        </span>
                    </div>

                    <div class="p-5 flex-grow">
                        <p class="text-slate-600 text-sm leading-relaxed italic break-all">
                            "<?= nl2br(htmlspecialchars($sug['suggestion_desc'])) ?>"
                        </p>

                        <?php if (!empty($sug['suggestion_img'])): ?>
                            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-100 relative group/img">
                                <img src="uploads/suggestions/<?= htmlspecialchars($sug['suggestion_img']) ?>"
                                    class="w-full h-32 object-cover transition-transform duration-500 group-hover/img:scale-110"
                                    alt="Attachment">
                                <a href="uploads/suggestions/<?= htmlspecialchars($sug['suggestion_img']) ?>" target="_blank"
                                    class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold backdrop-blur-[2px]">
                                    <i class="fa-solid fa-expand mr-2"></i> View Image
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 bg-slate-50/50 rounded-b-3xl mt-auto space-y-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[15px] font-bold text-slate-400 uppercase tracking-wider ml-1">
                                Status</label>
                            <select
                                class="status-updater w-full bg-white border border-slate-200 rounded-xl p-2 text-xs font-semibold focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                                data-report-id="<?= $sug['suggestion_id'] ?>">
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $sug['status_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['status_desc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($sug['suggestion_updated_by']): ?>
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-[15px] text-slate-400">
                                <i class="fa-solid fa-user-check"></i>
                                <span>Modified by <strong><?= htmlspecialchars($sug['updater_name']) ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($suggestions)): ?>
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300 text-2xl">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <p class="text-slate-500 font-medium">No suggestions found. Be the first to suggest something!</p>
            </div>
        <?php endif; ?>
    </div> <footer style=" background-color: #0a2550; color: white;">
     <div class="max-w-7xl mx-auto px-2 py-4 md:py-3">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8">

           
            <div class="text-center md:text-left space-y-1">
                <h2 class="text-2xl font-extrabold tracking-tight text-black-400">
                    Vinculum Technologies Inc.
                </h2>
                
            </div>

        
            <div class="hidden md:block w-px h-12 bg-slate-700"></div>

           
            <div class="text-center md:text-right space-y-1">
                <p class="text-sm text-slate-300">
                    © <span class="font-bold text-white">Vinculum</span>
                 
                </p>

                <div class="flex justify-center md:justify-end gap-4 text-[11px] uppercase tracking-widest text-white-500">
                    <span>System v1.0</span>
                    <span>-</span>
                    <span>VUMS REPORTING System</span>
                </div>
            </div>

        </div>
    </div>
</footer>

    <div id="projectModal"
        class="hidden fixed inset-0 z-[150] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">

        <div id="projectModalBackdrop"
            class="absolute inset-0 bg-slate-900/60 opacity-0 transition-opacity duration-300"
            onclick="toggleModal(false)"></div>

        <div id="projectModalContainer"
            class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 flex flex-col transform scale-95 opacity-0 transition-all duration-300 ease-out">

            <div class="bg-blue-600 px-6 py-5 flex justify-between items-center text-white">
                <div>
                    <h2 class="text-xl font-bold tracking-tight">New Suggestion</h2>
                    <p class="text-blue-100 text-medium mt-0.5">Share your ideas to improve the system.</p>
                </div>
                <button onclick="toggleModal(false)"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="suggestionForm" action="../controllers/add_suggestions.php" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-5">

                <div class="space-y-1.5">
                    <label class="text-[17px] font-semibold text-slate-600 ml-1">Describe your Suggestion</label>
                    <textarea name="suggestion_desc" id="suggestion_desc" rows="4"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all resize-none placeholder:text-slate-400"
                        placeholder="Tell us what's on your mind..." data-required="true"
                        data-error="Description is required."></textarea>
                    <div>
                        <p class="error-message hidden text-red-600 text-sm mt-1"></p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[17px] font-semibold text-slate-600 ml-1">Attach Image (Optional)</label>
                    <p class="text-[15px] text-slate-400 ml-1 mb-1">Tip: You can paste a screenshot directly into the
                        description box!</p>
                    <input type="file" name="suggestion_img" id="suggestion_img_input" accept="image/*"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-sm outline-none transition-all">
                    <div id="paste-preview-container" class="hidden mt-4 relative inline-block">
                        <img id="paste-preview"
                            class="max-h-40 w-auto rounded-xl border-2 border-blue-100 shadow-md object-cover" src="">
                        <button id="clear-preview-btn" type="button" onclick="clearPastedImage()"
                            class="hidden absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="toggleModal(false)"
                        class="flex-1 px-4 py-3 text-sm font-bold text-white bg-[#fb2424] hover:bg-[#c01c1c] rounded-2xl transition-all duration-200">
                        Cancel
                    </button>

                    <button type="submit"
                        class="flex-[2] px-4 py-3 text-sm bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                        Submit Suggestion
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="tooltip"
        class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-[200] px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
    </div>

    <?php ob_end_flush(); ?>

    <script>
        const currentUserId = "<?= $current_user_id ?>";
    </script>

    <script src="js/removeNotification.js" defer></script>
    <script src="js/suggestions.js"></script>
    <script src="js/tooltip.js"></script>
    <script src="js/paste_image_suggestion.js"></script>
    <script src="js/inputValidation.js" defer></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            initFormValidation("suggestionForm");
        });
    </script>