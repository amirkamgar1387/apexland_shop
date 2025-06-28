<?php
require_once '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_path = null;

    // Validate name
    if (empty($name)) {
        $_SESSION['error_message'] = "نام دسته‌بندی نمی‌تواند خالی باشد.";
        header("location: categories.php");
        exit;
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/categories/";
        // Create a unique filename to prevent overwriting existing files
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "فایل انتخاب شده تصویر نیست.";
            header("location: categories.php");
            exit;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error_message'] = "متاسفانه فقط فایل‌های JPG, JPEG, PNG & GIF مجاز هستند.";
            header("location: categories.php");
            exit;
        }

        // Attempt to move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/categories/' . $image_name; // Path to be stored in DB
        } else {
            $_SESSION['error_message'] = "متاسفانه در هنگام آپلود تصویر خطایی رخ داد.";
            header("location: categories.php");
            exit;
        }
    }

    // Insert into database
    $sql = "INSERT INTO categories (name, description, image) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $name, $description, $image_path);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "دسته‌بندی جدید با موفقیت اضافه شد.";
        } else {
            $_SESSION['error_message'] = "خطا در افزودن دسته‌بندی: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "خطا در آماده‌سازی کوئری: " . $conn->error;
    }

    header("location: categories.php");
    exit;

} else {
    // If not a POST request, redirect to categories page
    header("location: categories.php");
    exit;
}
?>