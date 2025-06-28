<?php
require_once '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "شناسه محصول نامعتبر است.";
    header("location: products.php");
    exit;
}

$id = intval($_GET['id']);

// --- Step 1: Get the image path before deleting the record ---
$sql_select = "SELECT image FROM products WHERE id = ?";
$image_path = null;
if ($stmt_select = $conn->prepare($sql_select)) {
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $stmt_select->bind_result($image_path);
    $stmt_select->fetch();
    $stmt_select->close();
}

// --- Step 2: Delete the image file from the server ---
if ($image_path) {
    $full_image_path = '../' . $image_path;
    if (file_exists($full_image_path)) {
        unlink($full_image_path);
    }
}

// --- Step 3: Delete the product from the database ---
$sql_delete = "DELETE FROM products WHERE id = ?";
if ($stmt_delete = $conn->prepare($sql_delete)) {
    $stmt_delete->bind_param("i", $id);

    if ($stmt_delete->execute()) {
        $_SESSION['success_message'] = "محصول با موفقیت حذف شد.";
    } else {
        $_SESSION['error_message'] = "خطا در حذف محصول: " . $conn->error;
    }
    $stmt_delete->close();
} else {
    $_SESSION['error_message'] = "خطا در آماده‌سازی کوئری حذف: " . $conn->error;
}

$conn->close();

header("location: products.php");
exit;
?>