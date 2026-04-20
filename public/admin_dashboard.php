<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth('Admin');
$current_user_id = $userData->user_id;
$user_role = $userData->role;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Admin Dashboard</title>
</head>

<body class="bg-[#f8fafc] min-h-screen flex flex-col antialiased text-slate-900 pt-24">

    <?php include "templates/navbar.php"; ?>
    <main class="flex-grow p-6 md:p-8 lg:p-10 space-y-6 max-w-[1600px] mx-auto w-full bg-slate-50">

        <div id="validationBlock" class="fixed bottom-28 right-5 z-[250] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
        <?php
        $username = ucfirst($userData->username ?? 'User');
        ?>

        <div class="bg-blue-600 text-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-black">
                    Hello, <?= htmlspecialchars($username) ?>
                </h2>
                <p class="text-sm text-blue-100 mt-1">
                    Welcome back, <?= htmlspecialchars($username) ?>. Here's what's happening today.
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

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

            <div
                class="xl:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex flex-col border-r border-slate-100 last:border-0 px-2">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-red-50 flex items-center justify-center">
                            <i class="fa-solid fa-fire text-[10px] text-red-600"></i>
                        </div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Critical</p>
                    </div>
                    <h3 id="stat-critical" class="text-2xl font-black text-red-600">0</h3>
                </div>
                <div class="flex flex-col border-r border-slate-100 last:border-0 px-2">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-orange-50 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-[10px] text-orange-500"></i>
                        </div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">High</p>
                    </div>
                    <h3 id="stat-high" class="text-2xl font-black text-orange-500">0</h3>
                </div>
                <div class="flex flex-col border-r border-slate-100 last:border-0 px-2">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-yellow-50 flex items-center justify-center">
                            <i class="fa-solid fa-circle-exclamation text-[10px] text-yellow-600"></i>
                        </div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Medium</p>
                    </div>
                    <h3 id="stat-medium" class="text-2xl font-black text-yellow-500">0</h3>
                </div>
                <div class="flex flex-col px-2">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-green-50 flex items-center justify-center">
                            <i class="fa-solid fa-shield-check text-[10px] text-green-600"></i>
                        </div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Low</p>
                    </div>
                    <h3 id="stat-low" class="text-2xl font-black text-green-500">0</h3>
                </div>
            </div>

            <div
                class="xl:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-blue-600 p-5 rounded-2xl shadow-lg shadow-blue-200">
                <div class="flex flex-col border-r border-blue-500/50 last:border-0 px-2">
                    <p
                        class="text-[10px] uppercase tracking-wider text-blue-100 font-bold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-bolt-lightning text-blue-200 opacity-70"></i> Active
                    </p>
                    <h3 id="stat-active" class="text-2xl font-black text-white">0</h3>
                </div>
                <div class="flex flex-col border-r border-blue-500/50 last:border-0 px-2">
                    <p
                        class="text-[10px] uppercase tracking-wider text-blue-100 font-bold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-200 opacity-70"></i> Pending
                    </p>
                    <h3 id="stat-pending" class="text-2xl font-black text-white">0</h3>
                </div>
                <div class="flex flex-col border-r border-blue-500/50 last:border-0 px-2">
                    <p
                        class="text-[10px] uppercase tracking-wider text-blue-100 font-bold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin-pulse text-blue-200 opacity-70"></i> In-Progress
                    </p>
                    <h3 id="stat-in-progress" class="text-2xl font-black text-white">0</h3>
                </div>
                <div class="flex flex-col px-2">
                    <p
                        class="text-[10px] uppercase tracking-wider text-blue-100 font-bold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-blue-200 opacity-70"></i> Resolved Today
                    </p>
                    <h3 id="stat-resolved" class="text-2xl font-black text-white">0</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-blue-500"></i> Monthly Report Trends
                        </h3>
                        <div class="flex space-x-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-xs text-slate-500 uppercase font-bold tracking-tighter">Volume</span>
                        </div>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-indigo-500"></i> Category Distribution
                        </h3>
                        <div class="h-[250px]">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-emerald-500"></i> Queue Status
                        </h3>
                        <div class="h-[250px]">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 h-[760px]">
                <div
                    class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col h-full overflow-hidden">
                    <div class="flex items-center justify-between mb-6 flex-none">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 rounded-xl">
                                <i class="fa-solid fa-cubes text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Module Distribution</h3>
                                <p class="text-xs text-slate-400">System performance per sector</p>
                            </div>
                        </div>
                        <div class="bg-blue-50 px-3 py-1 rounded-full">
                            <span id="overall-total" class="text-lg font-black text-blue-600">0</span>
                        </div>
                    </div>

                    <div id="module-container" class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                        <div class="space-y-4">
                            <div
                                class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-server text-slate-400"></i>
                                    <span class="text-sm font-semibold text-slate-700">Network Node Alpha</span>
                                </div>
                                <span class="text-xs font-bold text-blue-500">98%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-1">
                <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-medal text-amber-500"></i> Top Personnel
                </h3>
                <div class="overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="pb-3 text-left font-bold">User</th>
                                <th class="pb-3 text-right font-bold">Reports</th>
                            </tr>
                        </thead>
                        <tbody id="reporter-list" class="divide-y divide-slate-50">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-tower-broadcast text-red-500"></i> Critical Reports
                    </h3>
                    <span
                        class="flex items-center gap-2 text-[10px] bg-red-50 text-red-600 px-3 py-1 rounded-full font-bold animate-pulse">
                        <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span> LIVE FEED
                    </span>
                </div>
                <div id="critical-list" class="space-y-3">
                </div>
            </div>
        </div>
    </main>

    <?php include "templates/footer.php"; ?>
    <?php ob_end_flush(); ?>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.min.js"></script>
    <script src="js/charts.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Dashboard initialized");
            initDashboard();
        });
    </script>

</body>
<style>
    /* Custom thin scrollbar for the module list */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
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
</style>

</html>