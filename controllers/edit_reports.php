<?php
require_once __DIR__ . "/../init.php";
$userData = checkAuth('Admin');


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

    $db_cat_id = ($cat_id === "other") ? null : (int) $cat_id;
    $db_mod_id = ($mod_id === "other") ? null : (int) $mod_id;
    $db_sev_id = (int) $sev_id;

    
    
    
    $stmtOldFile = $conn->prepare("SELECT report_img FROM report WHERE report_id = ?");
    $stmtOldFile->bind_param("i", $report_id);
    $stmtOldFile->execute();
    $current_file = $stmtOldFile->get_result()->fetch_assoc()['report_img'];
    $new_filename = $current_file; 

    
    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['report_file']['tmp_name'];
        $file_original_name = $_FILES['report_file']['name'];
        $file_ext = strtolower(pathinfo($file_original_name, PATHINFO_EXTENSION));
        
        
        $unique_name = time() . '_' . uniqid() . '.' . $file_ext;

       
        $video_exts = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
        $is_video = in_array($file_ext, $video_exts);
        
        
        $target_dir = $is_video ? "../public/Videos/" : "../public/uploads/";
        
        if (move_uploaded_file($file_tmp, __DIR__ . "/" . $target_dir . $unique_name)) {
           
            if (!empty($current_file)) {
                $old_ext = strtolower(pathinfo($current_file, PATHINFO_EXTENSION));
                $old_dir = in_array($old_ext, $video_exts) ? "../public/Videos/" : "../public/uploads/";
                if (file_exists(__DIR__ . "/" . $old_dir . $current_file)) {
                    unlink(__DIR__ . "/" . $old_dir . $current_file);
                }
            }
            $new_filename = $unique_name;
        }
    }
    
    $catName = "xxx"; $modName = "xxx"; $sevChar = "x";
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

    if ($db_cat_id && !empty($resNames['cat_name'])) $catName = str_replace(' ', '-', strtolower($resNames['cat_name']));
    if ($db_mod_id && !empty($resNames['mod_name'])) $modName = str_replace(' ', '-', strtolower($resNames['mod_name']));
    if (!empty($resNames['sev_char'])) $sevChar = strtolower($resNames['sev_char']);


    $stmtOld = $conn->prepare("SELECT ref_num FROM report WHERE report_id = ?");
    $stmtOld->bind_param("i", $report_id);
    $stmtOld->execute();
    $oldRef = $stmtOld->get_result()->fetch_assoc()['ref_num'];
    $parts = explode('-', $oldRef);
    $sequence = end($parts);

    $new_ref_num = "{$catName}-{$modName}-{$sevChar}-{$sequence}";

  
    $sql = "UPDATE report SET 
                cat_id = ?, 
                mod_id = ?, 
                sev_id = ?, 
                report_desc = ?, 
                ref_num = ?, 
                report_img = ?, 
                updated_by = ?,
                report_updated_at = NOW() 
            WHERE report_id = ?";

    $stmt = $conn->prepare($sql);
    
    
    $stmt->bind_param(
        "iiissssi", 
        $db_cat_id,
        $db_mod_id,
        $db_sev_id,
        $report_desc,
        $new_ref_num,
        $new_filename,
        $updated_by,
        $report_id
    );

    if ($stmt->execute()) {
        setValidation('success', "Report updated! New Ref: " . $new_ref_num);
    } else {
        setValidation('error', "Update failed: " . $stmt->error);
    }

    header("Location: ../public/reports.php");
    exit();
}