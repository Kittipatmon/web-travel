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
    $category_name = trim($_POST['category_name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fas fa-map-marker-alt');
    $color = trim($_POST['color'] ?? '#0284c7');

    if (empty($category_name)) {
        $_SESSION['flash_alert'] = [
            'icon' => 'warning',
            'title' => 'กรุณากรอกข้อมูล',
            'text' => 'กรุณากรอกชื่อหมวดหมู่'
        ];
        header("Location: dashboard_category.php");
        exit;
    }

    // ตรวจสอบชื่อหมวดหมู่ซ้ำ
    $checkStmt = $conn->prepare("SELECT c_id FROM categories WHERE category_name = ?");
    $checkStmt->bind_param("s", $category_name);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ชื่อหมวดหมู่ซ้ำ',
            'text' => 'หมวดหมู่นี้มีอยู่ในระบบแล้ว'
        ];
        $checkStmt->close();
        header("Location: dashboard_category.php");
        exit;
    }
    $checkStmt->close();

    $stmt = $conn->prepare("INSERT INTO categories (category_name, icon, color) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $category_name, $icon, $color);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'สำเร็จ',
            'text' => 'เพิ่มหมวดหมู่ใหม่เรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด',
            'text' => 'ไม่สามารถเพิ่มหมวดหมู่ได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_category.php");
exit;

