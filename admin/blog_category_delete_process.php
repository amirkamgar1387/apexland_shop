<?php
require_once '../conn.php';

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "شناسه دسته‌بندی نامعتبر است.";
    header("location: blog_categories.php");
    exit;
}

$id = intval($_GET['id']);

// دریافت مسیر تصویر قبل از حذف رکورد
$sql_select = "SELECT image FROM blog_categories WHERE id = ?";
$image_path = null;
if ($stmt_select = $conn->prepare($sql_select)) {
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $stmt_select->bind_result($image_path);
    $stmt_select->fetch();
    $stmt_select->close();
}
// حذف فایل تصویر از سرور
if ($image_path) {
    $full_image_path = '../' . $image_path;
    if (file_exists($full_image_path)) {
        unlink($full_image_path);
    }
}
// حذف رکورد دسته‌بندی از دیتابیس
$sql_delete = "DELETE FROM blog_categories WHERE id = ?";
if ($stmt_delete = $conn->prepare($sql_delete)) {
    $stmt_delete->bind_param("i", $id);
    if ($stmt_delete->execute()) {
        $_SESSION['success_message'] = "دسته‌بندی وبلاگ با موفقیت حذف شد.";
    } else {
        $_SESSION['error_message'] = "خطا در حذف دسته‌بندی: " . $conn->error;
    }
    $stmt_delete->close();
} else {
    $_SESSION['error_message'] = "خطا در آماده‌سازی کوئری حذف: " . $conn->error;
}
$conn->close();
header("location: blog_categories.php");
exit; 