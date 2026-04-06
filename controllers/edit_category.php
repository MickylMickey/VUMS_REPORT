<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth();

// 1. Security Check
if ($userData->role !== 'Admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Capture and Clean Inputs

    $cat_id = $_POST['cat_id'] ?? null;
    $new_category = trim($_POST['edit_category'] ?? '');
    $new_desc = trim($_POST['edit_cat_desc'] ?? '');

    if (!$cat_id || empty($new_category)) {
        $_SESSION['error'] = "Category name is required.";
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 3. Duplicate Check

    $checkSql = "SELECT cat_id FROM category WHERE LOWER(category) = LOWER(?) AND cat_id != ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("si", $new_category, $cat_id);
    $checkStmt->execute();

    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Another category with the name '$new_category' already exists.";
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 4. Perform the Update
    $sql = "UPDATE category SET category = ?, cat_desc = ? WHERE cat_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $new_category, $new_desc, $cat_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }

    header("Location: ../public/categories_module.php");
    exit();
}