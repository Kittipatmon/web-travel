<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m_id = intval($_POST['m_id'] ?? 0);
    $u_id = intval($_POST['u_id'] ?? 0);
    $oldImagePath = trim($_POST['old_image_path'] ?? '');

    // ตรวจสอบว่า m_id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT m_id FROM images WHERE m_id = ?");
    $checkStmt->bind_param("i", $m_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่พบข้อมูลรูปภาพที่ต้องการแก้ไข'
        ];
        $checkStmt->close();
        header("Location: dashboard_spot_image.php");
        exit;
    }
    $checkStmt->close();

    $newImagePath = null;
    $uploadDir = "uploads/";

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $originalName = basename($file['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = uniqid('img_', true) . '.' . $extension;
        $newImagePath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
            // ลบไฟล์เก่า
            if (!empty($oldImagePath) && file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }
        } else {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'เกิดข้อผิดพลาด!',
                'text' => 'ไม่สามารถอัปโหลดไฟล์รูปภาพใหม่ได้'
            ];
            header("Location: dashboard_spot_image.php");
            exit;
        }
    }

    $finalFilename = $newImagePath ? basename($newImagePath) : basename($oldImagePath);

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE images SET filename = ?, uploaded_at = NOW(), u_id = ? WHERE m_id = ?");
    $stmt->bind_param("sii", $finalFilename, $u_id, $m_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'แก้ไขสำเร็จ!',
            'text' => 'ข้อมูลรูปภาพถูกอัปเดตเรียบร้อยแล้ว'
        ];
    } else {
        if ($newImagePath && file_exists($newImagePath)) {
            @unlink($newImagePath);
        }
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่สามารถอัปเดตข้อมูลได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_spot_image.php");
exit;
