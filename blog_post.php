<?php
require_once 'conn.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('مقاله مورد نظر یافت نشد.');
}
$post_id = intval($_GET['id']);
$stmt = $conn->prepare('SELECT p.*, c.name AS category_name, c.id AS category_id FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE p.id = ? AND p.status = "published"');
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('مقاله مورد نظر یافت نشد.');
}
$post = $result->fetch_assoc();
$stmt->close();

// --- دیدگاه‌ها ---
$comment_success = false;
$comment_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $name = trim($_POST['name'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
    if ($name === '' || $content === '') {
        $comment_error = 'لطفاً نام و متن دیدگاه را وارد کنید.';
    } else {
        if ($parent_id === null) {
            $stmt = $conn->prepare('INSERT INTO comments (post_id, parent_id, name, content, status, created_at) VALUES (?, NULL, ?, ?, "approved", NOW())');
            $stmt->bind_param('iss', $post_id, $name, $content);
        } else {
            $stmt = $conn->prepare('INSERT INTO comments (post_id, parent_id, name, content, status, created_at) VALUES (?, ?, ?, ?, "approved", NOW())');
            $stmt->bind_param('iiss', $post_id, $parent_id, $name, $content);
        }
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
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> | وبلاگ اپکس لند</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icon/apexland.png">
</head>
<body class="bg-gray-50">
<?php include 'templates/header.php'; ?>
<main class="container-main mx-auto px-6 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-6 md:p-10 border border-blue-100">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                <?php if (!empty($post['image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full md:w-64 h-48 object-cover rounded-xl border border-gray-200">
                <?php else: ?>
                    <img src="assets/images/default-blog-post.svg" alt="بدون تصویر" class="w-full md:w-64 h-48 object-cover rounded-xl border border-gray-200">
                <?php endif; ?>
                <div class="flex-1 flex flex-col gap-2">
                    <h1 class="text-3xl font-extrabold text-blue-800 mb-2"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <a href="blog_category.php?id=<?php echo $post['category_id']; ?>" class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-bold hover:bg-blue-100 transition"><i class="fas fa-folder-open ml-1"></i> <?php echo htmlspecialchars($post['category_name']); ?></a>
                        <span class="text-gray-400"><i class="fas fa-clock ml-1"></i> <?php echo date('Y/m/d', strtotime($post['created_at'])); ?></span>
                    </div>
                </div>
            </div>
            <?php if (!empty($post['summary'])): ?>
                <div class="bg-blue-50 text-blue-800 rounded-xl p-4 mb-4 text-base font-bold">
                    <?php echo nl2br(htmlspecialchars($post['summary'])); ?>
                </div>
            <?php endif; ?>
            <div class="text-gray-800 leading-relaxed text-lg mb-6">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
            <div class="flex justify-between items-center mt-8">
                <a href="blog.php" class="btn-primary-blue flex items-center gap-2"><i class="fas fa-arrow-right"></i> بازگشت به وبلاگ</a>
                <a href="blog_category.php?id=<?php echo $post['category_id']; ?>" class="btn-primary-gold flex items-center gap-2"><i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($post['category_name']); ?></a>
            </div>
        </div>
    </div>
    <?php
    // دریافت دیدگاه‌های تأییدشده این مقاله
    $comments = [];
    $stmt = $conn->prepare('SELECT * FROM comments WHERE post_id = ? AND status = "approved" ORDER BY created_at ASC');
    $stmt->bind_param('i', $post_id);
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
            echo '<li class="bg-white rounded-xl shadow p-4 border border-blue-100">';
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
    <div class="w-full max-w-3xl mx-auto mt-8">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-blue-800">دیدگاه کاربران</h2>
            <?php if (empty($commentTree)): ?>
                <p class="text-gray-500">هنوز دیدگاهی ثبت نشده است.</p>
            <?php else: ?>
                <?php renderComments($commentTree); ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- فرم ارسال دیدگاه -->
    <div class="w-full max-w-3xl mx-auto mt-8">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-blue-800">ارسال دیدگاه</h2>
            <?php if ($comment_success): ?>
                <div class="bg-green-100 text-green-800 rounded-lg px-4 py-2 mb-4">دیدگاه شما با موفقیت ثبت شد.</div>
            <?php elseif ($comment_error): ?>
                <div class="bg-red-100 text-red-800 rounded-lg px-4 py-2 mb-4"><?php echo $comment_error; ?></div>
            <?php endif; ?>
            <form method="post" id="comment-form">
                <input type="hidden" name="parent_id" id="parent_id" value="">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">نام شما:</label>
                    <input type="text" name="name" id="name" class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                </div>
                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-bold mb-2">متن دیدگاه:</label>
                    <textarea name="content" id="content" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required></textarea>
                </div>
                <button type="submit" name="add_comment" class="btn-primary-blue px-6 py-2 font-bold rounded-xl shadow-md hover:shadow-lg transition">ارسال دیدگاه</button>
            </form>
        </div>
    </div>
    <!-- پست‌های مرتبط -->
    <?php
    // دریافت ۳ پست مرتبط از همان دسته (به جز پست فعلی)
    $related_posts = [];
    if (!empty($post['category_id'])) {
        $stmt = $conn->prepare('SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.status = "published" ORDER BY RAND() LIMIT 3');
        $stmt->bind_param('ii', $post['category_id'], $post['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $related_posts[] = $row;
        }
        $stmt->close();
    }
    ?>
    <?php if (!empty($related_posts)): ?>
    <div class="w-full max-w-3xl mx-auto mt-12">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold mb-6 text-blue-800 flex items-center gap-2"><i class="fas fa-link text-blue-500"></i> پست‌های مرتبط</h2>
            <!-- دسکتاپ -->
            <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <?php foreach ($related_posts as $rel): ?>
                    <div class="bg-white rounded-2xl shadow-md flex flex-col overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-xl duration-300">
                        <a href="blog_post.php?id=<?php echo $rel['id']; ?>" class="block">
                            <div class="w-full aspect-[4/3] bg-blue-50 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($rel['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                <?php else: ?>
                                    <img src="assets/images/default-blog-post.svg" alt="بدون تصویر" class="w-24 h-24 mx-auto">
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="flex flex-col flex-1 p-4">
                            <a href="blog_post.php?id=<?php echo $rel['id']; ?>">
                                <h3 class="text-lg font-bold text-blue-900 mb-1 truncate"><?php echo htmlspecialchars($rel['title']); ?></h3>
                            </a>
                            <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($rel['summary']); ?></p>
                            <div class="mt-auto flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="bg-blue-50 text-blue-800 px-2 py-1 rounded-full text-xs">
                                        <?php echo htmlspecialchars($rel['category_name']); ?>
                                    </span>
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                        <?php echo date('Y/m/d', strtotime($rel['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="flex flex-row gap-2 mt-2">
                                    <a href="blog_post.php?id=<?php echo $rel['id']; ?>"
                                       class="btn-primary-blue flex-1 text-center text-sm py-2 px-2">مشاهده جزئیات</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- موبایل: کاروسل افقی -->
            <div class="flex md:hidden gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-2 mt-4">
                <?php foreach ($related_posts as $rel): ?>
                    <div class="bg-white rounded-2xl shadow-md flex flex-col overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-xl duration-300 w-72 flex-shrink-0 snap-start mx-auto">
                        <a href="blog_post.php?id=<?php echo $rel['id']; ?>" class="block">
                            <div class="w-full aspect-[4/3] bg-blue-50 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($rel['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                <?php else: ?>
                                    <img src="assets/images/default-blog-post.svg" alt="بدون تصویر" class="w-24 h-24 mx-auto">
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="flex flex-col flex-1 p-4">
                            <a href="blog_post.php?id=<?php echo $rel['id']; ?>">
                                <h3 class="text-lg font-bold text-blue-900 mb-1 truncate"><?php echo htmlspecialchars($rel['title']); ?></h3>
                            </a>
                            <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($rel['summary']); ?></p>
                            <div class="mt-auto flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="bg-blue-50 text-blue-800 px-2 py-1 rounded-full text-xs">
                                        <?php echo htmlspecialchars($rel['category_name']); ?>
                                    </span>
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                        <?php echo date('Y/m/d', strtotime($rel['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="flex flex-row gap-2 mt-2">
                                    <a href="blog_post.php?id=<?php echo $rel['id']; ?>"
                                       class="btn-primary-blue flex-1 text-center text-sm py-2 px-2">مشاهده جزئیات</a>
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
<?php require_once 'templates/footer.php'; ?>
</body>
</html> 