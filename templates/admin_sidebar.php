<!-- Sidebar -->
<aside class="hidden md:flex flex-col w-64 glass-admin text-gray-900 h-screen shadow-xl z-30 fixed top-0 right-0">
    <div class="px-6 py-6 border-b border-gray-200 flex items-center gap-x-3 bg-white/60 rounded-t-2xl">
        <img src="../assets/icon/apexland.png" alt="ApexLand Shop Logo" class="h-12 w-12 rounded-full shadow bg-white object-contain">
        <span class="font-extrabold text-xl tracking-tight text-yellow-700">ادمین اپکس لند</span>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="dashboard.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
            <i class="fas fa-tachometer-alt fa-fw ml-3 text-yellow-500 text-xl"></i>
            <span>داشبورد</span>
        </a>
        <a href="categories.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='categories.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
            <i class="fas fa-sitemap fa-fw ml-3 text-green-500 text-xl"></i>
            <span>مدیریت دسته‌بندی‌ها</span>
        </a>
        <a href="products.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='products.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
            <i class="fas fa-box-open fa-fw ml-3 text-blue-500 text-xl"></i>
            <span>مدیریت محصولات</span>
        </a>
        <a href="orders.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='orders.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
            <i class="fas fa-shopping-cart fa-fw ml-3 text-pink-500 text-xl"></i>
            <span>سفارشات</span>
        </a>
        <a href="comments.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='comments.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
            <i class="fas fa-comments fa-fw ml-3 text-purple-500 text-xl"></i>
            <span>مدیریت دیدگاه‌ها</span>
        </a>
        <!-- جداکننده بلاگ در انتها -->
        <div class="flex items-center my-4">
            <hr class="flex-1 border-blue-200">
            <span class="mx-3 text-blue-600 font-bold text-sm">بلاگ</span>
            <hr class="flex-1 border-blue-200">
        </div>
        <a href="blog_posts.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-blue-100 hover:text-blue-700 <?php if(basename($_SERVER['PHP_SELF'])=='blog_posts.php') echo 'bg-blue-50 text-blue-700'; ?>">
            <i class="fas fa-newspaper fa-fw ml-3 text-blue-500 text-xl"></i>
            <span>مقالات وبلاگ</span>
        </a>
        <a href="blog_categories.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-blue-100 hover:text-blue-700 <?php if(basename($_SERVER['PHP_SELF'])=='blog_categories.php') echo 'bg-blue-50 text-blue-700'; ?>">
            <i class="fas fa-folder-open fa-fw ml-3 text-blue-500 text-xl"></i>
            <span>دسته‌بندی‌های وبلاگ</span>
        </a>
    </nav>
    <div class="px-6 py-6 border-t border-gray-200 bg-white/60 rounded-b-2xl flex flex-col gap-3">
        <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition-all">
            <i class="fas fa-sign-out-alt fa-fw ml-3"></i>
            <span>خروج از حساب</span>
        </a>
        <a href="profile.php" class="flex items-center px-4 py-2 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-bold transition-all">
            <i class="fas fa-user-circle fa-fw ml-3"></i>
            <span>پروفایل من</span>
        </a>
    </div>
</aside>
<!-- Mobile Sidebar Button -->
<div class="md:hidden fixed top-4 right-4 z-30">
    <button id="openSidebar" class="bg-yellow-500 text-white p-3 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <i class="fas fa-bars text-xl"></i>
    </button>
</div>
<!-- Mobile Sidebar Drawer -->
<div id="mobileSidebar" class="fixed inset-0 bg-black/30 z-40 hidden">
    <div class="w-64 bg-white glass-admin h-full p-0 flex flex-col">
        <div class="px-6 py-6 border-b border-gray-200 flex items-center gap-x-3 bg-white/60 rounded-t-2xl">
            <img src="../assets/icon/apexland.png" alt="ApexLand Shop Logo" class="h-12 w-12 rounded-full shadow bg-white object-contain">
            <span class="font-extrabold text-xl tracking-tight text-yellow-700">ApexLand Admin</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="dashboard.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
                <i class="fas fa-tachometer-alt fa-fw ml-3 text-yellow-500 text-xl"></i>
                <span>داشبورد</span>
            </a>
            <a href="categories.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='categories.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
                <i class="fas fa-sitemap fa-fw ml-3 text-green-500 text-xl"></i>
                <span>مدیریت دسته‌بندی‌ها</span>
            </a>
            <a href="products.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='products.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
                <i class="fas fa-box-open fa-fw ml-3 text-blue-500 text-xl"></i>
                <span>مدیریت محصولات</span>
            </a>
            <a href="orders.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='orders.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
                <i class="fas fa-shopping-cart fa-fw ml-3 text-pink-500 text-xl"></i>
                <span>سفارشات</span>
            </a>
            <a href="comments.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-yellow-100 hover:text-yellow-700 <?php if(basename($_SERVER['PHP_SELF'])=='comments.php') echo 'bg-yellow-50 text-yellow-700'; ?>">
                <i class="fas fa-comments fa-fw ml-3 text-purple-500 text-xl"></i>
                <span>مدیریت دیدگاه‌ها</span>
            </a>
            <!-- جداکننده بلاگ در انتها -->
            <div class="flex items-center my-4">
                <hr class="flex-1 border-blue-200">
                <span class="mx-3 text-blue-600 font-bold text-sm">بلاگ</span>
                <hr class="flex-1 border-blue-200">
            </div>
            <a href="blog_posts.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-blue-100 hover:text-blue-700 <?php if(basename($_SERVER['PHP_SELF'])=='blog_posts.php') echo 'bg-blue-50 text-blue-700'; ?>">
                <i class="fas fa-newspaper fa-fw ml-3 text-blue-500 text-xl"></i>
                <span>مقالات وبلاگ</span>
            </a>
            <a href="blog_categories.php" class="flex items-center px-4 py-3 rounded-lg transition-all font-bold hover:bg-blue-100 hover:text-blue-700 <?php if(basename($_SERVER['PHP_SELF'])=='blog_categories.php') echo 'bg-blue-50 text-blue-700'; ?>">
                <i class="fas fa-folder-open fa-fw ml-3 text-blue-500 text-xl"></i>
                <span>دسته‌بندی‌های وبلاگ</span>
            </a>
        </nav>
        <div class="px-6 py-6 border-t border-gray-200 bg-white/60 rounded-b-2xl">
            <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition-all">
                <i class="fas fa-sign-out-alt fa-fw ml-3"></i>
                <span>خروج از حساب</span>
            </a>
        </div>
    </div>
</div>
<script>
    const openSidebar = document.getElementById('openSidebar');
    const mobileSidebar = document.getElementById('mobileSidebar');
    openSidebar.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
    });
    mobileSidebar.addEventListener('click', (e) => {
        if (e.target === mobileSidebar) {
            mobileSidebar.classList.add('hidden');
        }
    });
</script>

<!-- Main Content -->
<div class="flex-1 flex flex-col overflow-hidden md:mr-64">
    <!-- Header -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold"><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'پنل مدیریت'; ?></h1>
        <div class="flex items-center">
            <?php
                // Determine the name to display. Use full_name if available, otherwise username.
                $display_name = !empty($_SESSION["admin_full_name"]) ? $_SESSION["admin_full_name"] : $_SESSION["admin_username"];
                
                // Use the restored default avatar
                $photo_url = !empty($_SESSION["admin_photo"]) ? '../' . htmlspecialchars($_SESSION["admin_photo"]) : '../assets/images/default-avatar.svg';
            ?>
            <span class="ml-4">خوش آمدید، <?php echo htmlspecialchars($display_name); ?></span>
            <a href="../index.php" target="_blank" class="ml-2 px-3 py-1 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm flex items-center gap-1 font-bold transition">
                <i class="fas fa-home"></i>
                سایت
            </a>
            <img src="<?php echo $photo_url; ?>" alt="Admin" class="rounded-full w-10 h-10 object-cover border-2 border-gray-200">
        </div>
    </header>
    
    <!-- Page Content -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6"> 