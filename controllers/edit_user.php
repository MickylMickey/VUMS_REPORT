<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../vendor/autoload.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $username = $_POST["username"] ?? null;
    $email = $_POST["email"] ?? null;
    $role = $_POST["user_role"] ?? null;

    $conn->begin_transaction();

    try {
        // 1. Start the transaction (Crucial!)
        $conn->begin_transaction();

        // 2. Update the 'users' table
        $sql1 = "UPDATE users SET username = COALESCE(?, username), user_role_id = COALESCE(?, user_role_id) WHERE user_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sis", $username, $role, $user_id);
        $stmt1->execute();

        // 3. Update the 'user_profile' table (where email lives)
        $sql2 = "UPDATE user_profile SET email = COALESCE(?, email) WHERE user_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $email, $user_id);
        $stmt2->execute();

        // 4. Commit both updates at once
        $conn->commit();

        $stmt1->close();
        $stmt2->close();

    } catch (Exception $e) {
        // If either query fails, undo everything
        $conn->rollback();
        echo "User update error: " . $e->getMessage();
        exit;
    } finally {
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}