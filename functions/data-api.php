<?php

require_once __DIR__ . "/../init.php";

// 1. Clear any previous accidental output and set header
ob_clean();
header('Content-Type: application/json');

$response = [
    'status' => 'error', // Default to error
    'data' => [
        'categories' => [],
        'trends' => []
    ]
];

try {
    // 2. Get Totals by Category
    $catQuery = "SELECT c.category, COUNT(v.report_id) as total 
                 FROM category c 
                 LEFT JOIN v_dashboard_reports v ON c.cat_id = v.cat_id 
                 GROUP BY c.category";

    $catResult = $conn->query($catQuery);
    if (!$catResult)
        throw new Exception($conn->error);
    $response['data']['categories'] = $catResult->fetch_all(MYSQLI_ASSOC);

    // 3. Get Monthly Trends
    $trendQuery = "SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as total 
                   FROM v_dashboard_reports 
                   GROUP BY month 
                   ORDER BY MIN(created_at) ASC";

    $trendResult = $conn->query($trendQuery);
    if (!$trendResult)
        throw new Exception($conn->error);
    $response['data']['trends'] = $trendResult->fetch_all(MYSQLI_ASSOC);

    $statsQuery = "SELECT 
    -- severity counts (active table only)
    (SELECT COUNT(*) FROM report WHERE sev_id = 1) AS critical,
    (SELECT COUNT(*) FROM report WHERE sev_id = 2) AS high,
    (SELECT COUNT(*) FROM report WHERE sev_id = 3) AS medium,
    (SELECT COUNT(*) FROM report WHERE sev_id = 4) AS low,

    -- status counts
    (SELECT COUNT(*) FROM report WHERE status_id IN (1, 2)) AS active,
    (SELECT COUNT(*) FROM report WHERE status_id = 1) AS pending,
    (SELECT COUNT(*) FROM report WHERE status_id = 2) AS in_progress,

    -- resolved today (archive table)
    (
        SELECT COUNT(*) 
        FROM report_archive
        WHERE status_id = 3
          AND report_updated_at >= CURDATE()
          AND report_updated_at < CURDATE() + INTERVAL 1 DAY
    ) AS resolved_today
;";

    $statsResult = $conn->query($statsQuery);
    $response['data']['stats'] = $statsResult->fetch_assoc();

    $totalReportsQuery = "SELECT COUNT(*) as overall_total FROM report";
    $totalResult = $conn->query($totalReportsQuery);
    $response['data']['overall'] = $totalResult->fetch_assoc()['overall_total'];

    // B. Get Reports by Module
    $moduleQuery = "SELECT 
                    m.mod_desc, 
                    COUNT(r.report_id) as total_reports 
                FROM module m 
                LEFT JOIN report r ON m.mod_id = r.mod_id 
                GROUP BY m.mod_id, m.mod_desc
                ORDER BY total_reports DESC";

    $moduleResult = $conn->query($moduleQuery);
    if ($moduleResult) {
        $response['data']['modules'] = $moduleResult->fetch_all(MYSQLI_ASSOC);
    }
    // 5. Get Top Reporters
    $reporterQuery = "SELECT 
                    u.username, 
                    COUNT(r.report_id) as total 
                  FROM users u 
                  JOIN report r ON u.user_id = r.user_id 
                  GROUP BY u.user_id 
                  ORDER BY total DESC 
                  LIMIT 5";
    $response['data']['reporters'] = $conn->query($reporterQuery)->fetch_all(MYSQLI_ASSOC);

    // 6. Get Status Breakdown (for Pie Chart)
    $statusQuery = "SELECT 
    s.status_desc AS label,
    COUNT(r.status_id) AS total
FROM status s
LEFT JOIN (
    SELECT status_id FROM report
    UNION ALL
    SELECT status_id FROM report_archive
) r ON s.status_id = r.status_id
GROUP BY s.status_id, s.status_desc";
    $response['data']['statuses'] = $conn->query($statusQuery)->fetch_all(MYSQLI_ASSOC);

    // 7. Get Unresolved Critical Reports
    $criticalListQuery = "SELECT 
                        report_id, 
                        ref_num, 
                        DATE_FORMAT(created_at, '%b %d') as date
                      FROM v_dashboard_reports 
                      WHERE sev_id = 1 AND status_id NOT IN (3, 4)
                      ORDER BY created_at DESC 
                      LIMIT 5;";
    $response['data']['critical_reports'] = $conn->query($criticalListQuery)->fetch_all(MYSQLI_ASSOC);
    // 8. Success!
    $response['status'] = 'success';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// 9. Output exactly one JSON string
echo json_encode($response);
exit;