<?php
require_once __DIR__ . "/../init.php";
ob_clean();
header('Content-Type: application/json');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suggestionId = $_POST['suggestion_id'] ?? null;
    $statusId = $_POST['status_id'] ?? null;

    // DEBUG: Ensure this matches the key you used when the user logged in!
    $updated_by = $_SESSION['user_id'] ?? null;

    // 1. Update the status normally first
    $stmt = $conn->prepare("UPDATE user_suggestions SET status_id = ?, suggestion_updated_by = ? WHERE suggestion_id = ?");
    $stmt->bind_param("isi", $statusId, $updated_by, $suggestionId);

    if ($stmt->execute()) {
        // 2. Check if the new status is 3 (Completed) or 4 (Cancelled)
        if ($statusId == 3 || $statusId == 4) {

            // Start a Transaction (All or Nothing)
            $conn->begin_transaction();

            try {
                // A. Copy the data to the archive table
                $copySql = "INSERT INTO suggestion_archive SELECT * FROM user_suggestions WHERE suggestion_id = ?";
                $copyStmt = $conn->prepare($copySql);
                $copyStmt->bind_param("i", $suggestionId);
                $copyStmt->execute();

                // B. Delete from the active table
                $delSql = "DELETE FROM user_suggestions WHERE suggestion_id = ?";
                $delStmt = $conn->prepare($delSql);
                $delStmt->bind_param("i", $suggestionId);
                $delStmt->execute();

                // If both succeeded, save changes
                $conn->commit();
                echo json_encode(['success' => true, 'archived' => true]);
            } catch (Exception $e) {
                // If anything fails, undo the move so no data is lost
                $conn->rollback();
                echo json_encode(['success' => false, 'error' => 'Archive failed: ' . $e->getMessage()]);
            }
        } else {
            // Just a regular status update (e.g., Pending to In Progress)
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}