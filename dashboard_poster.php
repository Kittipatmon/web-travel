<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

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

// ดึงข้อมูลโปสเตอร์ทั้งหมด
$query = "SELECT * FROM posters ORDER BY sort_order ASC, created_at DESC";
$result = $conn->query($query);
$posters = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$totalPosters = count($posters);
$activePosters = count(array_filter($posters, fn($p) => $p['status'] === 'active'));
$inactivePosters = $totalPosters - $activePosters;
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการโปสเตอร์โปรโมท - Virtual Vista Admin</title>

    <!-- Bootstrap 5 & AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">

    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            overflow-y: auto;
            background-color: #f8fafc;
        }

        .poster-thumb-box {
            width: 140px;
            height: 65px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            background: #e2e8f0;
            display: inline-block;
        }

        .poster-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .poster-thumb-box:hover img {
            transform: scale(1.08);
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .status-badge {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .status-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }
    </style>
</head>

<body class="hold-transition sidebar-mini sidebar-mini-md">
    <div class="wrapper">

        <!-- Top Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link" href="dashboardmain.php">หน้าหลัก</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link" href="index.php" target="_blank">ดูหน้าเว็บไซต์หลัก</a>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar elevation-4">
            <a href="dashboardmain.php" class="brand-link" align="center">
                <img src="assets/images/logovr.png" alt="Logo">
            </a>
            <?php include "sidebar.php"; ?>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content py-4">
                <div class="container-fluid px-lg-4">

                    <!-- Page Title Pill Bar -->
                    <div class="p-3 mb-4 rounded-4 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3"
                         style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 255, 255, 0.2);" class="d-flex align-items-center justify-content-center shadow-xs">
                                <i class="fas fa-bullhorn fa-lg"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold m-0" style="font-size: 22px;">จัดการโปสเตอร์โปรโมท (Promotional Posters)</h3>
                                <small style="opacity: 0.95;">จัดการภาพโปสเตอร์แบนเนอร์โปรโมทการท่องเที่ยวเพชรบูรณ์ที่แสดงในหน้าแรกของเว็บไซต์</small>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="btn btn-light fw-bold px-4 py-2 shadow-sm rounded-pill" 
                                    style="color: #d97706;" data-bs-toggle="modal" data-bs-target="#addPosterModal">
                                <i class="fas fa-plus-circle me-1"></i> เพิ่มโปสเตอร์ใหม่
                            </button>
                        </div>
                    </div>

                    <!-- Main Container Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                        
                        <!-- Header Bar with Stats -->
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="fw-bold text-dark">โปสเตอร์ทั้งหมด</span>
                                <span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size: 13px;">
                                    <?= $totalPosters ?> แผ่น
                                </span>
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" style="font-size: 13px;">
                                    <i class="fas fa-check-circle me-1"></i> เปิดใช้งาน: <?= $activePosters ?>
                                </span>
                                <?php if ($inactivePosters > 0): ?>
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill" style="font-size: 13px;">
                                        <i class="fas fa-eye-slash me-1"></i> ปิดใช้งาน: <?= $inactivePosters ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small">
                                แสดงผลบนหน้าแรก: <a href="index.php#promo-section" target="_blank" class="text-decoration-none fw-bold text-warning">ดูตำแหน่งแสดงผล <i class="fas fa-external-link-alt ms-1"></i></a>
                            </div>
                        </div>

                        <!-- Posters Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 14.5px;">
                                <thead class="bg-light text-muted text-uppercase" style="font-size: 12.5px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th style="width: 70px;" class="text-center py-3">ลำดับ</th>
                                        <th style="width: 170px;" class="py-3">รูปภาพโปสเตอร์</th>
                                        <th class="py-3">ชื่อแคมเปญ & รายละเอียด</th>
                                        <th style="width: 180px;" class="py-3">ลิงก์ปลายทาง</th>
                                        <th style="width: 140px;" class="text-center py-3">สถานะ</th>
                                        <th style="width: 160px;" class="text-center py-3">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($posters)): ?>
                                        <?php foreach ($posters as $idx => $poster): ?>
                                            <tr>
                                                <!-- ลำดับแสดงผล -->
                                                <td class="text-center fw-bold text-muted">
                                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                                        #<?= htmlspecialchars($poster['sort_order']) ?>
                                                    </span>
                                                </td>

                                                <!-- รูปโปสเตอร์ -->
                                                <td>
                                                    <a href="<?= htmlspecialchars($poster['image_path']) ?>" target="_blank" title="คลิกเพื่อดูภาพขนาดเต็ม">
                                                        <div class="poster-thumb-box">
                                                            <img src="<?= htmlspecialchars($poster['image_path']) ?>" alt="<?= htmlspecialchars($poster['title']) ?>" onerror="this.src='assets/images/default.jpg'">
                                                        </div>
                                                    </a>
                                                </td>

                                                <!-- ชื่อและคำอธิบาย -->
                                                <td>
                                                    <div class="fw-bold text-dark" style="font-size: 15px;">
                                                        <?= htmlspecialchars($poster['title']) ?>
                                                    </div>
                                                    <?php if (!empty($poster['subtitle'])): ?>
                                                        <div class="text-muted small mt-1" style="max-width: 400px; line-height: 1.4;">
                                                            <?= htmlspecialchars($poster['subtitle']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- ลิงก์ปลายทาง -->
                                                <td>
                                                    <?php if (!empty($poster['link_url'])): ?>
                                                        <a href="<?= htmlspecialchars($poster['link_url']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2 text-truncate" style="max-width: 160px; font-size: 12px; border-radius: 6px;" title="<?= htmlspecialchars($poster['link_url']) ?>">
                                                            <i class="fas fa-link me-1 text-primary"></i> <?= htmlspecialchars($poster['link_url']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted small">- ไม่มีลิงก์ -</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- สถานะ (Active / Inactive Toggle) -->
                                                <td class="text-center">
                                                    <?php if ($poster['status'] === 'active'): ?>
                                                        <a href="toggle_poster_status.php?p_id=<?= $poster['p_id'] ?>" 
                                                           class="badge bg-success status-badge text-decoration-none px-3 py-2 rounded-pill"
                                                           title="คลิกเพื่อปิดการแสดงผล">
                                                            <i class="fas fa-check-circle me-1"></i> เปิดใช้งาน
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="toggle_poster_status.php?p_id=<?= $poster['p_id'] ?>" 
                                                           class="badge bg-secondary status-badge text-decoration-none px-3 py-2 rounded-pill"
                                                           title="คลิกเพื่อเปิดการแสดงผล">
                                                            <i class="fas fa-eye-slash me-1"></i> ปิดใช้งาน
                                                        </a>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- การจัดการ -->
                                                <td class="text-center">
                                                    <div class="d-inline-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-warning fw-bold text-dark px-3 shadow-xs" 
                                                                style="border-radius: 8px;"
                                                                data-bs-toggle="modal" data-bs-target="#editPosterModal"
                                                                data-id="<?= $poster['p_id'] ?>"
                                                                data-title="<?= htmlspecialchars($poster['title']) ?>"
                                                                data-subtitle="<?= htmlspecialchars($poster['subtitle'] ?? '') ?>"
                                                                data-path="<?= htmlspecialchars($poster['image_path']) ?>"
                                                                data-link="<?= htmlspecialchars($poster['link_url'] ?? '') ?>"
                                                                data-order="<?= htmlspecialchars($poster['sort_order']) ?>"
                                                                data-status="<?= htmlspecialchars($poster['status']) ?>">
                                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger fw-bold px-3 shadow-xs" 
                                                                style="border-radius: 8px;"
                                                                onclick="confirmDelete(<?= $poster['p_id'] ?>, '<?= addslashes(htmlspecialchars($poster['title'])) ?>')">
                                                            <i class="fas fa-trash-alt me-1"></i> ลบ
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-images fa-3x mb-3 text-secondary opacity-50"></i>
                                                <p class="m-0 fw-bold">ยังไม่มีข้อมูลโปสเตอร์โปรโมทในระบบ</p>
                                                <small>คลิกปุ่ม "เพิ่มโปสเตอร์ใหม่" เพื่อเริ่มสร้างโปสเตอร์โปรโมทแรก</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </section>
        </div>

        <!-- ==========================================
             MODAL: เพิ่มโปสเตอร์ใหม่
             ========================================== -->
        <div class="modal fade" id="addPosterModal" tabindex="-1" aria-labelledby="addPosterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff;">
                        <h5 class="modal-title fw-bold m-0" id="addPosterModalLabel">
                            <i class="fas fa-plus-circle me-2"></i> เพิ่มโปสเตอร์โปรโมทใหม่
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="add_poster.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body p-4">
                            <!-- ชื่อโปสเตอร์ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อโปสเตอร์ / แคมเปญโปรโมท <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="เช่น มหัศจรรย์สายหมอกเพชรบูรณ์ 360°">
                            </div>

                            <!-- คำอธิบายย่อย -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">คำอธิบายย่อย / สโลแกน</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="เช่น แหล่งรวมที่เที่ยวตำบลนาป่า เขาค้อ และภูทับเบิก">
                            </div>

                            <!-- อัปโหลดภาพโปสเตอร์พร้อมพรีวิว -->
                            <div class="p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <label class="form-label fw-bold">เลือกไฟล์ภาพโปสเตอร์ <span class="text-danger">*</span></label>
                                <div class="row align-items-center g-3">
                                    <div class="col-sm-5 text-center">
                                        <div style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 6px; background: #fff;">
                                            <img id="add-poster-preview" src="assets/images/default.jpg" class="img-fluid rounded" style="max-height: 140px; width: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="file" name="image" id="add-poster-file" class="form-control" accept="image/*" required>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i> แนะนำภาพแนวนอน อัตราส่วน 16:9 หรือ 21:9 (เช่น ขนาด 1200x500px ขึ้นไป)
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- ลิงก์ปลายทาง -->
                                <div class="col-md-7 mb-3">
                                    <label class="form-label fw-bold">ลิงก์ปลายทางเมื่อคลิกโปสเตอร์</label>
                                    <input type="text" name="link_url" class="form-control" placeholder="เช่น all_destinations.php หรือ https://...">
                                    <small class="text-muted">ปล่อยว่างไว้ได้หากไม่ต้องการให้คลิกไปหน้าอื่น</small>
                                </div>

                                <!-- ลำดับแสดงผล -->
                                <div class="col-md-2 col-6 mb-3">
                                    <label class="form-label fw-bold">ลำดับ</label>
                                    <input type="number" name="sort_order" class="form-control text-center" value="1" min="1">
                                </div>

                                <!-- สถานะ -->
                                <div class="col-md-3 col-6 mb-3">
                                    <label class="form-label fw-bold">สถานะ</label>
                                    <select name="status" class="form-select">
                                        <option value="active" selected>เปิดใช้งาน</option>
                                        <option value="inactive">ปิดใช้งาน</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm" style="color: #212529;">
                                <i class="fas fa-upload me-1"></i> อัปโหลดโปสเตอร์
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==========================================
             MODAL: แก้ไขโปสเตอร์
             ========================================== -->
        <div class="modal fade" id="editPosterModal" tabindex="-1" aria-labelledby="editPosterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff;">
                        <h5 class="modal-title fw-bold m-0" id="editPosterModalLabel">
                            <i class="fas fa-edit me-2"></i> แก้ไขโปสเตอร์โปรโมท
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="edit_poster.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="p_id" id="edit-p-id">
                        <input type="hidden" name="old_image_path" id="edit-old-path">

                        <div class="modal-body p-4">
                            <!-- ชื่อโปสเตอร์ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อโปสเตอร์ / แคมเปญโปรโมท <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="edit-title" class="form-control" required>
                            </div>

                            <!-- คำอธิบายย่อย -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">คำอธิบายย่อย / สโลแกน</label>
                                <input type="text" name="subtitle" id="edit-subtitle" class="form-control">
                            </div>

                            <!-- รูปปัจจุบัน & เลือกรูปใหม่ -->
                            <div class="p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <label class="form-label fw-bold">ภาพโปสเตอร์</label>
                                <div class="row align-items-center g-3">
                                    <div class="col-sm-5 text-center">
                                        <div style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 6px; background: #fff;">
                                            <img id="edit-poster-preview" src="" class="img-fluid rounded" style="max-height: 140px; width: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <label class="form-label small fw-bold text-muted">เปลี่ยนรูปใหม่ (หากต้องการเปลี่ยน):</label>
                                        <input type="file" name="image" id="edit-poster-file" class="form-control" accept="image/*">
                                        <small class="text-muted d-block mt-2">ปล่อยว่างไว้หากต้องการใช้รูปเดิม</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- ลิงก์ปลายทาง -->
                                <div class="col-md-7 mb-3">
                                    <label class="form-label fw-bold">ลิงก์ปลายทางเมื่อคลิกโปสเตอร์</label>
                                    <input type="text" name="link_url" id="edit-link" class="form-control">
                                </div>

                                <!-- ลำดับแสดงผล -->
                                <div class="col-md-2 col-6 mb-3">
                                    <label class="form-label fw-bold">ลำดับ</label>
                                    <input type="number" name="sort_order" id="edit-order" class="form-control text-center" min="1">
                                </div>

                                <!-- สถานะ -->
                                <div class="col-md-3 col-6 mb-3">
                                    <label class="form-label fw-bold">สถานะ</label>
                                    <select name="status" id="edit-status" class="form-select">
                                        <option value="active">เปิดใช้งาน</option>
                                        <option value="inactive">ปิดใช้งาน</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm" style="color: #212529;">
                                <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
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
        // Preview รูปภาพเมื่อเลือกไฟล์ใน Modal เพิ่ม
        document.getElementById('add-poster-file').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('add-poster-preview').src = reader.result;
            }
            if (event.target.files && event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        // Preview รูปภาพเมื่อเลือกไฟล์ใน Modal แก้ไข
        document.getElementById('edit-poster-file').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('edit-poster-preview').src = reader.result;
            }
            if (event.target.files && event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        // ส่งข้อมูลเข้า Edit Modal
        const editPosterModal = document.getElementById('editPosterModal');
        editPosterModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const p_id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const subtitle = button.getAttribute('data-subtitle') || '';
            const path = button.getAttribute('data-path') || '';
            const link = button.getAttribute('data-link') || '';
            const order = button.getAttribute('data-order') || '1';
            const status = button.getAttribute('data-status') || 'active';

            document.getElementById('edit-p-id').value = p_id;
            document.getElementById('edit-old-path').value = path;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-subtitle').value = subtitle;
            document.getElementById('edit-poster-preview').src = path;
            document.getElementById('edit-link').value = link;
            document.getElementById('edit-order').value = order;
            document.getElementById('edit-status').value = status;
        });

        // ฟังก์ชันยืนยันการลบโปสเตอร์
        function confirmDelete(p_id, title) {
            Swal.fire({
                title: 'ยืนยันการลบโปสเตอร์?',
                text: 'คุณต้องการลบโปสเตอร์ "' + title + '" หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_poster.php?p_id=' + p_id;
                }
            });
        }
    </script>

    <!-- Flash Alert Handler -->
    <?php if (!empty($_SESSION['flash_alert'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

