<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = trim($_POST['image_name'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $c_id = !empty($_POST['c_id']) ? intval($_POST['c_id']) : null;
    $contact_link = trim($_POST['contact_link'] ?? '');
    $vr_link = trim($_POST['vr_link'] ?? '');

    // ตรวจสอบชื่อสถานที่ซ้ำ
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM uploads WHERE image_name = ?");
    $checkStmt->bind_param("s", $imageName);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด',
            'text' => 'ชื่อสถานที่นี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น'
        ];
        header("Location: dashboard_pic.php");
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $uploadDir = "uploads/";
        $targetFilePath = $uploadDir . time() . "_" . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO uploads (image_name, image_path, content, contact, title, subtitle, c_id, contact_link, vr_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssiss", $imageName, $targetFilePath, $content, $contact, $title, $subtitle, $c_id, $contact_link, $vr_link);
            if ($stmt->execute()) {
                $_SESSION['flash_alert'] = [
                    'icon' => 'success',
                    'title' => 'เพิ่มสำเร็จ!',
                    'text' => 'อัปโหลดสถานที่ท่องเที่ยวใหม่เรียบร้อยแล้ว'
                ];
                header("Location: dashboard_pic.php");
                exit;
            } else {
                $_SESSION['flash_alert'] = [
                    'icon' => 'error',
                    'title' => 'ข้อผิดพลาด',
                    'text' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล: ' . $stmt->error
                ];
                header("Location: dashboard_pic.php");
                exit;
            }
        } else {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ข้อผิดพลาด',
                'text' => 'อัปโหลดไฟล์รูปภาพล้มเหลว'
            ];
            header("Location: dashboard_pic.php");
            exit;
        }
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด',
            'text' => 'กรุณาเลือกไฟล์รูปภาพที่ถูกต้อง'
        ];
        header("Location: dashboard_pic.php");
        exit;
    }
}
header("Location: dashboard_pic.php");
exit;
