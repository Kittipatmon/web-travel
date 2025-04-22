<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = $_POST['image_name'];
    $content = $_POST['content'];
    $contact = $_POST['contact'];
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $contact_link = $_POST['contact_link'];
    $vr_link = $_POST['vr_link'];

    // ตรวจสอบชื่อสถานที่ซ้ำ
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM uploads WHERE image_name = ?");
    $checkStmt->bind_param("s", $imageName);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: '❌ ชื่อสถานที่นี้มีอยู่ในระบบแล้ว',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                window.location.href = 'dashboard_pic.php';
            });
        </script>";
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $uploadDir = "uploads/";
        $targetFilePath = $uploadDir . time() . "_" . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO uploads (image_name, image_path, content, contact, title, subtitle, contact_link, vr_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $imageName, $targetFilePath, $content, $contact, $title, $subtitle, $contact_link, $vr_link);
            if ($stmt->execute()) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: '✅ อัปโหลดรูปภาพสำเร็จ!',
                        confirmButtonText: 'ตกลง',
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'dashboard_pic.php';
                    });
                </script>";
                exit;
            } else {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: '❌ เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        window.location.href = 'dashboard_pic.php';
                    });
                </script>";
                exit;
            }
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: '❌ อัปโหลดไฟล์ล้มเหลว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = 'dashboard_pic.php';
                });
            </script>";
            exit;
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: '❌ เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . $_FILES['image']['error'] . "',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                window.location.href = 'dashboard_pic.php';
            });
        </script>";
        exit;
    }
}
