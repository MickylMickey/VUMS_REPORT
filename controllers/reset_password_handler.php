<?php
require "../middleware/auth_middleware.php";
require "../config/config.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Get form data
        $current_user_id = $_POST["user_id"] ?? null;
        $current_password = $_POST["current_password"] ?? "";
        $new_password = $_POST["new_password"] ?? "";
        $confirm_password = $_POST["confirm_new_password"] ?? "";

        // Your existing code continues here...
        if (!$current_user_id) {
            $_SESSION['error'] = "Invalid User ID";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit();
        }

        // Check if the new password and confirm password match
        if ($new_password !== $confirm_password) {
            $_SESSION['error_match_password'] = "Passwords do not match";
            header("Location: ../public/reset_password.php?user_id=" . urlencode($current_user_id));
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // If the logged-in user is a regular user (not admin or superadmin), verify the current password
        if ($role !== 'admin') {
            // Prepare and execute the query to fetch the stored password
            $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $conn->error);
            }

            $stmt->bind_param("s", $current_user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                $_SESSION['error_user'] = "User not found.";
                header("Location: ../public/reset_password.php");
                exit();
            }

            $stmt->bind_result($stored_password);
            $stmt->fetch();
            $stmt->close();

            // Check if the provided current password matches the stored password
            if (!password_verify($current_password, $stored_password)) {
                $_SESSION['error_current_password'] = "Current password is incorrect.";
                header("Location: ../public/reset_password.php?user_id=" . urlencode($current_user_id));
                exit();
            }
        }

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt->bind_param("ss", $hashed_password, $current_user_id);
        $stmt->execute();
        $stmt->close();

        // After successful update, echo success message instead of redirect
        header("Location: ../public/login.php");
        exit();
    } catch (Exception $e) {
        // Log the exception and show an error message to the user
        error_log($e->getMessage()); // Log the error message for debugging

        $_SESSION['error'] = "An error occurred while resetting the password. Please try again.";
        header("Location: ../public/reset_password.php");
        exit();
    }
}