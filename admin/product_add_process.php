<?php
require_once '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Get and Validate Data ---
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price_usd = trim($_POST['price_usd']);
    $short_description = trim($_POST['short_description']);
    $full_description = trim($_POST['full_description']);
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : null;
    $brand = trim($_POST['brand'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $image_path = null;

    if (empty($name) || empty($category_id) || empty($price_usd)) {
        $_SESSION['error_message'] = "فیلدهای نام محصول، دسته‌بندی و قیمت اجباری هستند.";
        header("location: product_add.php");
        exit;
    }

    if (!is_numeric($price_usd) || $price_usd < 0) {
        $_SESSION['error_message'] = "قیمت وارد شده معتبر نیست.";
        header("location: product_add.php");
        exit;
    }

    // --- 2. Handle Image Upload ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/products/";
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "فایل انتخاب شده تصویر نیست.";
            header("location: product_add.php");
            exit;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error_message'] = "فقط فایل‌های JPG, JPEG, PNG & GIF مجاز هستند.";
            header("location: product_add.php");
            exit;
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/products/' . $image_name;
        } else {
            $_SESSION['error_message'] = "متاسفانه در هنگام آپلود تصویر خطایی رخ داد.";
            header("location: product_add.php");
            exit;
        }
    } else {
        $_SESSION['error_message'] = "تصویر شاخص محصول اجباری است.";
        header("location: product_add.php");
        exit;
    }

    // --- 3. Database Insertion ---
    $sql = "INSERT INTO products (name, category_id, price_usd, short_description, full_description, image, stock, brand, features) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssdssssss", $name, $category_id, $price_usd, $short_description, $full_description, $image_path, $stock, $brand, $features);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "محصول جدید با موفقیت اضافه شد.";
            header("location: products.php");
            exit;
        } else {
            $_SESSION['error_message'] = "خطا در افزودن محصول: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "خطا در آماده‌سازی کوئری: " . $conn->error;
    }

    // Redirect back to form on error
    header("location: product_add.php");
    exit;

} else {
    // If not a POST request, redirect
    header("location: product_add.php");
    exit;
}
?>