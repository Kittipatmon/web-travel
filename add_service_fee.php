<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u_id = $_POST['u_id'];
    $service_name = $_POST['service_name'];
    $fee = $_POST['fee'];
    $unit = $_POST['unit'];

    // ตรวจสอบว่า service_name ไม่ซ้ำกัน
    $check_sql = "SELECT * FROM service_fee WHERE service_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        die("ข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
    }
    $check_stmt->bind_param("s", $service_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: '❌ ข้อมูลซ้ำ: มีชื่อบริการนี้อยู่แล้ว!',
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'dashboard_service_fee.php';
                    });
                </script>";
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();

    // เพิ่มข้อมูลใหม่
    $sql = "INSERT INTO service_fee (service_name, fee, unit, u_id) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("ข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
    }
    $stmt->bind_param("sdss", $service_name, $fee, $unit, $u_id);

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
                        window.location.href = 'dashboard_service_fee.php';
                    });
                </script>";
        exit;
    } else {
        echo "ข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
