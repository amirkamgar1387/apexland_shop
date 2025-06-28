<?php
require_once '../conn.php';

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_path = null;

    if ($id <= 0 || empty($name)) {
        $_SESSION['error_message'] = "اطلاعات نامعتبر است.";
        header("Location: blog_category_edit.php?id=$id");
        exit;
    }

    // دریافت مسیر تصویر فعلی
    $sql = "SELECT image FROM blog_categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old = $result->fetch_assoc();
    $old_image = $old['image'] ?? null;
    $stmt->close();

    // آپلود تصویر جدید (در صورت وجود)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/blog_categories/";
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "فایل انتخاب شده تصویر نیست.";
            header("Location: blog_category_edit.php?id=$id");
            exit;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error_message'] = "فقط فایل‌های JPG, JPEG, PNG & GIF مجاز هستند.";
            header("Location: blog_category_edit.php?id=$id");
            exit;
        }
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/blog_categories/' . $image_name;
            // حذف تصویر قبلی (اختیاری)
            if (!empty($old_image) && file_exists("../".$old_image)) {
                @unlink("../".$old_image);
            }
        } else {
            $_SESSION['error_message'] = "در آپلود تصویر خطا رخ داد.";
            header("Location: blog_category_edit.php?id=$id");
            exit;
        }
    }

    // ساخت کوئری آپدیت
    if ($image_path) {
        $sql = "UPDATE blog_categories SET name=?, description=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $image_path, $id);
    } else {
        $sql = "UPDATE blog_categories SET name=?, description=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $id);
    }
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "تغییرات با موفقیت ذخیره شد.";
    } else {
        $_SESSION['error_message'] = "خطا در ذخیره تغییرات: " . $conn->error;
    }
    $stmt->close();
    header("Location: blog_category_edit.php?id=$id");
    exit;
} else {
    header("Location: blog_categories.php");
    exit;
} 