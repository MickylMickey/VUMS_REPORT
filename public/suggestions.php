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
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group">
    <div class="p-5 border-b border-slate-50 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm overflow-hidden border-2 border-slate-100">
                <?php if (!empty($sug['reporter_profile_pic'])): ?>
                    <img src="img/prof_pic/<?= htmlspecialchars($sug['reporter_profile_pic']) ?>"
                        alt="<?= htmlspecialchars($sug['username']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= substr(htmlspecialchars($sug['username']), 0, 1) ?>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 leading-none"><?= htmlspecialchars($sug['username']) ?></h3>
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
    <?php 
        $mediaFile = trim($sug['suggestion_img']);
        $fileExt = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
        $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
        $isVideo = in_array($fileExt, $videoExtensions);
        
        // Path linking: Kung gagamit ka ng hiwalay na folder, 
        // siguraduhin na tumutugma ito sa iyong add_suggestions.php controller
        $finalPath = "uploads/suggestions/" . $mediaFile; 
    ?>

    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-100 relative group/img bg-black">
    <?php if ($isVideo): ?>
        <video 
            src="<?= htmlspecialchars($finalPath) ?>" 
            muted 
            loop 
            class="w-full h-32 object-cover opacity-80 group-hover/img:opacity-100 transition-opacity"
            onmouseover="this.play()" 
            onmouseout="this.pause(); this.currentTime = 0;">
        </video>
        
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none group-hover/img:opacity-0 transition-opacity">
            <i class="fa-solid fa-play text-white text-2xl shadow-lg"></i>
        </div>
    <?php else: ?>
        <img src="<?= htmlspecialchars($finalPath) ?>"
            class="w-full h-32 object-cover transition-transform duration-500 group-hover/img:scale-110"
            alt="Attachment">
    <?php endif; ?>

    <a href="<?= htmlspecialchars($finalPath) ?>" target="_blank"
        class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/img:opacity-100 transition-opacity flex flex-col items-center justify-center text-white gap-2 backdrop-blur-[2px]">
        <i class="fa-solid <?= $isVideo ? 'fa-expand' : 'fa-expand' ?> text-xl"></i>
        <span class="text-xs font-bold"><?= $isVideo ? 'Full View' : 'View Image' ?></span>
    </a>
</div>
        <?php endif; ?>

    </div> <div class="p-4 bg-slate-50/50 rounded-b-3xl mt-auto space-y-3">
        <div class="flex flex-col gap-1.5">
            <label class="text-[15px] font-bold text-slate-400 uppercase tracking-wider ml-1">Status</label>
            
            <?php 
          
            $isOwner = ($sug['user_id'] == $current_user_id);
            $isAdmin = ($user_role === 'Admin');

            if ($isAdmin || $isOwner): ?>
                <select
                    class="status-updater w-full bg-white border border-slate-200 rounded-xl p-2 text-medium font-semibold focus:ring-2 focus:ring-blue-500 transition-all outline-none cursor-pointer"
                    data-report-id="<?= $sug['suggestion_id'] ?>">
                    <?php foreach ($statusOptions as $status): ?>
                        <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $sug['status_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status['status_desc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <div class="w-full bg-slate-100 border border-slate-200 rounded-xl p-2 text-medium font-semibold text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-[10px] opacity-60"></i>
                    <span><?= htmlspecialchars($sug['status_desc']) ?></span>
                </div>
            <?php endif; ?>
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
            <div
                class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                <div
                    class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300 text-2xl">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <p class="text-slate-500 font-medium">No suggestions found. Be the first to suggest something!</p>
            </div>
        <?php endif; ?>
    </div>
    <footer style=" background-color: #0a2550; color: white;">
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

                    <div
                        class="flex justify-center md:justify-end gap-4 text-[11px] uppercase tracking-widest text-white-500">
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
        </div>

        <div class="space-y-1.5">
            <label class="text-[17px] font-semibold text-slate-600 ml-1">Attach Media (Optional)</label>
            <p class="text-[13px] text-slate-400 ml-1 mb-1">Upload an image or a short video clip.</p>
            
            <input type="file" name="suggestion_img" id="suggestion_img_input" accept="image/*,video/*"
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-sm outline-none transition-all">
            
            <div id="paste-preview-container" class="hidden mt-4 relative inline-block">
                <img id="paste-preview" class="max-h-40 w-auto rounded-xl border-2 border-blue-100 shadow-md object-cover" src="">
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

            <button type="submit" id="submitBtn"
    class="flex-[2] px-4 py-3 text-sm bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
    <i id="loadingIcon" class="fa-solid fa-spinner fa-spin" style="display: none;"></i>
    <span id="btnText">Submit Suggestion</span>
</button>
        </div>
 </form>
</div> <!-- projectModalContainer -->
</div> <!-- projectModal -->

<div id="tooltip"
class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-[200] px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
</div>

<div id="statusConfirmModal" class="hidden" style="position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 1rem; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
    
    <div id="statusConfirmContainer" style="position: relative; background-color: rgba(255, 255, 255, 0.95); width: 100%; max-width: 420px; padding: 2.5rem; border-radius: 2.5rem; box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.3); transform: scale(0.95); opacity: 0; transition: all 0.3s ease-out; text-align: center;">
        
        <div style="margin: 0 auto 1.25rem; display: flex; height: 70px; width: 70px; align-items: center; justify-content: center; border-radius: 1.5rem; background: linear-gradient(135deg, #fffbeb 0%, #ffedd5 100%);">
            <svg xmlns="http://www.w3.org/2000/svg" style="height: 35px; width: 35px; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>

        <h3 style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0; letter-spacing: -0.025em; line-height: 1.2;">
            Confirm Update
        </h3>

        <p style="color: #64748b; margin-top: 0.75rem; font-size: 1rem; font-weight: 500; line-height: 1.5; padding: 0 1rem;">
            Are you sure you want to update this record? This action will take effect immediately.
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 2rem;">
            <button id="confirmStatusBtn" disabled style="width: 100%; padding: 1rem; border-radius: 1.25rem; font-weight: 700; color: white; background-color: #2563eb; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); opacity: 0.6;">
                Yes, Change it
            </button>

            <button id="cancelStatusBtn" style="width: 100%; padding: 0.85rem; border-radius: 1.25rem; background-color: #f1f5f9; color: #64748b; font-weight: 700; border: none; cursor: pointer; transition: background-color 0.2s;">
                Cancel
            </button>
        </div>
    </div>
</div>


    <?php ob_end_flush(); ?>

    <script>
        const currentUserId = "<?= $current_user_id ?>";
    </script>

    <script src="js/removeNotification.js" defer></script>
    <script src="js/tooltip.js"></script>
    <script src="js/paste_image_suggestion.js"></script>
    
    <script src="js/inputValidation.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof initFormValidation === "function") {
                initFormValidation("suggestionForm");
            }
        });
    </script>

   <script src="js/suggestions.js?v=5"></script>