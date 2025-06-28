<?php
require_once 'conn.php';
require_once 'cart_logic.php';
// دریافت دسته‌بندی‌ها برای فیلتر
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
$categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
// دریافت همه دسته‌بندی‌ها با تعداد محصول
$cat_sql = "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY product_count DESC";
$cat_result = $conn->query($cat_sql);
$all_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $all_categories[] = $row;
    }
}
$cart_item_count = getCartItemCount(); // شمارش آیتم‌های سبد خرید
$dollar_rate = getNavasanDollarRate();
if ($dollar_rate === false) {
    $dollar_rate = 830000;
} else {
    $dollar_rate = $dollar_rate * 10;
}
// توضیح فیلتر انتخابی
$sort_titles = [
    'newest' => ['title' => 'جدیدترین محصولات', 'desc' => 'محصولات به ترتیب جدیدترین تاریخ ثبت نمایش داده می‌شوند.'],
    'oldest' => ['title' => 'قدیمی‌ترین محصولات', 'desc' => 'محصولات به ترتیب قدیمی‌ترین تاریخ ثبت نمایش داده می‌شوند.'],
    'price_asc' => ['title' => 'ارزان‌ترین تا گران‌ترین', 'desc' => 'محصولات بر اساس قیمت از کم به زیاد مرتب شده‌اند.'],
    'price_desc' => ['title' => 'گران‌ترین تا ارزان‌ترین', 'desc' => 'محصولات بر اساس قیمت از زیاد به کم مرتب شده‌اند.'],
    'popular' => ['title' => 'محبوب‌ترین محصولات', 'desc' => 'محصولات بر اساس میزان بازدید یا محبوبیت نمایش داده می‌شوند.'],
];
$sort_key = $_GET['sort'] ?? 'newest';
$sort_info = $sort_titles[$sort_key] ?? $sort_titles['newest'];
$category_id = $_GET['category_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>همه محصولات | فروشگاه اپکس لند</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-50">
    <?php include 'templates/header.php'; ?>
    <main class="container-main mx-auto px-6 py-12">
        <!-- کامپوننت معرفی بزرگ -->
        <section class="flex flex-col md:flex-row items-center justify-center gap-6 bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-100">
            <div class="flex-shrink-0 flex items-center justify-center">
                <img src="assets/icon/apexland.png" alt="ApexLand Logo" class="w-28 h-28 md:w-32 md:h-32 rounded-full shadow-md object-contain bg-gray-50">
            </div>
            <div class="text-center md:text-right">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-2">همه محصولات فروشگاه اپکس لند</h1>
                <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                    در این بخش می‌توانید همه محصولات فروشگاه را مشاهده و با استفاده از فیلترهای متنوع، محصول مورد نظر خود را به راحتی پیدا کنید.
                </p>
            </div>
        </section>
        <!-- فیلتر محصولات -->
        <section class="mb-8">
            <form id="filter-form" method="get" class="flex flex-row flex-wrap items-center justify-center gap-6 bg-white rounded-2xl shadow-lg p-4 border border-gray-100 max-w-4xl mx-auto">
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-gray-700 font-semibold">مرتب‌سازی بر اساس:</label>
                    <select id="sort" name="sort" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition shadow-sm">
                        <option value="newest"<?= $sort_key=='newest'?' selected':''; ?>>جدیدترین</option>
                        <option value="oldest"<?= $sort_key=='oldest'?' selected':''; ?>>قدیمی‌ترین</option>
                        <option value="price_asc"<?= $sort_key=='price_asc'?' selected':''; ?>>قیمت (کم به زیاد)</option>
                        <option value="price_desc"<?= $sort_key=='price_desc'?' selected':''; ?>>قیمت (زیاد به کم)</option>
                        <option value="popular"<?= $sort_key=='popular'?' selected':''; ?>>محبوب‌ترین</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="category_id" class="text-gray-700 font-semibold">دسته‌بندی:</label>
                    <select id="category_id" name="category_id" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition shadow-sm">
                        <option value="">همه دسته‌بندی‌ها</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"<?= $category_id==$cat['id']?' selected':''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-primary-gold px-6 py-2 text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    اعمال فیلتر
                </button>
            </form>
        </section>
        <!-- عنوان و توضیح فیلتر انتخابی -->
        <div class="mb-8 text-center">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-1"><?php echo $sort_info['title']; ?></h2>
            <p class="text-gray-600 text-sm md:text-base"><?php echo $sort_info['desc']; ?></p>
        </div>
        <!-- اینجا محل قرارگیری فیلترها و لیست محصولات خواهد بود -->
        <!-- دریافت محصولات با فیلتر مرتب‌سازی و دسته‌بندی -->
        <?php
        // دریافت محصولات با فیلتر مرتب‌سازی و دسته‌بندی
        $where = '';
        if (!empty($category_id)) {
            $where = "WHERE p.category_id = " . intval($category_id);
        }
        $order_by = 'p.created_at DESC';
        if ($sort_key === 'oldest') {
            $order_by = 'p.created_at ASC';
        } elseif ($sort_key === 'price_asc') {
            $order_by = 'p.price_usd ASC';
        } elseif ($sort_key === 'price_desc') {
            $order_by = 'p.price_usd DESC';
        } elseif ($sort_key === 'popular') {
            // بعداً: مرتب‌سازی بر اساس بازدید (فعلاً غیرفعال)
            $order_by = 'p.created_at DESC';
        }
        $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id $where ORDER BY $order_by";
        $result = $conn->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        ?>
        <!-- لیست محصولات -->
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
    </main>
    <?php include 'templates/footer.php'; ?> 