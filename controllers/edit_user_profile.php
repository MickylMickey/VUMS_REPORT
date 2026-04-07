<?php
require_once __DIR__ . "/../init.php";
ob_start();


// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Consistency: Use the same variable names as your POST keys
    $user_id = $_POST["user_id"] ?? null;
    $username = $_POST["username"] ?? null;
    $firstName = $_POST["first_name"] ?? null;
    $middleName = $_POST["middle_name"] ?? null;
    $lastName = $_POST["last_name"] ?? null;
    $birthDate = $_POST["birth_date"] ?? null;
    $role = $_POST["user_role"] ?? null;
    $email = $_POST["email"] ?? null;

    $imageDirectory = __DIR__ . "/../public/img/prof_pic/";
    $imageDbPath = "img/prof_pic/";
    $targetFileName = null;

    try {
        // Fetch current image to handle deletion later
        // Note: Check if your column is 'user_prof' or 'image_path'
        $stmt = $conn->prepare("SELECT user_prof FROM user_profile WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentData = $result->fetch_assoc();
        $stmt->close();

        $oldImageName = $currentData['user_prof'] ?? 'default.png';

        // 2. Handle image upload
        if (isset($_FILES["user_image"]) && $_FILES["user_image"]["error"] === 0) {
            $ext = strtolower(pathinfo($_FILES["user_image"]["name"], PATHINFO_EXTENSION));
            $allowedTypes = ["jpg", "jpeg", "png", "gif", "webp"];

            if (!in_array($ext, $allowedTypes)) {
                throw new Exception("Invalid image type.");
            }

            $uniqueName = uniqid() . "." . $ext;
            $newFilePath = $imageDirectory . $uniqueName;

            if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $newFilePath)) {
                $targetFileName = $uniqueName;

                // Delete old image if it's not the default
                if ($oldImageName !== "default.png") {
                    $oldFullFile = $imageDirectory . $oldImageName;
                    if (file_exists($oldFullFile)) {
                        unlink($oldFullFile);
                    }
                }
            }
        } else {
            $targetFileName = $oldImageName;
        }

        // 3. Database Transaction
        $conn->begin_transaction();

        // Update core users table (Check if table is 'users' or 'user')
        $stmt1 = $conn->prepare("UPDATE users SET username = ?, user_role_id = ? WHERE user_id = ?");
        $stmt1->bind_param("sis", $username, $role, $user_id);
        $stmt1->execute();
        $stmt1->close();

        // Update user_profile table
        $stmt2 = $conn->prepare("UPDATE user_profile 
                               SET user_first_name = ?, 
                                   user_middle_name = ?, 
                                   user_last_name = ?, 
                                   user_dob = ?, 
                                   email = ?, 
                                   user_prof = ? 
                               WHERE user_id = ?");

        $stmt2->bind_param(
            "sssssss",
            $firstName,
            $middleName,
            $lastName,
            $birthDate,
            $email,
            $targetFileName,
            $user_id
        );
        $stmt2->execute();
        $stmt2->close();

        $conn->commit();
        setValidation('success', "Profile updated successfully!");

    } catch (Exception $e) {
        if (isset($conn))
            $conn->rollback();
        error_log("Edit Error: " . $e->getMessage());
        setValidation('error', "Update failed: " . $e->getMessage());
    }

    header("Location: " . ($_SERVER["HTTP_REFERER"] ?? "../public/user_list.php"));
    exit();
}