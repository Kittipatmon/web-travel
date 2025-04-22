<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = filter_var($_POST['image_name'], FILTER_SANITIZE_STRING);
    $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
    $contact = filter_var($_POST['contact'], FILTER_SANITIZE_STRING);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $subtitle = filter_var($_POST['subtitle'], FILTER_SANITIZE_STRING);
    $contact_link = filter_var($_POST['contact_link'], FILTER_SANITIZE_URL);
    $vr_link = filter_var($_POST['vr_link'], FILTER_SANITIZE_URL);

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
        $fileType = mime_content_type($file['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2 MB

        if (!in_array($fileType, $allowedTypes)) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: '❌ ประเภทไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ JPEG, PNG, GIF)',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = 'dashboard_pic.php';
                });
            </script>";
            exit;
        }

        if ($file['size'] > $maxFileSize) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: '❌ ขนาดไฟล์เกิน 2 MB',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = 'dashboard_pic.php';
                });
            </script>";
            exit;
        }

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
