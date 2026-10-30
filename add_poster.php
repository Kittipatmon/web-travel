<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $link_url = trim($_POST['link_url'] ?? '');
    $sort_order = isset($_POST['sort_order']) && $_POST['sort_order'] !== '' ? intval($_POST['sort_order']) : 1;
    $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

    if (empty($title)) {
        $_SESSION['flash_alert'] = [
            'icon' => 'warning',
            'title' => 'กรุณากรอกข้อมูล',
            'text' => 'กรุณากรอกชื่อโปสเตอร์ / แคมเปญ'
        ];
        header("Location: dashboard_poster.php");
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowedExts)) {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'ไฟล์ไม่ถูกต้อง',
                'text' => 'กรุณาอัปโหลดไฟล์รูปภาพประเภท JPG, PNG, WEBP หรือ GIF'
            ];
            header("Location: dashboard_poster.php");
            exit;
        }

        $uploadDir = "uploads/posters/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $targetPath = $uploadDir . time() . "_" . uniqid() . "." . $ext;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO posters (title, subtitle, image_path, link_url, status, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $title, $subtitle, $targetPath, $link_url, $status, $sort_order);

            if ($stmt->execute()) {
                $_SESSION['flash_alert'] = [
                    'icon' => 'success',
                    'title' => 'เพิ่มสำเร็จ',
                    'text' => 'เพิ่มโปสเตอร์โปรโมทใหม่เรียบร้อยแล้ว'
                ];
            } else {
                $_SESSION['flash_alert'] = [
                    'icon' => 'error',
                    'title' => 'ข้อผิดพลาด',
                    'text' => 'ไม่สามารถบันทึกข้อมูลได้: ' . $stmt->error
                ];
            }
            $stmt->close();
        } else {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'อัปโหลดล้มเหลว',
                'text' => 'ไม่สามารถบันทึกไฟล์ภาพโปสเตอร์ได้'
            ];
        }
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'warning',
            'title' => 'กรุณาเลือกรูปภาพ',
            'text' => 'กรุณาเลือกไฟล์ภาพโปสเตอร์โปรโมท'
        ];
    }
}

header("Location: dashboard_poster.php");
exit;

