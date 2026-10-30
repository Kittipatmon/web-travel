<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_id = intval($_POST['s_id'] ?? $_POST['t_id'] ?? 0);
    $u_id = intval($_POST['u_id'] ?? 0);
    $service_name = trim($_POST['service_name'] ?? '');
    $fee = floatval($_POST['fee'] ?? 0);
    $unit = trim($_POST['unit'] ?? '');

    // ตรวจสอบว่า id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT s_id FROM service_fee WHERE s_id = ?");
    $checkStmt->bind_param("i", $s_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่พบข้อมูลค่าบริการที่ต้องการแก้ไข'
        ];
        $checkStmt->close();
        header("Location: dashboard_service_fee.php");
        exit;
    }
    $checkStmt->close();

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE service_fee SET service_name = ?, fee = ?, unit = ?, u_id = ? WHERE s_id = ?");
    $stmt->bind_param("sdsii", $service_name, $fee, $unit, $u_id, $s_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'แก้ไขสำเร็จ!',
            'text' => 'ข้อมูลค่าบริการถูกอัปเดตเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่สามารถอัปเดตข้อมูลได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_service_fee.php");
exit;
