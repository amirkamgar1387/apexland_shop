<?php
$page_title = "مدیریت دیدگاه‌ها";
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

// دریافت همه کامنت‌ها با اطلاعات محصول
$sql = "SELECT c.*, p.name AS product_name FROM comments c LEFT JOIN products p ON c.product_id = p.id ORDER BY c.created_at DESC";
$result = $conn->query($sql);
$comments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
}

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

$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<div class="container mx-auto px-2 md:px-8">
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-yellow-100 mb-8">
        <h1 class="text-2xl font-extrabold text-yellow-700 mb-6 flex items-center gap-2"><i class="fas fa-comments text-yellow-500"></i> مدیریت دیدگاه‌ها</h1>
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
        <?php
        function renderCommentsAdmin($comments, $level = 0) {
            foreach ($comments as $comment) {
                $indent = $level > 0 ? 'ml-8 border-r-4 border-yellow-100 pr-4' : '';
                echo '<div class="bg-white/80 rounded-xl shadow p-4 mb-4 ' . $indent . '">';
                echo '<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">';
                echo '<div class="flex items-center gap-2">';
                echo '<span class="font-bold text-gray-800">' . htmlspecialchars($comment['name']) . '</span>';
                echo '<span class="text-xs text-gray-400">' . date('Y/m/d H:i', strtotime($comment['created_at'])) . '</span>';
                echo '</div>';
                echo '<span class="text-xs px-2 py-1 rounded-full ' . ($comment['status'] == 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') . '">' . ($comment['status'] == 'approved' ? 'تأیید شده' : 'در انتظار تأیید') . '</span>';
                echo '</div>';
                echo '<div class="text-gray-700 mb-2">' . nl2br(htmlspecialchars($comment['content'])) . '</div>';
                echo '<div class="text-xs text-gray-500 mb-2">';
                if (!empty($comment['product_id'])) {
                    echo '<span class="inline-flex items-center gap-1"><i class="fas fa-box text-blue-400"></i> محصول: <span class="font-bold">' . htmlspecialchars($comment['product_name']) . '</span></span>';
                } elseif (!empty($comment['post_id'])) {
                    // دریافت عنوان مقاله
                    $post_title = '';
                    if (!empty($comment['post_id'])) {
                        global $conn;
                        $stmt = $conn->prepare('SELECT title FROM blog_posts WHERE id = ?');
                        $stmt->bind_param('i', $comment['post_id']);
                        $stmt->execute();
                        $stmt->bind_result($post_title);
                        $stmt->fetch();
                        $stmt->close();
                    }
                    echo '<span class="inline-flex items-center gap-1"><i class="fas fa-newspaper text-blue-500"></i> مقاله: <span class="font-bold">' . htmlspecialchars($post_title) . '</span></span>';
                }
                echo '</div>';
                echo '<div class="flex gap-2">';
                if ($comment['status'] != 'approved') {
                    echo '<a href="comment_action.php?action=approve&id=' . $comment['id'] . '" class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-bold transition"><i class="fas fa-check"></i> تأیید</a>';
                }
                if ($comment['status'] == 'approved') {
                    echo '<a href="comment_action.php?action=reject&id=' . $comment['id'] . '" class="px-4 py-2 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-white font-bold transition"><i class="fas fa-times"></i> رد</a>';
                }
                echo '<a href="comment_action.php?action=delete&id=' . $comment['id'] . '" onclick="return confirm(\'آیا از حذف این دیدگاه مطمئن هستید؟\')" class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold transition"><i class="fas fa-trash"></i> حذف</a>';
                echo '</div>';
                // پاسخ‌ها
                if (!empty($comment['children'])) {
                    echo '<div class="mt-4">';
                    renderCommentsAdmin($comment['children'], $level + 1);
                    echo '</div>';
                }
                echo '</div>';
            }
        }
        if (empty($commentTree)) {
            echo '<div class="text-center text-gray-400 py-12">هیچ دیدگاهی ثبت نشده است.</div>';
        } else {
            renderCommentsAdmin($commentTree);
        }
        ?>
    </div>
</div>
<?php require_once '../templates/admin_footer.php'; ?> 