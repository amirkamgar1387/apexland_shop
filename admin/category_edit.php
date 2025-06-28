<?php
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

// دریافت id دسته‌بندی
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($category_id <= 0) {
    $_SESSION['error_message'] = 'دسته‌بندی نامعتبر است.';
    header('Location: categories.php');
    exit;
}

// دریافت اطلاعات دسته‌بندی از دیتابیس
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();
if (!$category) {
    $_SESSION['error_message'] = 'دسته‌بندی یافت نشد.';
    header('Location: categories.php');
    exit;
}

// پیام موفقیت/خطا
$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<div class="container mx-auto px-2 md:px-8">
    <div class="flex flex-col md:flex-row gap-8 mt-8">
        <div class="flex-1">
            <div class="glass-admin p-8 rounded-2xl shadow-xl border border-yellow-100 mb-8">
                <h2 class="text-2xl font-extrabold text-yellow-700 mb-6 flex items-center gap-2"><i class="fas fa-edit text-yellow-500"></i> ویرایش دسته‌بندی</h2>
                <?php if ($success): ?>
                    <div class="glass-admin bg-green-50 border border-green-200 text-green-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <div><div class="font-bold">موفقیت</div><div><?= htmlspecialchars($success) ?></div></div>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="glass-admin bg-red-50 border border-red-200 text-red-800 flex items-center gap-2 p-4 mb-6 rounded-xl shadow">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                        <div><div class="font-bold">خطا</div><div><?= htmlspecialchars($error) ?></div></div>
                    </div>
                <?php endif; ?>
                <form action="category_edit_process.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="id" value="<?= $category['id'] ?>">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="name" class="form-label">نام دسته‌بندی</label>
                            <input type="text" id="name" name="name" class="form-control-custom" value="<?= htmlspecialchars($category['name']) ?>" required>
                        </div>
                        <div>
                            <label for="description" class="form-label">توضیحات مختصر</label>
                            <input type="text" id="description" name="description" class="form-control-custom" value="<?= htmlspecialchars($category['description']) ?>">
                        </div>
                        <div>
                            <label for="image" class="form-label">تصویر شاخص</label>
                            <div class="flex flex-col gap-2">
                                <label for="image" class="flex flex-col items-center justify-center w-full h-32 bg-yellow-50 border-2 border-dashed border-yellow-200 rounded-xl cursor-pointer hover:bg-yellow-100 transition group">
                                    <span class="text-yellow-500 text-3xl mb-2"><i class="fas fa-image"></i></span>
                                    <span class="text-gray-500 group-hover:text-yellow-700 text-sm">انتخاب یا کشیدن تصویر جدید (اختیاری)</span>
                                    <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewCategoryImage(event)">
                                </label>
                                <div id="imagePreview" class="w-24 h-24 rounded-xl border border-gray-200 mt-2 overflow-hidden<?= !empty($category['image']) ? '' : ' hidden' ?>">
                                    <img src="<?= !empty($category['image']) ? '../' . htmlspecialchars($category['image']) : '' ?>" alt="پیش‌نمایش تصویر" class="w-full h-full object-cover" id="previewImg">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 items-center mt-2">
                        <button type="submit" class="btn-primary-gold flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition">
                            <i class="fas fa-save"></i> ذخیره تغییرات
                        </button>
                        <a href="categories.php" class="flex items-center gap-2 px-8 py-3 rounded-xl border border-yellow-300 bg-white text-yellow-700 font-bold shadow hover:bg-yellow-50 transition">
                            <i class="fas fa-arrow-right"></i> بازگشت
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function previewCategoryImage(event) {
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