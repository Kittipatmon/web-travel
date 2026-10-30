<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    echo "<!DOCTYPE html><html lang='th'><head><meta charset='UTF-8'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";
    echo "<script>
      Swal.fire({
          title: 'กรุณาเข้าสู่ระบบ!',
          text: 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
          icon: 'error'
      }).then(() => {
          window.location='frm_login.php'; 
      });
    </script></body></html>";
    exit;
}

// ดึงข้อมูลหมวดหมู่ทั้งหมดสำหรับใส่ใน dropdown
$catRes = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categoriesList = $catRes ? $catRes->fetch_all(MYSQLI_ASSOC) : [];

// ดึงข้อมูลสถานที่ท่องเที่ยวทั้งหมดเชื่อมกับหมวดหมู่
$stmt = $conn->query("
    SELECT u.*, c.category_name, c.icon AS cat_icon, c.color AS cat_color 
    FROM uploads u 
    LEFT JOIN categories c ON u.c_id = c.c_id 
    ORDER BY u.created_at DESC
");
$uploads = $stmt ? $stmt->fetch_all(MYSQLI_ASSOC) : [];
$totalLocations = count($uploads);
$totalViews = array_sum(array_column($uploads, 'view_count'));
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการอัปโหลดสถานที่ท่องเที่ยว - Virtual Vista</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Google Fonts Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            overflow-y: auto;
            background-color: #f8fafc;
        }

        .content-wrapper {
            min-height: calc(100vh - 55px);
            overflow: visible;
            background-color: #f8fafc;
        }

        .navbar-light .navbar-nav .nav-link {
            color: #ff6f00;
        }

        .text-light {
            color: #fff !important;
            font-weight: bold;
        }

        /* Modern Table Styling */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table-custom thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border: none;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .table-custom td {
            padding: 14px 16px;
            vertical-align: middle;
            color: #334155;
            font-size: 14px;
            border-top: 1px solid #f1f5f9;
        }

        /* Modern Card Styling */
        .spot-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .spot-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.12);
        }

        .spot-card-img-wrapper {
            position: relative;
            height: 190px;
            overflow: hidden;
            background-color: #e2e8f0;
        }

        .spot-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .spot-card:hover .spot-card-img {
            transform: scale(1.05);
        }

        .spot-thumb {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
        }

        .spot-thumb:hover {
            transform: scale(1.15);
        }

        /* Action Buttons */
        .btn-action-group {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .view-toggle-btn {
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .view-toggle-btn.active {
            background-color: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
        }
    </style>
</head>

<body class="hold-transition sidebar-mini sidebar-mini-md">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link" href="dashboardmain.php">หน้าหลัก</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">คู่มือการใช้งาน</a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar elevation-4">
            <h3 class="brand-link" align="center">
                <img src="assets/images/logovr.png" style="width:100%; height:60px; margin-top:10px; filter:drop-shadow(5px 0px 5px grey);">
            </h3>
            <hr class="hr-white">
            <div align="center">
                <a class="nav-link text-light" href="logout.php"><i class="fas fa-sign-in-alt"></i> Logout</a>
            </div>
            <hr class="hr-white">
            <?php include "sidebar.php"; ?>
        </aside>

        <!-- Content Area -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid py-4 px-3 px-md-4">

                    <!-- Header Banner -->
                    <div class="mb-4 p-4 rounded-4 shadow-sm text-white d-flex justify-content-between align-items-center flex-wrap gap-3"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 18px;">
                        <div>
                            <h3 class="m-0 font-weight-bold" style="font-size: 24px;">
                                <i class="fas fa-images mr-2"></i>รายการสถานที่ท่องเที่ยว (อัปโหลด)
                            </h3>
                            <p class="m-0 text-white-50 text-sm mt-1">
                                จัดการรูปภาพหลัก ข้อมูลสถานที่ ช่องทางติดต่อ ลิงก์ทัวร์เสมือนจริง VR 360° และสถิติการเข้าชม
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light px-3 py-2 font-weight-bold shadow-sm"
                                style="border-radius: 10px; color: #059669;"
                                data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fas fa-plus-circle mr-1"></i> อัปโหลดสถานที่ใหม่
                            </button>
                        </div>
                    </div>

                    <!-- Main Container Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                        
                        <!-- Toolbar Header -->
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="m-0 font-weight-bold text-gray-800" style="font-size: 16px;">
                                    <i class="fas fa-layer-group text-success mr-2"></i>ข้อมูลสถานที่ท่องเที่ยว
                                </h5>
                                <span class="badge px-3 py-2" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 13px; font-weight: 600; border-radius: 8px;">
                                    ทั้งหมด <?= number_format($totalLocations) ?> แห่ง
                                </span>
                                <span class="badge px-3 py-2" style="background: rgba(14, 165, 233, 0.1); color: #0284c7; font-size: 13px; font-weight: 600; border-radius: 8px;">
                                    <i class="fas fa-eye me-1"></i>เข้าชมรวม <?= number_format($totalViews) ?> ครั้ง
                                </span>
                            </div>

                            <!-- Controls: View Toggle & Search -->
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <!-- Search input -->
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="ค้นหาชื่อสถานที่ หรือตำบล...">
                                </div>

                                <!-- View Mode Buttons -->
                                <div class="btn-group btn-group-sm bg-light p-1 rounded-3 border" role="group">
                                    <button type="button" class="btn btn-sm view-toggle-btn active" id="btnViewTable" onclick="switchView('table')">
                                        <i class="fas fa-table mr-1"></i> ตาราง
                                    </button>
                                    <button type="button" class="btn btn-sm view-toggle-btn" id="btnViewGrid" onclick="switchView('grid')">
                                        <i class="fas fa-th-large mr-1"></i> การ์ด
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 1. TABLE VIEW (Default) -->
                        <div id="tableViewSection" class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table-custom mb-0" id="mainSpotTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 60px;">ลำดับ</th>
                                            <th style="min-width: 250px;">รูปภาพ & สถานที่ท่องเที่ยว</th>
                                            <th style="min-width: 200px;">ตำแหน่ง & การติดต่อ</th>
                                            <th class="text-center" style="width: 140px;">ยอดเข้าชม</th>
                                            <th style="width: 170px;">วันที่อัปโหลด</th>
                                            <th class="text-center" style="width: 160px;">การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($uploads)): ?>
                                            <?php foreach ($uploads as $index => $upload): 
                                                $u_id = $upload['u_id'];
                                                $imageName = htmlspecialchars($upload['image_name'] ?? '');
                                                $imagePath = htmlspecialchars($upload['image_path'] ?? 'assets/images/default.jpg');
                                                $title = htmlspecialchars($upload['title'] ?? '');
                                                $subtitle = htmlspecialchars($upload['subtitle'] ?? '');
                                                $contact = htmlspecialchars($upload['contact'] ?? '');
                                                $contactLink = htmlspecialchars($upload['contact_link'] ?? '');
                                                $vrLink = htmlspecialchars($upload['vr_link'] ?? '');
                                                $content = htmlspecialchars($upload['content'] ?? '');
                                                $viewCount = (int)($upload['view_count'] ?? 0);
                                                $createdAt = !empty($upload['created_at']) ? thai_date_format($upload['created_at']) : '-';
                                            ?>
                                                <tr class="spot-search-row" data-search="<?= strtolower($imageName . ' ' . $title . ' ' . $subtitle . ' ' . $contact) ?>">
                                                    <!-- ลำดับ -->
                                                    <td class="text-center font-weight-bold text-muted">
                                                        <?= $index + 1 ?>
                                                    </td>

                                                    <!-- รูปภาพ & สถานที่ -->
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <a href="<?= $imagePath ?>" target="_blank" title="คลิกดูภาพขนาดเต็ม">
                                                                <img src="<?= $imagePath ?>" class="spot-thumb" alt="<?= $imageName ?>" onerror="this.src='assets/images/default.jpg'">
                                                            </a>
                                                            <div>
                                                                <div class="font-weight-bold text-dark" style="font-size: 15px;">
                                                                    <?= $imageName ?>
                                                                </div>
                                                                <?php if (!empty($subtitle)): ?>
                                                                    <div class="text-muted small mt-1" style="max-width: 280px; white-space: normal; line-height: 1.4;">
                                                                        <?= $subtitle ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="d-flex gap-1 mt-1 flex-wrap align-items-center">
                                                                    <?php if (!empty($upload['category_name'])): ?>
                                                                        <span class="badge" style="background-color: <?= htmlspecialchars($upload['cat_color'] ?: '#0284c7') ?>; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 6px;">
                                                                            <i class="<?= htmlspecialchars($upload['cat_icon'] ?: 'fas fa-tag') ?> me-1"></i> <?= htmlspecialchars($upload['category_name']) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($vrLink)): ?>
                                                                        <a href="<?= $vrLink ?>" target="_blank" class="badge" style="background: rgba(139, 92, 246, 0.1); color: #7c3aed; text-decoration: none;">
                                                                            <i class="fas fa-vr-cardboard me-1"></i> VR 360°
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <!-- ปุ่มดูเนื้อหาเพิ่มเติม -->
                                                                    <button class="btn btn-link btn-xs p-0 text-decoration-none text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#desc_<?= $u_id ?>">
                                                                        <i class="fas fa-info-circle"></i> ดูเนื้อหา
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- รายละเอียดเนื้อหาย่อซ่อนไว้ -->
                                                        <div class="collapse mt-2" id="desc_<?= $u_id ?>">
                                                            <div class="p-2 rounded bg-light border text-muted small" style="white-space: pre-wrap; font-size: 12.5px;">
                                                                <?= $content ?>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- ตำแหน่ง & ช่องทางติดต่อ -->
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <?php if (!empty($title)): ?>
                                                                <div class="d-flex align-items-center text-dark small font-weight-medium">
                                                                    <i class="fas fa-map-marker-alt text-danger me-2" style="width: 14px;"></i>
                                                                    <span><?= $title ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($contact)): ?>
                                                                <div class="d-flex align-items-center text-muted small">
                                                                    <i class="fas fa-address-book text-success me-2" style="width: 14px;"></i>
                                                                    <span class="text-truncate" style="max-width: 220px;" title="<?= $contact ?>"><?= $contact ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($contactLink)): ?>
                                                                <div class="mt-1">
                                                                    <a href="<?= $contactLink ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="border-radius: 6px; font-size: 11px;">
                                                                        <i class="fas fa-external-link-alt me-1"></i> ลิงก์ติดต่อ
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>

                                                    <!-- ยอดเข้าชม -->
                                                    <td class="text-center">
                                                        <span class="badge px-3 py-2" style="background: rgba(14, 165, 233, 0.12); color: #0284c7; font-size: 13px; font-weight: 600; border-radius: 8px;">
                                                            <i class="fas fa-eye me-1"></i> <?= number_format($viewCount) ?>
                                                        </span>
                                                    </td>

                                                    <!-- วันที่อัปโหลด -->
                                                    <td>
                                                        <div class="text-muted small">
                                                            <i class="far fa-calendar-alt text-secondary me-1"></i> <?= $createdAt ?>
                                                        </div>
                                                    </td>

                                                    <!-- การจัดการ -->
                                                    <td class="text-center">
                                                        <div class="btn-action-group">
                                                            <button class="btn btn-warning btn-sm shadow-sm" style="border-radius: 8px; font-size: 12.5px; padding: 5px 12px;"
                                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                                data-id="<?= $u_id ?>"
                                                                data-c_id="<?= $upload['c_id'] ?? '' ?>"
                                                                data-name="<?= $imageName ?>"
                                                                data-path="<?= $imagePath ?>"
                                                                data-contact="<?= $contact ?>"
                                                                data-title="<?= $title ?>"
                                                                data-subtitle="<?= $subtitle ?>"
                                                                data-contact_link="<?= $contactLink ?>"
                                                                data-vr_link="<?= $vrLink ?>"
                                                                data-content="<?= $content ?>">
                                                                <i class="fas fa-edit mr-1"></i> แก้ไข
                                                            </button>

                                                            <button class="btn btn-danger btn-sm shadow-sm" style="border-radius: 8px; font-size: 12.5px; padding: 5px 12px;"
                                                                onclick="confirmDelete(<?= $u_id ?>)">
                                                                <i class="fas fa-trash-alt mr-1"></i> ลบ
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block opacity-50"></i>
                                                    ยังไม่มีข้อมูลสถานที่ท่องเที่ยว กรุณากดปุ่ม <b>"อัปโหลดสถานที่ใหม่"</b> ด้านบน
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 2. GRID / CARD VIEW -->
                        <div id="gridViewSection" class="card-body p-4 d-none">
                            <div class="row g-4" id="spotGridRow">
                                <?php if (!empty($uploads)): ?>
                                    <?php foreach ($uploads as $upload): 
                                        $u_id = $upload['u_id'];
                                        $imageName = htmlspecialchars($upload['image_name'] ?? '');
                                        $imagePath = htmlspecialchars($upload['image_path'] ?? 'assets/images/default.jpg');
                                        $title = htmlspecialchars($upload['title'] ?? '');
                                        $subtitle = htmlspecialchars($upload['subtitle'] ?? '');
                                        $contact = htmlspecialchars($upload['contact'] ?? '');
                                        $contactLink = htmlspecialchars($upload['contact_link'] ?? '');
                                        $vrLink = htmlspecialchars($upload['vr_link'] ?? '');
                                        $content = htmlspecialchars($upload['content'] ?? '');
                                        $viewCount = (int)($upload['view_count'] ?? 0);
                                        $createdAt = !empty($upload['created_at']) ? thai_date_format($upload['created_at']) : '-';
                                    ?>
                                        <div class="col-md-6 col-lg-4 col-xl-3 spot-grid-item" data-search="<?= strtolower($imageName . ' ' . $title . ' ' . $subtitle . ' ' . $contact) ?>">
                                            <div class="spot-card shadow-sm">
                                                <!-- Image Wrapper -->
                                                <div class="spot-card-img-wrapper">
                                                    <img src="<?= $imagePath ?>" class="spot-card-img" alt="<?= $imageName ?>" onerror="this.src='assets/images/default.jpg'">
                                                    
                                                    <!-- Floating Views Badge -->
                                                    <span class="badge position-absolute top-0 start-0 m-2 px-2 py-1 shadow-sm" style="background: rgba(15, 23, 42, 0.75); color: #fff; backdrop-filter: blur(4px); font-size: 11.5px; border-radius: 6px;">
                                                        <i class="fas fa-eye text-info me-1"></i> <?= number_format($viewCount) ?>
                                                    </span>

                                                    <!-- Floating VR Badge -->
                                                    <?php if (!empty($vrLink)): ?>
                                                        <a href="<?= $vrLink ?>" target="_blank" class="badge position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm" style="background: rgba(124, 58, 237, 0.85); color: #fff; text-decoration: none; backdrop-filter: blur(4px); font-size: 11.5px; border-radius: 6px;">
                                                            <i class="fas fa-vr-cardboard me-1"></i> VR 360°
                                                        </a>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Card Body -->
                                                <div class="p-3 d-flex flex-column flex-grow-1">
                                                    <h6 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">
                                                        <?= $imageName ?>
                                                    </h6>

                                                    <?php if (!empty($upload['category_name'])): ?>
                                                        <div class="mb-2">
                                                            <span class="badge" style="background-color: <?= htmlspecialchars($upload['cat_color'] ?: '#0284c7') ?>; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 6px;">
                                                                <i class="<?= htmlspecialchars($upload['cat_icon'] ?: 'fas fa-tag') ?> me-1"></i> <?= htmlspecialchars($upload['category_name']) ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($title)): ?>
                                                        <div class="text-muted small mb-2 d-flex align-items-center">
                                                            <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 12px;"></i>
                                                            <span class="text-truncate"><?= $title ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <p class="text-muted small mb-3" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">
                                                        <?= !empty($subtitle) ? $subtitle : $content ?>
                                                    </p>

                                                    <!-- Meta Info -->
                                                    <div class="mt-auto pt-2 border-top">
                                                        <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                                                            <span><i class="far fa-clock me-1"></i><?= $createdAt ?></span>
                                                            <?php if (!empty($contactLink)): ?>
                                                                <a href="<?= $contactLink ?>" target="_blank" class="text-primary text-decoration-none">
                                                                    <i class="fas fa-external-link-alt"></i> ติดต่อ
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Action Buttons Row -->
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-warning btn-sm w-50 font-weight-bold shadow-sm" style="border-radius: 8px;"
                                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                                data-id="<?= $u_id ?>"
                                                                data-c_id="<?= $upload['c_id'] ?? '' ?>"
                                                                data-name="<?= $imageName ?>"
                                                                data-path="<?= $imagePath ?>"
                                                                data-contact="<?= $contact ?>"
                                                                data-title="<?= $title ?>"
                                                                data-subtitle="<?= $subtitle ?>"
                                                                data-contact_link="<?= $contactLink ?>"
                                                                data-vr_link="<?= $vrLink ?>"
                                                                data-content="<?= $content ?>">
                                                                <i class="fas fa-edit mr-1"></i> แก้ไข
                                                            </button>
                                                            <button class="btn btn-danger btn-sm w-50 font-weight-bold shadow-sm" style="border-radius: 8px;"
                                                                onclick="confirmDelete(<?= $u_id ?>)">
                                                                <i class="fas fa-trash-alt mr-1"></i> ลบ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>

        <!-- Modal แก้ไข -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff;">
                        <h5 class="modal-title font-weight-bold m-0" id="editModalLabel">
                            <i class="fas fa-edit mr-2"></i>แก้ไขข้อมูลสถานที่ท่องเที่ยว
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="edit_update.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; height: 100%; margin: 0;">
                        <div class="modal-body px-4 py-3" style="overflow-y: auto; max-height: calc(85vh - 130px);">
                            <input type="hidden" name="u_id" id="edit-id">
                            <input type="hidden" name="old_image_path" id="edit-path">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ชื่อรูปภาพ / สถานที่ <span class="text-danger">*</span></label>
                                    <input type="text" name="image_name" id="edit-name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">หมวดหมู่สถานที่ <span class="text-danger">*</span></label>
                                    <select name="c_id" id="edit-c_id" class="form-select" required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        <?php foreach ($categoriesList as $cat): ?>
                                            <option value="<?= $cat['c_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ชื่อสถานที่ (ตำแหน่ง) <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="edit-title" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">คำอธิบายสถานที่ (แบบย่อ) <span class="text-danger">*</span></label>
                                    <input type="text" name="subtitle" id="edit-subtitle" class="form-control" required>
                                </div>
                            </div>

                            <!-- รูปปัจจุบัน & อัปโหลดใหม่ -->
                            <div class="p-3 mb-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="row align-items-center">
                                    <div class="col-auto text-center">
                                        <label class="form-label font-weight-bold d-block text-muted small mb-1">รูปปัจจุบัน</label>
                                        <img id="preview-image" src="" class="img-thumbnail rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                    <div class="col">
                                        <label class="form-label font-weight-bold">เลือกไฟล์รูปใหม่ (หากต้องการเปลี่ยน)</label>
                                        <input type="file" name="image" class="form-control" id="edit-file" accept="image/*">
                                        <small class="text-muted">ปล่อยว่างไว้หากต้องการใช้รูปภาพเดิม</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ช่องทางติดต่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="contact" id="edit-contact" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ลิงก์ ติดต่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_link" id="edit-contact_link" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">ลิงก์ VR 360° <span class="text-danger">*</span></label>
                                <input type="text" name="vr_link" id="edit-vr_link" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">เนื้อหารายละเอียดสถานที่ <span class="text-danger">*</span></label>
                                <textarea name="content" id="edit-content" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-warning px-4 font-weight-bold shadow-sm" style="color: #212529;">
                                <i class="fas fa-save mr-1"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal อัปโหลดใหม่ -->
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff;">
                        <h5 class="modal-title font-weight-bold m-0" id="uploadModalLabel">
                            <i class="fas fa-plus-circle mr-2"></i>อัปโหลดสถานที่ท่องเที่ยวใหม่
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="add_upload.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; height: 100%; margin: 0;">
                        <div class="modal-body px-4 py-3" style="overflow-y: auto; max-height: calc(85vh - 130px);">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ชื่อรูปภาพ / สถานที่ <span class="text-danger">*</span></label>
                                    <input type="text" name="image_name" class="form-control" required placeholder="เช่น ลานชมดาว">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">หมวดหมู่สถานที่ <span class="text-danger">*</span></label>
                                    <select name="c_id" class="form-select" required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        <?php foreach ($categoriesList as $cat): ?>
                                            <option value="<?= $cat['c_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ชื่อสถานที่ (ตำแหน่ง) <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required placeholder="เช่น ตำบลนาป่า อำเภอเมือง">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">คำอธิบายสถานที่ (แบบย่อ) <span class="text-danger">*</span></label>
                                    <input type="text" name="subtitle" class="form-control" required placeholder="เช่น จุดชมวิวทะเลหมอก และลานกางเต็นท์">
                                </div>
                            </div>

                            <!-- อัปโหลดไฟล์รูปภาพพร้อม Preview -->
                            <div class="p-3 mb-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="row align-items-center">
                                    <div class="col-auto text-center">
                                        <img id="upload-preview" src="assets/images/default.jpg" class="img-thumbnail rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'><rect fill=\'%23e2e8f0\' width=\'80\' height=\'80\'/><text fill=\'%2394a3b8\' x=\'50%\' y=\'50%\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'12\'>ตัวอย่าง</text></svg>'">
                                    </div>
                                    <div class="col">
                                        <label class="form-label font-weight-bold">เลือกไฟล์รูปภาพ <span class="text-danger">*</span></label>
                                        <input type="file" name="image" class="form-control" accept="image/*" id="upload-file" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ช่องทางติดต่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="contact" class="form-control" required placeholder="เช่น โทร. 081-xxx-xxxx">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">ลิงก์ ติดต่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_link" class="form-control" required placeholder="https://facebook.com/...">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">ลิงก์ VR 360° <span class="text-danger">*</span></label>
                                <input type="text" name="vr_link" class="form-control" required placeholder="https://...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">เนื้อหารายละเอียดสถานที่ <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control" rows="4" required placeholder="กรอกรายละเอียดสถานที่ท่องเที่ยว..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                                <i class="fas fa-upload mr-1"></i> อัปโหลดข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // สลับมุมมอง Table vs Grid
        function switchView(mode) {
            const tableSec = document.getElementById('tableViewSection');
            const gridSec = document.getElementById('gridViewSection');
            const btnTable = document.getElementById('btnViewTable');
            const btnGrid = document.getElementById('btnViewGrid');

            if (mode === 'grid') {
                tableSec.classList.add('d-none');
                gridSec.classList.remove('d-none');
                btnTable.classList.remove('active');
                btnGrid.classList.add('active');
                localStorage.setItem('spot_view_mode', 'grid');
            } else {
                gridSec.classList.add('d-none');
                tableSec.classList.remove('d-none');
                btnGrid.classList.remove('active');
                btnTable.classList.add('active');
                localStorage.setItem('spot_view_mode', 'table');
            }
        }

        // คืนค่ามุมมองที่ผู้ใช้เลือกไว้
        document.addEventListener('DOMContentLoaded', function() {
            const savedMode = localStorage.getItem('spot_view_mode');
            if (savedMode === 'grid') {
                switchView('grid');
            }

            // Real-time Search Filter
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();

                // กรองในตาราง
                document.querySelectorAll('#mainSpotTable tbody tr.spot-search-row').forEach(row => {
                    const searchData = row.getAttribute('data-search') || '';
                    row.style.display = searchData.includes(term) ? '' : 'none';
                });

                // กรองในการ์ด
                document.querySelectorAll('#spotGridRow .spot-grid-item').forEach(col => {
                    const searchData = col.getAttribute('data-search') || '';
                    col.style.display = searchData.includes(term) ? '' : 'none';
                });
            });
        });

        // ดึงข้อมูลไปใส่ Modal เมื่อกดปุ่มแก้ไข
        document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var u_id = button.getAttribute('data-id');
            var c_id = button.getAttribute('data-c_id');
            var name = button.getAttribute('data-name');
            var title = button.getAttribute('data-title');
            var subtitle = button.getAttribute('data-subtitle');
            var path = button.getAttribute('data-path');
            var content = button.getAttribute('data-content');
            var contact = button.getAttribute('data-contact');
            var contact_link = button.getAttribute('data-contact_link');
            var vr_link = button.getAttribute('data-vr_link');

            // ตั้งค่าฟิลด์ข้อมูล
            document.getElementById('edit-id').value = u_id || '';
            document.getElementById('edit-c_id').value = c_id || '';
            document.getElementById('edit-name').value = name || '';
            document.getElementById('edit-title').value = title || '';
            document.getElementById('edit-subtitle').value = subtitle || '';
            document.getElementById('edit-path').value = path || '';
            document.getElementById('edit-content').value = content || '';
            document.getElementById('edit-contact').value = contact || '';
            document.getElementById('preview-image').src = path || '';
            document.getElementById('edit-contact_link').value = contact_link || '';
            document.getElementById('edit-vr_link').value = vr_link || '';
        });

        // อัปเดตภาพตัวอย่างเมื่อเลือกไฟล์ใหม่ใน Modal แก้ไข
        document.getElementById('edit-file').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                document.getElementById('preview-image').src = reader.result;
            }
            if (event.target.files && event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        // แสดงตัวอย่างรูปที่อัปโหลดใหม่ใน Modal เพิ่ม
        document.getElementById('upload-file').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                document.getElementById('upload-preview').src = reader.result;
            }
            if (event.target.files && event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        // ฟังก์ชันยืนยันการลบ
        function confirmDelete(u_id) {
            Swal.fire({
                title: "ยืนยันการลบสถานที่?",
                text: "ข้อมูลสถานที่และรูปภาพที่เกี่ยวข้องจะถูกลบออกจากระบบอย่างถาวร",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<i class='fas fa-trash-alt me-1'></i> ใช่, ลบเลย",
                cancelButtonText: "ยกเลิก",
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_upload.php?u_id=' + u_id;
                }
            });
        }
    </script>

    <?php if (!empty($_SESSION['flash_alert'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: '<?= addslashes($_SESSION['flash_alert']['icon']) ?>',
                title: '<?= addslashes($_SESSION['flash_alert']['title']) ?>',
                text: '<?= addslashes($_SESSION['flash_alert']['text']) ?>',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true
            });
        });
    </script>
    <?php unset($_SESSION['flash_alert']); ?>
    <?php endif; ?>
</body>

</html>
