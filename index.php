<?php
require_once 'conn.php';
require_once 'cart_logic.php'; // Include cart logic for item count

// Fetch products with their category names
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get the dollar to IRR rate
$dollar_rate = getNavasanDollarRate();
if ($dollar_rate === false) {
    $dollar_rate = 830000; // مقدار پیش‌فرض
} else {
    $dollar_rate = $dollar_rate * 10; // تبدیل تومان به ریال
}
$cart_item_count = getCartItemCount(); // Get initial cart count

// --- دریافت همه دسته‌بندی‌ها با تعداد محصول ---
$cat_sql = "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY product_count DESC";
$cat_result = $conn->query($cat_sql);
$all_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $all_categories[] = $row;
    }
}

// فقط ۸ محصول آخر را نمایش بده
$products = array_slice($products, 0, 8);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فروشگاه اپکس لند - جدیدترین قطعات کامپیوتر</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
    <style>
        /* Custom styles for the bento grid */
        .product-card {
            background-color: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .product-card .product-info {
            position: relative;
            z-index: 10;
        }

        .product-card .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-card-overlay .product-info {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50">

<?php include 'templates/header.php'; ?>

    <!-- Main Content - Products Grid -->
    <main class="container-main mx-auto px-6 py-12">
        <!-- معرفی فروشگاه اپکس لند با لوگو و متن -->
        <section class="flex flex-col md:flex-row items-center justify-center gap-6 bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-100">
            <div class="flex-shrink-0 flex items-center justify-center">
                <img src="assets/icon/apexland.png" alt="ApexLand Logo" class="w-28 h-28 md:w-32 md:h-32 rounded-full shadow-md object-contain bg-gray-50">
            </div>
            <div class="text-center md:text-right">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-2">فروشگاه اپکس لند</h1>
                <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                    فروشگاه اپکس لند، مرجع تخصصی خرید قطعات و لوازم دیجیتال با بهترین قیمت و تضمین اصالت کالا.<br>
                    تجربه خریدی مطمئن، سریع و حرفه‌ای را با ما داشته باشید.
                </p>
            </div>
        </section>

        <!-- عنوان و توضیح دسته‌بندی‌های فروشگاه -->
        <div class="mb-6 text-center">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">دسته‌بندی‌های فروشگاه</h2>
            <p class="text-gray-600 text-base md:text-lg">محصولات ما را بر اساس دسته‌بندی‌های متنوع و تخصصی مشاهده کنید و راحت‌تر انتخاب کنید.</p>
        </div>

        <!-- نمایش دسته‌بندی‌های برتر -->
        <!-- (کد قبلی حذف شد) -->

        <!-- نمایش همه دسته‌بندی‌ها (کاروسل در صورت زیاد بودن) -->
        <?php if (!empty($all_categories)): ?>
            <?php
                $display_categories = array_slice($all_categories, 0, 6);
            ?>
            <div class="mb-10">
                <div class="hidden md:flex flex-wrap justify-center gap-6">
                    <?php foreach ($display_categories as $cat): ?>
                        <a href="category.php?id=<?php echo $cat['id']; ?>" class="group block w-48 bg-white rounded-xl shadow hover:shadow-lg transition p-4 text-center border border-gray-100 mx-auto">
                            <div class="w-24 h-24 mx-auto mb-3 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                <?php if (!empty($cat['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                                <?php else: ?>
                                    <img src="assets/images/default-category.svg" alt="بدون تصویر" class="w-16 h-16 mx-auto">
                                <?php endif; ?>
                            </div>
                            <div class="font-bold text-gray-800 text-base mb-1"><?php echo htmlspecialchars($cat['name']); ?></div>
                            <div class="text-xs text-gray-500 mb-1"><?php echo htmlspecialchars($cat['description']); ?></div>
                            <div class="text-xs text-blue-600 font-semibold">
                                <?php echo (int)$cat['product_count']; ?> محصول
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- کاروسل فقط در موبایل و تبلت -->
                <div class="flex md:hidden gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-2">
                    <?php foreach ($display_categories as $cat): ?>
                        <a href="category.php?id=<?php echo $cat['id']; ?>" class="group block w-40 flex-shrink-0 bg-white rounded-xl shadow hover:shadow-lg transition p-4 text-center border border-gray-100 snap-start mx-auto">
                            <div class="w-20 h-20 mx-auto mb-3 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                <?php if (!empty($cat['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                                <?php else: ?>
                                    <img src="assets/images/default-category.svg" alt="بدون تصویر" class="w-16 h-16 mx-auto">
                                <?php endif; ?>
                            </div>
                            <div class="font-bold text-gray-800 text-base mb-1"><?php echo htmlspecialchars($cat['name']); ?></div>
                            <div class="text-xs text-gray-500 mb-1"><?php echo htmlspecialchars($cat['description']); ?></div>
                            <div class="text-xs text-blue-600 font-semibold">
                                <?php echo (int)$cat['product_count']; ?> محصول
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <style>
                    .scrollbar-hide::-webkit-scrollbar { display: none; }
                    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
                </style>
            </div>
        <?php endif; ?>

        <!-- عنوان و توضیح آخرین محصولات -->
        <div class="mb-6 text-center">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">آخرین محصولات</h2>
            <p class="text-gray-600 text-base md:text-lg">جدیدترین محصولات فروشگاه را مشاهده کنید و از تخفیف‌ها و پیشنهادهای ویژه بهره‌مند شوید.</p>
        </div>

        <!-- نمایش محصولات -->
        <!-- دسکتاپ: گرید فعلی، موبایل/تبلت: کاروسل افقی -->
        <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php if (empty($products)): ?>
                <p class="text-center col-span-full">فعلا محصولی برای نمایش وجود ندارد.</p>
            <?php else: ?>
                <?php foreach ($products as $index => $product): ?>
                    <!-- Apple Store Style Product Card -->
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
        <!-- دکمه دیدن همه محصولات زیر آخرین محصولات -->
        <div class="flex justify-center mt-8 mb-12">
            <a href="all_products.php" class="btn-primary-gold text-lg px-8 py-3 rounded-full shadow hover:shadow-lg transition font-bold flex items-center gap-2">
                <i class="fas fa-th-large"></i>
                دیدن همه محصولات
            </a>
        </div>

        <!-- کامپوننت مقالات وبلاگ -->
        <?php
        // دریافت ۴ مقاله آخر وبلاگ
        $blog_sql = "SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE p.status = 'published' ORDER BY p.created_at DESC LIMIT 4";
        $blog_result = $conn->query($blog_sql);
        $blog_posts = [];
        if ($blog_result && $blog_result->num_rows > 0) {
            while($row = $blog_result->fetch_assoc()) {
                $blog_posts[] = $row;
            }
        }
        ?>
        <section class="max-w-7xl mx-auto mt-16 mb-12">
            <div class="mb-8 text-center">
                <h2 class="text-xl md:text-2xl font-bold text-blue-800 mb-2 flex items-center justify-center gap-2"><i class="fas fa-newspaper text-blue-500"></i> جدیدترین مقالات وبلاگ</h2>
                <p class="text-gray-600 text-base md:text-lg">مطالب آموزشی و اخبار دنیای دیجیتال را در وبلاگ ما دنبال کنید.</p>
            </div>
            <!-- دسکتاپ: گرید ۴تایی -->
            <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                <?php if (empty($blog_posts)): ?>
                    <div class="col-span-full text-center text-gray-400 py-16">هنوز مقاله‌ای ثبت نشده است.</div>
                <?php else: ?>
                    <?php foreach ($blog_posts as $post): ?>
                    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 flex flex-col h-full p-4">
                        <a href="blog_post.php?id=<?= $post['id'] ?>">
                            <img src="<?= !empty($post['image']) ? htmlspecialchars($post['image']) : 'assets/images/default-blog-post.svg' ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-40 object-cover rounded-xl mb-4 border border-gray-200">
                        </a>
                        <div class="flex-1 flex flex-col">
                            <a href="blog_post.php?id=<?= $post['id'] ?>" class="font-extrabold text-lg text-blue-800 hover:text-blue-900 transition mb-2 line-clamp-2"><?= htmlspecialchars($post['title']) ?></a>
                            <div class="text-gray-500 text-sm mb-2 line-clamp-3"><?= htmlspecialchars($post['summary']) ?></div>
                            <div class="flex items-center gap-2 mt-auto">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-folder-open ml-1"></i> <?= htmlspecialchars($post['category_name']) ?></span>
                                <span class="text-xs text-gray-400"><i class="fas fa-clock ml-1"></i> <?= date('Y/m/d', strtotime($post['created_at'])) ?></span>
                            </div>
                            <a href="blog_post.php?id=<?= $post['id'] ?>" class="btn-primary-blue mt-4 text-center">مشاهده مقاله</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- موبایل و تبلت: کاروسل افقی -->
            <div class="flex md:hidden gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-2">
                <?php if (empty($blog_posts)): ?>
                    <p class="text-center w-full">هنوز مقاله‌ای ثبت نشده است.</p>
                <?php else: ?>
                    <?php foreach ($blog_posts as $post): ?>
                    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 flex flex-col h-full p-4 w-72 flex-shrink-0 snap-start mx-auto">
                        <a href="blog_post.php?id=<?= $post['id'] ?>">
                            <img src="<?= !empty($post['image']) ? htmlspecialchars($post['image']) : 'assets/images/default-blog-post.svg' ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-40 object-cover rounded-xl mb-4 border border-gray-200">
                        </a>
                        <div class="flex-1 flex flex-col">
                            <a href="blog_post.php?id=<?= $post['id'] ?>" class="font-extrabold text-lg text-blue-800 hover:text-blue-900 transition mb-2 line-clamp-2"><?= htmlspecialchars($post['title']) ?></a>
                            <div class="text-gray-500 text-sm mb-2 line-clamp-3"><?= htmlspecialchars($post['summary']) ?></div>
                            <div class="flex items-center gap-2 mt-auto">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-folder-open ml-1"></i> <?= htmlspecialchars($post['category_name']) ?></span>
                                <span class="text-xs text-gray-400"><i class="fas fa-clock ml-1"></i> <?= date('Y/m/d', strtotime($post['created_at'])) ?></span>
                            </div>
                            <a href="blog_post.php?id=<?= $post['id'] ?>" class="btn-primary-blue mt-4 text-center">مشاهده مقاله</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="flex justify-center mt-8">
                <a href="blog.php" class="btn-primary-blue text-lg px-8 py-3 rounded-full shadow hover:shadow-lg transition font-bold flex items-center gap-2">
                    <i class="fas fa-newspaper"></i>
                    مشاهده همه مقالات
                </a>
            </div>
        </section>
    </main>

<?php include 'templates/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const cartItemCountElement = document.getElementById('cart-item-count');

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const productId = this.dataset.productId;
                    const button = this;

                    // Disable button and show loading state
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('cart_actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'add',
                            product_id: productId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update cart count in header
                                cartItemCountElement.textContent = data.item_count;

                                // Update button state to show success
                                button.innerHTML = '<i class="fas fa-check"></i> اضافه شد';
                                // Keep it disabled to prevent re-adding
                            } else {
                                // Handle error
                                console.error('Error adding to cart:', data.message);
                                alert('خطایی در افزودن محصول به سبد رخ داد.');
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-cart-plus"></i> افزودن به سبد';
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-cart-plus"></i> افزودن به سبد';
                        });
                });
            });
        });
    </script>
</body>

</html>