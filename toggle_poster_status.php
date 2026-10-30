<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

$p_id = intval($_GET['p_id'] ?? 0);

if ($p_id > 0) {
    $stmt = $conn->prepare("SELECT status FROM posters WHERE p_id = ?");
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row) {
        $newStatus = ($row['status'] === 'active') ? 'inactive' : 'active';
        $upd = $conn->prepare("UPDATE posters SET status = ? WHERE p_id = ?");
        $upd->bind_param("si", $newStatus, $p_id);
        if ($upd->execute()) {
            $_SESSION['flash_alert'] = [
                'icon' => 'success',
                'title' => 'เปลี่ยนสถานะสำเร็จ',
                'text' => ($newStatus === 'active') ? 'เปิดใช้งานโปสเตอร์เรียบร้อยแล้ว' : 'ปิดการแสดงผลโปสเตอร์เรียบร้อยแล้ว'
            ];
        }
        $upd->close();
    }
}

header("Location: dashboard_poster.php");
exit;

