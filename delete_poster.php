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
    // ดึง path ไฟล์รูป
    $stmt = $conn->prepare("SELECT image_path FROM posters WHERE p_id = ?");
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $poster = $res->fetch_assoc();
    $stmt->close();

    if ($poster) {
        $imgPath = $poster['image_path'];
        // ลบไฟล์ถ้าอยู่ใน uploads/
        if (!empty($imgPath) && strpos($imgPath, 'uploads/') === 0 && file_exists($imgPath)) {
            @unlink($imgPath);
        }

        $delStmt = $conn->prepare("DELETE FROM posters WHERE p_id = ?");
        $delStmt->bind_param("i", $p_id);
        if ($delStmt->execute()) {
            $_SESSION['flash_alert'] = [
                'icon' => 'success',
                'title' => 'ลบสำเร็จ',
                'text' => 'ลบโปสเตอร์โปรโมทเรียบร้อยแล้ว'
            ];
        } else {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ข้อผิดพลาด',
                'text' => 'ไม่สามารถลบข้อมูลได้: ' . $delStmt->error
            ];
        }
        $delStmt->close();
    }
}

header("Location: dashboard_poster.php");
exit;

