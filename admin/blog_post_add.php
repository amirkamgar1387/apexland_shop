<?php
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';
// دریافت دسته‌بندی‌های وبلاگ
$sql = "SELECT * FROM blog_categories ORDER BY name ASC";
$result = $conn->query($sql);
$categories = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<div class="container mx-auto px-2 md:px-8">
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-blue-100 mb-8 max-w-3xl mx-auto">
        <h1 class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i> فرم افزودن مقاله جدید</h1>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="glass-admin bg-red-50 border border-red-200 text-red-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
                <div><div class="font-bold">خطا</div><div><?= htmlspecialchars($_SESSION['error_message']) ?></div></div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <form action="blog_post_add_process.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="form-label">عنوان مقاله</label>
                    <input type="text" id="title" name="title" class="form-control-custom" required>
                </div>
                <div>
                    <label for="category_id" class="form-label">دسته‌بندی</label>
                    <select id="category_id" name="category_id" class="form-control-custom" required>
                        <option value="">انتخاب کنید...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <label for="summary" class="form-label">خلاصه مقاله</label>
                <textarea id="summary" name="summary" class="form-control-custom" rows="3" required></textarea>
            </div>
            <div class="mt-2">
                <label for="content" class="form-label">متن کامل مقاله</label>
                <textarea id="content" name="content" class="form-control-custom" rows="8" required></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                <div>
                    <label for="image" class="form-label">تصویر شاخص مقاله</label>
                    <div class="flex flex-col gap-2">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-32 bg-blue-50 border-2 border-dashed border-blue-200 rounded-xl cursor-pointer hover:bg-blue-100 transition group">
                            <span class="text-blue-500 text-3xl mb-2"><i class="fas fa-image"></i></span>
                            <span class="text-gray-500 group-hover:text-blue-700 text-sm">انتخاب یا کشیدن تصویر شاخص</span>
                            <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewBlogPostImage(event)">
                        </label>
                        <div id="imagePreview" class="w-24 h-24 rounded-xl border border-gray-200 mt-2 hidden overflow-hidden">
                            <img src="" alt="پیش‌نمایش تصویر" class="w-full h-full object-cover" id="previewImg">
                        </div>
                    </div>
                </div>
                <div>
                    <label for="status" class="form-label">وضعیت انتشار</label>
                    <select id="status" name="status" class="form-control-custom" required>
                        <option value="published">منتشر شده</option>
                        <option value="draft">پیش‌نویس</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 items-center mt-4 border-t pt-6">
                <button type="submit" class="btn-primary-blue flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition">
                    <i class="fas fa-save"></i> افزودن مقاله
                </button>
                <a href="blog_posts.php" class="flex items-center gap-2 px-8 py-3 rounded-xl border border-blue-300 bg-white text-blue-700 font-bold shadow hover:bg-blue-50 transition">
                    <i class="fas fa-arrow-right"></i> انصراف
                </a>
            </div>
        </form>
    </div>
</div>
<script>
function previewBlogPostImage(event) {
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
<?php require_once '../templates/admin_footer.php'; ?> 