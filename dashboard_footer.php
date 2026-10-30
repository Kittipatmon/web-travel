<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'กรุณาเข้าสู่ระบบ!',
            text: 'คุณต้องเข้าสู่ระบบในฐานะผู้ดูแลระบบเพื่อเข้าถึงหน้านี้',
            icon: 'error'
        }).then(() => {
            window.location='frm_login.php'; 
        });
    </script>";
    exit;
}

// ฟังก์ชันดึงค่าการตั้งค่าทั้งหมด
function get_all_settings($conn) {
    $res = $conn->query("SELECT setting_key, setting_value FROM site_settings");
    $settings = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

$success_msg = '';
$error_msg = '';

// บันทึกข้อมูลเมื่อมีการ Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_footer') {
    $footer_copyright = trim($_POST['footer_copyright'] ?? '');
    $footer_brand_title = trim($_POST['footer_brand_title'] ?? '');
    $footer_brand_subtitle = trim($_POST['footer_brand_subtitle'] ?? '');
    $footer_desc = trim($_POST['footer_desc'] ?? '');
    $footer_fb_url = trim($_POST['footer_fb_url'] ?? '');
    $footer_tw_url = trim($_POST['footer_tw_url'] ?? '');
    $footer_yt_url = trim($_POST['footer_yt_url'] ?? '');
    $footer_email_url = trim($_POST['footer_email_url'] ?? '');

    if (empty($footer_copyright)) {
        $footer_copyright = '© ' . (date('Y') + 543) . ' Virtual Vista PCRU. All Rights Reserved.';
    }

    $updates = [
        'footer_copyright' => $footer_copyright,
        'footer_brand_title' => $footer_brand_title,
        'footer_brand_subtitle' => $footer_brand_subtitle,
        'footer_desc' => $footer_desc,
        'footer_fb_url' => $footer_fb_url,
        'footer_tw_url' => $footer_tw_url,
        'footer_yt_url' => $footer_yt_url,
        'footer_email_url' => $footer_email_url
    ];

    $all_ok = true;
    foreach ($updates as $k => $v) {
        $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $k, $v, $v);
        if (!$stmt->execute()) {
            $all_ok = false;
        }
    }

    if ($all_ok) {
        $success_msg = 'บันทึกการตั้งค่าข้อมูล Footer เรียบร้อยแล้ว';
    } else {
        $error_msg = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง';
    }
}

$settings = get_all_settings($conn);
$footer_copyright = $settings['footer_copyright'] ?? '© 2569 Virtual Vista PCRU. All Rights Reserved.';
$footer_brand_title = $settings['footer_brand_title'] ?? 'VIRTUAL VISTA';
$footer_brand_subtitle = $settings['footer_brand_subtitle'] ?? 'PCRU.AC.TH / NAPA TRAVEL';
$footer_desc = $settings['footer_desc'] ?? 'เว็บท่องเที่ยว Virtual Vista PCRU รวมข้อมูลข่าวสารสถานที่ท่องเที่ยวในตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ สัมผัสธรรมชาติ จุดชมวิว ลานกางเต็นท์ และระบบท่องเที่ยวเสมือนจริง 360° จัดทริปท่องเที่ยวง่ายๆ ผ่านเว็บไซต์ของเรา';
$footer_fb_url = $settings['footer_fb_url'] ?? 'https://www.facebook.com';
$footer_tw_url = $settings['footer_tw_url'] ?? 'https://twitter.com';
$footer_yt_url = $settings['footer_yt_url'] ?? 'https://youtube.com';
$footer_email_url = $settings['footer_email_url'] ?? 'contact.php';

// ดึงข้อมูล Admin
$name_user = 'ผู้ดูแลระบบ';
$userResult = $conn->query("SELECT name_user FROM users WHERE role_user='admin' LIMIT 1");
if ($userResult && $userResult->num_rows > 0) {
    $userData = $userResult->fetch_assoc();
    $name_user = $userData['name_user'];
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ Footer เว็บไซต์ - Virtual Vista Admin</title>
    
    <!-- Bootstrap 5 & AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">
    
    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern-travel.css">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8fafc;
        }

        .page-header-box {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 16px;
            padding: 24px 28px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
            margin-bottom: 24px;
        }

        .settings-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .settings-card .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 24px;
        }

        .preview-footer-box {
            background: linear-gradient(135deg, #f97316 0%, #ff8c00 24%, #ff991b 52%, #ffa726 80%, #ffb74d 100%);
            border-radius: 14px;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.25);
            position: relative;
            overflow: hidden;
        }

        .preview-footer-box .preview-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 14px;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .preview-footer-box .preview-bottom-bar {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13.5px;
            margin-top: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff991b;
            box-shadow: 0 0 0 0.25rem rgba(255, 153, 27, 0.2);
        }

        .btn-orange-primary {
            background: linear-gradient(135deg, #f97316 0%, #ff991b 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            transition: all 0.2s ease;
        }

        .btn-orange-primary:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.35);
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Top Navigation Bar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="ย่อ/ขยายเมนู">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link" href="index.php" target="_blank">
                        <i class="fas fa-globe mr-1 text-primary"></i> หน้าหลักเว็บไซต์
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link" href="dashboardmain.php">
                        <i class="fas fa-tachometer-alt mr-1 text-info"></i> แดชบอร์ด
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item mr-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-2 text-right d-none d-md-block">
                            <div style="font-size: 11px; color: #64748b; font-weight: 500;">เข้าสู่ระบบในฐานะ</div>
                            <div style="font-size: 13.5px; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($name_user); ?></div>
                        </div>
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #2563eb); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 15px;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="btn btn-sm btn-outline-danger shadow-sm" href="logout.php" style="border-radius: 8px; font-weight: 500; padding: 6px 14px;">
                        <i class="fas fa-sign-out-alt mr-1"></i> ออกจากระบบ
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar elevation-4">
            <a href="dashboardmain.php" class="brand-link" align="center">
                <img src="assets/images/logovr.png" style="max-height: 46px; width: auto;">
            </a>
            <?php include "sidebar.php"; ?>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="container-fluid px-4 py-4">

                <!-- Header Box -->
                <div class="page-header-box d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 6px;">Website Settings</span>
                            <h1 class="h3 font-weight-bold m-0 text-white">จัดการข้อมูลส่วนท้าย (Footer)</h1>
                        </div>
                        <p class="text-sm text-gray-300 m-0">
                            แก้ไขข้อความลิขสิทธิ์ © Copyright, ข้อมูลโครงการ, และช่องทางโซเชียลมีเดียที่แสดงผลท้ายเว็บทุกหน้า
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="index.php" target="_blank" class="btn btn-light btn-sm shadow-sm font-weight-500" style="border-radius: 8px;">
                            <i class="fas fa-external-link-alt mr-1"></i> ดูหน้าเว็บจริง
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Settings Form -->
                    <div class="col-lg-7">
                        <div class="settings-card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0 font-weight-bold text-gray-800 d-flex align-items-center gap-2">
                                    <i class="fas fa-sliders-h text-warning"></i>
                                    ฟอร์มแก้ไขข้อมูล Footer
                                </h5>
                                <span class="badge bg-light text-secondary border">แสดงผลทุกหน้าเว็บ</span>
                            </div>

                            <form action="dashboard_footer.php" method="POST" id="footerForm" class="p-4">
                                <input type="hidden" name="action" value="save_footer">

                                <!-- 1. Copyright Text -->
                                <div class="mb-4">
                                    <label class="form-label font-weight-bold text-gray-800 d-flex justify-content-between align-items-center">
                                        <span><i class="far fa-copyright text-primary mr-1"></i> ข้อความลิขสิทธิ์ (Copyright Text) <span class="text-danger">*</span></span>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPresetCopyright('© 2569 Virtual Vista PCRU. All Rights Reserved.')" style="font-size: 11px;">
                                            ใช้ค่า: © 2569 Virtual Vista PCRU. All Rights Reserved.
                                        </button>
                                    </label>
                                    <input type="text" class="form-control form-control-lg font-weight-bold" name="footer_copyright" id="inputCopyright" 
                                           value="<?= htmlspecialchars($footer_copyright); ?>" required oninput="updateLivePreview()">
                                    <div class="form-text text-muted" style="font-size: 12px;">
                                        ตัวอย่าง: <code>© 2569 Virtual Vista PCRU. All Rights Reserved.</code> หรือ <code>Copyright © 2025 - 2026 Virtual Vista PCRU. All Rights Reserved.</code>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- 2. Brand Title -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-600 text-gray-700">
                                            <i class="fas fa-heading mr-1 text-info"></i> ชื่อแบรนด์หลัก (Brand Title)
                                        </label>
                                        <input type="text" class="form-control" name="footer_brand_title" id="inputBrandTitle"
                                               value="<?= htmlspecialchars($footer_brand_title); ?>" oninput="updateLivePreview()">
                                    </div>

                                    <!-- 3. Brand Subtitle -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-600 text-gray-700">
                                            <i class="fas fa-globe mr-1 text-info"></i> สโลแกน / โดเมน (Subtitle)
                                        </label>
                                        <input type="text" class="form-control" name="footer_brand_subtitle" id="inputBrandSubtitle"
                                               value="<?= htmlspecialchars($footer_brand_subtitle); ?>" oninput="updateLivePreview()">
                                    </div>
                                </div>

                                <!-- 4. Project Description -->
                                <div class="mb-3">
                                    <label class="form-label font-weight-600 text-gray-700">
                                        <i class="fas fa-align-left mr-1 text-info"></i> คำอธิบายโครงการ / รายละเอียดใน Footer (Col 1)
                                    </label>
                                    <textarea class="form-control" name="footer_desc" id="inputDesc" rows="3" oninput="updateLivePreview()"><?= htmlspecialchars($footer_desc); ?></textarea>
                                </div>

                                <!-- 5. Social Media Links -->
                                <div class="card p-3 bg-light border-0 rounded-3 mb-4">
                                    <h6 class="font-weight-bold text-gray-800 mb-3 d-flex align-items-center gap-2" style="font-size: 14px;">
                                        <i class="fas fa-share-alt text-primary"></i> ลิงก์โซเชียลมีเดีย (Follow Us)
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white text-primary"><i class="fab fa-facebook-f"></i></span>
                                                <input type="text" class="form-control form-control-sm" name="footer_fb_url" id="inputFb" 
                                                       value="<?= htmlspecialchars($footer_fb_url); ?>" placeholder="Facebook URL">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white text-info"><i class="fab fa-twitter"></i></span>
                                                <input type="text" class="form-control form-control-sm" name="footer_tw_url" id="inputTw" 
                                                       value="<?= htmlspecialchars($footer_tw_url); ?>" placeholder="Twitter / X URL">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white text-danger"><i class="fab fa-youtube"></i></span>
                                                <input type="text" class="form-control form-control-sm" name="footer_yt_url" id="inputYt" 
                                                       value="<?= htmlspecialchars($footer_yt_url); ?>" placeholder="YouTube URL">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white text-warning"><i class="fas fa-envelope"></i></span>
                                                <input type="text" class="form-control form-control-sm" name="footer_email_url" id="inputMail" 
                                                       value="<?= htmlspecialchars($footer_email_url); ?>" placeholder="Email / Contact page URL">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" class="btn btn-orange-primary shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="fas fa-save"></i>
                                        <span>บันทึกการตั้งค่า Footer</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetDefaults()">
                                        <i class="fas fa-undo mr-1"></i> คืนค่าเริ่มต้น
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column: Live Interactive Preview -->
                    <div class="col-lg-5">
                        <div class="settings-card sticky-top" style="top: 20px;">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0 font-weight-bold text-gray-800 d-flex align-items-center gap-2">
                                    <i class="fas fa-eye text-primary"></i>
                                    ตัวอย่างแสดงผลสด (Live Preview)
                                </h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fas fa-bolt mr-1"></i> Real-time
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted text-xs mb-3">
                                    รูปแบบการแสดงผลจริงที่ปรากฏบริเวณส่วนล่างของหน้าหลัก, หน้ารายการทั้งหมด และหน้าติดต่อ:
                                </p>

                                <!-- Mock Footer Box -->
                                <div class="preview-footer-box">
                                    <div class="preview-top-row">
                                        <div>
                                            <div class="font-weight-bold" style="font-size: 18px; letter-spacing: 0.5px;" id="prevBrandTitle">
                                                <?= htmlspecialchars($footer_brand_title); ?>
                                            </div>
                                            <div style="font-size: 11px; opacity: 0.9;" id="prevBrandSubtitle">
                                                <?= htmlspecialchars($footer_brand_subtitle); ?>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <span style="font-size: 11px; font-weight: 600; margin-right: 4px;">FOLLOW US</span>
                                            <span style="width: 26px; height: 26px; border-radius: 6px; background: rgba(255,255,255,0.25); display: inline-flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fab fa-facebook-f"></i></span>
                                            <span style="width: 26px; height: 26px; border-radius: 6px; background: rgba(255,255,255,0.25); display: inline-flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fab fa-twitter"></i></span>
                                            <span style="width: 26px; height: 26px; border-radius: 6px; background: rgba(255,255,255,0.25); display: inline-flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fab fa-youtube"></i></span>
                                        </div>
                                    </div>

                                    <div style="font-size: 12px; line-height: 1.5; opacity: 0.95;" id="prevDesc">
                                        <?= htmlspecialchars($footer_desc); ?>
                                    </div>

                                    <!-- Bottom Copyright Bar Preview -->
                                    <div class="preview-bottom-bar">
                                        <div class="font-weight-600" id="prevCopyright">
                                            <?= htmlspecialchars($footer_copyright); ?>
                                        </div>
                                        <div style="width: 26px; height: 26px; border-radius: 50%; background: rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; font-size: 11px;">
                                            <i class="fas fa-chevron-up"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 bg-light rounded-3 border" style="font-size: 13px;">
                                    <div class="font-weight-bold text-gray-800 mb-1">
                                        <i class="fas fa-info-circle text-info mr-1"></i> หมายเหตุการทำงาน:
                                    </div>
                                    <ul class="m-0 pl-3 text-muted">
                                        <li>เมื่อกดบันทึก ข้อมูลลิขสิทธิ์จะถูกอัปเดตลงฐานข้อมูล <code>site_settings</code> ทันที</li>
                                        <li>ทุกหน้าเว็บที่เรียกใช้ฟุตเตอร์ (เช่น <code>index.php</code>, <code>all_destinations.php</code>, <code>contact.php</code>) จะแสดงผลข้อความลิขสิทธิ์ที่ตั้งค่าไว้อย่างเป็นระเบียบและสอดคล้องกัน</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function updateLivePreview() {
            const copyright = document.getElementById('inputCopyright').value;
            const brandTitle = document.getElementById('inputBrandTitle').value;
            const brandSub = document.getElementById('inputBrandSubtitle').value;
            const desc = document.getElementById('inputDesc').value;

            document.getElementById('prevCopyright').textContent = copyright || '© 2569 Virtual Vista PCRU. All Rights Reserved.';
            document.getElementById('prevBrandTitle').textContent = brandTitle || 'VIRTUAL VISTA';
            document.getElementById('prevBrandSubtitle').textContent = brandSub || 'PCRU.AC.TH / NAPA TRAVEL';
            document.getElementById('prevDesc').textContent = desc || '';
        }

        function setPresetCopyright(val) {
            document.getElementById('inputCopyright').value = val;
            updateLivePreview();
        }

        function resetDefaults() {
            Swal.fire({
                title: 'คืนค่าเริ่มต้น?',
                text: 'ต้องการคืนค่าฟุตเตอร์เป็นค่าเริ่มต้นของระบบใช่หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ff991b',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ใช่, คืนค่าเริ่มต้น',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('inputCopyright').value = '© 2569 Virtual Vista PCRU. All Rights Reserved.';
                    document.getElementById('inputBrandTitle').value = 'VIRTUAL VISTA';
                    document.getElementById('inputBrandSubtitle').value = 'PCRU.AC.TH / NAPA TRAVEL';
                    document.getElementById('inputDesc').value = 'เว็บท่องเที่ยว Virtual Vista PCRU รวมข้อมูลข่าวสารสถานที่ท่องเที่ยวในตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ สัมผัสธรรมชาติ จุดชมวิว ลานกางเต็นท์ และระบบท่องเที่ยวเสมือนจริง 360° จัดทริปท่องเที่ยวง่ายๆ ผ่านเว็บไซต์ของเรา';
                    document.getElementById('inputFb').value = 'https://www.facebook.com';
                    document.getElementById('inputTw').value = 'https://twitter.com';
                    document.getElementById('inputYt').value = 'https://youtube.com';
                    document.getElementById('inputMail').value = 'contact.php';
                    updateLivePreview();
                }
            });
        }

        <?php if (!empty($success_msg)): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= addslashes($success_msg); ?>',
                confirmButtonColor: '#ff991b',
                confirmButtonText: 'ตกลง'
            });
        <?php elseif (!empty($error_msg)): ?>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: '<?= addslashes($error_msg); ?>',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'ตกลง'
            });
        <?php endif; ?>
    </script>
</body>
</html>

