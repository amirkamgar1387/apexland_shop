<?php
require_once __DIR__ . '/../conn.php';
require_once __DIR__ . '/../cart_logic.php';
// --- دریافت همه دسته‌بندی‌ها با تعداد محصول ---
$cat_sql = "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY product_count DESC";
$cat_result = $conn->query($cat_sql);
$all_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $all_categories[] = $row;
    }
}
// دریافت دسته‌بندی‌های وبلاگ
$blog_cat_sql = "SELECT * FROM blog_categories ORDER BY name ASC";
$blog_cat_result = $conn->query($blog_cat_sql);
$blog_categories = [];
if ($blog_cat_result && $blog_cat_result->num_rows > 0) {
    while($row = $blog_cat_result->fetch_assoc()) {
        $blog_categories[] = $row;
    }
}
$cart_item_count = getCartItemCount();
?>
<!-- Header -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
    <nav class="container-main mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-x-3">
            <img src="assets/icon/apexland.png" alt="ApexLand Shop Logo" class="h-14">
            <span class="text-xl font-bold text-gray-800 hidden sm:block">فروشگاه اپکس لند</span>
        </a>
        <div class="flex-1 flex justify-center">
            <div class="flex items-center gap-6 hidden md:flex">
                <a href="index.php" class="text-gray-700 hover:text-yellow-500 font-bold">صفحه اصلی</a>
                <a href="all_products.php" class="text-gray-700 hover:text-yellow-500 font-bold">محصولات</a>
                <div class="relative group">
                    <button class="text-gray-700 hover:text-yellow-500 font-bold flex items-center gap-1 focus:outline-none">
                        دسته بندی محصولات <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto hover:opacity-100 hover:visible hover:pointer-events-auto invisible pointer-events-none transition z-50">
                        <?php foreach (array_slice($all_categories, 0, 6) as $cat): ?>
                            <a href="category.php?id=<?php echo $cat['id']; ?>" class="block px-4 py-2 text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 rounded-xl transition">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- منوی وبلاگ -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 font-bold flex items-center gap-1 focus:outline-none">
                        وبلاگ <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 w-48 bg-white rounded-xl shadow-lg border border-blue-100 opacity-0 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto hover:opacity-100 hover:visible hover:pointer-events-auto invisible pointer-events-none transition z-50">
                        <a href="blog.php" class="block px-4 py-2 text-blue-700 hover:bg-blue-50 hover:text-blue-800 rounded-xl transition font-bold">همه مقالات</a>
                        <?php foreach (array_slice($blog_categories, 0, 6) as $cat): ?>
                            <a href="blog_category.php?id=<?php echo $cat['id']; ?>" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- موبایل: منوی همبرگری -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-btn" class="text-gray-700 hover:text-yellow-500 focus:outline-none text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="cart.php" class="text-gray-600 hover:text-yellow-500 relative">
                <i class="fas fa-shopping-cart fa-lg"></i>
                <span id="cart-item-count"
                    class="absolute -top-2 -right-3 bg-yellow-500 text-white rounded-full px-2 py-1 text-xs"><?php echo $cart_item_count; ?></span>
            </a>
            <a href="admin/login.php" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded-xl shadow transition text-sm">ورود به پنل</a>
        </div>
    </nav>
    <!-- موبایل: منوی کشویی -->
    <div id="mobile-menu" class="fixed top-0 right-0 w-64 h-full bg-white shadow-lg z-50 transform translate-x-full transition-transform duration-300 md:hidden">
        <div class="flex flex-col gap-6 p-6">
            <button id="mobile-menu-close" class="self-end text-gray-700 text-2xl focus:outline-none mb-4"><i class="fas fa-times"></i></button>
            <a href="index.php" class="text-gray-700 hover:text-yellow-500 font-bold">صفحه اصلی</a>
            <a href="all_products.php" class="text-gray-700 hover:text-yellow-500 font-bold">محصولات</a>
            <div class="relative group">
                <button class="text-gray-700 hover:text-yellow-500 font-bold flex items-center gap-1 focus:outline-none">
                    دسته بندی محصولات <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mt-2 w-full bg-white rounded-xl shadow-lg border border-gray-100">
                    <?php foreach (array_slice($all_categories, 0, 6) as $cat): ?>
                        <a href="category.php?id=<?php echo $cat['id']; ?>" class="block px-4 py-2 text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 rounded-xl transition">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- منوی وبلاگ موبایل -->
            <div class="relative group">
                <button class="text-gray-700 hover:text-blue-600 font-bold flex items-center gap-1 focus:outline-none">
                    وبلاگ <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mt-2 w-full bg-white rounded-xl shadow-lg border border-blue-100">
                    <a href="blog.php" class="block px-4 py-2 text-blue-700 hover:bg-blue-50 hover:text-blue-800 rounded-xl transition font-bold">همه مقالات</a>
                    <?php foreach (array_slice($blog_categories, 0, 6) as $cat): ?>
                        <a href="blog_category.php?id=<?php echo $cat['id']; ?>" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="cart.php" class="text-gray-700 hover:text-yellow-500 font-bold">سبد خرید</a>
            <a href="admin/login.php" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded-xl shadow transition text-sm">ورود به پنل</a>
        </div>
    </div>
    <script>
        // موبایل: باز و بسته کردن منو
        document.addEventListener('DOMContentLoaded', function () {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const closeBtn = document.getElementById('mobile-menu-close');
            menuBtn && menuBtn.addEventListener('click', () => {
                menu.classList.remove('translate-x-full');
            });
            closeBtn && closeBtn.addEventListener('click', () => {
                menu.classList.add('translate-x-full');
            });
            // بستن منو با کلیک بیرون
            menu.addEventListener('click', function(e) {
                if (e.target === menu) menu.classList.add('translate-x-full');
            });
        });
    </script>
</header> 