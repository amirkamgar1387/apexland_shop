<?php
require_once '../conn.php';

// بررسی ورود ادمین
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_path = null;

    // اعتبارسنجی نام
    if (empty($name)) {
        $_SESSION['error_message'] = "نام دسته‌بندی نمی‌تواند خالی باشد.";
        header("location: blog_categories.php");
        exit;
    }

    // آپلود تصویر (اختیاری)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/blog_categories/";
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "فایل انتخاب شده تصویر نیست.";
            header("location: blog_categories.php");
            exit;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error_message'] = "فقط فایل‌های JPG, JPEG, PNG & GIF مجاز هستند.";
            header("location: blog_categories.php");
            exit;
        }
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/blog_categories/' . $image_name;
        } else {
            $_SESSION['error_message'] = "در هنگام آپلود تصویر خطایی رخ داد.";
            header("location: blog_categories.php");
            exit;
        }
    }

    // درج در دیتابیس
    $sql = "INSERT INTO blog_categories (name, description, image) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $name, $description, $image_path);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "دسته‌بندی وبلاگ با موفقیت اضافه شد.";
        } else {
            $_SESSION['error_message'] = "خطا در افزودن دسته‌بندی: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "خطا در آماده‌سازی کوئری: " . $conn->error;
    }
    header("location: blog_categories.php");
    exit;
} else {
    header("location: blog_categories.php");
    exit;
}
?>