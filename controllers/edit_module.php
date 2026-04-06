<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth();

// 1. Security Check
if ($userData->role !== 'Admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Capture and Clean Inputs

    $mod_id = $_POST['module_id'] ?? null;
    $new_module = trim($_POST['edit_module_name'] ?? '');
    $new_desc = trim($_POST['edit_module_desc'] ?? '');

    if (!$mod_id || empty($new_module)) {
        $_SESSION['error'] = "Module name is required.";
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 3. Duplicate Check

    $checkSql = "SELECT mod_id FROM module WHERE LOWER(module) = LOWER(?) AND mod_id != ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("si", $new_module, $mod_id);
    $checkStmt->execute();

    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Another module with the name '$new_module' already exists.";
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 4. Perform the Update
    $sql = "UPDATE module SET module = ?, mod_desc = ? WHERE mod_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $new_module, $new_desc, $mod_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Module updated successfully!";
        } else {
            $_SESSION['error'] = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }

    header("Location: ../public/categories_module.php");
    exit();
}