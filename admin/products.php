<?php
$page_title = "مدیریت محصولات";
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

// Fetch products with their category names
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<div class="container mx-auto px-2 md:px-8">
    <!-- Success/Error Messages -->
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
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
        <h1 class="text-2xl font-extrabold text-yellow-700 flex items-center gap-2"><i class="fas fa-list-alt text-yellow-500"></i> لیست محصولات</h1>
        <a href="product_add.php" class="btn-primary-gold flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition">
            <i class="fas fa-plus"></i> افزودن محصول جدید
        </a>
    </div>
    <!-- Products List Table -->
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-yellow-100">
        <div class="overflow-x-auto">
            <table class="w-full text-right rounded-xl overflow-hidden">
                <thead class="bg-yellow-50 text-yellow-700">
                    <tr>
                        <th class="p-4">تصویر</th>
                        <th class="p-4">نام محصول</th>
                        <th class="p-4">دسته‌بندی</th>
                        <th class="p-4">قیمت (دلار)</th>
                        <th class="p-4">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="5" class="text-center p-8 text-gray-400">هیچ محصولی یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr class="border-b last:border-b-0 hover:bg-yellow-50 transition">
                            <td class="p-4">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-16 h-16 object-cover rounded-xl border border-gray-200 shadow">
                                <?php else: ?>
                                    <img src="../assets/images/default-product.svg" alt="بدون تصویر" class="w-16 h-16 object-cover rounded-xl border border-gray-200">
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-bold text-lg text-gray-800"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="p-4 text-gray-600"><?php echo htmlspecialchars($product['category_name'] ?? 'بدون دسته‌بندی'); ?></td>
                            <td class="p-4 text-blue-700 font-bold">$<?php echo number_format($product['price_usd'], 2); ?></td>
                            <td class="p-4 flex gap-2">
                                <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="flex items-center gap-1 text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition"><i class="fas fa-edit"></i> ویرایش</a>
                                <a href="product_delete_process.php?id=<?php echo $product['id']; ?>" onclick="return confirm('آیا از حذف این محصول مطمئن هستید؟ این عملیات قابل بازگشت نیست.')" class="flex items-center gap-1 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition"><i class="fas fa-trash"></i> حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../templates/admin_footer.php';
?> 