<?php
// functions/user-data-api.php
require_once __DIR__ . "/../init.php";

// 1. JWT Authentication
// Assuming checkAuth() populates the user data or returns the user object
$userData = checkAuth(['User', 'HR']); // Change 'User' to the appropriate role if needed
$userId = $userData->user_id;

ob_clean();
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'data' => []
];

try {    // 3. Status & Severity Stats (User-Specific)
    $statsQuery = "SELECT 
        (SELECT COUNT(*) FROM report WHERE sev_id = 1 AND user_id = '$userId') AS critical,
        (SELECT COUNT(*) FROM report WHERE sev_id = 2 AND user_id = '$userId') AS high,
        (SELECT COUNT(*) FROM report WHERE sev_id = 3 AND user_id = '$userId') AS medium,
        (SELECT COUNT(*) FROM report WHERE sev_id = 4 AND user_id = '$userId') AS low,
        (SELECT COUNT(*) FROM report WHERE status_id IN (1, 2) AND user_id = '$userId') AS active,
        (SELECT COUNT(*) FROM report WHERE status_id = 1 AND user_id = '$userId') AS pending,
        (SELECT COUNT(*) FROM report WHERE status_id = 2 AND user_id = '$userId') AS in_progress,
        (
            SELECT COUNT(*) 
            FROM report_archive
            WHERE status_id = 3
              AND user_id = '$userId'
              AND report_updated_at >= CURDATE()
              AND report_updated_at < CURDATE() + INTERVAL 1 DAY
        ) AS resolved_today;";

    $statsResult = $conn->query($statsQuery);
    $response['data']['stats'] = $statsResult->fetch_assoc();

    // 4. Overall Total for this User
    $totalReportsQuery = "SELECT COUNT(*) as overall_total FROM report WHERE user_id = '$userId'";
    $totalResult = $conn->query($totalReportsQuery);
    $response['data']['overall'] = $totalResult->fetch_assoc()['overall_total'];

    // 5. Status Breakdown (Pie Chart - User-Specific)
    $statusQuery = "SELECT 
        s.status_desc AS label,
        COUNT(r.status_id) AS total
    FROM status s
    LEFT JOIN (
        SELECT status_id, user_id FROM report
        UNION ALL
        SELECT status_id, user_id FROM report_archive
    ) r ON s.status_id = r.status_id AND r.user_id = '$userId'
    GROUP BY s.status_id, s.status_desc";

    $response['data']['statuses'] = $conn->query($statusQuery)->fetch_all(MYSQLI_ASSOC);

    // 6. User's Personal Unresolved Critical Reports
    $criticalListQuery = "SELECT 
                            report_id, 
                            ref_num, 
                            DATE_FORMAT(created_at, '%b %d') as date
                          FROM v_dashboard_reports 
                          WHERE sev_id = 1 
                            AND status_id NOT IN (3, 4)
                            AND user_id = '$userId'
                          ORDER BY created_at DESC 
                          LIMIT 5;";

    $response['data']['critical_reports'] = $conn->query($criticalListQuery)->fetch_all(MYSQLI_ASSOC);
    $response['status'] = 'success';

    $reporterQuery = "SELECT 
                    u.username, 
                    COUNT(r.report_id) as total 
                  FROM users u 
                  JOIN report r ON u.user_id = r.user_id 
                  GROUP BY u.user_id 
                  ORDER BY total DESC 
                  LIMIT 5";
    $response['data']['reporters'] = $conn->query($reporterQuery)->fetch_all(MYSQLI_ASSOC);

    $totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
    $response['data']['total_users'] = $conn->query($totalUsersQuery)->fetch_assoc()['total_users'];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;