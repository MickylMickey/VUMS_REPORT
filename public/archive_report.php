<?php
require_once __DIR__ . "/../init.php";
ob_start();
$userData = checkAuth();

$current_user_id = $userData->user_id;
$user_role = $userData->role;
$isAdmin = (isset($userData->role) && $userData->role === 'Admin');


$reportSearch = $_GET['report_q'] ?? '';
$sugSearch = $_GET['sug_q'] ?? '';

$visibility = new reportArchiveVisibility($conn);
$reportArchive = $visibility->getVisibleArchiveReports($current_user_id, $user_role);

$sql = "SELECT sa.*, st.status_desc, updater.username AS updater_name, UPPER(u.username) AS username 
        FROM suggestion_archive sa
        LEFT JOIN status st ON sa.status_id = st.status_id
        LEFT JOIN users updater ON sa.suggestion_updated_by = updater.user_id
        LEFT JOIN users u ON sa.user_id = u.user_id
        $whereClause
        ORDER BY sa.suggestion_updated_at DESC";

$suggestions = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Archive Management</title>
</head>

<body class="bg-slate-50 pt-24 text-slate-800">
    <?php include "templates/navbar.php"; ?>

    <main class="container mx-auto px-4 pb-20 max-w-7xl">
        <div class="mt-12 mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Archive Repository</h1>
            <p class="text-slate-500 mt-2">View and manage all historical reports and system suggestions.</p>
        </div>

        <section class="mb-16">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <h2 class="text-xl font-bold">System Reports Archive</h2>
                </div>

                <form class="flex flex-wrap gap-2">
                    <div class="relative min-w-[280px]">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="report_q" placeholder="Search reference or description..."
                            value="<?= htmlspecialchars($reportSearch) ?>"
                            class="w-full pl-11 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <select
                        class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:border-blue-500">
                        <option value="">All Status</option>
                    </select>
                    <button type="submit"
                        class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-700 transition-all">Filter</button>
                </form>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Ref
                                    Number</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                    Reporter</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                    Classification</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                    Severity</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                    Status</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                    Archived On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($reportArchive as $archive): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs uppercase">
                                            <?= htmlspecialchars($archive['ref_num']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-sm text-slate-700">
                                        <?= htmlspecialchars($archive['reporter_name']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-slate-400 font-bold uppercase">
                                            <?= htmlspecialchars($archive['cat_desc'] ?? 'Other') ?>
                                        </div>
                                        <div class="text-sm text-slate-600">
                                            <?= htmlspecialchars($archive['mod_desc'] ?? 'Other') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php $sevClass = (stripos($archive['severity'], 'Critical') !== false) ? 'bg-red-50 text-red-600 border-red-100' : 'bg-amber-50 text-amber-600 border-amber-100'; ?>
                                        <span
                                            class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $sevClass ?>">
                                            <?= strtoupper($archive['severity']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-xs font-medium text-slate-500 italic"><?= htmlspecialchars($archive['status_desc']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400">
                                        <?= date('M d, Y', strtotime($archive['report_updated_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h2 class="text-xl font-bold">Suggestions Archive</h2>
                </div>

                <form class="flex gap-2">
                    <div class="relative min-w-[280px]">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="sug_q" placeholder="Search users or text..."
                            value="<?= htmlspecialchars($sugSearch) ?>"
                            class="w-full pl-11 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                    </div>
                    <button type="submit"
                        class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-700 transition-all">Search</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($suggestions)):
                    foreach ($suggestions as $sug): ?>
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= date('M d, Y', strtotime($sug['suggestion_created_at'])) ?></span>
                                <?php
                                $sColor = (stripos($sug['status_desc'], 'Completed') !== false) ? 'text-green-600 bg-green-50' : 'text-slate-400 bg-slate-50';
                                ?>
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold <?= $sColor ?>"><?= $sug['status_desc'] ?></span>
                            </div>
                            <p class="text-slate-600 text-sm italic mb-4 line-clamp-3">
                                "<?= htmlspecialchars($sug['suggestion_desc']) ?>"</p>
                            <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold uppercase text-slate-500">
                                        <?= substr($sug['username'], 0, 1) ?>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-slate-700"><?= htmlspecialchars($sug['username']) ?></span>
                                </div>
                                <span class="text-[10px] text-slate-300">Archived by:
                                    <?= htmlspecialchars($sug['updater_name'] ?? 'System') ?></span>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                    <div
                        class="col-span-full py-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400">
                        No archived suggestions found.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>

</html>