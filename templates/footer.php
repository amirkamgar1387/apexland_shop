<!-- Footer -->
<footer class="bg-gray-900 text-white pt-12 pb-6 mt-12 border-t border-gray-800">
    <div class="container-main mx-auto px-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-10 md:gap-0">
            <!-- Logo & About -->
            <div class="flex flex-col items-center md:items-start gap-4 md:w-1/3">
                <img src="assets/icon/apexland.png" alt="ApexLand Shop Logo" class="h-20 w-20 rounded-full shadow-lg bg-white p-2">
                <h2 class="text-2xl font-extrabold">فروشگاه اپکس لند</h2>
                <p class="text-gray-300 text-sm leading-relaxed text-center md:text-right">
                    مرجع تخصصی خرید قطعات و لوازم دیجیتال با تضمین اصالت کالا و بهترین قیمت. تجربه خریدی مطمئن، سریع و حرفه‌ای را با ما داشته باشید.
                </p>
            </div>
            <!-- Links -->
            <div class="flex flex-col items-center gap-3 md:w-1/3">
                <h3 class="font-bold text-lg mb-2">دسترسی سریع</h3>
                <div class="flex flex-col gap-2">
                    <a href="index.php" class="hover:text-yellow-400 transition">صفحه اصلی</a>
                    <a href="all_products.php" class="hover:text-yellow-400 transition">محصولات</a>
                    <a href="cart.php" class="hover:text-yellow-400 transition">سبد خرید</a>
                    <a href="admin/login.php" class="hover:text-yellow-400 transition">ورود ادمین</a>
                </div>
            </div>
            <!-- Contact & Social -->
            <div class="flex flex-col items-center gap-3 md:w-1/3">
                <h3 class="font-bold text-lg mb-2">ارتباط با ما</h3>
                <div class="flex flex-col gap-1 text-gray-300 text-sm">
                    <span><i class="fas fa-map-marker-alt ml-2"></i>شیراز ، بلوار مدرس ، احمدآباد صغاد</span>
                    <span><i class="fas fa-phone ml-2"></i> 09917671038</span>
                    <span><i class="fas fa-envelope ml-2"></i> kamgaramir1387@gmail.com</span>
                </div>
                <div class="flex gap-4 mt-3">
                    <a href="#" class="hover:text-yellow-400 text-2xl" title="اینستاگرام"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-yellow-400 text-2xl" title="تلگرام"><i class="fab fa-telegram-plane"></i></a>
                    <a href="#" class="hover:text-yellow-400 text-2xl" title="واتساپ"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <!-- Blog -->
            <div class="flex flex-col items-center gap-3 md:w-1/3">
                <h3 class="font-bold text-lg mb-2">وبلاگ</h3>
                <div class="flex flex-col gap-2">
                    <a href="blog.php" class="hover:text-blue-400 transition font-bold">همه مقالات</a>
                    <?php
                    $blog_cat_sql = "SELECT * FROM blog_categories ORDER BY name ASC LIMIT 4";
                    $blog_cat_result = $conn->query($blog_cat_sql);
                    if ($blog_cat_result && $blog_cat_result->num_rows > 0):
                        while($cat = $blog_cat_result->fetch_assoc()): ?>
                            <a href="blog_category.php?id=<?php echo $cat['id']; ?>" class="hover:text-blue-400 transition"><?php echo htmlspecialchars($cat['name']); ?></a>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm flex flex-col md:flex-row md:justify-between md:items-center gap-2">
            <span>&copy; <?php echo date('Y'); ?> فروشگاه اپکس لند. تمام حقوق محفوظ است.</span>
            <span>طراحی و توسعه با <i class="fas fa-heart text-red-500"></i> توسط تیم امیرمحمد کامگار</span>
        </div>
    </div>
</footer> 