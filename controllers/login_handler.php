<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../init.php";

ob_start();

use Ramsey\Uuid\Uuid;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $loginType = $_POST['login_type'] ?? ''; // safer default
    $remember = isset($_POST['remember_me']); // ✅ remember me

    try {

        $sql = "SELECT user_id, username, password, user_status_id FROM users WHERE BINARY username = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("DB Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $users = $result->fetch_assoc();

            if ((int) $users['user_status_id'] === 2) {
                setValidation("error", "Account is archived. Please contact the administrator.");
                header("Location: /index.php");
                exit();
            }

            if ((int) $users['user_status_id'] >= 4) {
                setValidation("error", "Invalid username or password.");
                header("Location: /index.php");
                exit();
            }


            if (password_verify($password, $users["password"])) {

                // set active
                $updateStatus = $conn->prepare("UPDATE users SET user_status_id = 1 WHERE user_id = ?");
                $updateStatus->bind_param("s", $users["user_id"]);
                $updateStatus->execute();

                // refresh user data
                $refresh = $conn->prepare("
                    SELECT 
                        u.user_id, 
                        u.username, 
                        up.email,
                        up.user_prof,
                        u.user_role_id, 
                        ur.role_name,
                        CONCAT(up.user_first_name, ' ', up.user_last_name) AS user_full_name 
                    FROM users u  
                    LEFT JOIN user_profile up ON up.user_id = u.user_id 
                    LEFT JOIN user_role ur ON ur.user_role_id = u.user_role_id 
                    WHERE u.user_id = ?;
                ");
                $refresh->bind_param("s", $users["user_id"]);
                $refresh->execute();
                $updatedUser = $refresh->get_result()->fetch_assoc();

                $isAdmin = in_array($updatedUser["role_name"], ["Admin", "System Administrator"]);

                if ($loginType === "Admin" && !$isAdmin) {
                    setValidation("error", "Access denied: Admin Access required!");
                    header("Location: /index.php");
                    exit();
                }

                // ✅ unified expiry
                $expiry = $remember
                    ? time() + (86400 * 30) // 30 days
                    : time() + 3600;        // 1 hour

                $payload = [
                    "user_id" => $updatedUser["user_id"],
                    "username" => $updatedUser["username"],
                    "role" => $updatedUser["role_name"],
                    "user_prof" => $updatedUser["user_prof"],
                    "exp" => $expiry
                ];

                $_SESSION['user_id'] = $updatedUser["user_id"];

                $jwt = JwtHelper::generateToken($payload);
                $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

                setcookie("auth_token", $jwt, [
                    "expires" => $remember ? $expiry : 0, // session vs persistent
                    "path" => "/",
                    "domain" => "",
                    "secure" => $isSecure,
                    "httponly" => true,
                    "samesite" => "Lax",
                ]);

                ob_end_clean();

                if ($isAdmin) {
                    header("Location: ../public/admin_dashboard.php");
                } else {
                    header("Location: /index.php");
                }
                exit();
            }
        }

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