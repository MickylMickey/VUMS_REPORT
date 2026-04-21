<?php
require_once __DIR__ . "/../init.php";
ob_start();
$userData = checkAuth();

$current_user_id = $userData->user_id;
$user_role = $userData->role;
$isAdmin = (isset($userData->role) && $userData->role === 'Admin');
$severities = fetchAllFromTable($conn, 'severity');
$visibility = new reportArchiveVisibility($conn);

// 1. Define your filters first
if ($isAdmin) {
    $where = "ra.status_id != ?";
    $params = [0];
    $types = "i";
} else {
    $where = "ra.status_id != ? AND ra.user_id = ?";
    $params = [0, $current_user_id];
    $types = "is";
}

// 2. RUN PAGINATION FIRST to generate $limit and $offset
$pagination = getPaginationData(
    $conn,
    "report_archive ra", // Use the alias 'ra' to match your $where clause
    $_GET['limit'] ?? 50,
    $_GET['page'] ?? 1,
    $where,
    $params,
    $types
);

// 3. NOW you can extract these (this fixes the 'Undefined variable' warning)
$limit = $pagination['limit'];
$offset = $pagination['offset'];
$totalPages = $pagination['totalPages'];
$totalRecords = $pagination['totalRecords'];
$page = $pagination['page'];

// 4. FINALLY, fetch the users using those fresh variables
$reportArchive = $visibility->getVisibleArchiveReports($current_user_id, $user_role, $limit, $offset);

// --- SUGGESTION PAGINATION ---

// 1. Define filters for suggestions
$s_where = "sa.status_id != ?";
$s_params = [0];
$s_types = "i";

// 2. RUN PAGINATION (Using 's_page' and 's_limit' to keep it separate)
$s_pagination = getPaginationData(
    $conn,
    "suggestion_archive sa",
    $_GET['s_limit'] ?? 12,
    $_GET['s_page'] ?? 1,
    $s_where,
    $s_params,
    $s_types
);

// 3. Extract Suggestion Variables
$s_limit = $s_pagination['limit'];
$s_offset = $s_pagination['offset'];
$s_totalPages = $s_pagination['totalPages'];
$s_totalRecords = $s_pagination['totalRecords'];
$s_page = $s_pagination['page'];

// 4. Fetch the actual suggestions using LIMIT and OFFSET
$sql = "SELECT sa.*, st.status_desc, updater.username AS updater_name, UPPER(u.username) AS username 
        FROM suggestion_archive sa
        LEFT JOIN status st ON sa.status_id = st.status_id
        LEFT JOIN users updater ON sa.suggestion_updated_by = updater.user_id
        LEFT JOIN users u ON sa.user_id = u.user_id
        WHERE $s_where
        ORDER BY sa.suggestion_updated_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $s_params[0], $s_limit, $s_offset);
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
    <title>Archive Management</title>
</head>

<body class="bg-slate-50 pt-24 text-slate-800 min-h-screen flex flex-col">
    <?php include "templates/navbar.php"; ?>

    <main class="container mx-auto px-4 pb-20 max-w-7xl flex-grow">
        <div class="mt-12 mb-10">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Archive Repository</h1>
            <p class="text-slate-500 mt-2">View and manage all historical reports and system suggestions.</p>
        </div>

        <section class="mb-16">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-box-archive" id="card1" onclick="handleCardClick('card1')"></i>
                    </div>
                    <h2 class="text-xl font-bold">System Reports Archive</h2>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <div class="relative flex-grow md:flex-grow-0 md:min-w-[300px]">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" placeholder="Search by Reporter or Description . . ."
                            data-tooltip="Type to search reports and descriptions"
                            class="w-full pl-11 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    </div>

                    <select id="severityFilter" data-tooltip="Filter reports by severity"
                        class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-600 outline-none focus:border-blue-500 transition-all cursor-pointer h-[40px]">
                        <option value="">All Severities</option>
                        <?php foreach ($severities as $severity): ?>
                            <option value="<?= htmlspecialchars($severity['sev_id']) ?>">
                                <?= htmlspecialchars($severity['sev_desc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button id="resetBtn"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl transition-all h-[40px] flex items-center justify-center"
                        title="Reset Filters">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-blue-500 border-b border-blue-600">
                                <th class="px-6 py-4 text-[13px] font-bold text-white uppercase tracking-widest">
                                    Ref Number
                                </th>
                                <th class="px-6 py-4 text-[13px] font-bold text-white uppercase tracking-widest">
                                    Reporter
                                </th>
                                <th class="px-6 py-4 text-[13px] font-bold text-white uppercase tracking-widest">
                                    Classification
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-[13px] font-bold text-white uppercase tracking-wider">
                                    Description
                                </th>
                                <th
                                    class="px-6 py-4 text-[13px] font-bold text-white uppercase tracking-widest text-center">
                                    Severity
                                </th>
                                <th class="px-6 py-4 text-[13px] font-bold text-white uppercase tracking-widest">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-[13px] font-bold text-white uppercase tracking-widest">
                                    Archived On
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($reportArchive as $archive): ?>
                                <tr class="report-row hover:bg-blue-50/30 transition-colors group"
                                    data-ref="<?= htmlspecialchars($archive['ref_num']) ?>"
                                    data-desc="<?= htmlspecialchars($archive['report_desc']) ?>"
                                    data-reporter="<?= htmlspecialchars($archive['reporter_name']) ?>"
                                    data-severity="<?= htmlspecialchars($archive['sev_id']) ?>">
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-medium uppercase">
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
                                    <td class="px-6 py-4">
                                        <p class="text-medium text-slate-500 max-w-[200px] truncate"
                                            title="<?= htmlspecialchars($archive['report_desc']) ?>">
                                            <?= htmlspecialchars($archive['report_desc']) ?>
                                        </p>
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
                                            class="text-m font-medium text-slate-500 italic"><?= htmlspecialchars($archive['status_desc']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400">
                                        <?= date('M d, Y', strtotime($archive['report_updated_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($reportArchive as $archive): ?>
                            <?php endforeach; ?>

                            <tr id="noResultsRow" class="hidden">
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-3xl mb-2 block opacity-20"></i>
                                    No archived reports match your search criteria.
                                </td>
                            </tr>
                        </tbody>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <p class="text-sm text-slate-500">
                        Showing <span class="font-medium text-slate-700">
                            <?= $offset + 1 ?>
                        </span>
                        to <span class="font-medium text-slate-700">
                            <?= min($offset + $limit, $totalRecords) ?>
                        </span>
                        of <span class="font-medium text-slate-700">
                            <?= $totalRecords ?>
                        </span> Reports
                    </p>

                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>"
                                class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Previous
                            </a>
                        <?php endif; ?>

                        <div class="hidden sm:flex gap-1">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>&limit=<?= $limit ?>"
                                    class="px-3 py-2 text-sm font-medium rounded-lg border transition-all <?= $i == $page ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>"
                                class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
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

                <div class="flex gap-2">
                    <div class="relative min-w-[280px]">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="sugSearchInput" name="sug_q" placeholder="Search users or suggestion..."
                            data-tooltip="Search by username or suggestion"
                            class="w-full pl-11 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                    </div>
                    <button id="resetSugBtn" data-tooltip="Reset suggestion search"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl transition-all">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <div id="suggestionsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($suggestions)):
                    foreach ($suggestions as $sug): ?>
                        <div class="suggestion-card bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all"
                            data-user="<?= htmlspecialchars(strtolower($sug['username'])) ?>"
                            data-text="<?= htmlspecialchars(strtolower($sug['suggestion_desc'])) ?>">

                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[15px] font-bold text-slate-400 uppercase tracking-widest">
                                    <?= date('M d, Y', strtotime($sug['suggestion_created_at'])) ?>
                                </span>
                                <?php
                                $sColor = (stripos($sug['status_desc'], 'Completed') !== false) ? 'text-green-600 bg-green-50' : 'text-slate-400 bg-slate-50';
                                ?>
                                <span class="px-2 py-0.5 rounded text-[15px] font-bold <?= $sColor ?>">
                                    <?= $sug['status_desc'] ?>
                                </span>
                            </div>

                            <p class="text-slate-600 text-sm italic mb-4 line-clamp-3">
                                "<?= htmlspecialchars($sug['suggestion_desc']) ?>"
                            </p>

                            <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold uppercase text-slate-500">
                                        <?= substr($sug['username'], 0, 1) ?>
                                    </div>
                                    <span class="text-[13px] font-bold text-slate-700">
                                        <?= htmlspecialchars($sug['username']) ?>
                                    </span>
                                </div>
                                <span class="text-[15px] text-slate-300">
                                    Archived by: <?= htmlspecialchars($sug['updater_name'] ?? 'System') ?>
                                </span>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
                <div id="noSugResults"
                    class="hidden col-span-full py-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400">
                    No suggestions match your search.
                </div>

            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Showing <span class="font-medium text-slate-700">
                        <?= $s_offset + 1 ?>
                    </span>
                    to <span class="font-medium text-slate-700">
                        <?= min($s_offset + $s_limit, $s_totalRecords) ?>
                    </span>
                    of <span class="font-medium text-slate-700">
                        <?= $s_totalRecords ?>
                    </span> Suggestions
                </p>

                <div class="flex gap-2">
                    <?php if ($s_page > 1): ?>
                        <a href="?s_page=<?= $s_page - 1 ?>&s_limit=<?= $s_limit ?>"
                            class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                            Previous
                        </a>
                    <?php endif; ?>

                    <div class="hidden sm:flex gap-1">
                        <?php for ($i = 1; $i <= $s_totalPages; $i++): ?>
                            <a href="?s_page=<?= $i ?>&s_limit=<?= $s_limit ?>"
                                class="px-3 py-2 text-sm font-medium rounded-lg border transition-all <?= $i == $s_page ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>

                    <?php if ($s_page < $s_totalPages): ?>
                        <a href="?s_page=<?= $s_page ?>&s_page=<?= $s_page + 1 ?>&limit=<?= $s_limit ?>"
                            class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <div id="tooltip"
        class="fixed pointer-events-none opacity-0 transition-all duration-150 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
    </div>
    <div class="mt-auto">
        <?php include "templates/footer.php"; ?>
    </div>
</body>
<script src="js/archive_module.js"></script>
<script src="js/tooltip.js"></script>


</html>