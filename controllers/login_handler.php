<?php
// 1. Enable Error Reporting for debugging (Remove this in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../helper/jwt_helper.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../helper/generalValidationMessage.php';

use Ramsey\Uuid\Uuid;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $loginType = $_POST['login_type'] ?? 'Admin';

    try {
        $sql = "SELECT user_id, username, password FROM users WHERE BINARY username = ? AND user_status_id < 4";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("DB Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $users = $result->fetch_assoc();

            if (password_verify($password, $users["password"])) {

                $updateStatus = $conn->prepare("UPDATE users SET user_status_id = 1 WHERE user_id = ?");
                $updateStatus->bind_param("s", $users["user_id"]);
                $updateStatus->execute();

                $refresh = $conn->prepare("
        SELECT 
    u.user_id, 
    u.username, 
    up.email,
    u.user_role_id, 
    ur.role_name,
    CONCAT(up.user_first_name, ' ', up.user_last_name) AS user_full_name 
FROM users u  
LEFT JOIN user_profile up ON up.user_id = u.user_id 
LEFT JOIN user_role ur ON ur.user_role_id = u.user_role_id 
WHERE u.user_id =?;
    ");
                $refresh->bind_param("s", $users["user_id"]);
                $refresh->execute();
                $updatedUser = $refresh->get_result()->fetch_assoc();

                $isAdmin = in_array($updatedUser["role_name"], ["Admin, System Administrator"]);
                if ($loginType === "Admin" && !$isAdmin) {
                    setValidation("Error", "Access denied: Admin Access required!");
                    header("Location:/index.php");
                    exit();
                }
                $payload = [
                    "user_id" => $updatedUser["user_id"],
                    "username" => $updatedUser["username"],
                    "role" => $updatedUser["role_name"],
                    "exp" => time() + 3600 // 1 hour
                ];

                $jwt = JwtHelper::generateToken($payload);
                $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

                setcookie("auth_token", $jwt, [
                    "expires" => time() + 3600,
                    "path" => "/",
                    "domain" => "", // Leave empty for current domain
                    "secure" => $isSecure,
                    "httponly" => true,
                    "samesite" => "Lax", // "Strict" can sometimes block cookies on initial redirect
                ]);

                ob_end_clean(); // Clear buffer before redirect
                if ($isAdmin) {
                    header("Location: /admin_dashboard.php");
                } else {
                    header("Location: /index.php");
                }
                exit();
            }
        } else {
            setValidation("error", "Invalid username or password.");
            header("Location: /index.php");
            exit();
        }
        // Credentials failed
        setValidation("error", "Incorrect Username or Password");
        header("Location: /index.php");
        exit();

    } catch (Exception $e) {
        setValidation("error", "System error occurred. Please try again later.");
        header("Location: /index.php");
        exit();
    }
} else {
    header("Location: /index.php");
    exit();
}
