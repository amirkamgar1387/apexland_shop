<?php
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

// دریافت مقالات وبلاگ همراه با نام دسته‌بندی
$sql = "SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$posts = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>
<div class="container mx-auto px-2 md:px-8">
    <div class="flex justify-between items-center mb-8 mt-8">
        <h2 class="text-2xl font-extrabold text-blue-700 flex items-center gap-2"><i class="fas fa-newspaper text-blue-500"></i> مدیریت مقالات وبلاگ</h2>
        <a href="blog_post_add.php" class="btn-primary-blue flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition"><i class="fas fa-plus"></i> افزودن مقاله جدید</a>
    </div>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="glass-admin bg-green-50 border border-green-200 text-green-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
            <i class="fas fa-check-circle text-2xl"></i>
            <div><div class="font-bold">موفقیت</div><div><?= htmlspecialchars($_SESSION['success_message']) ?></div></div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="glass-admin bg-red-50 border border-red-200 text-red-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
            <div><div class="font-bold">خطا</div><div><?= htmlspecialchars($_SESSION['error_message']) ?></div></div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-blue-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right rounded-xl overflow-hidden">
                <thead class="bg-blue-50 text-blue-700">
                    <tr>
                        <th class="p-4">تصویر</th>
                        <th class="p-4">عنوان</th>
                        <th class="p-4">دسته‌بندی</th>
                        <th class="p-4">تاریخ</th>
                        <th class="p-4">وضعیت</th>
                        <th class="p-4">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="6" class="text-center p-8 text-gray-400">هیچ مقاله‌ای یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <tr class="border-b last:border-b-0 hover:bg-blue-50 transition">
                            <td class="p-4">
                                <?php if (!empty($post['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-16 h-16 object-cover rounded-xl border border-gray-200 shadow">
                                <?php else: ?>
                                    <img src="../assets/images/default-blog-post.svg" alt="بدون تصویر" class="w-16 h-16 object-cover rounded-xl border border-gray-200">
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-bold text-lg text-gray-800"><?= htmlspecialchars($post['title']) ?></td>
                            <td class="p-4 text-blue-700 font-bold"><?= htmlspecialchars($post['category_name']) ?></td>
                            <td class="p-4 text-gray-600"><?= htmlspecialchars(date('Y/m/d H:i', strtotime($post['created_at']))) ?></td>
                            <td class="p-4 text-center align-middle">
                                <?php if ($post['status'] == 'published'): ?>
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full">
                                        <i class="fas fa-check-circle text-green-500"></i> منتشر شده
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 bg-gray-200 text-gray-700 text-sm font-bold px-3 py-1 rounded-full">
                                        <i class="fas fa-clock text-gray-500"></i> پیش‌نویس
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 flex gap-2">
                                <a href="blog_post_edit.php?id=<?= $post['id'] ?>" class="flex items-center gap-1 text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition"><i class="fas fa-edit"></i> ویرایش</a>
                                <a href="blog_post_delete_process.php?id=<?= $post['id'] ?>" onclick="return confirm('آیا از حذف این مقاله مطمئن هستید؟')" class="flex items-center gap-1 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition"><i class="fas fa-trash"></i> حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../templates/admin_footer.php'; ?> 