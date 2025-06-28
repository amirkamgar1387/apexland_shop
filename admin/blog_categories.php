<?php
$page_title = "مدیریت دسته‌بندی‌های وبلاگ";
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

// دریافت دسته‌بندی‌های وبلاگ
$sql = "SELECT * FROM blog_categories ORDER BY created_at DESC";
$result = $conn->query($sql);
$blog_categories = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $blog_categories[] = $row;
    }
}
?>
<div class="container mx-auto px-2 md:px-8">
    <!-- پیام موفقیت/خطا -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="glass-admin bg-green-50 border border-green-200 text-green-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
            <i class="fas fa-check-circle text-2xl"></i>
            <div>
                <div class="font-bold">موفقیت</div>
                <div><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="glass-admin bg-red-50 border border-red-200 text-red-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
            <div>
                <div class="font-bold">خطا</div>
                <div><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <!-- فرم افزودن دسته‌بندی وبلاگ -->
    <div class="glass-admin p-8 rounded-2xl shadow-xl mb-8 border border-blue-100">
        <h2 class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i> افزودن دسته‌بندی جدید وبلاگ</h2>
        <form action="blog_category_add_process.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="name" class="form-label">نام دسته‌بندی</label>
                    <input type="text" id="name" name="name" class="form-control-custom" required>
                </div>
                <div>
                    <label for="description" class="form-label">توضیح کوتاه</label>
                    <input type="text" id="description" name="description" class="form-control-custom">
                </div>
                <div>
                    <label for="image" class="form-label">تصویر شاخص</label>
                    <div class="flex flex-col gap-2">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-32 bg-blue-50 border-2 border-dashed border-blue-200 rounded-xl cursor-pointer hover:bg-blue-100 transition group">
                            <span class="text-blue-500 text-3xl mb-2"><i class="fas fa-image"></i></span>
                            <span class="text-gray-500 group-hover:text-blue-700 text-sm">انتخاب یا کشیدن تصویر شاخص</span>
                            <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewBlogCategoryImage(event)">
                        </label>
                        <div id="imagePreview" class="w-24 h-24 rounded-xl border border-gray-200 mt-2 hidden overflow-hidden">
                            <img src="" alt="پیش‌نمایش تصویر" class="w-full h-full object-cover" id="previewImg">
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" class="btn-primary-blue flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition">
                    <i class="fas fa-plus"></i> افزودن دسته
                </button>
            </div>
        </form>
    </div>
    <!-- جدول لیست دسته‌بندی‌های وبلاگ -->
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-blue-100">
        <h2 class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fas fa-list-alt text-blue-500"></i> لیست دسته‌بندی‌های وبلاگ</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-right rounded-xl overflow-hidden">
                <thead class="bg-blue-50 text-blue-700">
                    <tr>
                        <th class="p-4">تصویر</th>
                        <th class="p-4">نام دسته‌بندی</th>
                        <th class="p-4">توضیح کوتاه</th>
                        <th class="p-4">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($blog_categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center p-8 text-gray-400">هیچ دسته‌بندی یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($blog_categories as $category): ?>
                        <tr class="border-b last:border-b-0 hover:bg-blue-50 transition">
                            <td class="p-4">
                                <?php if (!empty($category['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-16 h-16 object-cover rounded-xl border border-gray-200 shadow">
                                <?php else: ?>
                                    <img src="../assets/images/default-blog-category.svg" alt="بدون تصویر" class="w-16 h-16 object-cover rounded-xl border border-gray-200">
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-bold text-lg text-gray-800"><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="p-4 text-gray-600"><?php echo htmlspecialchars($category['description']); ?></td>
                            <td class="p-4 flex gap-2">
                                <a href="blog_category_edit.php?id=<?php echo $category['id']; ?>" class="flex items-center gap-1 text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition"><i class="fas fa-edit"></i> ویرایش</a>
                                <a href="blog_category_delete_process.php?id=<?php echo $category['id']; ?>" onclick="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟')" class="flex items-center gap-1 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition"><i class="fas fa-trash"></i> حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function previewBlogCategoryImage(event) {
    const input = event.target;
    const previewBox = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '';
        previewBox.classList.add('hidden');
    }
}
</script>
<?php
require_once '../templates/admin_footer.php';
?>