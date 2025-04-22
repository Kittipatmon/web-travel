<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_limit = $_POST['visitor_limit'];
    $recommended_visitors = $_POST['recommended_visitors'];
    $area = $_POST['area'];
    $open_time = $_POST['open_time'];
    $close_time = $_POST['close_time'];
    $date_time = $_POST['date_time'];
    $time_period = $_POST['time_period'];
    $day_period = $_POST['day_period'];
    $season = $_POST['season'];
    $u_id = $_POST['u_id'];

    // ตรวจสอบว่า u_id และ area ไม่ซ้ำกัน
    $check_sql = "SELECT * FROM tourist_spot WHERE u_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        die("ข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
    }
    $check_stmt->bind_param("s", $u_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: '❌ ข้อมูลซ้ำ: มีสถานที่นี้อยู่แล้ว!',
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'dashboard_detail.php';
                    });
                </script>";
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();


    // เพิ่มข้อมูลใหม่
    $sql = "INSERT INTO tourist_spot (visitor_limit, recommended_visitors, area, open_time, close_time, date_time, u_id, time_period, day_period, season) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("ข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
    }
    $stmt->bind_param("sisssss", $visitor_limit, $recommended_visitors, $area, $open_time, $close_time, $date_time, $u_id);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: '✅ อัปโหลดข้อมูลสำเร็จ!',
                        confirmButtonText: 'ตกลง',
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'dashboard_detail.php';
                    });
                </script>";
        exit;
    } else {
        echo "ข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
