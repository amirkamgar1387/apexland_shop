<?php
require_once 'conn.php';
require_once 'cart_logic.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('محصول مورد نظر یافت نشد.');
}
$product_id = intval($_GET['id']);
$stmt = $conn->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?');
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('محصول مورد نظر یافت نشد.');
}
$product = $result->fetch_assoc();
$stmt->close();

// --- دریافت همه دسته‌بندی‌ها با تعداد محصول ---
$cat_sql = "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY product_count DESC";
$cat_result = $conn->query($cat_sql);
$all_categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $all_categories[] = $row;
    }
}
$cart_item_count = getCartItemCount();

$comment_success = false;
$comment_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $name = trim($_POST['name'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
    if ($name === '' || $content === '') {
        $comment_error = 'لطفاً نام و متن دیدگاه را وارد کنید.';
    } else {
        $stmt = $conn->prepare('INSERT INTO comments (product_id, parent_id, name, content, status, created_at) VALUES (?, ?, ?, ?, "approved", NOW())');
        $stmt->bind_param('iiss', $product_id, $parent_id, $name, $content);
        if ($stmt->execute()) {
            $comment_success = true;
        } else {
            $comment_error = 'خطا در ثبت دیدگاه. لطفاً دوباره تلاش کنید.';
        }
        $stmt->close();
        if ($comment_success) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

// دریافت نرخ دلار به ریال
$dollar_rate = getNavasanDollarRate();
if ($dollar_rate === false) {
    $dollar_rate = 830000;
} else {
    $dollar_rate = $dollar_rate * 10;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> | فروشگاه اپکس لند</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-50">
<?php include 'templates/header.php'; ?>
    <!-- Main Content -->
    <main class="container-main mx-auto px-6 py-12">
        <div class="flex flex-col md:flex-row gap-10 items-start">
            <div class="md:w-1/2 w-full flex justify-center items-center">
                <div class="w-full max-w-md aspect-[4/3] bg-gray-100 rounded-2xl shadow-lg flex items-center justify-center overflow-hidden">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <img src="assets/images/default-product.svg" alt="بدون تصویر" class="w-40 h-40 mx-auto">
                    <?php endif; ?>
                </div>
            </div>
            <div class="md:w-1/2 w-full flex flex-col gap-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                <span class="text-gray-500 text-base mb-2">دسته‌بندی: <?php echo htmlspecialchars($product['category_name']); ?></span>
                <!-- Product Info Box -->
                <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2 border border-gray-100">
                    <?php if (!empty($product['brand'])): ?>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-industry text-gray-400"></i>
                            <span>برند:</span>
                            <span class="font-bold"><?php echo htmlspecialchars($product['brand']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!is_null($product['stock'])): ?>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-boxes text-gray-400"></i>
                            <span>موجودی:</span>
                            <span class="font-bold"><?php echo (int)$product['stock'] > 0 ? (int)$product['stock'] . ' عدد' : 'ناموجود'; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['features'])): ?>
                        <div class="flex items-start gap-2 text-sm text-gray-700">
                            <i class="fas fa-list text-gray-400 mt-1"></i>
                            <div>
                                <span class="font-bold">ویژگی‌ها:</span>
                                <div class="mt-1 leading-relaxed"><?php echo nl2br(htmlspecialchars($product['features'])); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="text-lg text-gray-700 mb-3"><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-base">
                        <?php echo $product['price_usd']; ?> $
                    </span>
                    <span class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-base border border-yellow-200">
                        <?php echo number_format($product['price_usd'] * $dollar_rate); ?> ریال
                    </span>
                </div>
                <form action="cart_actions.php" method="post" class="mt-6">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn-primary-gold w-full text-lg">افزودن به سبد خرید</button>
                </form>
            </div>
        </div>
        <!-- توضیح کامل زیر عکس و دکمه -->
        <div class="w-full max-w-4xl mx-auto mt-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">توضیحات کامل محصول</h2>
                <div class="text-gray-800 leading-relaxed"><?php echo nl2br(htmlspecialchars($product['full_description'])); ?></div>
            </div>
        </div>
        <!-- بخش دیدگاه‌ها -->
        <?php
        // دریافت دیدگاه‌های تأییدشده این محصول
        $comments = [];
        $stmt = $conn->prepare('SELECT * FROM comments WHERE product_id = ? AND status = "approved" ORDER BY created_at ASC');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        $stmt->close();
        // ساخت آرایه تو در تو برای نمایش پاسخ‌ها
        function buildCommentTree($comments, $parent_id = null) {
            $branch = [];
            foreach ($comments as $comment) {
                if ($comment['parent_id'] == $parent_id) {
                    $children = buildCommentTree($comments, $comment['id']);
                    if ($children) {
                        $comment['children'] = $children;
                    }
                    $branch[] = $comment;
                }
            }
            return $branch;
        }
        $commentTree = buildCommentTree($comments);
        // تابع نمایش دیدگاه‌ها به صورت تو در تو
        function renderComments($comments) {
            echo '<ul class="space-y-6">';
            foreach ($comments as $comment) {
                echo '<li class="bg-white rounded-xl shadow p-4 border border-gray-100">';
                echo '<div class="flex items-center gap-2 mb-2">';
                echo '<span class="font-bold text-gray-800">' . htmlspecialchars($comment['name']) . '</span>';
                echo '<span class="text-xs text-gray-400">' . date('Y/m/d H:i', strtotime($comment['created_at'])) . '</span>';
                echo '</div>';
                echo '<div class="text-gray-700 mb-2">' . nl2br(htmlspecialchars($comment['content'])) . '</div>';
                // دکمه پاسخ
                echo '<button class="reply-btn text-xs text-blue-600 hover:underline mt-2" data-comment-id="' . $comment['id'] . '">پاسخ</button>';
                // پاسخ‌ها
                if (!empty($comment['children'])) {
                    echo '<div class="ml-6 mt-4">';
                    renderComments($comment['children']);
                    echo '</div>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        ?>
        <div class="w-full max-w-4xl mx-auto mt-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">دیدگاه کاربران</h2>
                <?php if (empty($commentTree)): ?>
                    <p class="text-gray-500">هنوز دیدگاهی ثبت نشده است.</p>
                <?php else: ?>
                    <?php renderComments($commentTree); ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- فرم ارسال دیدگاه -->
        <div class="w-full max-w-4xl mx-auto mt-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">ارسال دیدگاه</h2>
                <?php if ($comment_success): ?>
                    <div class="bg-green-100 text-green-800 rounded-lg px-4 py-2 mb-4">دیدگاه شما با موفقیت ثبت شد.</div>
                <?php elseif ($comment_error): ?>
                    <div class="bg-red-100 text-red-800 rounded-lg px-4 py-2 mb-4"><?php echo $comment_error; ?></div>
                <?php endif; ?>
                <form method="post" id="comment-form">
                    <input type="hidden" name="parent_id" id="parent_id" value="">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2">نام شما:</label>
                        <input type="text" name="name" id="name" class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" required>
                    </div>
                    <div class="mb-4">
                        <label for="content" class="block text-gray-700 font-bold mb-2">متن دیدگاه:</label>
                        <textarea name="content" id="content" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" required></textarea>
                    </div>
                    <button type="submit" name="add_comment" class="btn-primary-gold px-6 py-2 font-bold rounded-xl shadow-md hover:shadow-lg transition">ارسال دیدگاه</button>
                </form>
            </div>
        </div>
        <!-- محصولات مرتبط -->
        <?php
        // دریافت ۳ محصول مرتبط از همان دسته (به جز محصول فعلی)
        $related_products = [];
        if (!empty($product['category_id'])) {
            $stmt = $conn->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? ORDER BY RAND() LIMIT 3');
            $stmt->bind_param('ii', $product['category_id'], $product['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $related_products[] = $row;
            }
            $stmt->close();
        }
        ?>
        <?php if (!empty($related_products)): ?>
        <div class="w-full max-w-4xl mx-auto mt-12">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2"><i class="fas fa-link text-yellow-500"></i> محصولات مرتبط</h2>
                <!-- دسکتاپ -->
                <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                    <?php foreach ($related_products as $rel): ?>
                        <div class="bg-white rounded-2xl shadow-md flex flex-col overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-xl duration-300">
                            <a href="product.php?id=<?php echo $rel['id']; ?>" class="block">
                                <div class="w-full aspect-[4/3] bg-gray-100 flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($rel['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                    <?php else: ?>
                                        <img src="assets/images/default-product.svg" alt="بدون تصویر" class="w-24 h-24 mx-auto">
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="flex flex-col flex-1 p-4">
                                <a href="product.php?id=<?php echo $rel['id']; ?>">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1 truncate"><?php echo htmlspecialchars($rel['name']); ?></h3>
                                </a>
                                <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($rel['short_description']); ?></p>
                                <div class="mt-auto flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                            <?php echo $rel['price_usd']; ?> $
                                        </span>
                                        <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                            <?php echo number_format($rel['price_usd'] * $dollar_rate); ?> ریال
                                        </span>
                                    </div>
                                    <div class="flex flex-row gap-2 mt-2">
                                        <a href="product.php?id=<?php echo $rel['id']; ?>"
                                           class="btn-primary-gold flex-1 text-center text-sm py-2 px-2">مشاهده جزئیات</a>
                                        <form action="cart_actions.php" method="post" class="flex-1">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id" value="<?php echo $rel['id']; ?>">
                                            <button type="submit" class="btn-primary-gold w-full text-sm flex items-center justify-center gap-1 py-2 px-2"><i class="fas fa-cart-plus"></i> سبد خرید</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- موبایل: کاروسل افقی -->
                <div class="flex md:hidden gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-2 mt-4">
                    <?php foreach ($related_products as $rel): ?>
                        <div class="bg-white rounded-2xl shadow-md flex flex-col overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-xl duration-300 w-72 flex-shrink-0 snap-start mx-auto">
                            <a href="product.php?id=<?php echo $rel['id']; ?>" class="block">
                                <div class="w-full aspect-[4/3] bg-gray-100 flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($rel['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                    <?php else: ?>
                                        <img src="assets/images/default-product.svg" alt="بدون تصویر" class="w-24 h-24 mx-auto">
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="flex flex-col flex-1 p-4">
                                <a href="product.php?id=<?php echo $rel['id']; ?>">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1 truncate"><?php echo htmlspecialchars($rel['name']); ?></h3>
                                </a>
                                <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($rel['short_description']); ?></p>
                                <div class="mt-auto flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                            <?php echo $rel['price_usd']; ?> $
                                        </span>
                                        <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full text-xs border border-yellow-200">
                                            <?php echo number_format($rel['price_usd'] * $dollar_rate); ?> ریال
                                        </span>
                                    </div>
                                    <div class="flex flex-row gap-2 mt-2">
                                        <a href="product.php?id=<?php echo $rel['id']; ?>"
                                           class="btn-primary-gold flex-1 text-center text-sm py-2 px-2">مشاهده جزئیات</a>
                                        <form action="cart_actions.php" method="post" class="flex-1">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id" value="<?php echo $rel['id']; ?>">
                                            <button type="submit" class="btn-primary-gold w-full text-sm flex items-center justify-center gap-1 py-2 px-2"><i class="fas fa-cart-plus"></i> سبد خرید</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <script>
        // دکمه پاسخ به دیدگاه
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.reply-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var commentId = this.getAttribute('data-comment-id');
                    document.getElementById('parent_id').value = commentId;
                    document.getElementById('name').focus();
                    document.getElementById('comment-form').scrollIntoView({behavior: 'smooth'});
                });
            });
        });
        </script>
    </main>
<?php include 'templates/footer.php'; ?> 