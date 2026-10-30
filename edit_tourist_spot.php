<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t_id = intval($_POST['t_id'] ?? 0);
    $visitor_limit = trim($_POST['visitor_limit'] ?? '');
    $recommended_visitors = isset($_POST['recommended_visitors']) && $_POST['recommended_visitors'] !== '' ? intval($_POST['recommended_visitors']) : null;
    $area = trim($_POST['area'] ?? '');
    $open_time = trim($_POST['open_time'] ?? '');
    $close_time = trim($_POST['close_time'] ?? '');
    $date_time = trim($_POST['date_time'] ?? '');
    $time_period = trim($_POST['time_period'] ?? '');
    $day_period = trim($_POST['day_period'] ?? '');
    $season = trim($_POST['season'] ?? '');
    $u_id = intval($_POST['u_id'] ?? 0);

    // ตรวจสอบว่า id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT t_id FROM tourist_spot WHERE t_id = ?");
    $checkStmt->bind_param("i", $t_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่พบข้อมูลสถานที่ท่องเที่ยว'
        ];
        $checkStmt->close();
        header("Location: dashboard_detail.php");
        exit;
    }
    $checkStmt->close();

    // ตรวจสอบสถานที่ซ้ำ
    $linkCheckStmt = $conn->prepare("SELECT t_id FROM tourist_spot WHERE u_id = ? AND t_id != ?");
    $linkCheckStmt->bind_param("ii", $u_id, $t_id);
    $linkCheckStmt->execute();
    $linkCheckStmt->store_result();

    if ($linkCheckStmt->num_rows > 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด!',
            'text' => 'สถานที่นี้มีข้อมูลรายละเอียดอยู่แล้ว กรุณาเลือกสถานที่อื่น'
        ];
        $linkCheckStmt->close();
        header("Location: dashboard_detail.php");
        exit;
    }
    $linkCheckStmt->close();

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE tourist_spot SET visitor_limit = ?, recommended_visitors = ?, area = ?, open_time = ?, close_time = ?, date_time = ?, time_period = ?, day_period = ?, season = ?, u_id = ? WHERE t_id = ?");
    $stmt->bind_param("siissssssii", $visitor_limit, $recommended_visitors, $area, $open_time, $close_time, $date_time, $time_period, $day_period, $season, $u_id, $t_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'แก้ไขสำเร็จ!',
            'text' => 'ข้อมูลรายละเอียดสถานที่ถูกอัปเดตเรียบร้อยแล้ว'
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

header("Location: dashboard_detail.php");
exit;
