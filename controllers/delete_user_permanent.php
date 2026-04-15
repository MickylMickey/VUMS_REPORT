<?php
require_once __DIR__ . "/../init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // SQL para tuluyang burahin ang user
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    
   
    $stmt->bind_param("s", $user_id); 

    if ($stmt->execute()) {
        $_SESSION['success'] = "User has been permanently deleted.";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }


    header("Location: ../public/archive_list.php");
    exit();
}