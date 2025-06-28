<?php
$page_title = "داشبورد";
require_once '../templates/admin_header.php';
// The header includes the session check and initial HTML structure
// So, it's not needed to require conn.php here again.
require_once '../templates/admin_sidebar.php';

// --- Fetching Dynamic Stats from Database ---

// 1. Count total products
$sql_products = "SELECT COUNT(id) as total_products FROM products";
$result_products = $conn->query($sql_products);
$total_products = $result_products->fetch_assoc()['total_products'] ?? 0;

// 2. Count total categories
$sql_categories = "SELECT COUNT(id) as total_categories FROM categories";
$result_categories = $conn->query($sql_categories);
$total_categories = $result_categories->fetch_assoc()['total_categories'] ?? 0;

// 3. Count total orders (placeholder for now)
// We assume an 'orders' table will exist in the future.
// For now, this will be 0.
$total_orders = 0; 
// Example query for the future:
// $sql_orders = "SELECT COUNT(id) as total_orders FROM orders WHERE status = 'new'";
// $result_orders = $conn->query($sql_orders);
// $total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0;

// آمار وبلاگ
$sql_blog_posts = "SELECT COUNT(id) as total_blog_posts FROM blog_posts";
$result_blog_posts = $conn->query($sql_blog_posts);
$total_blog_posts = $result_blog_posts->fetch_assoc()['total_blog_posts'] ?? 0;

$sql_blog_categories = "SELECT COUNT(id) as total_blog_categories FROM blog_categories";
$result_blog_categories = $conn->query($sql_blog_categories);
$total_blog_categories = $result_blog_categories->fetch_assoc()['total_blog_categories'] ?? 0;

// دریافت ۵ دیدگاه آخر
$last_comments_sql = "SELECT c.*, p.name AS product_name FROM comments c LEFT JOIN products p ON c.product_id = p.id ORDER BY c.created_at DESC LIMIT 5";
$last_comments_result = $conn->query($last_comments_sql);
$last_comments = [];
if ($last_comments_result && $last_comments_result->num_rows > 0) {
    while ($row = $last_comments_result->fetch_assoc()) {
        $last_comments[] = $row;
    }
}

// دریافت ۵ مقاله آخر
$last_blog_sql = "SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5";
$last_blog_result = $conn->query($last_blog_sql);
$last_blog_posts = [];
if ($last_blog_result && $last_blog_result->num_rows > 0) {
    while ($row = $last_blog_result->fetch_assoc()) {
        $last_blog_posts[] = $row;
    }
}
?>

<!-- Main content of the dashboard -->
<div class="container mx-auto px-2 md:px-8">
    <!-- Welcome message -->
    <div class="bg-gradient-to-tr from-yellow-50 via-white to-yellow-100 p-8 rounded-2xl shadow-xl mb-8 flex flex-col md:flex-row items-center gap-6">
        <img src="../assets/icon/apexland.png" alt="ApexLand Logo" class="w-24 h-24 rounded-full shadow bg-white object-contain">
        <div>
            <h2 class="text-3xl font-extrabold text-yellow-700 mb-2">به پنل مدیریت فروشگاه خوش آمدید!</h2>
            <p class="text-gray-600 text-lg">از این پنل می‌توانید محصولات، دسته‌بندی‌ها و سفارشات را مدیریت کنید.</p>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Products Card -->
        <div class="glass-admin p-8 rounded-2xl shadow-lg flex items-center justify-between border border-yellow-100">
            <div>
                <h3 class="text-lg font-bold text-gray-500">تعداد محصولات</h3>
                <p class="text-4xl font-extrabold text-gray-800 mt-2"><?php echo $total_products; ?></p>
            </div>
            <div class="text-6xl text-blue-500">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
        <!-- Categories Card -->
        <div class="glass-admin p-8 rounded-2xl shadow-lg flex items-center justify-between border border-yellow-100">
            <div>
                <h3 class="text-lg font-bold text-gray-500">تعداد دسته‌بندی‌ها</h3>
                <p class="text-4xl font-extrabold text-gray-800 mt-2"><?php echo $total_categories; ?></p>
            </div>
            <div class="text-6xl text-green-500">
                <i class="fas fa-sitemap"></i>
            </div>
        </div>
        <!-- Orders Card -->
        <div class="glass-admin p-8 rounded-2xl shadow-lg flex items-center justify-between border border-yellow-100">
            <div>
                <h3 class="text-lg font-bold text-gray-500">سفارشات جدید</h3>
                <p class="text-4xl font-extrabold text-gray-800 mt-2"><?php echo $total_orders; ?></p>
            </div>
            <div class="text-6xl text-pink-500">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <!-- Blog Posts Card -->
        <div class="glass-admin p-8 rounded-2xl shadow-lg flex items-center justify-between border border-blue-100">
            <div>
                <h3 class="text-lg font-bold text-blue-500">تعداد مقالات وبلاگ</h3>
                <p class="text-4xl font-extrabold text-blue-700 mt-2"><?php echo $total_blog_posts; ?></p>
            </div>
            <div class="text-6xl text-blue-500">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
        <!-- Blog Categories Card -->
        <div class="glass-admin p-8 rounded-2xl shadow-lg flex items-center justify-between border border-blue-100">
            <div>
                <h3 class="text-lg font-bold text-blue-500">دسته‌بندی‌های وبلاگ</h3>
                <p class="text-4xl font-extrabold text-blue-700 mt-2"><?php echo $total_blog_categories; ?></p>
            </div>
            <div class="text-6xl text-blue-400">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>
    </div>
    <!-- آخرین دیدگاه‌ها -->
    <div class="max-w-6xl mx-auto mt-12">
        <div class="glass-admin p-14 rounded-2xl shadow-xl border border-yellow-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-extrabold text-yellow-700 flex items-center gap-2"><i class="fas fa-comments text-yellow-500"></i> آخرین دیدگاه‌ها</h2>
                <a href="comments.php" class="text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1"><i class="fas fa-arrow-left"></i> مدیریت دیدگاه‌ها</a>
            </div>
            <?php if (empty($last_comments)): ?>
                <div class="text-center text-gray-400 py-8">هنوز دیدگاهی ثبت نشده است.</div>
            <?php else: ?>
                <ul class="divide-y divide-yellow-50">
                    <?php foreach ($last_comments as $comment): ?>
                    <li class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <span class="font-bold text-gray-800"><?= htmlspecialchars($comment['name']) ?></span>
                            <span class="text-xs text-gray-400"><?= date('Y/m/d H:i', strtotime($comment['created_at'])) ?></span>
                            <span class="text-xs px-2 py-1 rounded-full <?= $comment['status'] == 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $comment['status'] == 'approved' ? 'تأیید شده' : 'در انتظار تأیید' ?></span>
                        </div>
                        <div class="text-gray-700 text-sm line-clamp-1 max-w-xs md:max-w-md" title="<?= htmlspecialchars($comment['content']) ?>">
                            <?= mb_substr(strip_tags($comment['content']), 0, 60) . (mb_strlen($comment['content']) > 60 ? '...' : '') ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            <?php if (!empty($comment['product_id'])): ?>
                                <span class="inline-flex items-center gap-1"><i class="fas fa-box text-blue-400"></i> محصول: <span class="font-bold"><?= htmlspecialchars($comment['product_name']) ?></span></span>
                            <?php elseif (!empty($comment['post_id'])): ?>
                                <?php
                                // دریافت عنوان مقاله
                                $post_title = '';
                                if (!empty($comment['post_id'])) {
                                    $stmt = $conn->prepare('SELECT title FROM blog_posts WHERE id = ?');
                                    $stmt->bind_param('i', $comment['post_id']);
                                    $stmt->execute();
                                    $stmt->bind_result($post_title);
                                    $stmt->fetch();
                                    $stmt->close();
                                }
                                ?>
                                <span class="inline-flex items-center gap-1"><i class="fas fa-newspaper text-blue-500"></i> مقاله: <span class="font-bold"><?= htmlspecialchars($post_title) ?></span></span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once '../templates/admin_footer.php';
?>