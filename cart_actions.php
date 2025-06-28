<?php
header('Content-Type: application/json');
require_once 'conn.php';
require_once 'cart_logic.php';

// تشخیص نوع درخواست
$is_json = (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);

if ($is_json) {
    // دریافت داده به صورت JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $product_id = $data['product_id'] ?? 0;
    $quantity = $data['quantity'] ?? 0;
} else {
    // دریافت داده به صورت فرم POST معمولی
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
}

$response = [
    'success' => false,
    'message' => 'Invalid action.',
    'item_count' => getCartItemCount()
];

if ($action && $product_id > 0) {
    switch ($action) {
        case 'add':
            addToCart($product_id, 1);
            $response = [
                'success' => true,
                'message' => 'محصول به سبد خرید اضافه شد.',
                'item_count' => getCartItemCount()
            ];
            break;
        
        case 'remove':
            removeFromCart($product_id);
            // This case would be used on the cart page itself
            $response = [
                'success' => true,
                'message' => 'محصول از سبد خرید حذف شد.',
                'item_count' => getCartItemCount()
            ];
            break;

        case 'update':
            updateCartQuantity($product_id, $quantity);
            // This case would be used on the cart page
            $response = [
                'success' => true,
                'message' => 'سبد خرید به‌روزرسانی شد.',
                'item_count' => getCartItemCount()
            ];
            break;
    }
}

if ($is_json) {
    echo json_encode($response);
    exit;
} else {
    // اگر درخواست معمولی بود، ریدایرکت به سبد خرید
    header('Location: cart.php');
    exit;
}
?>