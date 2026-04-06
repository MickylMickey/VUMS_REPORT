<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Capture Inputs
    $cat_id = trim($_POST['cat_id'] ?? '');
    $mod_id = trim($_POST['mod_id'] ?? '');
    $sev_id = trim($_POST['sev_id'] ?? '');
    $report_desc = $_POST['rep_desc'] ?? '';
    $user_id = trim($userData->user_id);

    // 2. Basic Validation
    if (empty($cat_id) || empty($mod_id) || empty($sev_id) || empty($report_desc)) {
        $_SESSION['error'] = "All fields except the image are required.";
        header("Location: ../public/reports.php");
        exit();
    }

    // 3. Handle Image Upload (Keep your existing logic)
    $image_path = null;
    if (isset($_FILES['rep_img']) && $_FILES['rep_img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../public/uploads/";
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);
        $file_extension = pathinfo($_FILES['rep_img']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . "_" . bin2hex(random_bytes(5)) . "." . $file_extension;
        $target_file = $upload_dir . $new_filename;
        if (getimagesize($_FILES['rep_img']['tmp_name'])) {
            if (move_uploaded_file($_FILES['rep_img']['tmp_name'], $target_file)) {
                $image_path = $new_filename;
            }
        }
    }

    // 4. Convert "other" to literal NULL or Integer

    $db_cat_id = (strcasecmp($cat_id, "other") === 0) ? null : (int) $cat_id;
    $db_mod_id = (strcasecmp($mod_id, "other") === 0) ? null : (int) $mod_id;
    // Severity cannot be NULL in your DB, so use ID 99
    $db_sev_id = (strcasecmp($sev_id, "other") === 0) ? 99 : (int) $sev_id;

    // 5. Fetch names for the Reference Number
    $catName = "xxx";
    $modName = "xxx";
    $sevChar = "xxx";

    // Query names using the NULL-safe spaceship operator (<=>)
    $queryNames = "
        SELECT 
            (SELECT category FROM category WHERE cat_id <=> ?) as cat_name,
            (SELECT module FROM module WHERE mod_id <=> ?) as mod_name,
            (SELECT SUBSTRING(severity, 1, 1) FROM severity WHERE sev_id <=> ?) as sev_char
    ";
    $stmtNames = $conn->prepare($queryNames);
    $stmtNames->bind_param("iii", $db_cat_id, $db_mod_id, $db_sev_id);
    $stmtNames->execute();
    $resNames = $stmtNames->get_result()->fetch_assoc();

    if ($db_cat_id !== null && !empty($resNames['cat_name'])) {
        $catName = str_replace(' ', '-', strtolower($resNames['cat_name']));
    }
    if ($db_mod_id !== null && !empty($resNames['mod_name'])) {
        $modName = str_replace(' ', '-', strtolower($resNames['mod_name']));
    }
    if ($db_sev_id !== 99 && !empty($resNames['sev_char'])) {
        $sevChar = strtolower($resNames['sev_char']);
    }

    // 6. Generate Ref Number (Counter)
    $queryCount = "SELECT COUNT(*) as total FROM report WHERE (cat_id <=> ?) AND (mod_id <=> ?)";
    $stmtCount = $conn->prepare($queryCount);
    $stmtCount->bind_param("ii", $db_cat_id, $db_mod_id);
    $stmtCount->execute();
    $rowCount = $stmtCount->get_result()->fetch_assoc();

    $nextNumber = str_pad($rowCount['total'] + 1, 3, '0', STR_PAD_LEFT);
    $ref_num = "{$catName}-{$modName}-{$sevChar}-{$nextNumber}";

    // 7. Database Insertion
    $sql = "INSERT INTO report (user_id, cat_id, mod_id, sev_id, ref_num, report_desc, report_img) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // bind_param will correctly send NULL to the DB for $db_cat_id and $db_mod_id
        $stmt->bind_param("siiisss", $user_id, $db_cat_id, $db_mod_id, $db_sev_id, $ref_num, $report_desc, $image_path);

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