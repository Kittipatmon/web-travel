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
    $u_id = intval($_POST['u_id'] ?? 0);
    $imageName = trim($_POST['image_name'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $c_id = !empty($_POST['c_id']) ? intval($_POST['c_id']) : null;
    $contact_link = trim($_POST['contact_link'] ?? '');
    $vr_link = trim($_POST['vr_link'] ?? '');
    $oldImagePath = trim($_POST['old_image_path'] ?? '');

    // ตรวจสอบชื่อสถานที่ซ้ำ
    $checkStmt = $conn->prepare("SELECT u_id FROM uploads WHERE image_name = ? AND u_id != ?");
    $checkStmt->bind_param("si", $imageName, $u_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด!',
            'text' => 'ชื่อสถานที่ซ้ำ กรุณาใช้ชื่ออื่น'
        ];
        header("Location: dashboard_pic.php");
        exit;
    }
    $checkStmt->close();

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxFileSize = 15 * 1024 * 1024; // 15 MB

        if (!in_array($ext, $allowedExts)) {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ไฟล์ไม่ถูกต้อง',
                'text' => 'อนุญาตเฉพาะไฟล์รูปภาพประเภท JPG, PNG, WEBP หรือ GIF เท่านั้น'
            ];
            header("Location: dashboard_pic.php");
            exit;
        }

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
        $newImagePath = $uploadDir . time() . "_" . uniqid() . "." . $ext;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
            // ลบไฟล์เก่า ถ้ามีและไม่ใช่ค่าเริ่มต้น
            if (!empty($oldImagePath) && file_exists($oldImagePath) && strpos($oldImagePath, 'uploads/') === 0) {
                @unlink($oldImagePath);
            }
        } else {
            $newImagePath = $oldImagePath;
        }
    } else {
        $newImagePath = $oldImagePath;
    }

    // อัปเดตฐานข้อมูล
    $stmt = $conn->prepare("UPDATE uploads SET image_name = ?, image_path = ?, content = ?, contact = ?, title = ?, subtitle = ?, c_id = ?, contact_link = ?, vr_link = ? WHERE u_id = ?");
    $stmt->bind_param("ssssssissi", $imageName, $newImagePath, $content, $contact, $title, $subtitle, $c_id, $contact_link, $vr_link, $u_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'แก้ไขสำเร็จ!',
            'text' => 'ข้อมูลสถานที่ท่องเที่ยวถูกอัปเดตเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่สามารถอัปเดตข้อมูลได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_pic.php");
exit;
