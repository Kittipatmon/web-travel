<?php
include "connect.php";

if (isset($_GET['u_id'])) {
    $u_id = $_GET['u_id'];

    // ลบไฟล์ภาพก่อน
    $stmt = $conn->prepare("SELECT image_path FROM uploads WHERE u_id = ?");
    $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM uploads WHERE u_id = ?");
    $stmt->bind_param("i", $u_id);
    $stmt->execute();

    header("Location: dashboard_pic.php");
    exit;
}
