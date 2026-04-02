<?php
require_once __DIR__ . '/../helper/jwt_helper.php';
require_once __DIR__ . '/../helper/generalValidationMessage.php';

function checkAuth($requiredRole = null)
{
    if (!isset($_COOKIE['auth_token'])) {
        setValidation("error", "Please login to access this page.");
        header("Location: /public/login.php");
        exit();
    }

    $decoded = JwtHelper::verifyToken($_COOKIE['auth_token']);

    if (!$decoded || !isset($decoded->data)) {
        setcookie("auth_token", "", time() - 3600, "/");
        setValidation("info", "Your session has expired. Please login again.");
        header("Location: /public/login.php");
        exit();
    }

    // Use the nested 'data' object consistently
    $userData = $decoded->data;

    // Role check
    if ($requiredRole && (!isset($userData->role) || $userData->role !== $requiredRole)) {
        setValidation("error", "You do not have permission to view that page.");
        header("Location: /index.php");
        exit();
    }

    // Define globals so they are accessible in the files that call this function
    global $user_id, $username, $role_id, $municipality, $barangay, $barangay_name, $role, $email, $user_status, $user_profile, $user_full_name;

    $user_id = $userData->user_id ?? null;
    $username = $userData->username ?? null;
    $municipality = $userData->municipality ?? null;
    $role_id = $userData->role_id ?? null;
    $barangay = $userData->barangay ?? null;
    $barangay_name = $userData->barangay_name ?? null;
    $role = $userData->role ?? null;
    $email = $userData->email ?? null;
    $user_status = $userData->user_status ?? null;
    $user_profile = $userData->user_profile ?? null;
    $user_full_name = $userData->user_full_name ?? null;

    return $userData;
}