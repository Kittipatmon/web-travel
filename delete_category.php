<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

$c_id = intval($_GET['c_id'] ?? 0);

if ($c_id > 0) {
    // ปลด c_id ใน uploads ให้เป็น NULL เพื่อความปลอดภัยของข้อมูลสถานที่
    $resetStmt = $conn->prepare("UPDATE uploads SET c_id = NULL WHERE c_id = ?");
    $resetStmt->bind_param("i", $c_id);
    $resetStmt->execute();
    $resetStmt->close();

    $stmt = $conn->prepare("DELETE FROM categories WHERE c_id = ?");
    $stmt->bind_param("i", $c_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'ลบสำเร็จ',
            'text' => 'ลบหมวดหมู่เรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด',
            'text' => 'ไม่สามารถลบหมวดหมู่ได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_category.php");
exit;

