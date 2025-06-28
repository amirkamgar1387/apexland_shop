<?php
require_once '../conn.php';

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $summary = trim($_POST['summary'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $image_path = null;

    // اعتبارسنجی
    if (empty($title) || $category_id <= 0 || empty($summary) || empty($content)) {
        $_SESSION['error_message'] = "همه فیلدهای ضروری را پر کنید.";
        header("Location: blog_post_add.php");
        exit;
    }

    // آپلود تصویر شاخص (اختیاری)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/blog/";
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "فایل انتخاب شده تصویر نیست.";
            header("Location: blog_post_add.php");
            exit;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error_message'] = "فقط فایل‌های JPG, JPEG, PNG & GIF مجاز هستند.";
            header("Location: blog_post_add.php");
            exit;
        }
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/blog/' . $image_name;
        } else {
            $_SESSION['error_message'] = "در هنگام آپلود تصویر خطا رخ داد.";
            header("Location: blog_post_add.php");
            exit;
        }
    }

    // درج در دیتابیس
    $sql = "INSERT INTO blog_posts (title, category_id, summary, content, image, status) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sissss", $title, $category_id, $summary, $content, $image_path, $status);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "مقاله جدید با موفقیت اضافه شد.";
            header("Location: blog_posts.php");
            exit;
        } else {
            $_SESSION['error_message'] = "خطا در افزودن مقاله: " . $conn->error;
            header("Location: blog_post_add.php");
            exit;
        }
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
    } else {
        $_SESSION['error_message'] = "خطا در آماده‌سازی کوئری: " . $conn->error;
        header("Location: blog_post_add.php");
        exit;
    }
} else {
    header("Location: blog_posts.php");
    exit;
} 