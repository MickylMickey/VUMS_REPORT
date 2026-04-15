<?php
require_once __DIR__ . "/../init.php";

// 1. TANGGALIN ang session_start() dito dahil nag-start na ito sa init.php
// Ito ang aayos sa "Notice: session_start(): Ignoring..."

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $archiveStatus = 2; 

    // 2. AYUSIN ang bind_param (Ang 's' ay para sa String/UUID, 'i' para sa Integer)
    // Ang error na "Truncated incorrect DOUBLE value" ay dahil sa maling data type binding.
    $query = "UPDATE users SET user_status_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Ginawa nating "is" (Integer para sa status, String para sa user_id/UUID)
        $stmt->bind_param("is", $archiveStatus, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User successfully archived.";
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to prepare the statement.";
    }

    header("Location: ../public/user_list.php");
    exit();
}