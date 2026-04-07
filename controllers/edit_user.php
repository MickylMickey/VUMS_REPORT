<?php
require_once __DIR__ . "/../init.php";

ob_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_POST["user_id"];
    $username = $_POST["username"] ?? null;
    $role = $_POST["user_role"] ?? null;
    $password = $_POST["password"] ?? null;

    try {
        $conn->begin_transaction();

        if (!empty($password)) {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if (!empty($role)) {
                $sql = "UPDATE users SET username = ?, user_role_id = ?, password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siss", $username, $role, $hashed_password, $user_id);
            } else {
                $sql = "UPDATE users SET username = ?, password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $username, $hashed_password, $user_id);
            }
        } else {
            if (!empty($role)) {
                $sql = "UPDATE users SET username = ?, user_role_id = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sis", $username, $role, $user_id);
            } else {
                $sql = "UPDATE users SET username = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $username, $user_id);
            }
        }

        if ($stmt->execute()) {
            $conn->commit();

            setValidation('success', "User '{$username}' updated successfully!");
        } else {
            throw new Exception($stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        if ($conn)
            $conn->rollback();
        setValidation('error', "User update error: " . $e->getMessage());
    } finally {
        // Use a fallback if HTTP_REFERER is missing
        $redirect = $_SERVER['HTTP_REFERER'] ?? "../public/user_list.php";
        header("Location: " . $redirect);
        exit;
    }
}