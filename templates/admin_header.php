<?php
// This file should be included at the top of all admin pages.
require_once __DIR__ . '/../conn.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'پنل مدیریت'; ?> - فروشگاه اپکس لند</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/icon/apexland.png">
    <style>
        .glass-admin {
            background: rgba(255,255,255,0.7);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        body, .glass-admin, .custom-card, .btn-primary-gold, .form-control-custom, .form-label, .sidebar, .admin-header, .admin-footer, .admin-main, .admin-dashboard, .admin-sidebar, .admin-content, .admin-stats, .admin-welcome {
            font-family: 'Noto Sans Arabic', Tahoma, Arial, sans-serif !important;
        }
    </style>
</head>
</html> 