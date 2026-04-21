<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth('HR');
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
    <title>HR Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom scrollbar for the critical alerts list */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #e2e8f0;
        }
    </style>
</head>

<body class="bg-slate-50 pt-24 min-h-screen">
    <?php include "templates/navbar.php"; ?>

    <main class="max-w-[1600px] mx-auto p-4 lg:p-8">

        <!-- HEADER BENTO SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            <!-- Welcome Banner -->
            <div
                class="lg:col-span-8 bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-xl shadow-indigo-100">
                <div class="relative z-10">
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-3">
                        Welcome back, <?= htmlspecialchars(ucfirst($username)) ?>!
                    </h1>
                    <p class="text-indigo-100 text-lg md:text-xl opacity-90 max-w-xl leading-relaxed">
                        Manage your reports and community suggestions in one place. Here is your overview for today.
                    </p>
                </div>
                <!-- Decorative Icon -->
                <i
                    class="fa-solid fa-rocket absolute -right-10 -bottom-10 text-[18rem] text-white/10 -rotate-12 pointer-events-none"></i>
            </div>

            <!-- Date Card -->
            <div
                class="lg:col-span-4 bg-white border border-slate-200 rounded-[2.5rem] p-8 flex flex-col justify-center items-center text-center shadow-sm">
                <p class="text-slate-400 uppercase tracking-[0.2em] text-xs font-bold mb-2"><?= date('l') ?></p>
                <h2 class="text-4xl font-black text-slate-800"><?= date('M d, Y') ?></h2>
                <div
                    class="mt-6 px-5 py-2.5 bg-emerald-50 rounded-full text-xs font-black text-emerald-600 uppercase tracking-widest flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    System Active
                </div>
            </div>
        </div>

        <!-- MAIN GRID AREA -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 items-start">

            <!-- COLUMN 1: TOTALS & PRIORITY -->
            <div class="lg:col-span-3 flex flex-col gap-6">
                <!-- Total Reports Card -->
                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Total Reports</p>
                    <div id="overall-total" class="text-7xl font-black italic">0</div>
                    <div
                        class="mt-4 flex items-center text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                        <i class="fa-solid fa-arrow-trend-up mr-2"></i> Live Tracking
                    </div>
                </div>

                <!-- Priority Breakdown Card -->
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
                    <h3
                        class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 border-b border-slate-50 pb-4">
                        Priority Breakdown</h3>
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-slate-600 font-bold text-sm">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span> Critical
                            </span>
                            <span id="stat-critical" class="font-black text-slate-900 text-xl">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-slate-600 font-bold text-sm">
                                <span class="w-3 h-3 rounded-full bg-orange-500"></span> High
                            </span>
                            <span id="stat-high" class="font-black text-slate-900 text-xl">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-slate-600 font-bold text-sm">
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span> Medium
                            </span>
                            <span id="stat-medium" class="font-black text-slate-900 text-xl">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-slate-600 font-bold text-sm">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Low
                            </span>
                            <span id="stat-low" class="font-black text-slate-900 text-xl">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: SUGGESTIONS & TOP REPORTERS -->
            <div class="lg:col-span-6 flex flex-col gap-6">
                <!-- Recent Suggestions Section -->
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Recent Suggestions</h2>
                        <a href="suggestions.php"
                            class="bg-slate-100 hover:bg-slate-200 p-2 px-5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">View
                            All</a>
                    </div>

                    <div class="grid gap-4">
                        <?php foreach ($suggestions as $sug):
                            $statusColor = match ((int) $sug['status_id']) {
                                1 => 'bg-amber-100 text-amber-700',
                                2 => 'bg-blue-100 text-blue-700',
                                3 => 'bg-emerald-100 text-emerald-700',
                                default => 'bg-slate-100 text-slate-600'
                            };
                            ?>
                            <div
                                class="p-5 rounded-3xl bg-slate-50/50 border border-slate-100 flex gap-5 items-center hover:bg-white hover:shadow-md hover:border-transparent transition-all duration-300">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-sm">
                                    <?php if (!empty($sug['reporter_profile_pic'])): ?>
                                        <img src="img/prof_pic/<?= htmlspecialchars($sug['reporter_profile_pic']) ?>"
                                            class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span
                                            class="font-black text-indigo-400 text-lg"><?= substr($sug['username'], 0, 1) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-black text-slate-800 text-sm truncate">
                                            <?= htmlspecialchars($sug['username']) ?>
                                        </h4>
                                        <span
                                            class="text-[9px] px-2 py-1 rounded-lg font-black uppercase tracking-tighter <?= $statusColor ?>">
                                            <?= htmlspecialchars($sug['status_desc']) ?>
                                        </span>
                                    </div>
                                    <p class="text-slate-500 text-xs line-clamp-2 italic leading-relaxed">
                                        "<?= htmlspecialchars($sug['suggestion_desc']) ?>"</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Top Reporters Section -->
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
                    <h2 class="text-xl font-black text-slate-800 mb-6 tracking-tight">Top Reporters</h2>
                    <div class="overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="text-slate-400 text-[10px] uppercase tracking-[0.2em] border-b border-slate-100">
                                    <th class="pb-4 font-black">Reporter Name</th>
                                    <th class="pb-4 text-right font-black">Report Count</th>
                                </tr>
                            </thead>
                            <tbody id="reporter-list" class="divide-y divide-slate-50">
                                <!-- user_chat.js populates this -->
                                <tr>
                                    <td colspan="2" class="py-10 text-center text-slate-400 text-xs italic">Loading
                                        database records...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- COLUMN 3: CHART & URGENT LIST -->
            <div class="lg:col-span-3 flex flex-col gap-6">
                <!-- Status Distribution (Chart) -->
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8 text-center">Status
                        Distribution</h3>
                    <div class="h-44 relative">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Small Workflow Counters -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 text-center shadow-sm">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Pending</p>
                        <p id="stat-pending" class="text-3xl font-black text-slate-800">0</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 text-center shadow-sm">
                        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Resolved</p>
                        <p id="stat-resolved" class="text-3xl font-black text-slate-800">0</p>
                    </div>
                </div>

                <!-- Critical Alerts (Urgent Alerts) -->
                <div class="bg-white border border-rose-100 rounded-[2.5rem] p-6 shadow-sm shadow-rose-50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xs font-black text-rose-600 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> Urgent Alerts
                        </h3>
                        <span class="flex h-2 w-2 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                    </div>
                    <!-- user_chat.js populates this container -->
                    <div id="critical-list" class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        <p class="text-xs text-slate-400 text-center py-4 italic">Scanning for critical issues...</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div class="mt-12">
        <?php include "templates/footer.php"; ?>
    </div>

    <!-- External Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.3.2/dist/countUp.umd.js"></script>
    <script src="js/user_chart.js" defer></script>
</body>

</html>