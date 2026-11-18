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

    // ตรวจสอบว่า u_id ซ้ำหรือไม่
    $check_sql = "SELECT t_id FROM tourist_spot WHERE u_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt) {
        $check_stmt->bind_param("i", $u_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ข้อผิดพลาด!',
                'text' => 'ข้อมูลซ้ำ: สถานที่นี้มีรายละเอียดอยู่แล้ว'
            ];
            $check_stmt->close();
            header("Location: dashboard_detail.php");
            exit;
        }
        $check_stmt->close();
    }

    // เพิ่มข้อมูลใหม่
    $sql = "INSERT INTO tourist_spot (visitor_limit, recommended_visitors, area, open_time, close_time, date_time, u_id, time_period, day_period, season) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "sissssssss",
            $visitor_limit,
            $recommended_visitors,
            $area,
            $open_time,
            $close_time,
            $date_time,
            $u_id,
            $time_period,
            $day_period,
            $season
        );

        if ($stmt->execute()) {
            $_SESSION['flash_alert'] = [
                'icon' => 'success',
                'title' => 'เพิ่มสำเร็จ!',
                'text' => 'บันทึกข้อมูลรายละเอียดสถานที่เรียบร้อยแล้ว'
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

header("Location: dashboard_detail.php");
exit;
