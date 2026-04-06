<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth('Admin');

// 1. HARD SECURITY CHECK
if ($userData->role !== 'Admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_by = $_POST['updated_by'] ?? $_SESSION['user_id'] ?? null;
    $report_id = $_POST['report_id'];
    $cat_id = $_POST['cat_id'];
    $mod_id = $_POST['mod_id'];
    $sev_id = $_POST['sev_id'];
    $report_desc = $_POST['report_desc'];

    // 2. Handle the "Other" logic again
    $db_cat_id = ($cat_id === "other") ? null : (int) $cat_id;
    $db_mod_id = ($mod_id === "other") ? null : (int) $mod_id;
    $db_sev_id = (int) $sev_id;

    // 2. Fetch New Names for the Ref Number
    $catName = "xxx";
    $modName = "xxx";
    $sevChar = "x";

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

    if ($db_cat_id && !empty($resNames['cat_name'])) {
        $catName = str_replace(' ', '-', strtolower($resNames['cat_name']));
    }
    if ($db_mod_id && !empty($resNames['mod_name'])) {
        $modName = str_replace(' ', '-', strtolower($resNames['mod_name']));
    }
    if (!empty($resNames['sev_char'])) {
        $sevChar = strtolower($resNames['sev_char']);
    }


    $stmtOld = $conn->prepare("SELECT ref_num FROM report WHERE report_id = ?");
    $stmtOld->bind_param("i", $report_id);
    $stmtOld->execute();
    $oldRef = $stmtOld->get_result()->fetch_assoc()['ref_num'];


    $parts = explode('-', $oldRef);
    $sequence = end($parts);

    // 4. Construct the updated Ref Number
    $new_ref_num = "{$catName}-{$modName}-{$sevChar}-{$sequence}";

    // 5. Final Update including ref_num
    $sql = "UPDATE report SET 
                cat_id = ?, 
                mod_id = ?, 
                sev_id = ?, 
                report_desc = ?, 
                ref_num = ?, 
                updated_by = ?,
                report_updated_at = NOW() 
            WHERE report_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiisssi",
        $db_cat_id,
        $db_mod_id,
        $db_sev_id,
        $report_desc,
        $new_ref_num,
        $updated_by,
        $report_id
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Report and Ref Number updated! New Ref: " . $new_ref_num;
    } else {
        $_SESSION['error'] = "Update failed: " . $stmt->error;
    }

    header("Location: ../public/reports.php");
    exit();
}