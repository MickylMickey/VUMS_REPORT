<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth('User');
$current_user_id = $userData->user_id;
$user_role = $userData->role;
$username = $userData->username;

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
        ORDER BY us.suggestion_created_at DESC
        LIMIT 3";

$stmt = $conn->prepare($sql);
$stmt->execute();
$suggestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>

<body class="bg-slate-50 pt-24 min-h-screen">
    <?php include "templates/navbar.php"; ?>

    <main class="max-w-[1600px] mx-auto p-4 lg:p-8">

        <div class="bg-blue-600 text-white p-6 rounded-2xl shadow-lg flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-black">
                    Hello, <?= htmlspecialchars(ucfirst($username)) ?>
                </h2>
                <p class="text-sm text-blue-100 mt-1">
                    Welcome back, <?= htmlspecialchars(ucfirst($username)) ?>. Here's what's happening today.
                </p>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-blue-200 uppercase tracking-widest">Today</p>
                    <p class="font-bold">
                        <?= date('F d, Y') ?>
                    </p>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-3 space-y-6">
                <div
                    class="bg-indigo-600 p-6 rounded-3xl shadow-xl shadow-indigo-100 text-white relative overflow-hidden group">
                    <i class="fa-solid fa-file-invoice absolute -right-4 -bottom-4 text-8xl opacity-10"></i>
                    <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest mb-1">Total Reports</p>
                    <div id="overall-total" class="text-5xl font-black italic">0</div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Priority Breakdown</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2 text-slate-600 font-semibold text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div> Critical
                            </span>
                            <span id="stat-critical" class="font-black text-slate-900">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2 text-slate-600 font-semibold text-sm">
                                <div class="w-2 h-2 rounded-full bg-orange-400"></div> High
                            </span>
                            <span id="stat-high" class="font-black text-slate-900">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2 text-slate-600 font-semibold text-sm">
                                <div class="w-2 h-2 rounded-full bg-yellow-400"></div> Medium
                            </span>
                            <span id="stat-medium" class="font-black text-slate-900">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2 text-slate-600 font-semibold text-sm">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div> Low
                            </span>
                            <span id="stat-low" class="font-black text-slate-900">0</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Workflow Status</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Pending</p>
                            <p id="stat-pending" class="text-xl font-black text-slate-800">0</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">In-Progress</p>
                            <p id="stat-in-progress" class="text-xl font-black text-slate-800">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-xl font-bold text-slate-800">Community Suggestions</h2>
                    <a href="suggestions.php" class="text-indigo-600 text-sm font-bold hover:underline">View all</a>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($suggestions as $sug):
                        $statusColor = match ((int) $sug['status_id']) {
                            1 => 'bg-amber-100 text-amber-700',
                            2 => 'bg-blue-100 text-blue-700',
                            3 => 'bg-emerald-100 text-emerald-700',
                            default => 'bg-slate-100 text-slate-600'
                        };
                        ?>
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex gap-5">
                            <div class="hidden sm:block">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-100 flex-shrink-0 overflow-hidden border-2 border-white shadow-sm">
                                    <?php if (!empty($sug['reporter_profile_pic'])): ?>
                                        <img src="img/prof_pic/<?= htmlspecialchars($sug['reporter_profile_pic']) ?>"
                                            class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center font-bold text-slate-400">
                                            <?= substr($sug['username'], 0, 1) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-slate-800"><?= htmlspecialchars($sug['username']) ?></h4>
                                        <p class="text-[11px] text-slate-400 font-medium">
                                            <?= date('M d, Y', strtotime($sug['suggestion_created_at'])) ?>
                                        </p>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter <?= $statusColor ?>">
                                        <?= htmlspecialchars($sug['status_desc']) ?>
                                    </span>
                                </div>

                                <p class="text-slate-600 text-sm italic mb-4 break-words line-clamp-3 leading-relaxed">
                                    "<?= htmlspecialchars($sug['suggestion_desc']) ?>"
                                </p>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest mb-6">Status Distribution</h3>
                    <div class="h-48">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <div class="bg-rose-50 border border-rose-100 p-6 rounded-3xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-rose-700 uppercase tracking-wider">Critical Action</h3>
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                    </div>

                    <div id="critical-list" class="space-y-3 h-[270px] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="bg-white/80 p-4 rounded-2xl border border-rose-100/50 shadow-sm">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-slate-800 text-sm">System Outage</span>
                                <span
                                    class="text-[10px] bg-rose-500 text-white px-1.5 py-0.5 rounded font-black">URGENT</span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2">The main API gateway is reporting 500 errors
                                across all regions...</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
<style>
    /* Custom thin scrollbar to keep the Bento aesthetic */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fecdd3;
        /* rose-200 */
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #fda4af;
        /* rose-300 */
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.3.2/dist/countUp.umd.js"></script>
<script src="js/user_chart.js"></script>

</html>