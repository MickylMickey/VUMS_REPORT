<?php
require_once __DIR__ . "/helper/jwt_helper.php";

if (isset($_COOKIE['auth_token'])) {
    $decoded = JwtHelper::verifyToken($_COOKIE['auth_token']);

    if ($decoded && isset($decoded->data->role)) {
        $role = $decoded->data->role;

        // Redirect based on the actual role in the token
        if ($role === 'Admin') {
            header("Location: ../public/admin_dashboard.php");
            exit();
        } elseif ($role === 'HR') {
            header("Location: ../public/hr_dashboard.php");
            exit();

        } else {
            // If they are a normal user, send them to the user area (or just stay here)
            header("Location: ../public/user_dashboard.php");
            echo "Welcome, User. You do not have Admin access.";
            exit();
        }
    }
}

// No valid token? Go to login.
header("Location: ../public/login.php");
exit();