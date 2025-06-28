<?php
$page_title = "پروفایل من";
require_once '../templates/admin_header.php';
require_once '../templates/admin_sidebar.php';

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admin_users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<div class="container mx-auto px-2 md:px-8">
    <div class="glass-admin p-8 rounded-2xl shadow-xl border border-yellow-100 mb-8 max-w-2xl mx-auto mt-10">
        <h1 class="text-2xl font-extrabold text-yellow-700 mb-6 flex items-center gap-2"><i class="fas fa-user-circle text-yellow-500"></i> پروفایل من</h1>
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
        <form action="profile_process.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="flex flex-col items-center gap-4 mb-6">
                <div id="avatarPreview" class="w-28 h-28 rounded-full border-4 border-yellow-200 shadow overflow-hidden">
                    <img src="<?= !empty($admin['photo']) ? '../' . htmlspecialchars($admin['photo']) : '../assets/images/default-avatar.svg' ?>" alt="آواتار" class="w-full h-full object-cover" id="previewImg">
                </div>
                <label for="photo" class="flex flex-col items-center justify-center w-48 h-12 bg-yellow-50 border-2 border-dashed border-yellow-200 rounded-xl cursor-pointer hover:bg-yellow-100 transition group">
                    <span class="text-yellow-500 text-lg"><i class="fas fa-camera"></i> تغییر عکس پروفایل</span>
                    <input type="file" id="photo" name="photo" class="hidden" accept="image/*" onchange="previewProfileImage(event)">
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="form-label">نام کاربری</label>
                    <input type="text" id="username" name="username" class="form-control-custom" value="<?= htmlspecialchars($admin['username']) ?>" required>
                </div>
                <div>
                    <label for="full_name" class="form-label">نام کامل</label>
                    <input type="text" id="full_name" name="full_name" class="form-control-custom" value="<?= htmlspecialchars($admin['full_name']) ?>">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="form-label">رمز عبور جدید</label>
                    <input type="password" id="password" name="password" class="form-control-custom" placeholder="در صورت تغییر وارد کنید">
                </div>
                <div>
                    <label for="password_confirm" class="form-label">تکرار رمز عبور</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control-custom" placeholder="تکرار رمز جدید">
                </div>
            </div>
            <div class="flex gap-3 items-center mt-4 border-t pt-6">
                <button type="submit" class="btn-primary-gold flex items-center gap-2 text-lg px-8 py-3 shadow hover:shadow-lg transition">
                    <i class="fas fa-save"></i> ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function previewProfileImage(event) {
    const input = event.target;
    const previewBox = document.getElementById('avatarPreview');
    const previewImg = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php require_once '../templates/admin_footer.php'; ?> 