<?php
require_once __DIR__ . "/../init.php";

ob_start();
use Ramsey\Uuid\Uuid;

// 1. Initialize variables for logging
$userId = null;
$username = "Unknown User";

// 2. Identify who is logging out (before we destroy the cookie)
if (isset($_COOKIE['auth_token'])) {
    $decoded = JwtHelper::verifyToken($_COOKIE['auth_token']);
    if ($decoded && isset($decoded->data)) {
        $userId = $decoded->data->user_id;
        $username = $decoded->data->username;
    }
}



// 4. Clear the Auth Cookie
// We set the expiration to the past (time() - 3600) to tell the browser to delete it
setcookie("auth_token", "", time() - 3600, "/");

// 5. Clear any legacy PHP Sessions (just in case)
session_start();
session_unset();
session_destroy();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 6. Set a success message for the login page
setValidation("success", "You have been logged out successfully.");

// 7. Redirect to login
header("Location: /public/login.php");
exit();