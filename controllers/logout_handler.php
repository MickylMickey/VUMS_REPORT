<?php
require_once __DIR__ . "/../init.php";

session_start();

ob_start();

// 1. Identify user from token (optional logging)
$userId = null;
$username = "Unknown User";

if (isset($_COOKIE['auth_token'])) {
    $decoded = JwtHelper::verifyToken($_COOKIE['auth_token']);
    if ($decoded && isset($decoded->data)) {
        $userId = $decoded->data->user_id ?? null;
        $username = $decoded->data->username ?? "Unknown User";
    }
}

// 2. Clear session properly
$_SESSION = [];
session_unset();
session_destroy();

// 3. Clear session cookie (PHP session)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 4. Clear auth token cookie (IMPORTANT FIX)
$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

setcookie("auth_token", "", [
    "expires" => time() - 3600,
    "path" => "/",
    "domain" => "",
    "secure" => $isSecure,
    "httponly" => true,
    "samesite" => "Lax",
]);

// 5. Feedback message
setValidation("success", "You have been logged out successfully.");

// 6. Redirect
header("Location: /public/login.php");
exit();