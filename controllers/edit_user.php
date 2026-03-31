<?php
require_once __DIR__ . "/../init.php";

ob_start();
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_POST["user_id"];
    $username = $_POST["username"] ?? null;
    $role = $_POST["user_role"] ?? null;
    $password = $_POST["password"] ?? null;

    try {
        $conn->begin_transaction();

        if (!empty($password)) {

            if (!empty($role)) {
                $sql = "UPDATE users 
                SET username = ?, user_role_id = ?, password = ?
                WHERE user_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siss", $username, $role, $hashed_password, $user_id);

            } else {
                $sql = "UPDATE users 
                SET username = ?, password = ?
                WHERE user_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $username, $hashed_password, $user_id);
            }

        } else {

            if (!empty($role)) {
                $sql = "UPDATE users 
                SET username = ?, user_role_id = ?
                WHERE user_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sis", $username, $role, $user_id);

            } else {
                $sql = "UPDATE users 
                SET username = ?
                WHERE user_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $username, $user_id);
            }
        }

        $stmt->execute();
        $conn->commit();

        $stmt->close();

    } catch (Exception $e) {
        $conn->rollback();
        echo "User update error: " . $e->getMessage();
        exit;
    } finally {
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}