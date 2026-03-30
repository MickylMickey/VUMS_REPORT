<?php
require_once __DIR__ . "/../init.php";

ob_start();
session_start();

use Ramsey\Uuid\Uuid;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a unique user ID
    $userId = Uuid::uuid4()->toString();

    $username = isset($_POST['username']) ? trim(filter_var($_POST['username'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
    $password = isset($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_BCRYPT) : null;
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : null;
    $firstName = isset($_POST['fname']) ? trim(filter_var($_POST['fname'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
    $middleName = isset($_POST['mname']) ? trim(filter_var($_POST['mname'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
    $lastName = isset($_POST['lname']) ? trim(filter_var($_POST['lname'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
    $roleId = isset($_POST['user_role']) ? (int) $_POST['user_role'] : null;
    $birthDate = !empty($_POST['birthday']) ? $_POST['birthday'] : null;

    // 1. Configuration
    $uploadDir = __DIR__ . "/../public/img/prof_pic/"; // Use absolute path for reliability
    $dbImageName = "default.png"; // Default filename for the database

    // 2. Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // 3. Process Upload
    if (isset($_FILES["prof_pic"]) && $_FILES["prof_pic"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES["prof_pic"]["tmp_name"];
        $fileName = $_FILES["prof_pic"]["name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validation
        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($fileExtension, $allowedExtensions)) {
            $_SESSION["error"] = "Invalid image type. Use JPG, PNG, or GIF.";
            header("Location: ../public/user_list.php");
            exit();
        }

        // Create a clean, unique filename (e.g., 65e1f2a3b4c5d.png)
        $newFileName = uniqid() . "." . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $dbImageName = $newFileName; // Success! This is what you save to the DB
        } else {
            $_SESSION["error"] = "Server Error: Could not move uploaded file.";
            header("Location: ../public/register.php");
            exit();
        }
    }

    // 4. Database Step
// Use $dbImageName in your SQL: INSERT INTO users (profile_img) VALUES ('$dbImageName')
    //Start Transaction
    $conn->begin_transaction();

    //1st stmt
    try {
        // Insert user into `user` table
        $stmt1 = $conn->prepare("INSERT INTO users (user_id, username, password, user_role_id, user_created_at) 
            VALUES (?, ?, ?, ?, NOW())");
        $stmt1->bind_param(
            "sssi",
            $userId,
            $username,
            $password,
            $roleId
        );

        if ($stmt1->execute()) {
            error_log("DEBUG: User inserted successfully into `user` table. user_id: $userId, username: $username, role_id: $roleId");
        } else {
            error_log("DEBUG ERROR: Failed to insert user into `user` table. Error: " . $stmt1->error);
        }
        $stmt1->close();

        //2nd stmt
        $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, user_first_name, user_middle_name, user_last_name, user_dob, email, user_prof)
        VALUES (?,?,?,?,?,?,?)");
        $stmt2->bind_param(
            "sssssss",
            $userId,
            $firstName,
            $middleName,
            $lastName,
            $birthDate,
            $email,
            $dbImageName
        );
        if ($stmt2->execute()) {
            error_log("DEBUG: User profile inserted successfully. user_id: $userId, first_name: $firstName, last_name: $lastName");
        } else {
            error_log("DEBUG ERROR: Failed to insert user profile. Error: " . $stmt2->error);
        }
        $stmt2->close();

        // Commit transaction to save changes
        $conn->commit();

        setValidation("success", "User registered successfully");

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();

        // Delete uploaded file if an error happened
        if (file_exists($dbImageName)) {
            unlink($dbImageName);
        }

        // Log the error
        error_log("Registration Error: " . $e->getMessage());

        // Redirect to registration page with an error message
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>