<?php
require_once __DIR__ . "/../init.php";

ob_start();


/**
 * 2. Use checkAuth() instead of session_start()
 * This function will:
 *  - Check if the JWT cookie exists
 *  - Verify if the JWT is valid
 *  - Check if the user has the 'Admin' role
 *  - Redirect to login.php automatically if any of the above fails
 */
// Change 'Admin' to 'System Administrator' if that's the exact string in your DB
$userData = checkAuth('User');
$current_user_id = $userData->user_id;
$user_role = $userData->role;
// If the code reaches here, the user is authenticated.
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body class="pt-24">
    <div>
        <?php include "templates/navbar.php"; ?>
    </div>
    <h1>TEST</h1>

</body>

</html>