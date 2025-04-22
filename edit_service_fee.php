<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_id = $_POST['s_id'];
    $u_id = $_SESSION['u_id'];
    $service_name = $_POST['service_name'];
    $fee = $_POST['fee'];
    $unit = $_POST['unit'];

    // ตรวจสอบว่า id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT s_id FROM service_fee WHERE s_id = ?");
    $checkStmt->bind_param("i", $s_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่พบข้อมูลค่าบริการ',
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

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE service_fee SET service_name = ?, fee = ?, unit = ?, u_id = ? WHERE s_id = ?");
    $stmt->bind_param("sdsii", $service_name, $fee, $unit, $u_id, $s_id);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'แก้ไขสำเร็จ!',
                text: 'ข้อมูลค่าบริการถูกอัปเดตเรียบร้อยแล้ว',
                icon: 'success',
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.location='dashboard_service_fee.php'; 
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
                window.location='dashboard_service_fee.php'; 
            });
        </script>";
        exit;
    }
}
