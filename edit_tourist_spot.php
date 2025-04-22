<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t_id = $_POST['t_id'];
    $visitor_limit = $_POST['visitor_limit'];
    $recommended_visitors = isset($_POST['recommended_visitors']) && $_POST['recommended_visitors'] !== '' ? $_POST['recommended_visitors'] : null;
    $area = $_POST['area'];
    $open_time = $_POST['open_time'];
    $close_time = $_POST['close_time'];
    $date_time = $_POST['date_time'];
    $time_period = $_POST['time_period'];
    $day_period = $_POST['day_period'];
    $season = $_POST['season'];
    $u_id = $_POST['u_id'];

    // ตรวจสอบว่า id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT t_id FROM tourist_spot WHERE t_id = ?");
    $checkStmt->bind_param("i", $t_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่พบข้อมูลสถานที่ท่องเที่ยว',
                icon: 'error',
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.history.back(); 
            });
        </script>";
        exit;
    }
    $checkStmt->close();

    // ตรวจสอบสถานที่ซ้ำ
    $linkCheckStmt = $conn->prepare("SELECT t_id FROM tourist_spot WHERE u_id = ? AND t_id != ?");
    $linkCheckStmt->bind_param("ii", $u_id, $t_id);
    $linkCheckStmt->execute();
    $linkCheckStmt->store_result();

    if ($linkCheckStmt->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: 'สถานที่ซ้ำ กรุณาเลือกสถานที่อื่น',
            icon: 'error',
            timer: 1500,
            timerProgressBar: true
        }).then(() => {
            window.history.back(); 
        });
    </script>";
        exit; // หยุดการทำงาน
    }
    $linkCheckStmt->close();

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE tourist_spot SET visitor_limit = ?, recommended_visitors = ?, area = ?, open_time = ?, close_time = ?, date_time = ?, time_period = ?, day_period = ?, season = ?, u_id = ? WHERE t_id = ?");
    $stmt->bind_param("siissssssii", $visitor_limit, $recommended_visitors, $area, $open_time, $close_time, $date_time, $time_period, $day_period, $season, $u_id, $t_id);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'แก้ไขสำเร็จ!',
                text: 'ข้อมูลถูกอัปเดตเรียบร้อยแล้ว',
                icon: 'success',
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.location='dashboard_detail.php'; 
            });
        </script>";
        exit;
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถอัปเดตข้อมูลได้',
                icon: 'error',
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.location='dashboard_detail.php'; 
            });
        </script>";
        exit;
    }
}
