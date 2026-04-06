<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth();

// 1. Security Check
if ($userData->role !== 'Admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Capture Inputs
    $category_name = trim($_POST['module'] ?? '');
    $category_desc = trim($_POST['mod_desc'] ?? '');

    // 3. Validation
    if (empty($category_name)) {
        $_SESSION['error'] = "Module name is required.";
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 4. Duplicate Check
    $checkSql = "SELECT mod_id FROM module WHERE UPPER(module) = UPPER(?)";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $category_name);
    $checkStmt->execute();

    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "The module '$category_name' already exists.";
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 5. Insert with Description
    $sql = "INSERT INTO module (module, mod_desc) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $category_name, $category_desc);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Module created successfully!";
        } else {
            $_SESSION['error'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }

    header("Location: ../public/categories_module.php");
    exit();
}