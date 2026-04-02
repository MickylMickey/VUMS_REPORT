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
        $_SESSION['error'] = "Suggestion description is required.";
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

        $file_extension = pathinfo($_FILES['suggestion_img']['name'], PATHINFO_EXTENSION);
        $new_filename = "sug_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
        $target_file = $upload_dir . $new_filename;

        if (getimagesize($_FILES['suggestion_img']['tmp_name'])) {
            if (move_uploaded_file($_FILES['suggestion_img']['tmp_name'], $target_file)) {
                $image_path = $new_filename;
            }
        }
    }

    // 4. Database Insertion
    $sql = "INSERT INTO user_suggestions (user_id, suggestion_desc, suggestion_img) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $user_id, $suggestion_desc, $image_path);

        // Debug Check: Ensure user exists (Table name changed to 'user' to match your schema)
        $checkQuery = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $checkQuery->bind_param("s", $user_id);
        $checkQuery->execute();

        if ($checkQuery->get_result()->num_rows === 0) {
            die("ERROR: UUID ($user_id) not found in 'users' table. Try logging out and back in.");
        }

        // --- CRITICAL: You must call execute() to actually save to DB ---
        if ($stmt->execute()) {
            $_SESSION['success'] = "Suggestion submitted successfully!";
        } else {
            $_SESSION['error'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "System Error: Could not prepare statement.";
    }

    header("Location: ../public/suggestions.php");
    exit();
}