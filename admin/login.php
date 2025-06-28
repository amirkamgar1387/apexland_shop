<?php
require_once '../conn.php';

// If admin is already logged in, redirect to dashboard
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header("location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل مدیریت - فروشگاه اپکس لند</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .glass-card {
            background: rgba(255,255,255,0.7);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .login-bg {
            background: linear-gradient(135deg, #f9fafb 0%, #f3e9d2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center">
    <div class="w-full min-h-screen flex items-center justify-center py-8 px-2">
        <div class="glass-card custom-card login-card w-full max-w-md mx-auto p-8 relative">
            <div class="flex flex-col items-center mb-6">
                <img src="../assets/icon/apexland.png" alt="ApexLand Logo" class="w-20 h-20 rounded-full shadow mb-3 bg-white object-contain">
                <h1 class="text-2xl font-extrabold text-gray-800">ورود به پنل مدیریت</h1>
                <p class="text-gray-500 mt-1">به فروشگاه اپکس لند خوش آمدید</p>
            </div>
            <?php
            if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) {
                echo '<div class="flex items-center gap-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">';
                echo '<i class="fas fa-exclamation-triangle text-xl"></i>';
                echo '<span class="font-bold">خطا:</span>';
                echo '<span>' . htmlspecialchars($_SESSION['error_message']) . '</span>';
                echo '</div>';
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="login_process.php" method="post" class="space-y-5">
                <div>
                    <label for="username" class="form-label">نام کاربری</label>
                    <input type="text" id="username" name="username" class="form-control-custom focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" required autocomplete="username">
                </div>
                <div>
                    <label for="password" class="form-label">رمز عبور</label>
                    <input type="password" id="password" name="password" class="form-control-custom focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" required autocomplete="current-password">
                </div>
                <button type="submit" class="w-full btn-primary-gold flex items-center justify-center gap-2 text-lg py-3 mt-2 shadow hover:shadow-lg transition">
                    <i class="fas fa-lock"></i>
                    <span>ورود</span>
                </button>
            </form>
            <a href="../index.php" class="w-full mt-6 flex items-center justify-center gap-2 btn-primary-blue text-lg py-3 rounded-xl shadow hover:shadow-lg transition font-bold">
                <i class="fas fa-home"></i>
                مشاهده سایت
            </a>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</body>
</html> 