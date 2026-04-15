<?php
require_once __DIR__ . "/../init.php";

// Tandaan: Ang init.php mo ay dapat may session_start() na.
// Kung wala, magdagdag ng session_start() dito.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $activeStatus = 1; // 1 para ibalik sa "Active" status

    // I-update ang status ng user pabalik sa Active
    $query = "UPDATE users SET user_status_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // "is" = Integer para sa status, String para sa UUID/User ID
        $stmt->bind_param("is", $activeStatus, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User has been successfully restored to active status.";
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to prepare the database statement.";
    }

    // Pagkatapos ng restore, ibalik si Admin sa archived_list.php
    header("Location: ../public/archive_list.php");
    exit();
} else {
    // Kung tinangka i-access ang file nang hindi POST
    header("Location: ../public/user_list.php");
    exit();
}