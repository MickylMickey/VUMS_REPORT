<?php
require_once __DIR__ . "/../init.php";
ob_clean();
header('Content-Type: application/json');

// session_start should be called, but check if it's already active in init.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportId = $_POST['report_id'] ?? null;
    $statusId = $_POST['status_id'] ?? null;

    // DEBUG: Ensure this matches the key you used when the user logged in!
    $updated_by = $_SESSION['user_id'] ?? null;

    if ($reportId && $statusId && $updated_by) {
        try {
            $stmt = $conn->prepare("
                UPDATE report
                SET status_id = ?, updated_by = ? 
                WHERE report_id = ?
            ");

            $success = $stmt->execute([$statusId, $updated_by, $reportId]);

            // If execute is true but no rows changed, it means the status was already that value
            echo json_encode(['success' => $success]);

        } catch (PDOException $e) {
            // This catches SQL errors (like wrong column names)
            echo json_encode([
                'success' => false,
                'error' => 'Database Error: ' . $e->getMessage()
            ]);
        }
    } else {
        // This tells you exactly which variable is empty
        $missing = [];
        if (!$reportId)
            $missing[] = 'report_id';
        if (!$statusId)
            $missing[] = 'status_id';
        if (!$updated_by)
            $missing[] = 'updated_by (Session empty)';

        echo json_encode([
            'success' => false,
            'error' => 'Missing data: ' . implode(', ', $missing)
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid Request Method']);
}
exit;