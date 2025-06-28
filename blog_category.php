<?php
require_once 'conn.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('دسته‌بندی مورد نظر یافت نشد.');
}
$category_id = intval($_GET['id']);
$stmt = $conn->prepare('SELECT * FROM blog_categories WHERE id = ?');
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('دسته‌بندی مورد نظر یافت نشد.');
}
$category = $result->fetch_assoc();
$stmt->close();
// دریافت مقالات این دسته‌بندی
$stmt = $conn->prepare('SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.status = "published" ORDER BY p.created_at DESC');
$stmt->bind_param('i', $category_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$posts = [];
if ($posts_result && $posts_result->num_rows > 0) {
    while($row = $posts_result->fetch_assoc()) {
        $posts[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($category['name']); ?> | وبلاگ اپکس لند</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-50">
<?php include 'templates/header.php'; ?>
<main class="container-main mx-auto px-6 py-12">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row gap-8 mb-10 items-center">
            <?php if (!empty($category['image'])): ?>
                <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-full md:w-56 h-40 object-cover rounded-xl border border-blue-100">
            <?php endif; ?>
            <div class="flex-1 flex flex-col gap-2">
                <h1 class="text-3xl font-extrabold text-blue-800 mb-2 flex items-center gap-2"><i class="fas fa-folder-open text-blue-500"></i> <?php echo htmlspecialchars($category['name']); ?></h1>
                <?php if (!empty($category['description'])): ?>
                    <div class="bg-blue-50 text-blue-800 rounded-xl p-3 text-base font-bold mb-2">
                        <?php echo nl2br(htmlspecialchars($category['description'])); ?>
                    </div>
                <?php endif; ?>
                <a href="blog.php" class="btn-primary-blue w-max flex items-center gap-2 mt-2"><i class="fas fa-arrow-right"></i> بازگشت به همه مقالات</a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($posts)): ?>
                <div class="col-span-full text-center text-gray-400 py-16">هنوز مقاله‌ای در این دسته‌بندی ثبت نشده است.</div>
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
    </div>
</main>
<?php require_once 'templates/footer.php'; ?>
</body>
</html> 