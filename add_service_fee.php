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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u_id = intval($_POST['u_id'] ?? 0);
    $service_name = trim($_POST['service_name'] ?? '');
    $fee = floatval($_POST['fee'] ?? 0);
    $unit = trim($_POST['unit'] ?? '');

    // ตรวจสอบว่า service_name ไม่ซ้ำกันในสถานที่ท่องเที่ยวเดียวกัน
    $check_sql = "SELECT s_id FROM service_fee WHERE service_name = ? AND u_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt) {
        $check_stmt->bind_param("si", $service_name, $u_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ข้อผิดพลาด!',
                'text' => 'มีชื่อบริการนี้ในสถานที่ท่องเที่ยวนี้อยู่แล้ว'
            ];
            $check_stmt->close();
            header("Location: dashboard_service_fee.php");
            exit;
        }
        $check_stmt->close();
    }

    // เพิ่มข้อมูลใหม่
    $sql = "INSERT INTO service_fee (service_name, fee, unit, u_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sdsi", $service_name, $fee, $unit, $u_id);
        if ($stmt->execute()) {
            $_SESSION['flash_alert'] = [
                'icon' => 'success',
                'title' => 'เพิ่มสำเร็จ!',
                'text' => 'บันทึกข้อมูลค่าบริการเรียบร้อยแล้ว'
            ];
        } else {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'เกิดข้อผิดพลาด!',
                'text' => 'ไม่สามารถบันทึกข้อมูลได้: ' . $stmt->error
            ];
        }
        $stmt->close();
    }
}

header("Location: dashboard_service_fee.php");
exit;
