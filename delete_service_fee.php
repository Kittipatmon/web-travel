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

if (isset($_GET['s_id'])) {
    $s_id = intval($_GET['s_id']);

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM service_fee WHERE s_id = ?");
    $stmt->bind_param("i", $s_id);
    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'ลบข้อมูลสำเร็จ!',
            'text' => 'ข้อมูลค่าบริการถูกลบเรียบร้อยแล้ว'
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

header("Location: dashboard_service_fee.php");
exit;
