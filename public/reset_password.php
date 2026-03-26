<?php
require "../config/config.php";
require "../middleware/auth_middleware.php";

$login_user_id = $user_id;
$user_id = $_GET["user_id"] ?? $login_user_id;

session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="output.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-[750] mb-6 text-center">Reset Password</h2>
        <form method="POST" action="../controllers/reset_password_handler.php">
            <?php if ($role == "super_admin" || $role == "admin"): ?>
                <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                <input type="text" name="user_id" id="user_id" disabled
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-200 text-gray-700"
                    value="<?= htmlspecialchars($user_id) ?>">
                <?php if (isset($_SESSION['error_user'])): ?>
                    <p class="text-sm text-red-500 mt-1">
                        <?= htmlspecialchars($_SESSION['error_user']); ?>
                    </p>
                    <?php unset($_SESSION['error_user']); ?> <!-- Clear error message after displaying -->
                <?php endif; ?>
            <?php else: ?>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

                <label for="current_password" class="block text-sm font-medium text-gray-700 mt-2">Current Password</label>
                <input type="password" name="current_password" id="current_password" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-200">

                <!-- Error Message for Current Password -->
                <?php if (isset($_SESSION['error_current_password'])): ?>
                    <p class="text-sm text-red-500 mt-1">
                        <?= htmlspecialchars($_SESSION['error_current_password']); ?>
                    </p>
                    <?php unset($_SESSION['error_current_password']); ?> <!-- Clear error message after displaying -->
                <?php endif; ?>
            <?php endif; ?>

            <label for="new_password" class="block text-sm font-medium text-gray-700 mt-2">New Password</label>
            <input type="password" name="new_password" id="new_password" required
                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-200">

            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mt-2">Confirm Password</label>
            <input type="password" name="confirm_new_password" id="confirm_password" required
                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-200">

            <!-- Error Message for Confirm Password -->
            <?php if (isset($_SESSION['error_match_password'])): ?>
                <p class="text-sm text-red-500 mt-1">
                    <?= htmlspecialchars($_SESSION['error_match_password']); ?>
                </p>
                <?php unset($_SESSION['error_match_password']); ?> <!-- Clear error message after displaying -->
            <?php endif; ?>

            <button type="submit"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition mt-3">
                Reset Password
            </button>
        </form>
    </div>
</body>

</html>