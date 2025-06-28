<?php
require_once 'conn.php';
require_once 'cart_logic.php';

// دریافت اطلاعات دسته‌بندی بر اساس id
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cat_stmt = $conn->prepare('SELECT * FROM categories WHERE id = ?');
$cat_stmt->bind_param('i', $category_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
if ($cat_result->num_rows === 0) {
    die('دسته‌بندی مورد نظر یافت نشد.');
}
$category = $cat_result->fetch_assoc();
$cat_stmt->close();

// دریافت محصولات این دسته‌بندی
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$stmt->close();

// همه دسته‌بندی‌ها برای منو
$cat_sql = "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY product_count DESC";
$cat_result = $conn->query($cat_sql);
$all_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $all_categories[] = $row;
    }
}
$cart_item_count = getCartItemCount();

$dollar_rate = getNavasanDollarRate();
if ($dollar_rate === false) {
    $dollar_rate = 830000;
} else {
    $dollar_rate = $dollar_rate * 10;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> | فروشگاه اپکس لند</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-50">
<?php include 'templates/header.php'; ?>
<main class="container-main mx-auto px-6 py-12">
    <!-- کامپوننت معرفی دسته‌بندی -->
    <section class="flex flex-col md:flex-row items-center justify-center gap-6 bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-100">
        <div class="flex-shrink-0 flex items-center justify-center">
            <?php if (!empty($category['image'])): ?>
                <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-28 h-28 md:w-32 md:h-32 rounded-full shadow-md object-cover bg-gray-50">
            <?php else: ?>
                <img src="assets/images/default-category.svg" alt="بدون تصویر" class="w-full h-full object-cover">
            <?php endif; ?>
        </div>
        <div class="text-center md:text-right">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-2"><?php echo htmlspecialchars($category['name']); ?></h1>
            <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                <?php echo htmlspecialchars($category['description']); ?>
            </p>
        </div>
    </section>
    <!-- لیست محصولات این دسته‌بندی -->
    <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php if (empty($products)): ?>
            <p class="text-center col-span-full">فعلا محصولی برای نمایش وجود ندارد.</p>
        <?php else: ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="bg-white rounded-2xl shadow-md flex flex-col overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-xl duration-300">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                        <div class="w-full aspect-[4/3] bg-gray-100 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                            <?php else: ?>
                                <img src="assets/images/default-product.svg" alt="بدون تصویر" class="w-24 h-24 mx-auto">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="flex flex-col flex-1 p-4">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <h2 class="text-lg font-bold text-gray-900 mb-1 truncate"><?php echo htmlspecialchars($product['name']); ?></h2>
                        </a>
                        <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($product['short_description']); ?></p>
                        <div class="mt-auto flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                    <?php echo $product['price_usd']; ?> $
                                </span>
                                <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                    <?php echo number_format($product['price_usd'] * $dollar_rate); ?> ریال
                                </span>
                            </div>
                            <div class="flex flex-row gap-2 mt-2">
                                <a href="product.php?id=<?php echo $product['id']; ?>"
                                   class="btn-primary-gold flex-1 text-center text-sm py-2 px-2">مشاهده جزئیات</a>
                                <button type="button"
                                        class="btn-primary-gold flex-1 add-to-cart-btn flex items-center justify-center gap-1 text-sm py-2 px-2"
                                        data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                    سبد خرید
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- موبایل و تبلت: کاروسل افقی -->
    <div class="flex md:hidden gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-2 mb-8">
        <?php if (empty($products)): ?>
            <p class="text-center w-full">فعلا محصولی برای نمایش وجود ندارد.</p>
        <?php else: ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="bg-white rounded-2xl shadow-md flex flex-col overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-xl duration-300 w-72 flex-shrink-0 snap-start mx-auto">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                        <div class="w-full aspect-[4/3] bg-gray-100 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                            <?php else: ?>
                                <img src="assets/images/default-product.svg" alt="بدون تصویر" class="w-24 h-24 mx-auto">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="flex flex-col flex-1 p-4">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <h2 class="text-lg font-bold text-gray-900 mb-1 truncate"><?php echo htmlspecialchars($product['name']); ?></h2>
                        </a>
                        <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($product['short_description']); ?></p>
                        <div class="mt-auto flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                    <?php echo $product['price_usd']; ?> $
                                </span>
                                <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                    <?php echo number_format($product['price_usd'] * $dollar_rate); ?> ریال
                                </span>
                            </div>
                            <div class="flex flex-row gap-2 mt-2">
                                <a href="product.php?id=<?php echo $product['id']; ?>"
                                   class="btn-primary-gold flex-1 text-center text-sm py-2 px-2">مشاهده جزئیات</a>
                                <button type="button"
                                        class="btn-primary-gold flex-1 add-to-cart-btn flex items-center justify-center gap-1 text-sm py-2 px-2"
                                        data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                    سبد خرید
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</main>
<?php include 'templates/footer.php'; ?>
</body>
</html> 