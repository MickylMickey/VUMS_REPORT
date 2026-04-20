<?php
require_once __DIR__ . '/../init.php';
header('Content-Type: application/json');

$user = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Remove intval() because UUIDs are strings, not integers
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];

    try {
        if ($role === 'Admin') {
            $sql = "UPDATE notifications 
                    SET is_read = 1 
                    WHERE (receiver_id IS NULL OR receiver_id = ?) AND is_read = 0";
        } else {
            $sql = "UPDATE notifications n
                    INNER JOIN report r ON n.report_id = r.report_id
                    SET n.is_read = 1
                    WHERE r.user_id = ? AND n.is_read = 0";
        }

        $stmt = $conn->prepare($sql);

        // 2. CHANGE "i" TO "s" HERE
        $stmt->bind_param("s", $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}