<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m_id = $_POST['m_id'] ?? null;
    $u_id = $_POST['u_id'] ?? null;
    $oldImagePath = $_POST['old_image_path'] ?? null;

    // ตรวจสอบว่า m_id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT m_id FROM images WHERE m_id = ?");
    $checkStmt->bind_param("i", $m_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่พบข้อมูลรูปภาพ',
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

    $newImagePath = null;

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $originalName = basename($file['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uploadDir = "uploads/";
        $newFileName = uniqid('img_', true) . '.' . $extension;
        $newImagePath = $uploadDir . $newFileName;

        // ตรวจสอบว่ามีชื่อไฟล์ซ้ำในฐานข้อมูลหรือไม่
        $checkDuplicateStmt = $conn->prepare("SELECT m_id FROM images WHERE filename = ? AND m_id != ?");
        $checkDuplicateStmt->bind_param("si", $newFileName, $m_id);
        $checkDuplicateStmt->execute();
        $checkDuplicateStmt->store_result();

        if ($checkDuplicateStmt->num_rows > 0) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ชื่อไฟล์ซ้ำ กรุณาเปลี่ยนชื่อไฟล์',
                    icon: 'error',
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    window.history.back(); 
                });
            </script>";
            exit;
        }
        $checkDuplicateStmt->close();

        // อัปโหลดไฟล์ใหม่
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
            // ลบไฟล์เก่า ถ้ามีและไม่ใช่ค่าเริ่มต้น
            if (!empty($oldImagePath) && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถอัปโหลดไฟล์ใหม่ได้',
                    icon: 'error',
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    window.history.back(); 
                });
            </script>";
            exit;
        }
    }

    // Prepare variables for bind_param
    $finalFilename = $newImagePath ? basename($newImagePath) : basename($oldImagePath);

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE images SET filename = ?, uploaded_at = NOW(), u_id = ? WHERE m_id = ?");
    $stmt->bind_param("sii", $finalFilename, $u_id, $m_id);

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
                window.location='dashboard_spot_image.php'; 
            });
        </script>";
        exit;
    } else {
        // ลบไฟล์ใหม่ที่อัปโหลด หากไม่มีการบันทึกลงฐานข้อมูล
        if ($newImagePath && file_exists($newImagePath)) {
            unlink($newImagePath);
        }
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถอัปเดตข้อมูลได้',
                icon: 'error',
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.history.back(); 
            });
        </script>";
        exit;
    }
}
