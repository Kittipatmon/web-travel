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

// ดึงข้อมูลหมวดหมู่ทั้งหมดพร้อมนับจำนวนสถานที่ท่องเที่ยว
$query = "
    SELECT c.*, COUNT(u.u_id) AS total_spots 
    FROM categories c 
    LEFT JOIN uploads u ON c.c_id = u.c_id 
    GROUP BY c.c_id 
    ORDER BY c.c_id ASC
";
$result = $conn->query($query);
$categories = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$totalCategories = count($categories);

// รวมจำนวนสถานที่ทั้งหมด
$totalSpotsRes = $conn->query("SELECT COUNT(*) AS total FROM uploads");
$totalSpotsCount = $totalSpotsRes ? (int)$totalSpotsRes->fetch_assoc()['total'] : 0;
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่สถานที่ท่องเที่ยว - Virtual Vista Admin</title>
    
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

        .category-badge-preview {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13.5px;
            font-weight: 600;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .icon-picker-btn {
            cursor: pointer;
            border-radius: 8px;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .icon-picker-btn:hover {
            border-color: #0284c7;
            background: #f0f9ff;
            color: #0284c7;
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
                    <a class="nav-link" href="dashboard_pic.php">รายการอัปโหลด</a>
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
                         style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 255, 255, 0.2);" class="d-flex align-items-center justify-content-center shadow-xs">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold m-0" style="font-size: 22px;">จัดการหมวดหมู่สถานที่ท่องเที่ยว</h3>
                                <small style="opacity: 0.9;">กำหนดหมวดหมู่และประเภทเพื่อใช้จัดกลุ่มสถานที่ท่องเที่ยวในเว็บไซต์</small>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="btn btn-light fw-bold px-4 py-2 shadow-sm rounded-pill" 
                                    style="color: #059669;" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fas fa-plus-circle me-1"></i> เพิ่มหมวดหมู่ใหม่
                            </button>
                        </div>
                    </div>

                    <!-- Main Container Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                        
                        <!-- Header Bar with Stats -->
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-layer-group text-success"></i>
                                <span class="fw-bold text-dark">หมวดหมู่ทั้งหมด</span>
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill fw-bold" style="font-size: 13px;">
                                    <?= $totalCategories ?> หมวดหมู่
                                </span>
                            </div>
                            <div class="text-muted small">
                                สถานที่ท่องเที่ยวทั้งหมดในระบบ: <strong class="text-dark"><?= $totalSpotsCount ?> แห่ง</strong>
                            </div>
                        </div>

                        <!-- Categories Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 14.5px;">
                                <thead class="bg-light text-muted text-uppercase" style="font-size: 12.5px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th style="width: 70px;" class="text-center py-3">ลำดับ</th>
                                        <th class="py-3">ไอคอน & ชื่อหมวดหมู่</th>
                                        <th style="width: 180px;" class="text-center py-3">รหัสไอคอน / สี</th>
                                        <th style="width: 200px;" class="text-center py-3">จำนวนสถานที่ในระบบ</th>
                                        <th style="width: 180px;" class="text-center py-3">วันที่สร้าง</th>
                                        <th style="width: 180px;" class="text-center py-3">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($categories)): ?>
                                        <?php $index = 1; ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <tr>
                                                <!-- ลำดับ -->
                                                <td class="text-center fw-bold text-muted"><?= $index++ ?></td>

                                                <!-- หมวดหมู่ -->
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="category-badge-preview" style="background-color: <?= htmlspecialchars($cat['color'] ?: '#0284c7') ?>;">
                                                            <i class="<?= htmlspecialchars($cat['icon'] ?: 'fas fa-map-marker-alt') ?>"></i>
                                                            <?= htmlspecialchars($cat['category_name']) ?>
                                                        </span>
                                                    </div>
                                                </td>

                                                <!-- รหัสไอคอน & โค้ดสี -->
                                                <td class="text-center">
                                                    <div class="small text-muted font-monospace">
                                                        <i class="<?= htmlspecialchars($cat['icon']) ?> me-1" style="color: <?= htmlspecialchars($cat['color']) ?>"></i>
                                                        <?= htmlspecialchars($cat['icon']) ?>
                                                    </div>
                                                    <div class="d-inline-flex align-items-center gap-1 mt-1">
                                                        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background-color: <?= htmlspecialchars($cat['color']) ?>; border: 1px solid #cbd5e1;"></span>
                                                        <small class="text-muted font-monospace"><?= htmlspecialchars($cat['color']) ?></small>
                                                    </div>
                                                </td>

                                                <!-- จำนวนสถานที่ในระบบ -->
                                                <td class="text-center">
                                                    <?php if ((int)$cat['total_spots'] > 0): ?>
                                                        <a href="dashboard_pic.php" class="badge px-3 py-2 text-decoration-none" 
                                                           style="background-color: rgba(16, 185, 129, 0.12); color: #059669; font-size: 13px; font-weight: 600; border-radius: 8px;"
                                                           title="ดูสถานที่ในหมวดนี้">
                                                            <i class="fas fa-map-marker-alt me-1"></i> <?= $cat['total_spots'] ?> สถานที่
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="badge px-3 py-2 text-muted" style="background-color: #f1f5f9; font-size: 13px; font-weight: 500; border-radius: 8px;">
                                                            0 สถานที่
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- วันที่สร้าง -->
                                                <td class="text-center text-muted small">
                                                    <?= date('d/m/Y H:i', strtotime($cat['created_at'])) ?>
                                                </td>

                                                <!-- การจัดการ -->
                                                <td class="text-center">
                                                    <div class="d-inline-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-warning fw-bold text-dark px-3 shadow-xs" 
                                                                style="border-radius: 8px;"
                                                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                                data-id="<?= $cat['c_id'] ?>"
                                                                data-name="<?= htmlspecialchars($cat['category_name']) ?>"
                                                                data-icon="<?= htmlspecialchars($cat['icon']) ?>"
                                                                data-color="<?= htmlspecialchars($cat['color']) ?>">
                                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger fw-bold px-3 shadow-xs" 
                                                                style="border-radius: 8px;"
                                                                onclick="confirmDelete(<?= $cat['c_id'] ?>, '<?= addslashes(htmlspecialchars($cat['category_name'])) ?>', <?= (int)$cat['total_spots'] ?>)">
                                                            <i class="fas fa-trash-alt me-1"></i> ลบ
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                                <p class="m-0 fw-bold">ยังไม่มีข้อมูลหมวดหมู่ในระบบ</p>
                                                <small>คลิกปุ่ม "เพิ่มหมวดหมู่ใหม่" เพื่อสร้างหมวดหมู่แรก</small>
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
             MODAL: เพิ่มหมวดหมู่ใหม่
             ========================================== -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff;">
                        <h5 class="modal-title fw-bold m-0" id="addCategoryModalLabel">
                            <i class="fas fa-plus-circle me-2"></i> เพิ่มหมวดหมู่สถานที่ใหม่
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="add_category.php" method="POST">
                        <div class="modal-body p-4">
                            <!-- ชื่อหมวดหมู่ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                                <input type="text" name="category_name" class="form-control" required placeholder="เช่น จุดชมวิว, กางเต็นท์, ที่พัก/โฮมสเตย์">
                            </div>

                            <!-- ไอคอนประจำหมวด -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">คลาสไอคอน FontAwesome <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light" id="add-icon-preview">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="icon" id="add-icon-input" class="form-control" value="fas fa-map-marker-alt" required placeholder="เช่น fas fa-campground">
                                </div>
                                <div class="mt-2 d-flex flex-wrap gap-1">
                                    <span class="small text-muted me-2">ไอคอนแนะนำ:</span>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-mountain')"><i class="fas fa-mountain"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-campground')"><i class="fas fa-campground"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-home')"><i class="fas fa-home"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-seedling')"><i class="fas fa-seedling"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-utensils')"><i class="fas fa-utensils"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-hiking')"><i class="fas fa-hiking"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-camera')"><i class="fas fa-camera"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectAddIcon('fas fa-tree')"><i class="fas fa-tree"></i></button>
                                </div>
                            </div>

                            <!-- สีประจำหมวดหมู่ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">สีประจำหมวดหมู่ (Theme Color)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" name="color" id="add-color-input" class="form-control form-control-color" value="#0284c7" title="เลือกสีหมวดหมู่">
                                    <span class="text-muted small">ใช้สำหรับแสดงสี Badge ในตารางและการ์ด</span>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==========================================
             MODAL: แก้ไขหมวดหมู่
             ========================================== -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff;">
                        <h5 class="modal-title fw-bold m-0" id="editCategoryModalLabel">
                            <i class="fas fa-edit me-2"></i> แก้ไขหมวดหมู่
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="edit_category.php" method="POST">
                        <input type="hidden" name="c_id" id="edit-c-id">

                        <div class="modal-body p-4">
                            <!-- ชื่อหมวดหมู่ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                                <input type="text" name="category_name" id="edit-name" class="form-control" required>
                            </div>

                            <!-- ไอคอนประจำหมวด -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">คลาสไอคอน FontAwesome <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light" id="edit-icon-preview">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="icon" id="edit-icon-input" class="form-control" required>
                                </div>
                                <div class="mt-2 d-flex flex-wrap gap-1">
                                    <span class="small text-muted me-2">ไอคอนแนะนำ:</span>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-mountain')"><i class="fas fa-mountain"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-campground')"><i class="fas fa-campground"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-home')"><i class="fas fa-home"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-seedling')"><i class="fas fa-seedling"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-utensils')"><i class="fas fa-utensils"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-hiking')"><i class="fas fa-hiking"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-camera')"><i class="fas fa-camera"></i></button>
                                    <button type="button" class="icon-picker-btn" onclick="selectEditIcon('fas fa-tree')"><i class="fas fa-tree"></i></button>
                                </div>
                            </div>

                            <!-- สีประจำหมวดหมู่ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">สีประจำหมวดหมู่ (Theme Color)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" name="color" id="edit-color-input" class="form-control form-control-color" title="เลือกสีหมวดหมู่">
                                    <span class="text-muted small">ใช้สำหรับแสดงสี Badge ในตารางและการ์ด</span>
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
        function selectAddIcon(iconClass) {
            document.getElementById('add-icon-input').value = iconClass;
            document.getElementById('add-icon-preview').innerHTML = '<i class="' + iconClass + '"></i>';
        }

        function selectEditIcon(iconClass) {
            document.getElementById('edit-icon-input').value = iconClass;
            document.getElementById('edit-icon-preview').innerHTML = '<i class="' + iconClass + '"></i>';
        }

        // อัปเดตไอคอนพรีวิวเมื่อผู้ใช้พิมพ์
        document.getElementById('add-icon-input').addEventListener('input', function() {
            document.getElementById('add-icon-preview').innerHTML = '<i class="' + this.value + '"></i>';
        });
        document.getElementById('edit-icon-input').addEventListener('input', function() {
            document.getElementById('edit-icon-preview').innerHTML = '<i class="' + this.value + '"></i>';
        });

        // ส่งข้อมูลเข้า Edit Modal เมื่อเปิด
        const editCategoryModal = document.getElementById('editCategoryModal');
        editCategoryModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const c_id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const icon = button.getAttribute('data-icon') || 'fas fa-map-marker-alt';
            const color = button.getAttribute('data-color') || '#0284c7';

            document.getElementById('edit-c-id').value = c_id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-icon-input').value = icon;
            document.getElementById('edit-icon-preview').innerHTML = '<i class="' + icon + '"></i>';
            document.getElementById('edit-color-input').value = color;
        });

        // ฟังก์ชันยืนยันการลบหมวดหมู่
        function confirmDelete(c_id, name, spotsCount) {
            let warningText = 'คุณต้องการลบหมวดหมู่ "' + name + '" หรือไม่?';
            if (spotsCount > 0) {
                warningText = 'หมวดหมู่นี้มีสถานที่ท่องเที่ยวผูกอยู่ ' + spotsCount + ' แห่ง หากลบ ระบบจะปลดหมวดหมู่ของสถานที่เหล่านั้นออก คุณแน่ใจหรือไม่?';
            }

            Swal.fire({
                title: 'ยืนยันการลบหมวดหมู่?',
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_category.php?c_id=' + c_id;
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

