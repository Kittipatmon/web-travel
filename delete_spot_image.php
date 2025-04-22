<?php
include "connect.php";

if (isset($_GET['m_id'])) {
    $m_id = $_GET['m_id'];

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
            unlink($filePath);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM images WHERE m_id = ?");
    $stmt->bind_param("i", $m_id);
    $stmt->execute();

    header("Location: dashboard_spot_image.php");
    exit;
}
