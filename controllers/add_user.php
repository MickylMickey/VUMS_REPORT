<?php
require_once __DIR__ . "/../init.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

ob_start();

use Ramsey\Uuid\Uuid;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {

        $userId = Uuid::uuid4()->toString();

        $username = isset($_POST['username']) ? trim(filter_var($_POST['username'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
        $password = !empty($_POST['password']) ? password_hash(trim($_POST["password"]), PASSWORD_BCRYPT) : null;

        // email validation method 
        $rawEmail = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';


        if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format error: Mangyaring gumamit ng valid na email format.");
        }


        $email = $rawEmail;


        $firstName = isset($_POST['fname']) ? trim(filter_var($_POST['fname'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
        $middleName = isset($_POST['mname']) ? trim(filter_var($_POST['mname'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
        $lastName = isset($_POST['lname']) ? trim(filter_var($_POST['lname'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)) : null;
        $roleId = isset($_POST['user_role']) ? (int) $_POST['user_role'] : null;
        $birthDate = !empty($_POST['birthday']) ? $_POST['birthday'] : null;

        $uploadDir = __DIR__ . "/../public/img/prof_pic/";
        $dbImageName = "default.png";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // for profile picture upload
        if (isset($_FILES["prof_pic"]) && $_FILES["prof_pic"]["error"] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES["prof_pic"]["tmp_name"];
            $fileName = $_FILES["prof_pic"]["name"];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ["jpg", "jpeg", "png", "gif"];

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Invalid image type. Use JPG, PNG, or GIF.");
            }

            $newFileName = $userId . "." . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $dbImageName = $newFileName;
            } else {
                throw new Exception("Server Error: Could not move uploaded file.");
            }
        }

        // 4. Database Transaction
        $conn->begin_transaction();

        // Statement 1: Core User Table
        $stmt1 = $conn->prepare("INSERT INTO users (user_id, username, password, user_role_id, user_created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt1->bind_param("sssi", $userId, $username, $password, $roleId);
        $stmt1->execute();
        $stmt1->close();

        // Statement 2: User Profile Table
        $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, user_first_name, user_middle_name, user_last_name, user_dob, email, user_prof) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("sssssss", $userId, $firstName, $middleName, $lastName, $birthDate, $email, $dbImageName);
        $stmt2->execute();
        $stmt2->close();

        // Commit if both succeeded
        $conn->commit();

        setValidation('success', "User registered successfully");

        // Redirect back
        $redirectTo = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../public/user_list.php";
        header("Location: " . $redirectTo);
        exit();

    } catch (Exception $e) {
        // Something went wrong, undo DB changes
        if (isset($conn)) {
            $conn->rollback();
        }

        // Clean up uploaded image if DB failed (don't delete the default)
        if (isset($dbImageName) && $dbImageName !== "default.png") {
            $fullPath = $uploadDir . $dbImageName;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        // Log the actual error for the developer
        error_log("Registration Error: " . $e->getMessage());

        // Set error message for the user
        setValidation('error', "Registration failed: " . $e->getMessage());

        $redirectTo = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../public/register.php";
        header("Location: " . $redirectTo);
        exit();
    }
}
?>