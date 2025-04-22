<?php
session_start();
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u_id = $_POST['u_id'] ?? null;

    if (!$u_id) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "เกิดข้อผิดพลาด!",
                text: "ไม่พบข้อมูลผู้ใช้งาน"
            });
        </script>';
        exit;
    }

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $success = [];
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
                            $success[] = $originalName;
                        } else {
                            $errors[] = "บันทึกข้อมูลไม่สำเร็จ: " . $originalName;
                        }
                        $stmt->close();
                    } else {
                        $errors[] = "SQL Error: " . $conn->error;
                    }
                } else {
                    $errors[] = "ไม่สามารถอัปโหลดไฟล์: " . $originalName;
                }
            }
        }

        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        if (!empty($success)) {
            echo '<script>
            Swal.fire({
                icon: "success",
                title: "อัปโหลดสำเร็จ",
                text: "' . implode(", ", $success) . '"
            }).then(() => {
                window.location.href = "dashboard_spot_image.php";
            });
            </script>';
        }
        if (!empty($errors)) {
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "พบข้อผิดพลาด",
                html: "' . implode("<br>", $errors) . '"
            });
            </script>';
        }
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            Swal.fire({
            icon: "warning",
            title: "ไม่มีไฟล์ที่อัปโหลด!"
            });
        </script>';
    }
} else {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "คำขอไม่ถูกต้อง!"
        });
        </script>';
}
