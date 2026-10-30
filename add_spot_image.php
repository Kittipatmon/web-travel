<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u_id = intval($_POST['u_id'] ?? 0);

    if (!$u_id) {
        $_SESSION['flash_alert'] = [
            'icon' => 'error',
            'title' => 'เกิดข้อผิดพลาด!',
            'text' => 'กรุณาเลือกสถานที่ท่องเที่ยว'
        ];
        header("Location: dashboard_spot_image.php");
        exit;
    }

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $success = 0;
    $errors = [];

    if (isset($_FILES['images']['tmp_name']) && is_array($_FILES['images']['tmp_name'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) {
                $originalName = basename($_FILES['images']['name'][$key]);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newFileName = uniqid('img_', true) . '.' . $extension;
                $targetFilePath = $targetDir . $newFileName;

                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $stmt = $conn->prepare("INSERT INTO images (filename, uploaded_at, u_id) VALUES (?, NOW(), ?)");
                    if ($stmt) {
                        $stmt->bind_param("si", $newFileName, $u_id);
                        if ($stmt->execute()) {
                            $success++;
                        } else {
                            $errors[] = $originalName;
                        }
                        $stmt->close();
                    }
                } else {
                    $errors[] = $originalName;
                }
            }
        }

        if ($success > 0) {
            $_SESSION['flash_alert'] = [
                'icon' => 'success',
                'title' => 'อัปโหลดสำเร็จ!',
                'text' => 'อัปโหลดรูปภาพจำนวน ' . $success . ' รูปเรียบร้อยแล้ว'
            ];
        } else {
            $_SESSION['flash_alert'] = [
                'icon' => 'error',
                'title' => 'เกิดข้อผิดพลาด!',
                'text' => 'ไม่สามารถอัปโหลดรูปภาพได้ กรุณาตรวจสอบไฟล์'
            ];
        }
    } else {
        $_SESSION['flash_alert'] = [
            'icon' => 'warning',
            'title' => 'แจ้งเตือน',
            'text' => 'ไม่มีไฟล์ที่ถูกเลือกอัปโหลด'
        ];
    }
}

header("Location: dashboard_spot_image.php");
exit;
