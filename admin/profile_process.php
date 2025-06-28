<?php
require_once '../conn.php';

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    $photo_path = null;

    if (empty($username)) {
        $_SESSION['error_message'] = "نام کاربری نمی‌تواند خالی باشد.";
        header("Location: profile.php");
        exit;
    }

    // دریافت مسیر عکس فعلی
    $sql = "SELECT photo FROM admin_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old = $result->fetch_assoc();
    $old_photo = $old['photo'] ?? null;
    $stmt->close();

    // آپلود عکس جدید (در صورت وجود)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../uploads/admin/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = uniqid() . '-' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "فایل انتخاب شده تصویر نیست.";
            header("Location: profile.php");
            exit;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error_message'] = "فقط فایل‌های JPG, JPEG, PNG & GIF مجاز هستند.";
            header("Location: profile.php");
            exit;
        }
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = 'uploads/admin/' . $photo_name;
            // حذف عکس قبلی (اختیاری)
            if (!empty($old_photo) && file_exists("../".$old_photo)) {
                @unlink("../".$old_photo);
            }
        } else {
            $_SESSION['error_message'] = "در آپلود عکس خطا رخ داد.";
            header("Location: profile.php");
            exit;
        }
    }

    // اعتبارسنجی رمز عبور جدید (در صورت وارد شدن)
    $update_password = false;
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $_SESSION['error_message'] = "رمز عبور و تکرار آن یکسان نیستند.";
            header("Location: profile.php");
            exit;
        }
        $update_password = true;
    }

    // ساخت کوئری آپدیت
    if ($photo_path && $update_password) {
        $sql = "UPDATE admin_users SET username=?, full_name=?, password=?, photo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $full_name, $password, $photo_path, $admin_id);
    } elseif ($photo_path) {
        $sql = "UPDATE admin_users SET username=?, full_name=?, photo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $full_name, $photo_path, $admin_id);
    } elseif ($update_password) {
        $sql = "UPDATE admin_users SET username=?, full_name=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $full_name, $password, $admin_id);
    } else {
        $sql = "UPDATE admin_users SET username=?, full_name=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $full_name, $admin_id);
    }
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "تغییرات پروفایل با موفقیت ذخیره شد.";
        // بروزرسانی اطلاعات سشن
        $_SESSION["admin_username"] = $username;
        $_SESSION["admin_full_name"] = $full_name;
        if ($photo_path) $_SESSION["admin_photo"] = $photo_path;
    } else {
        $_SESSION['error_message'] = "خطا در ذخیره تغییرات: " . $conn->error;
    }
    $stmt->close();
    header("Location: profile.php");
    exit;
} else {
    header("Location: profile.php");
    exit;
} 