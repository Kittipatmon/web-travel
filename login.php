<?php
session_start();
require_once 'connect.php'; // เชื่อมต่อฐานข้อมูล

$email = strtolower(trim($_POST['email_user'] ?? ''));
$password = trim($_POST['password_user'] ?? '');

$stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(email_user) = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if ($password === $user['password_user']) {
        if ($user['role_user'] === 'admin') {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['email_user'] = $user['email_user'];
            $_SESSION['role_user'] = $user['role_user'];

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'เข้าสู่ระบบสำเร็จ',
                    text: 'ยินดีต้อนรับผู้ดูแลระบบ {$user['name_user']}'
                }).then(() => {
                    window.location.href = 'dashboardmain.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'คุณไม่มีสิทธิ์เข้าถึง'
                }).then(() => {
                    window.location.href = 'frm_login.php';
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'รหัสผ่านไม่ถูกต้อง'
            }).then(() => {
                window.location.href = 'frm_login.php';
            });
        </script>";
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด',
            text: 'ไม่พบผู้ใช้งานที่มีอีเมลนี้'
        }).then(() => {
            window.location.href = 'frm_login.php';
        });
    </script>";
}
