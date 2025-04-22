<?php
include "connect.php";
session_start(); // ต้องเริ่ม session ก่อน

// ตรวจสอบว่าเป็นการร้องขอแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u_id = $_POST['u_id'];
    $imageName = $_POST['image_name'];
    $content = $_POST['content'];
    $contact = $_POST['contact'];
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $contact_link = $_POST['contact_link'];
    $vr_link = $_POST['vr_link'];
    $oldImagePath = $_POST['old_image_path']; // เก็บไฟล์รูปเก่า

    // ดึงข้อมูลเดิมจากฐานข้อมูล
    $query = $conn->prepare("SELECT contact_link, vr_link FROM uploads WHERE u_id = ?");
    $query->bind_param("i", $u_id);
    $query->execute();
    $query->bind_result($oldContactLink, $oldVrLink);
    $query->fetch();
    $query->close();

    // ตรวจสอบชื่อสถานที่ซ้ำ
    $checkStmt = $conn->prepare("SELECT u_id FROM uploads WHERE image_name = ? AND u_id != ?");
    $checkStmt->bind_param("si", $imageName, $u_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ชื่อสถานที่ซ้ำ กรุณาใช้ชื่ออื่น',
                icon: 'error',
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.history.back(); 
            });
        </script>";
        exit; // หยุดการทำงาน
    }
    $checkStmt->close();

    // ตรวจสอบลิ้งค์ซ้ำ
    $linkCheckStmt = $conn->prepare("SELECT u_id FROM uploads WHERE (contact_link = ? OR vr_link = ?) AND u_id != ?");
    $linkCheckStmt->bind_param("ssi", $contact_link, $vr_link, $u_id);
    $linkCheckStmt->execute();
    $linkCheckStmt->store_result();

    if ($linkCheckStmt->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ลิ้งค์ซ้ำ กรุณาใช้ลิ้งค์อื่น',
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

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $uploadDir = "uploads/";
        $newImagePath = $uploadDir . time() . "_" . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
            // ลบไฟล์เก่า ถ้ามีและไม่ใช่ค่าเริ่มต้น
            if (!empty($oldImagePath) && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        } else {
            $newImagePath = $oldImagePath; // ใช้รูปเดิมหากอัปโหลดไม่สำเร็จ
        }
    } else {
        $newImagePath = $oldImagePath; // ไม่มีการอัปโหลด ใช้รูปเดิม
    }

    // ตรวจสอบว่าลิงก์มีการเปลี่ยนแปลงหรือไม่
    $contactLinkChanged = ($contact_link !== $oldContactLink);
    $vrLinkChanged = ($vr_link !== $oldVrLink);

    // อัปเดตฐานข้อมูล
    $stmt = $conn->prepare("UPDATE uploads SET image_name = ?, image_path = ?, content = ?, contact = ?, title = ?, subtitle = ?, contact_link = ?, vr_link = ? WHERE u_id = ?");
    $stmt->bind_param("ssssssssi", $imageName, $newImagePath, $content, $contact, $title, $subtitle, $contact_link, $vr_link, $u_id);

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
                window.location='dashboard_pic.php'; 
            });
        </script>";
        exit; // Stop execution to prevent further processing
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
                window.location='dashboard_pic.php'; 
            });
        </script>";
        exit; // Stop execution to prevent further processing
    }
}
