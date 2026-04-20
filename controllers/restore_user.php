<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth(['Admin', 'HR']);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $activeStatus = 1;


    $query = "UPDATE users SET user_status_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {

        $stmt->bind_param("is", $activeStatus, $user_id);

        if ($stmt->execute()) {
            setValidation('success', 'User has been successfully restored to active status.');
        } else {
            setValidation('error', "Database error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        setValidation('error', "Failed to prepare the database statement.");
    }


    header("Location: ../public/archive_list.php");
    exit();
} else {

    header("Location: ../public/user_list.php");
    exit();
}