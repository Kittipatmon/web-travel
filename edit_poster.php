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
    $p_id = intval($_POST['p_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $link_url = trim($_POST['link_url'] ?? '');
    $sort_order = isset($_POST['sort_order']) && $_POST['sort_order'] !== '' ? intval($_POST['sort_order']) : 1;
    $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';
    $old_image_path = trim($_POST['old_image_path'] ?? '');

    if ($p_id <= 0 || empty($title)) {
        $_SESSION['flash_alert'] = [
            'icon' => 'warning',
            'title' => 'ข้อมูลไม่ครบถ้วน',
            'text' => 'กรุณากรอกชื่อโปสเตอร์'
        ];
        header("Location: dashboard_poster.php");
        exit;
    }

    $imagePath = $old_image_path;

    // ถ้ามีการอัปโหลดไฟล์รูปใหม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($ext, $allowedExts)) {
            $uploadDir = "uploads/posters/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $targetPath = $uploadDir . time() . "_" . uniqid() . "." . $ext;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // ลบรูปเก่าถ้าอยู่ใน uploads/
                if (!empty($old_image_path) && strpos($old_image_path, 'uploads/') === 0 && file_exists($old_image_path)) {
                    @unlink($old_image_path);
                }
                $imagePath = $targetPath;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE posters SET title = ?, subtitle = ?, image_path = ?, link_url = ?, status = ?, sort_order = ? WHERE p_id = ?");
    $stmt->bind_param("sssssii", $title, $subtitle, $imagePath, $link_url, $status, $sort_order, $p_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'สำเร็จ',
            'text' => 'บันทึกการแก้ไขโปสเตอร์โปรโมทเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด',
            'text' => 'ไม่สามารถบันทึกข้อมูลได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_poster.php");
exit;

