<?php
require_once __DIR__ . "/../init.php";
header('Content-Type: application/json'); // Ensure the browser knows this is JSON
$userData = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $sender_id = $userData->user_id;

    // 1. Anti-Spam: Check if a reminder was already sent in the last 24 hours
    $checkSql = "SELECT created_at FROM notifications 
                 WHERE sender_id = ? AND report_id = ? 
                 AND report_ref_snapshot LIKE 'Reminded:%'
                 AND created_at > NOW() - INTERVAL 1 DAY 
                 LIMIT 1";

    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("si", $sender_id, $report_id);
    $stmtCheck->execute();
    $alreadySent = $stmtCheck->get_result()->fetch_assoc();

    if ($alreadySent) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already sent a reminder for this report in the last 24 hours.'
        ]);
        exit;
    }

    // 2. Fetch the reference number for the message snapshot
    $stmtRef = $conn->prepare("SELECT ref_num FROM report WHERE report_id = ? LIMIT 1");
    $stmtRef->bind_param("i", $report_id);
    $stmtRef->execute();
    $reportData = $stmtRef->get_result()->fetch_assoc();
    $displayRef = $reportData['ref_num'] ?? "#" . $report_id;

    // 3. Insert Notification
    $sender_name = $userData->username;
    $message = "Reminded: $sender_name is requesting an update on Report: $displayRef.";

    // Logic: We set receiver_id to NULL. 
    // Our fetch function will treat NULL as "Visible to all Admins/HR".
    $sql = "INSERT INTO notifications (sender_id, receiver_id, report_id, report_ref_snapshot, is_read) 
            VALUES (?, NULL, ?, ?, 0)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $sender_id, $report_id, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
}