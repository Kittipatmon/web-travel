<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if (isset($_GET['u_id'])) {
    $u_id = intval($_GET['u_id']);

    // 1. ลบไฟล์ภาพหลักใน uploads
    $stmt = $conn->prepare("SELECT image_path FROM uploads WHERE u_id = ?");
    $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if (!empty($imagePath) && file_exists($imagePath)) {
        @unlink($imagePath);
    }

    // 2. ลบไฟล์ภาพแกลเลอรีในตาราง images ที่เกี่ยวข้อง
    $imgStmt = $conn->prepare("SELECT filename FROM images WHERE u_id = ?");
    $imgStmt->bind_param("i", $u_id);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();
    while ($rowImg = $imgResult->fetch_assoc()) {
        $imgPath = "uploads/" . $rowImg['filename'];
        if (!empty($rowImg['filename']) && file_exists($imgPath)) {
            @unlink($imgPath);
        }
    }
    $imgStmt->close();

    // 3. ลบข้อมูลจากตารางย่อยทั้งหมดที่เชื่อมโยงกับ u_id
    $delImages = $conn->prepare("DELETE FROM images WHERE u_id = ?");
    $delImages->bind_param("i", $u_id);
    $delImages->execute();
    $delImages->close();

    $delSpots = $conn->prepare("DELETE FROM tourist_spot WHERE u_id = ?");
    $delSpots->bind_param("i", $u_id);
    $delSpots->execute();
    $delSpots->close();

    $delFee = $conn->prepare("DELETE FROM service_fee WHERE u_id = ?");
    $delFee->bind_param("i", $u_id);
    $delFee->execute();
    $delFee->close();

    // 4. ลบข้อมูลจากตาราง uploads
    $stmt = $conn->prepare("DELETE FROM uploads WHERE u_id = ?");
    $stmt->bind_param("i", $u_id);
    if ($stmt->execute()) {
        $_SESSION['flash_alert'] = [
            'icon' => 'success',
            'title' => 'ลบข้อมูลสำเร็จ!',
            'text' => 'ข้อมูลและไฟล์ที่เกี่ยวข้องถูกลบเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'ไม่สามารถลบข้อมูลได้: ' . $stmt->error
        ];
    }
    $stmt->close();
}

header("Location: dashboard_pic.php");
exit;
