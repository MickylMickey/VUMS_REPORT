<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth();

// 1. Security Check
if ($userData->role !== 'Admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Capture Inputs
    $category_name = trim($_POST['category'] ?? '');
    $category_desc = trim($_POST['cat_desc'] ?? '');

    // 3. Validation
    if (empty($category_name)) {
        setValidation('error', "Category name is required.");
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 4. Duplicate Check
    $checkSql = "SELECT cat_id FROM category WHERE UPPER(category) = UPPER(?)";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $category_name);
    $checkStmt->execute();

    if ($checkStmt->get_result()->num_rows > 0) {
        setValidation('error', "The category '$category_name' already exists.");
        header("Location: ../public/categories_module.php");
        exit();
    }

    // 5. Insert with Description
    $sql = "INSERT INTO category (category, cat_desc) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $category_name, $category_desc);

        if ($stmt->execute()) {
            setValidation('success', "Category created successfully!");
        } else {
            setValidation('error', "Database Error: " . $stmt->error);
        }
        $stmt->close();
    }

    header("Location: ../public/categories_module.php");
    exit();
}