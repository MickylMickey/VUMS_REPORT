<?php
require_once "../init.php";
$userData = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $notif_id = intval($_POST['id']);

    // Safety check: ensure the notification belongs to a report owned by this user
    $sql = "UPDATE notifications n 
            INNER JOIN report r ON n.report_id = r.report_id 
            SET n.is_read = 1 
            WHERE n.notification_id = ? AND r.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $notif_id, $userData->user_id);

    echo json_encode(['success' => $stmt->execute()]);
}