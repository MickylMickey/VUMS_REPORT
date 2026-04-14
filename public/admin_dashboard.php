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
$userData = checkAuth('Admin');
$current_user_id = $userData->user_id;
$user_role = $userData->role;
// If the code reaches here, the user is authenticated.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Admin Dashboard</title>
</head>

<body class="bg-[#f8fafc] min-h-screen flex flex-col antialiased text-slate-900 pt-24">
    <div>
        <?php include "templates/navbar.php"; ?>
    </div>

    <main class="flex-grow">
        <div class="max-w-4xl mx-auto mt-4 px-8">
            <?= showValidation() ?>
        </div>

        <div class="p-8">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>

            <div class="mt-6 p-6 bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
                <h2 class="font-semibold mb-2 text-slate-700">User Info (Decoded from JWT):</h2>
                <pre class="bg-slate-50 p-4 rounded-lg text-xs font-mono text-slate-600 border border-slate-100"><?php print_r($userData); ?></pre>
            </div>

            </div>
    </main>

    <div class="mt-auto">
        <?php include "templates/footer.php"; ?>
    </div>

    <?php ob_end_flush(); ?>
</body>

</html>