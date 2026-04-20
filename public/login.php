<?php
require_once __DIR__ . "/../init.php";



if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_COOKIE['auth_token'])) {
    $decoded = JwtHelper::verifyToken($_COOKIE['auth_token']);


    if ($decoded && isset($decoded->data->role)) {
        if (trim($decoded->data->role) === 'Admin') {
            header("Location: /public/admin_dashboard.php");
            exit();
        } else {
            header("Location: /public/user_dashboard.php");
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Login - VUMS report</title>
</head>

<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center font-sans p-4">

    <div class="mb-6 text-center">
        <img src="img/images.jpg" alt="Vinculum Logo" class="h-20 w-20 mx-auto mb-3 drop-shadow-sm">
        <h1 class="text-blue-900 font-bold text-xl uppercase tracking-tight">Vinculum Technologies Corporation</h1>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-widest">VUMS Report</p>
    </div>

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="h-1.5 bg-blue-500"></div>
        <div class="p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Login</h2>

            <?php if (isset($_COOKIE['validation_message'])): ?>
                <div id="validationBlock">
                    <span><?= showValidation() ?></span>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="../controllers/login_handler.php" method="POST" class="space-y-6">

                <input type="hidden" name="login_type">


                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-gray-600">Username</label>
                    <div class="relative group">
                        <i
                            class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition"></i>

                        <input type="text" name="username" autocomplete="username" class="w-full pl-10 pr-4 py-3 rounded-xl bg-white border border-gray-200 text-gray-900 placeholder-gray-400
                   focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all duration-200"
                            placeholder="Enter your username" data-required="true" data-error="Username is required.">

                        <p class="error-message hidden text-red-500 text-xs mt-1"></p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-gray-600">Password</label>
                    <div class="relative group">
                        <i
                            class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition"></i>

                        <input id="passwordInput" type="password" name="password" autocomplete="current-password" class="w-full pl-10 pr-10 py-3 rounded-xl bg-white border border-gray-200 text-gray-900 placeholder-gray-400
                   focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all duration-200"
                            placeholder="••••••••" data-required="true" data-error="Password is required.">

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>

                        <p class="error-message hidden text-red-500 text-xs mt-1"></p>
                    </div>
                </div>


                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700
               text-white font-semibold shadow-md active:scale-[0.97] transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Sign In
                </button>

            </form>
        </div>
    </div>
</body>
<?php ob_end_flush(); ?>
<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
<script src="js/removeNotification.js" defer></script>
<script src="js/inputValidation.js" defer></script>
<script>document.addEventListener("DOMContentLoaded", () => {
        initFormValidation("loginForm");
    });</script>

</html>