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
    $c_id = intval($_POST['c_id'] ?? 0);
    $category_name = trim($_POST['category_name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fas fa-map-marker-alt');
    $color = trim($_POST['color'] ?? '#0284c7');

    if ($c_id <= 0 || empty($category_name)) {
        $_SESSION['flash_alert'] = [
            'icon' => 'warning',
            'title' => 'ข้อมูลไม่ครบถ้วน',
            'text' => 'กรุณากรอกชื่อหมวดหมู่'
        ];
        header("Location: dashboard_category.php");
        exit;
    }

    // ตรวจสอบชื่อหมวดหมู่ซ้ำแถวอื่น
    $checkStmt = $conn->prepare("SELECT c_id FROM categories WHERE category_name = ? AND c_id != ?");
    $checkStmt->bind_param("si", $category_name, $c_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ชื่อหมวดหมู่ซ้ำ',
            'text' => 'มีหมวดหมู่อื่นที่ใช้ชื่อนี้แล้ว'
        ];
        $checkStmt->close();
        header("Location: dashboard_category.php");
        exit;
    }
    $checkStmt->close();

    $stmt = $conn->prepare("UPDATE categories SET category_name = ?, icon = ?, color = ? WHERE c_id = ?");
    $stmt->bind_param("sssi", $category_name, $icon, $color, $c_id);

    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'สำเร็จ',
            'text' => 'บันทึกการแก้ไขหมวดหมู่เรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'ข้อผิดพลาด',
            'text' => 'ไม่สามารถแก้ไขหมวดหมู่ได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_category.php");
exit;

