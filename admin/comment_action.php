<?php
require_once '../conn.php';

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($id <= 0 || !in_array($action, ['approve', 'reject', 'delete'])) {
    $_SESSION['error_message'] = 'درخواست نامعتبر است.';
    header('Location: comments.php');
    exit;
}

if ($action === 'approve') {
    $sql = "UPDATE comments SET status='approved' WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'دیدگاه با موفقیت تأیید شد.';
    } else {
        $_SESSION['error_message'] = 'خطا در تأیید دیدگاه.';
    }
    $stmt->close();
} elseif ($action === 'reject') {
    $sql = "UPDATE comments SET status='pending' WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'دیدگاه به حالت انتظار بازگشت.';
    } else {
        $_SESSION['error_message'] = 'خطا در تغییر وضعیت دیدگاه.';
    }
    $stmt->close();
} elseif ($action === 'delete') {
    $sql = "DELETE FROM comments WHERE id=? OR parent_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id, $id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'دیدگاه و پاسخ‌های آن حذف شد.';
    } else {
        $_SESSION['error_message'] = 'خطا در حذف دیدگاه.';
    }
    $stmt->close();
}

header('Location: comments.php');
exit; 