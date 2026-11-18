<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ผู้ดูแลระบบ (Admin Role Verification)
if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

include "connect.php";

if (isset($_GET['m_id'])) {
    $m_id = intval($_GET['m_id']);

    // ดึงชื่อไฟล์จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT filename FROM images WHERE m_id = ?");
    $stmt->bind_param("i", $m_id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
    $stmt->close();

    // ลบไฟล์ในโฟลเดอร์ uploads
    if (!empty($filename)) {
        $filePath = "uploads/" . $filename;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM images WHERE m_id = ?");
    $stmt->bind_param("i", $m_id);
    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'ลบข้อมูลสำเร็จ!',
            'text' => 'รูปภาพถูกลบเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่สามารถลบรูปภาพได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_spot_image.php");
exit;
