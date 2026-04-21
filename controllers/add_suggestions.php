<?php
require_once __DIR__ . "/../init.php";

ob_start();
$userData = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Capture Inputs
    $suggestion_desc = $_POST['suggestion_desc'] ?? '';
    $user_id = trim($userData->user_id);

    // 2. Basic Validation
    if (empty($suggestion_desc)) {
        setValidation('error', "Suggestion description is required.");
        header("Location: ../public/suggestions.php");
        exit();
    }

    // 3. Handle Image Upload

    $image_path = null;
    if (isset($_FILES['suggestion_img']) && $_FILES['suggestion_img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../public/uploads/suggestions/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES['suggestion_img']['name'];
        $file_tmp = $_FILES['suggestion_img']['tmp_name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        //  formats
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = "sug_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
            $target_file = $upload_dir . $new_filename;


            if (move_uploaded_file($file_tmp, $target_file)) {
                $image_path = $new_filename;
            }
        } else {
            setValidation('error', "Invalid file type. Please upload an image or video.");
            header("Location: ../public/suggestions.php");
            exit();
        }
    }

    // 4. Database Insertion
    $sql = "INSERT INTO user_suggestions (user_id, suggestion_desc, suggestion_img) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $user_id, $suggestion_desc, $image_path);

        // Debug Check: Ensure user exists
        $checkQuery = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $checkQuery->bind_param("s", $user_id);
        $checkQuery->execute();

        if ($checkQuery->get_result()->num_rows === 0) {
            die("ERROR: UUID ($user_id) not found in 'users' table. Try logging out and back in.");
        }


        if ($stmt->execute()) {
            setValidation('success', "Suggestion submitted successfully!");
        } else {
            setValidation('error', "Database Error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        setValidation('error', "System Error: Could not prepare statement.");
    }

    header("Location: ../public/suggestions.php");
    exit();
}