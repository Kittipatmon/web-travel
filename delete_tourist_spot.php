<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ผู้ดูแลระบบ (Admin Role Verification)
if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

include "connect.php";

if (isset($_GET['t_id'])) {
    $t_id = intval($_GET['t_id']);

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM tourist_spot WHERE t_id = ?");
    $stmt->bind_param("i", $t_id);
    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'ลบข้อมูลสำเร็จ!',
            'text' => 'ข้อมูลรายละเอียดสถานที่ถูกลบเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่สามารถลบข้อมูลได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_detail.php");
exit;
