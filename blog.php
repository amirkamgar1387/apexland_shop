<?php
require_once 'conn.php';
// دریافت دسته‌بندی‌های وبلاگ
$cat_sql = "SELECT * FROM blog_categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
$blog_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $blog_categories[] = $row;
    }
}
// دریافت مقالات وبلاگ
$sql = "SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE p.status = 'published' ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$posts = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وبلاگ | فروشگاه اپکس لند</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-50">
<?php include 'templates/header.php'; ?>
<div class="container-main mx-auto px-6 py-10 min-h-[60vh]">
    <h1 class="text-3xl font-extrabold text-blue-700 mb-8 flex items-center gap-2"><i class="fas fa-newspaper text-blue-500"></i> وبلاگ</h1>
    <div class="flex flex-col md:flex-row gap-8">
        <!-- سایدبار دسته‌بندی -->
        <aside class="md:w-1/4 mb-8 md:mb-0">
            <div class="glass-admin p-6 rounded-2xl shadow-xl border border-blue-100">
                <h2 class="text-lg font-bold text-blue-700 mb-4 flex items-center gap-2"><i class="fas fa-folder-open text-blue-400"></i> دسته‌بندی‌های وبلاگ</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="blog.php" class="flex items-center gap-2 font-bold text-blue-700 hover:text-blue-900 transition bg-blue-50 hover:bg-blue-100 rounded-xl px-3 py-2">
                            <img src="assets/images/default-blog-category.svg" alt="همه" class="w-6 h-6">
                            همه مقالات
                        </a>
                    </li>
                    <?php foreach ($blog_categories as $cat): ?>
                        <li>
                            <a href="blog_category.php?id=<?= $cat['id'] ?>" class="flex items-center gap-2 text-gray-700 hover:text-blue-700 transition hover:bg-blue-50 rounded-xl px-3 py-2">
                                <?php if (!empty($cat['image'])): ?>
                                    <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-6 h-6 object-cover rounded-full border border-blue-100">
                                <?php else: ?>
                                    <img src="assets/images/default-blog-category.svg" alt="بدون تصویر" class="w-6 h-6">
                                <?php endif; ?>
                                <span class="flex-1 truncate"><?= htmlspecialchars($cat['name']) ?></span>
                                <?php
                                // شمارش تعداد مقالات هر دسته
                                $count_sql = "SELECT COUNT(*) FROM blog_posts WHERE category_id = ? AND status = 'published'";
                                $stmt = $conn->prepare($count_sql);
                                $stmt->bind_param('i', $cat['id']);
                                $stmt->execute();
                                $stmt->bind_result($count);
                                $stmt->fetch();
                                $stmt->close();
                                ?>
                                <span class="bg-blue-100 text-blue-700 rounded-full px-2 py-0.5 text-xs font-bold ml-1"> <?= $count ?> </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
        <!-- لیست مقالات -->
        <section class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (empty($posts)): ?>
                    <div class="col-span-full text-center text-gray-400 py-16">هنوز مقاله‌ای ثبت نشده است.</div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="glass-admin flex flex-col h-full p-4 rounded-2xl shadow-xl border border-blue-100">
                        <a href="blog_post.php?id=<?= $post['id'] ?>">
                            <img src="<?= !empty($post['image']) ? htmlspecialchars($post['image']) : 'assets/images/default-blog-post.svg' ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-gray-200">
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
        </section>
    </div>
</div>
<?php require_once 'templates/footer.php'; ?>
</body>
</html>