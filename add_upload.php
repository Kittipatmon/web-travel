<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

// ป้องกันการเข้าถึงโดยไม่ได้รับอนุญาต (Admin Role Verification)
if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

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
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxFileSize = 15 * 1024 * 1024; // 15 MB

        // 1. ตรวจสอบนามสกุลไฟล์ที่ปลอดภัย (Whitelist Extension)
        if (!in_array($ext, $allowedExts)) {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ไฟล์ไม่ถูกต้อง',
                'text' => 'อนุญาตเฉพาะไฟล์รูปภาพประเภท JPG, PNG, WEBP หรือ GIF เท่านั้น'
            ];
            header("Location: dashboard_pic.php");
            exit;
        }

        // 2. ตรวจสอบ MIME type จริงของไฟล์
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowedMimes)) {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ไฟล์ไม่ปลอดภัย',
                'text' => 'เนื้อหาไฟล์ไม่ใช่รูปภาพที่ถูกต้อง กรุณาอัปโหลดรูปภาพจริง'
            ];
            header("Location: dashboard_pic.php");
            exit;
        }

        // 3. ตรวจสอบขนาดไฟล์
        if ($file['size'] > $maxFileSize) {
            $_SESSION['flash_alert'] = [
                'icon' => 'warning',
                'title' => 'ไฟล์มีขนาดใหญ่เกินไป',
                'text' => 'ขนาดไฟล์ต้องไม่เกิน 15 MB'
            ];
            header("Location: dashboard_pic.php");
            exit;
        }

        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 4. สุ่มชื่อไฟล์ใหม่ที่ปลอดภัย ป้องกัน Directory Traversal และชื่อไฟล์ซ้ำ
        $targetFilePath = $uploadDir . time() . "_" . uniqid() . "." . $ext;

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
