<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../helper/generalValidationMessage.php';

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

    //handle image upload
    $imageDirectory = "../public/img/prof_pic/";
    $targetFilePath = "../public/img/prof_pic/default.png"; // Default to profile

    if (isset($_FILES["prof_pic"]) && $_FILES["prof_pic"]["error"] === 0) {
        $uniqueName = uniqid() . "_" . basename($_FILES["prof_pic"]["name"]);
        $tempPath = $_FILES["prof_pic"]["tmp_name"];
        $imageFileType = strtolower(pathinfo($uniqueName, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($imageFileType, $allowedTypes)) {
            $_SESSION["error"] =
                "Invalid image type. Allowed types: JPG, JPEG, PNG, GIF.";
            header("Location: ../public/user_list.php");
            exit();
        }

        $newFilePath = $imageDirectory . $uniqueName;
        if (!move_uploaded_file($tempPath, $newFilePath)) {
            $_SESSION["error"] = "Failed to save uploaded image.";
            header("Location: ../public/register.php");
            exit();
        }

        $targetFilePath = $newFilePath; // Set only if upload is successful
    }

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
            $targetFilePath
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
        if (file_exists($targetFilePath)) {
            unlink($targetFilePath);
        }

        // Log the error
        error_log("Registration Error: " . $e->getMessage());

        // Redirect to registration page with an error message
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>