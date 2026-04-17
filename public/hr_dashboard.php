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
    <title>User Dashboard</title>
</head>

<body class="bg-[#f8fafc] pt-24 min-h-screen antialiased text-slate-900">
    <?php include "templates/navbar.php"; ?>

    <main class="max-w-[1600px] mx-auto p-4 lg:p-8">

        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-5 auto-rows-auto">

            <div
                class="md:col-span-4 lg:col-span-9 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[2rem] shadow-xl shadow-blue-100 flex items-center justify-between overflow-hidden relative group">
                <div class="relative z-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                        Hello, <?= htmlspecialchars(ucfirst($username)) ?>
                    </h2>
                    <p class="text-blue-100 mt-2 text-lg opacity-90">
                        Here's your dashboard overview for <span
                            class="font-semibold text-white"><?= date('F d, Y') ?></span>
                    </p>
                </div>
                <i
                    class="fa-solid fa- rocket absolute right-[-20px] top-[-20px] text-[12rem] text-white/10 -rotate-12 group-hover:rotate-0 transition-transform duration-700"></i>
            </div>

            <div
                class="md:col-span-2 lg:col-span-3 bg-indigo-50 border border-indigo-100 p-8 rounded-[2rem] flex flex-col justify-between relative overflow-hidden">
                <div>
                    <p class="text-indigo-600 text-xs font-black uppercase tracking-[0.2em] mb-1">Your Total Reports</p>
                    <div id="overall-total" class="text-6xl font-black text-indigo-900 leading-none">0</div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-indigo-500 font-bold text-sm">
                    <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
                    Live Data
                </div>
            </div>

            <div class="md:col-span-2 lg:col-span-3 bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Priority Breakdown</h3>
                <div class="space-y-5">
                    <?php
                    $priorities = [
                        ['label' => 'Critical', 'id' => 'stat-critical', 'color' => 'bg-rose-500'],
                        ['label' => 'High', 'id' => 'stat-high', 'color' => 'bg-orange-400'],
                        ['label' => 'Medium', 'id' => 'stat-medium', 'color' => 'bg-amber-400'],
                        ['label' => 'Low', 'id' => 'stat-low', 'color' => 'bg-emerald-400'],
                    ];
                    foreach ($priorities as $p): ?>
                        <div class="flex justify-between items-center group">
                            <span class="flex items-center gap-3 text-slate-600 font-bold text-sm">
                                <div class="w-2.5 h-2.5 rounded-full <?= $p['color'] ?> ring-4 ring-slate-50"></div>
                                <?= $p['label'] ?>
                            </span>
                            <span id="<?= $p['id'] ?>"
                                class="font-black text-slate-900 bg-slate-50 px-3 py-1 rounded-lg group-hover:bg-slate-100 transition-colors">0</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="md:col-span-4 lg:col-span-6 bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">Community Suggestions</h2>
                    <a href="suggestions.php"
                        class="bg-slate-50 hover:bg-slate-100 text-indigo-600 px-4 py-2 rounded-xl text-xs font-black transition-all">VIEW
                        ALL</a>
                </div>

                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                    <?php foreach ($suggestions as $sug):
                        $statusColor = match ((int) $sug['status_id']) {
                            1 => 'bg-amber-50 text-amber-600 border-amber-100',
                            2 => 'bg-blue-50 text-blue-600 border-blue-100',
                            3 => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            default => 'bg-slate-50 text-slate-600 border-slate-100'
                        };
                        ?>
                        <div
                            class="bg-slate-50/50 p-5 rounded-2xl border border-transparent hover:border-slate-200 hover:bg-white transition-all duration-300">
                            <div class="flex gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-indigo-600 overflow-hidden shadow-sm">
                                    <?php if (!empty($sug['reporter_profile_pic'])): ?>
                                        <img src="img/prof_pic/<?= htmlspecialchars($sug['reporter_profile_pic']) ?>"
                                            class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= substr($sug['username'], 0, 1) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start">
                                        <span
                                            class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($sug['username']) ?></span>
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[9px] font-black border <?= $statusColor ?>">
                                            <?= htmlspecialchars($sug['status_desc']) ?>
                                        </span>
                                    </div>
                                    <p class="text-slate-500 text-xs mt-1 italic line-clamp-2 leading-relaxed">
                                        "<?= htmlspecialchars($sug['suggestion_desc']) ?>"
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div
                class="md:col-span-2 lg:col-span-3 bg-rose-50 border border-rose-100 p-8 rounded-[2rem] flex flex-col h-full">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-rose-700 uppercase tracking-widest">Critical Action</h3>
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                </div>

                <div id="critical-list" class="space-y-3 overflow-y-auto pr-1 custom-scrollbar">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-rose-100">
                        <p class="font-bold text-rose-900 text-sm">System Outage</p>
                        <p class="text-xs text-rose-600/80 mt-1 line-clamp-2">API gateway reporting 500 errors...</p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 lg:col-span-3 bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6">Status Distribution</h3>
                <div class="h-40">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div
                class="md:col-span-2 lg:col-span-3 bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col justify-center">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Workflow Status</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 p-5 rounded-[1.5rem] border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Pending</p>
                        <p id="stat-pending" class="text-2xl font-black text-slate-800">0</p>
                    </div>
                    <div class="bg-indigo-50 p-5 rounded-[1.5rem] border border-indigo-100">
                        <p class="text-[10px] font-black text-indigo-400 uppercase mb-1">In-Progress</p>
                        <p id="stat-in-progress" class="text-2xl font-black text-indigo-700">0</p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 lg:col-span-3 bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-medal text-amber-500"></i> Top Reporter
                </h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            <th class="pb-3 text-left">User</th>
                            <th class="pb-3 text-right">Reports</th>
                        </tr>
                    </thead>
                    <tbody id="reporter-list" class="divide-y divide-slate-50">
                    </tbody>
                </table>
            </div>

            <div
                class="md:col-span-2 lg:col-span-3 bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col justify-between">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">User Count</h3>
                <div class="mt-4">
                    <div id="totalUsers" class="text-4xl font-black text-slate-900">--</div>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase">Registered Users</p>
                </div>
            </div>

        </div>
    </main>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        /* Ensures cards stay consistent height in the same row if needed */
        .grid-flow-row-dense {
            grid-auto-flow: row dense;
        }
    </style>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.3.2/dist/countUp.umd.js"></script>
<script src="js/user_chart.js"></script>

</html>