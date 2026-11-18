<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connect.php'; // เชื่อมต่อฐานข้อมูล

// ระบบป้องกัน Brute-Force Rate Limiting
$max_attempts = 5;
$lockout_time = 15 * 60; // 15 นาที
$now = time();

if (isset($_SESSION['login_locked_until']) && $_SESSION['login_locked_until'] > $now) {
    $remaining = ceil(($_SESSION['login_locked_until'] - $now) / 60);
    $error_message = "คุณเข้าสู่ระบบผิดพลาดเกินกำหนด กรุณารออีก {$remaining} นาที แล้วลองใหม่อีกครั้ง";
    $login_success = false;
    $user_name = '';
} else {
    // พ้นเวลาล็อคแล้วให้รีเซ็ตสถานะ
    if (isset($_SESSION['login_locked_until']) && $_SESSION['login_locked_until'] <= $now) {
        unset($_SESSION['login_locked_until']);
        $_SESSION['login_attempts'] = 0;
    }

    $email = strtolower(trim($_POST['email_user'] ?? ''));
    $password = trim($_POST['password_user'] ?? '');

    $login_success = false;
    $user_name = '';
    $error_message = '';

    if (empty($email) || empty($password)) {
        $error_message = 'กรุณากรอกอีเมลและรหัสผ่านให้ครบถ้วน';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(email_user) = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $is_password_valid = false;

            // 1. ตรวจสอบด้วย bcrypt hash (มาตรฐาน OWASP)
            if (password_verify($password, $user['password_user'])) {
                $is_password_valid = true;
            } 
            // 2. รองรับรหัสผ่านเดิมเพื่อความต่อเนื่อง (Backward Compatibility) และแปลงเป็น hash ทันที
            elseif ($password === $user['password_user'] || md5($password) === $user['password_user']) {
                $is_password_valid = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upStmt = $conn->prepare("UPDATE users SET password_user = ? WHERE id_user = ?");
                $upStmt->bind_param("si", $newHash, $user['id_user']);
                $upStmt->execute();
                $upStmt->close();
            }

            if ($is_password_valid) {
                if ($user['role_user'] === 'admin') {
                    // ป้องกัน Session Fixation
                    session_regenerate_id(true);

                    // รีเซ็ตประวัติการพยายามล็อกอิน
                    unset($_SESSION['login_attempts']);
                    unset($_SESSION['login_locked_until']);
                    unset($_SESSION['login_error']);

                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['email_user'] = $user['email_user'];
                    $_SESSION['role_user'] = $user['role_user'];
                    $login_success = true;
                    $user_name = $user['name_user'];
                    session_write_close();
                } else {
                    $error_message = 'คุณไม่มีสิทธิ์เข้าถึงระบบผู้ดูแล';
                }
            } else {
                $error_message = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
            }
        } else {
            $error_message = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        }

        // หากล็อกอินล้มเหลว บันทึกจำนวนครั้ง
        if (!$login_success) {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= $max_attempts) {
                $_SESSION['login_locked_until'] = $now + $lockout_time;
                $error_message = "คุณเข้าสู่ระบบผิดเกิน 5 ครั้ง ระบบถูกล็อคชั่วคราวเป็นเวลา 15 นาที";
            }
        }
    }
}

if (!$login_success) {
    $_SESSION['login_error'] = $error_message;
    session_write_close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $login_success ? 'เข้าสู่ระบบสำเร็จ' : 'เข้าสู่ระบบไม่สำเร็จ' ?> - Virtual Vista PCRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Prompt', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1f4068, #162447);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            padding: 20px;
        }
        .status-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px 40px;
            border-radius: 18px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 420px;
            width: 100%;
        }
        .status-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: <?= $login_success ? '#10b981' : '#ef4444' ?>;
        }
        .status-desc {
            font-size: 15px;
            color: #555;
            margin-bottom: 20px;
        }
        .btn-redirect {
            display: inline-block;
            padding: 10px 24px;
            background: <?= $login_success ? '#10b981' : '#3498db' ?>;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="status-card">
        <h3 class="status-title"><?= $login_success ? 'เข้าสู่ระบบสำเร็จ' : 'ข้อผิดพลาด' ?></h3>
        <p class="status-desc">
            <?= $login_success ? 'ยินดีต้อนรับผู้ดูแลระบบ ' . htmlspecialchars($user_name) . '<br>กำลังพาท่านเข้าสู่หน้าแดชบอร์ด...' : htmlspecialchars($error_message) ?>
        </p>
        <a href="<?= $login_success ? 'dashboardmain.php' : 'frm_login.php' ?>" class="btn-redirect">
            <?= $login_success ? 'ไปที่แดชบอร์ดทันที' : 'กลับไปหน้าเข้าสู่ระบบ' ?>
        </a>
    </div>

    <script>
        const targetUrl = '<?= $login_success ? "dashboardmain.php" : "frm_login.php" ?>';
        function goNext() {
            window.location.href = targetUrl;
        }

        <?php if ($login_success): ?>
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'เข้าสู่ระบบสำเร็จ',
                    text: 'ยินดีต้อนรับผู้ดูแลระบบ <?= addslashes($user_name) ?>',
                    timer: 1500,
                    showConfirmButton: false
                }).then(goNext);
            } else {
                setTimeout(goNext, 800);
            }
            // Fallback safety timer
            setTimeout(goNext, 2000);
        <?php else: ?>
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'เข้าสู่ระบบไม่สำเร็จ',
                    text: '<?= addslashes($error_message) ?>',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3498db'
                }).then(goNext);
            } else {
                setTimeout(goNext, 1800);
            }
        <?php endif; ?>
    </script>
</body>
</html>
