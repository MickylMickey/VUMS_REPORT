<?php
require_once __DIR__ . "/../init.php";
ob_clean();
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportId = $_POST['report_id'] ?? null;
    $statusId = $_POST['status_id'] ?? null;

    // If the user isn't logged in, $updated_by will be literal null
    $updated_by = $_POST['updated_by'] ?? $_SESSION['user_id'] ?? null;

    if (!$reportId || !$statusId) {
        echo json_encode(['success' => false, 'error' => 'Missing ID or Status']);
        exit;
    }

    // Start Transaction at the very beginning
    $conn->begin_transaction();

    try {
        // 1. Update the status and updated_by (handles NULL automatically)
        $stmt = $conn->prepare("UPDATE report SET status_id = ?, updated_by = ? WHERE report_id = ?");
        $stmt->bind_param("isi", $statusId, $updated_by, $reportId);

        if (!$stmt->execute()) {
            throw new Exception("Update failed: " . $stmt->error);
        }

        // 2. Check if the new status triggers Archiving (Completed or Cancelled)
        if ($statusId == 3 || $statusId == 4) {

            // A. Copy to archive
            $copySql = "INSERT INTO report_archive SELECT * FROM report WHERE report_id = ?";
            $copyStmt = $conn->prepare($copySql);
            $copyStmt->bind_param("i", $reportId);
            $copyStmt->execute();

            if ($copyStmt->affected_rows === 0) {
                throw new Exception("Failed to copy report to archive.");
            }

            // B. Delete from active table
            $delSql = "DELETE FROM report WHERE report_id = ?";
            $delStmt = $conn->prepare($delSql);
            $delStmt->bind_param("i", $reportId);
            $delStmt->execute();
        }

        // If we reached here, everything is fine
        $conn->commit();
        echo json_encode(['success' => true, 'archived' => ($statusId == 3 || $statusId == 4)]);

    } catch (Exception $e) {
        // If anything fails (Update, Insert, or Delete), revert everything
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}