<?php
require_once __DIR__ . "/../init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $archiveStatus = 2;


    $query = "UPDATE users SET user_status_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {

        $stmt->bind_param("is", $archiveStatus, $user_id);

        if ($stmt->execute()) {
            setValidation('success', 'User successfully archived.');
        } else {
            setValidation('error', "Database error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        setValidation('error', "Failed to prepare the statement.");
    }

    header("Location: ../public/user_list.php");
    exit();
}