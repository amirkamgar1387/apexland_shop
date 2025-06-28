<?php
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

// دریافت id محصول
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id <= 0) {
    $_SESSION['error_message'] = 'محصول نامعتبر است.';
    header('Location: products.php');
    exit;
}

// دریافت اطلاعات محصول
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
if (!$product) {
    $_SESSION['error_message'] = 'محصول یافت نشد.';
    header('Location: products.php');
    exit;
}

// دریافت دسته‌بندی‌ها
$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($sql);
$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<div class="container mx-auto px-2 md:px-8">
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-yellow-100 mb-8">
        <h1 class="text-2xl font-extrabold text-yellow-700 mb-6 flex items-center gap-2"><i class="fas fa-edit text-yellow-500"></i> ویرایش محصول</h1>
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
        <form action="product_edit_process.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="form-label">نام محصول</label>
                    <input type="text" id="name" name="name" class="form-control-custom" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                <div>
                    <label for="category_id" class="form-label">دسته‌بندی</label>
                    <select id="category_id" name="category_id" class="form-control-custom" required>
                        <option value="">یک دسته‌بندی انتخاب کنید</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <label for="short_description" class="form-label">توضیحات مختصر</label>
                <textarea id="short_description" name="short_description" rows="3" class="form-control-custom"><?= htmlspecialchars($product['short_description']) ?></textarea>
            </div>
            <div class="mt-2">
                <label for="full_description" class="form-label">توضیحات کامل</label>
                <textarea id="full_description" name="full_description" rows="8" class="form-control-custom"><?= htmlspecialchars($product['full_description']) ?></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                <div>
                    <label for="price_usd" class="form-label">قیمت (دلار)</label>
                    <input type="number" id="price_usd" name="price_usd" class="form-control-custom" step="0.01" required value="<?= htmlspecialchars($product['price_usd']) ?>">
                </div>
                <div>
                    <label for="image" class="form-label">عکس شاخص محصول</label>
                    <div class="flex flex-col gap-2">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-32 bg-yellow-50 border-2 border-dashed border-yellow-200 rounded-xl cursor-pointer hover:bg-yellow-100 transition group">
                            <span class="text-yellow-500 text-3xl mb-2"><i class="fas fa-image"></i></span>
                            <span class="text-gray-500 group-hover:text-yellow-700 text-sm">انتخاب یا کشیدن عکس جدید (اختیاری)</span>
                            <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewProductImage(event)">
                        </label>
                        <div id="imagePreview" class="w-24 h-24 rounded-xl border border-gray-200 mt-2 overflow-hidden<?= !empty($product['image']) ? '' : ' hidden' ?>">
                            <img src="<?= !empty($product['image']) ? '../' . htmlspecialchars($product['image']) : '' ?>" alt="پیش‌نمایش تصویر" class="w-full h-full object-cover" id="previewImg">
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
                <div>
                    <label for="stock" class="form-label">موجودی</label>
                    <input type="number" id="stock" name="stock" class="form-control-custom" min="0" value="<?= htmlspecialchars($product['stock']) ?>">
                </div>
                <div>
                    <label for="brand" class="form-label">برند</label>
                    <input type="text" id="brand" name="brand" class="form-control-custom" value="<?= htmlspecialchars($product['brand']) ?>">
                </div>
                <div>
                    <label for="features" class="form-label">ویژگی‌ها</label>
                    <textarea id="features" name="features" rows="3" class="form-control-custom"><?= htmlspecialchars($product['features']) ?></textarea>
                </div>
            </div>
            <div class="flex gap-3 items-center mt-4 border-t pt-6">
                <button type="submit" class="btn-primary-gold flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition">
                    <i class="fas fa-save"></i> ذخیره تغییرات
                </button>
                <a href="products.php" class="flex items-center gap-2 px-8 py-3 rounded-xl border border-yellow-300 bg-white text-yellow-700 font-bold shadow hover:bg-yellow-50 transition">
                    <i class="fas fa-arrow-right"></i> بازگشت
                </a>
            </div>
        </form>
    </div>
</div>
<script>
function previewProductImage(event) {
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