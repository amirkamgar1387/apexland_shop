<?php
require_once 'conn.php';
require_once 'cart_logic.php';

// --- دریافت همه دسته‌بندی‌ها با تعداد محصول ---
$cat_sql = "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY product_count DESC";
$cat_result = $conn->query($cat_sql);
$all_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $all_categories[] = $row;
    }
}
$cart_item_count = getCartItemCount(); // شمارش آیتم‌های سبد خرید

// Handle REMOVING an item from the cart
if (isset($_GET['remove']) && !empty($_GET['remove'])) {
    $product_to_remove = intval($_GET['remove']);
    removeFromCart($product_to_remove);
    header('Location: cart.php'); // Redirect to clean the URL
    exit;
}

// Handle cart updates from form submission (e.g., changing quantities)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        updateCartQuantity($product_id, $quantity);
    }
    header('Location: cart.php'); // Redirect to avoid form resubmission
    exit;
}

// Get cart contents
$cart_items = getCartContents($conn);
$cart_total = 0;

$dollar_rate = getNavasanDollarRate();
if ($dollar_rate === false) {
    $dollar_rate = 830000;
} else {
    $dollar_rate = $dollar_rate * 10;
}
?>
<?php include 'templates/header.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبد خرید - فروشگاه اپکس لند</title>
    <!-- CSS and Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-100">

    <!-- Main Content -->
    <main class="container-main mx-auto px-2 sm:px-4 md:px-6 py-8 md:py-12">
        <h1 class="text-3xl font-bold mb-8 text-center">سبد خرید شما</h1>

        <?php if (empty($cart_items)): ?>
            <div class="text-center bg-white p-12 rounded-2xl shadow-lg max-w-xl mx-auto">
                <i class="fas fa-shopping-cart fa-4x text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-semibold mb-2">سبد خرید شما خالی است.</h2>
                <p class="text-gray-500 mb-6">به نظر می‌رسد هنوز محصولی به سبد خود اضافه نکرده‌اید.</p>
                <a href="index.php" class="btn-primary-gold px-8 py-3 rounded-full font-bold text-lg flex items-center gap-2 mx-auto w-fit">
                    <i class="fas fa-arrow-right ml-2"></i> بازگشت به فروشگاه
                </a>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="lg:w-2/3 w-full">
                    <form action="cart.php" method="POST">
                        <div class="bg-white rounded-2xl shadow-lg divide-y divide-gray-100">
                            <?php foreach ($cart_items as $item): ?>
                                <?php
                                $subtotal = $item['price_usd'] * $item['quantity'];
                                $cart_total += $subtotal;
                                ?>
                                <div class="flex flex-col sm:flex-row items-center gap-4 p-4">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-24 h-24 object-cover rounded-xl shadow">
                                    <?php else: ?>
                                        <img src="assets/images/default-product.svg" alt="بدون تصویر" class="w-24 h-24 object-cover rounded-xl shadow">
                                    <?php endif; ?>
                                    <div class="flex-grow w-full sm:w-auto text-center sm:text-right">
                                        <h3 class="font-bold text-lg text-gray-900 mb-1"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <div class="flex flex-col sm:flex-row gap-2 items-center sm:justify-start justify-center text-gray-500 text-sm mb-2">
                                            <span>قیمت واحد: <?php echo number_format($item['price_usd'], 2); ?> $</span>
                                            <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                                <?php echo number_format($item['price_usd'] * $dollar_rate); ?> ریال
                                            </span>
                                            <span class="hidden sm:inline">|</span>
                                            <span>جمع کل: <span class="font-bold text-gray-800"><?php echo number_format($subtotal, 2); ?> $</span></span>
                                            <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                                <?php echo number_format($subtotal * $dollar_rate); ?> ریال
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-row items-center gap-2 w-full sm:w-auto justify-center">
                                        <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 h-10 rounded-xl border border-gray-300 text-center font-bold text-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition shadow-sm">
                                        <a href="cart.php?remove=<?php echo $item['id']; ?>" class="text-red-500 hover:text-red-700 text-xl ml-2" title="حذف" onclick="return confirm('آیا از حذف این آیتم مطمئنید؟')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" name="update_cart" class="btn-primary-gold mt-6 px-8 py-3 rounded-full font-bold text-lg flex items-center gap-2 mx-auto w-fit">
                            <i class="fas fa-sync-alt ml-2"></i> به‌روزرسانی سبد
                        </button>
                    </form>
                </div>
                <!-- Cart Summary -->
                <div class="lg:w-1/3 w-full">
                    <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-24 flex flex-col gap-4">
                        <h2 class="text-2xl font-bold border-b pb-4 mb-4 text-center text-gray-800">جمع کل سبد خرید</h2>
                        <div class="flex justify-between mb-2 text-lg">
                            <span>جمع جزء</span>
                            <span><?php echo number_format($cart_total, 2); ?> $</span>
                            <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                <?php echo number_format($cart_total * $dollar_rate); ?> ریال
                            </span>
                        </div>
                        <div class="flex flex-col gap-2 border-t pt-4 mt-4">
                            <div class="flex justify-between items-center font-bold text-xl">
                                <span>مبلغ قابل پرداخت</span>
                                <span class="flex items-center gap-2">
                                    <span class="text-blue-700 flex items-center gap-1"><i class="fas fa-dollar-sign"></i> <?php echo number_format($cart_total, 2); ?> $</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-gray-500 text-base flex items-center gap-1"><i class="fas fa-money-bill-wave"></i> معادل ریالی</span>
                                <span class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-base border border-yellow-200 font-bold">
                                    <?php echo number_format($cart_total * $dollar_rate); ?> ریال
                                </span>
                            </div>
                        </div>
                        <button class="w-full btn-primary-gold mt-6 !bg-green-600 !border-green-600 text-lg font-bold py-3 rounded-xl flex items-center justify-center gap-2">
                            ادامه جهت تسویه حساب <i class="fas fa-arrow-left mr-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

<?php include 'templates/footer.php'; ?>
</body>
</html> 