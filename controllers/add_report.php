<?php
require_once __DIR__ . "/../init.php";

ob_start();
session_start();

$userData = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Capture Inputs
    $cat_id = $_POST['cat_id'] ?? null;
    $mod_id = $_POST['mod_id'] ?? null;
    $sev_id = $_POST['sev_id'] ?? null;
    $report_desc = $_POST['rep_desc'] ?? '';
    $user_id = $userData->user_id;
    // Inside add_report.php
    $user_id = trim($userData->user_id);

    // 2. Basic Validation
    if (!$cat_id || !$mod_id || !$sev_id || empty($report_desc)) {
        $_SESSION['error'] = "All fields except the image are required.";
        header("Location: ../public/reports.php");
        exit();
    }

    // 3. Handle Image Upload
    $image_path = null;
    if (isset($_FILES['rep_img']) && $_FILES['rep_img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../public/uploads/";

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['rep_img']['name'], PATHINFO_EXTENSION);
        // Rename file to prevent overwrites (e.g., 16252431_uuid.jpg)
        $new_filename = time() . "_" . bin2hex(random_bytes(5)) . "." . $file_extension;
        $target_file = $upload_dir . $new_filename;

        // Simple check: is it actually an image?
        $check = getimagesize($_FILES['rep_img']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['rep_img']['tmp_name'], $target_file)) {
                $image_path = $new_filename; // Store just the filename in DB
            }
        }
    }

    // 4. Get Names for Ref Number
    $queryNames = "
        SELECT 
            (SELECT category FROM category WHERE cat_id = ?) as cat_name,
            (SELECT module FROM module WHERE mod_id = ?) as mod_name,
            (SELECT SUBSTRING(severity, 1, 1) FROM severity WHERE sev_id = ?) as sev_char
    ";
    $stmtNames = $conn->prepare($queryNames);
    $stmtNames->bind_param("iii", $cat_id, $mod_id, $sev_id);
    $stmtNames->execute();
    $resNames = $stmtNames->get_result()->fetch_assoc();

    $catName = str_replace(' ', '-', strtolower($resNames['cat_name']));
    $modName = str_replace(' ', '-', strtolower($resNames['mod_name']));
    $sevChar = strtolower($resNames['sev_char']);

    // 5. Generate Ref Number
    $queryCount = "SELECT COUNT(*) as total FROM report WHERE cat_id = ? AND mod_id = ?";
    $stmtCount = $conn->prepare($queryCount);
    $stmtCount->bind_param("ii", $cat_id, $mod_id);
    $stmtCount->execute();
    $rowCount = $stmtCount->get_result()->fetch_assoc();

    $nextNumber = str_pad($rowCount['total'] + 1, 3, '0', STR_PAD_LEFT);
    $ref_num = "{$catName}-{$modName}-{$sevChar}-{$nextNumber}";

    // 6. Database Insertion
    $sql = "INSERT INTO report (user_id, cat_id, mod_id, sev_id, ref_num, report_desc, report_img) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Types: s (uuid), i, i, i, s (ref), s (desc), s (img path)
        $stmt->bind_param("siiisss", $user_id, $cat_id, $mod_id, $sev_id, $ref_num, $report_desc, $image_path);

        // TEMPORARY DEBUG: Check if the user exists in the DB right before inserting
        $checkQuery = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $checkQuery->bind_param("s", $user_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows === 0) {
            die("ERROR: The UUID from your JWT ($user_id) does not exist in the 'user' table. Check your database!");
        }


        if ($stmt->execute()) {
            $_SESSION['success'] = "Report submitted! Reference: " . $ref_num;
        } else {
            $_SESSION['error'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }

    header("Location: ../public/reports.php");
    exit();
}