<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth('Admin');
$current_user_id = $userData->user_id;
$user_role = $userData->role;

// dashboard_stats.php

// 1. Get the main 4 metrics
$statsQuery = "SELECT 
    COUNT(CASE WHEN sev_id = 1 AND status_id != 3 THEN 1 END) AS critical_count,
    COUNT(CASE WHEN status_id IN (1, 2) THEN 1 END) AS active_count,
    COUNT(CASE WHEN status_id = 1 THEN 1 END) AS pending_count,
    COUNT(CASE WHEN status_id = 3 AND DATE(report_updated_at) = CURDATE() THEN 1 END) AS resolved_today
FROM report";

$result = $conn->query($statsQuery);
$data = $result->fetch_assoc();

// 2. Get 'Trend' (Reports created in last 24 hours)
$trendQuery = "SELECT COUNT(*) as recent FROM report WHERE report_created_at >= NOW() - INTERVAL 1 DAY";
$trendResult = $conn->query($trendQuery);
$trendData = $trendResult->fetch_assoc();

// Variables for your HTML
$critical = $data['critical_count'] ?? 0;
$active = $data['active_count'] ?? 0;
$pending = $data['pending_count'] ?? 0;
$resolved = $data['resolved_today'] ?? 0;
$newToday = $trendData['recent'] ?? 0;
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
    <div>
        <?php include "templates/navbar.php"; ?>
    </div>

    <main class="flex-grow p-20">
        <div class="max-w-4xl mx-auto mt-4 px-8">
            <?= showValidation() ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">+2 since
                        yesterday</span>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?= $critical ?></h3>
                    <p class="text-sm font-medium text-slate-500">Critical Alerts</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                        <i class="fa-solid fa-envelope-open-text text-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?= $active ?></h3>
                    <p class="text-sm font-medium text-slate-500">Active Reports</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                        <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?= $pending ?></h3>
                    <p class="text-sm font-medium text-slate-500">Pending Triage</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <i class="fa-solid fa-check-double text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">84% Rate</span>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?= $resolved ?></h3>
                    <p class="text-sm font-medium text-slate-500">Resolved Today</p>
                </div>
            </div>

        </div>
    </main>

    <div class="mt-auto">
        <?php include "templates/footer.php"; ?>
    </div>

    <?php ob_end_flush(); ?>
</body>

</html>