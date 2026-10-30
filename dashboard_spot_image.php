<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {

    // ดึงข้อมูลสถานที่ทั้งหมดพร้อมนับจำนวนภาพ
    $sqlLoc = "SELECT u.u_id, u.image_name, u.image_path, COUNT(i.m_id) AS img_count, MAX(i.uploaded_at) AS last_upload 
               FROM uploads u 
               LEFT JOIN images i ON u.u_id = i.u_id 
               GROUP BY u.u_id 
               ORDER BY img_count DESC, u.image_name ASC";
    $locResult = $conn->query($sqlLoc);

    // ดึงภาพทั้งหมดจัดกลุ่มตาม u_id
    $imgSql = "SELECT m_id, filename, uploaded_at, u_id FROM images ORDER BY uploaded_at DESC";
    $imgRes = $conn->query($imgSql);
    $imagesByUid = [];
    $totalImages = 0;
    if ($imgRes) {
        while ($row = $imgRes->fetch_assoc()) {
            $imagesByUid[$row['u_id']][] = $row;
            $totalImages++;
        }
    }
?>
    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>รูปภาพจุดท่องเที่ยว - แยกตามสถานที่</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Prompt', sans-serif;
                overflow-y: auto;
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

            .nav-link:hover {
                color: rgb(22, 16, 212) !important;
            }

            .location-row {
                transition: background-color 0.2s ease;
                cursor: pointer;
            }

            .location-row:hover {
                background-color: #f1f5f9 !important;
            }

            .btn-toggle-collapse {
                border-radius: 8px;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
            }

            .toggle-icon {
                transition: transform 0.25s ease;
                display: inline-block;
            }

            .btn-toggle-collapse[aria-expanded="true"] .toggle-icon,
            .location-row.is-open .toggle-icon {
                transform: rotate(180deg);
            }

            .img-hover-zoom {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .img-hover-zoom:hover {
                transform: scale(1.15);
                box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            }

            .subtable-container {
                background: #f8fafc;
                border-bottom: 2px solid #e2e8f0;
                padding: 16px 20px;
            }
        </style>
    </head>

    <body class="hold-transition sidebar-mini sidebar-mini-md">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a class="nav-link" href="dashboardmain.php">หน้าหลัก</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a class="nav-link" href="#">คู่มือการใช้งาน</a>
                    </li>
                </ul>
            </nav>

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
                                    <i class="fas fa-images mr-2"></i>จัดการรูปภาพจุดท่องเที่ยว
                                </h3>
                                <p class="m-0 text-white-50 text-sm mt-1">
                                    จัดกลุ่มรูปภาพแยกตามสถานที่ท่องเที่ยว สามารถคลิกดูรายการรูปภาพ (List) และจัดการแต่ละรูปได้สะดวก
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-light px-3 py-2 font-weight-bold shadow-sm" 
                                        style="border-radius: 10px; color: #059669;"
                                        data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-plus-circle mr-1"></i> อัปโหลดรูปภาพใหม่
                                </button>
                            </div>
                        </div>

                        <!-- Card Grouped Table -->
                        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="m-0 font-weight-bold text-gray-800" style="font-size: 16px;">
                                        <i class="fas fa-layer-group text-success mr-2"></i>รายการสถานที่ท่องเที่ยว
                                    </h5>
                                    <span class="badge px-3 py-2" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 13px; font-weight: 600; border-radius: 8px;">
                                        รวมทั้งหมด <?= number_format($totalImages) ?> รูปภาพ
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="ค้นหาสถานที่ หรือชื่อไฟล์...">
                                    </div>
                                    <button type="button" id="btnToggleAll" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                                        <i class="fas fa-expand-alt mr-1"></i> ขยายทั้งหมด
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0" id="groupedImageTable">
                                        <thead class="bg-light text-muted text-uppercase" style="font-size: 12.5px; letter-spacing: 0.5px;">
                                            <tr>
                                                <th style="width: 70px;" class="text-center py-3">ลำดับ</th>
                                                <th class="py-3">สถานที่ท่องเที่ยว</th>
                                                <th style="width: 170px;" class="text-center py-3">จำนวนรูปภาพ</th>
                                                <th style="width: 220px;" class="text-center py-3">อัปโหลดล่าสุด</th>
                                                <th style="width: 240px; white-space: nowrap;" class="text-center py-3">รายการรูปภาพ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no = 1;
                                            if ($locResult && $locResult->num_rows > 0): 
                                                while ($loc = $locResult->fetch_assoc()):
                                                    $uid = $loc['u_id'];
                                                    $imgCount = (int)$loc['img_count'];
                                                    $spotImages = $imagesByUid[$uid] ?? [];
                                            ?>
                                                <!-- Main Location Row -->
                                                <tr class="location-row" data-target="#collapse-loc-<?= $uid ?>" style="background-color: #fff;">
                                                    <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="<?= htmlspecialchars($loc['image_path']) ?>" 
                                                                 alt="<?= htmlspecialchars($loc['image_name']) ?>" 
                                                                 class="rounded shadow-sm" 
                                                                 style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;">
                                                            <div>
                                                                <span class="font-weight-bold text-gray-800 d-block loc-name" style="font-size: 15px;">
                                                                    <?= htmlspecialchars($loc['image_name']) ?>
                                                                </span>
                                                                <small class="text-muted"><i class="fas fa-images mr-1"></i><?= $imgCount ?> รูปภาพในแกลเลอรี</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($imgCount > 0): ?>
                                                            <span class="badge px-3 py-2 text-primary" style="background: rgba(2, 132, 199, 0.1); font-size: 13px; font-weight: 600; border-radius: 8px;">
                                                                <i class="fas fa-images mr-1"></i> <?= $imgCount ?> รูปภาพ
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge px-3 py-2 text-muted" style="background: #f1f5f9; font-size: 13px; font-weight: 500; border-radius: 8px;">
                                                                0 รูปภาพ
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center text-muted text-sm">
                                                        <?= !empty($loc['last_upload']) ? thai_date_format($loc['last_upload']) : '-' ?>
                                                    </td>
                                                    <td class="text-center" style="white-space: nowrap;">
                                                        <div class="d-inline-flex align-items-center justify-content-center gap-2" style="white-space: nowrap;">
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-toggle-collapse px-3 text-nowrap" 
                                                                    data-bs-toggle="collapse" data-bs-target="#collapse-loc-<?= $uid ?>" 
                                                                    aria-expanded="false" aria-controls="collapse-loc-<?= $uid ?>">
                                                                <i class="fas fa-chevron-down mr-1 toggle-icon"></i> ดูรายการ (<?= $imgCount ?>)
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center shadow-xs" 
                                                                    style="width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;"
                                                                    onclick="openUploadForSpot(<?= $uid ?>)" title="อัปโหลดรูปเพิ่มให้สถานที่นี้">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Sub-item Collapse Row (คลี่ลงเป็น List) -->
                                                <tr class="collapse-row">
                                                    <td colspan="5" class="p-0 border-0">
                                                        <div class="collapse" id="collapse-loc-<?= $uid ?>">
                                                            <div class="subtable-container">
                                                                <?php if ($imgCount > 0): ?>
                                                                    <div class="table-responsive bg-white rounded shadow-sm border" style="border-radius: 12px; overflow: hidden;">
                                                                        <table class="table table-sm table-hover mb-0">
                                                                            <thead class="bg-light text-muted" style="font-size: 12px;">
                                                                                <tr>
                                                                                    <th style="width: 80px;" class="text-center py-2">รูปตัวอย่าง</th>
                                                                                    <th class="py-2">ชื่อไฟล์รูปภาพ</th>
                                                                                    <th style="width: 220px;" class="text-center py-2">วันที่และเวลาที่อัปโหลด</th>
                                                                                    <th style="width: 170px;" class="text-center py-2">การจัดการ</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($spotImages as $img): ?>
                                                                                    <tr class="file-row">
                                                                                        <td class="text-center py-2 align-middle">
                                                                                            <a href="uploads/<?= htmlspecialchars($img['filename']) ?>" target="_blank" title="คลิกดูรูปขนาดเต็ม">
                                                                                                <img src="uploads/<?= htmlspecialchars($img['filename']) ?>" 
                                                                                                     alt="<?= htmlspecialchars($img['filename']) ?>"
                                                                                                     class="rounded shadow-xs img-hover-zoom" 
                                                                                                     style="width: 44px; height: 44px; object-fit: cover; border: 1px solid #cbd5e1;">
                                                                                            </a>
                                                                                        </td>
                                                                                        <td class="py-2 align-middle">
                                                                                            <span class="file-name font-monospace text-sm text-gray-700">
                                                                                                <?= htmlspecialchars($img['filename']) ?>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="text-center py-2 align-middle text-muted text-sm">
                                                                                            <?= thai_date_format($img['uploaded_at']) ?>
                                                                                        </td>
                                                                                        <td class="text-center py-2 align-middle">
                                                                                            <div class="btn-group btn-group-sm">
                                                                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                                                                        data-id="<?= $img['m_id'] ?>"
                                                                                                        data-u_id="<?= htmlspecialchars($img['u_id']) ?>"
                                                                                                        data-filename="<?= htmlspecialchars($img['filename']) ?>"
                                                                                                        data-uploaded_at="<?= htmlspecialchars($img['uploaded_at']) ?>"
                                                                                                        style="border-radius: 6px 0 0 6px;">
                                                                                                    <i class="fas fa-edit mr-1"></i>แก้ไข
                                                                                                </button>
                                                                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $img['m_id'] ?>)"
                                                                                                        style="border-radius: 0 6px 6px 0;">
                                                                                                    <i class="fas fa-trash-alt mr-1"></i>ลบ
                                                                                                </button>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="text-center py-3 text-muted bg-white rounded border">
                                                                        <i class="fas fa-info-circle mr-1 text-warning"></i> ยังไม่มีรูปภาพในสถานที่นี้
                                                                        <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="openUploadForSpot(<?= $uid ?>)">
                                                                            <i class="fas fa-upload mr-1"></i> อัปโหลดรูปภาพเลย
                                                                        </button>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php 
                                                endwhile;
                                            else:
                                            ?>
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i class="fas fa-folder-open mr-1"></i> ไม่พบข้อมูลสถานที่ท่องเที่ยวในระบบ
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
                                <i class="fas fa-edit mr-2"></i>แก้ไขรูปภาพจุดท่องเที่ยว
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="edit_spot_image.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; height: 100%; margin: 0;">
                            <div class="modal-body px-4 py-3" style="overflow-y: auto;">
                                <input type="hidden" name="m_id" id="edit-id">
                                <input type="hidden" name="old_image_path" id="edit-path">

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">ชื่อสถานที่ <span class="text-danger">*</span></label>
                                    <select class="form-select" name="u_id" id="edit-u_id" required>
                                        <?php
                                        $sql2 = "SELECT u_id, image_name FROM uploads ORDER BY image_name ASC";
                                        $result2 = mysqli_query($conn, $sql2);
                                        while ($rs2 = mysqli_fetch_array($result2)) {
                                            echo "<option value=\"" . htmlspecialchars($rs2['u_id']) . "\">" . htmlspecialchars($rs2['image_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
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
                                            <input type="file" name="image" class="form-control" id="edit-filename" accept="image/*">
                                            <small class="text-muted">ปล่อยว่างไว้หากต้องการใช้รูปภาพเดิม</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i> ยกเลิก
                                </button>
                                <button type="submit" class="btn btn-warning px-4 font-weight-bold shadow-sm" style="color: #212529;">
                                    <i class="fas fa-save mr-1"></i> บันทึกการเปลี่ยนแปลง
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
                                <i class="fas fa-cloud-upload-alt mr-2"></i>อัปโหลดรูปภาพจุดท่องเที่ยว
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="add_spot_image.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; height: 100%; margin: 0;">
                            <div class="modal-body px-4 py-3" style="overflow-y: auto;">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">เลือกสถานที่ท่องเที่ยว <span class="text-danger">*</span></label>
                                    <select class="form-select" name="u_id" id="u_id" required>
                                        <option value="">-- กรุณาเลือกสถานที่ --</option>
                                        <?php
                                        $sql2 = "SELECT u_id, image_name FROM uploads ORDER BY image_name ASC";
                                        $result2 = mysqli_query($conn, $sql2);
                                        while ($rs2 = mysqli_fetch_array($result2)) {
                                            echo "<option value=\"" . htmlspecialchars($rs2['u_id']) . "\">" . htmlspecialchars($rs2['image_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">เลือกรูปภาพ (สามารถเลือกได้หลายรูป) <span class="text-danger">*</span></label>
                                    <input type="file" name="images[]" class="form-control" id="upload-files" multiple accept="image/*" required>
                                    <small class="text-muted">กดปุ่ม Ctrl หรือ Shift ค้างไว้เพื่อเลือกหลายรูปภาพพร้อมกันได้</small>
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i> ยกเลิก
                                </button>
                                <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                                    <i class="fas fa-upload mr-1"></i> อัปโหลดรูปภาพ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- JS Script Section -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // เปิด Modal อัปโหลดพร้อมเลือกสถานที่ให้อัตโนมัติ
            function openUploadForSpot(u_id) {
                const sel = document.getElementById('u_id');
                if (sel) {
                    sel.value = u_id;
                }
                const modalEl = document.getElementById('uploadModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            }

            // Quick Search Filter (ค้นหาสถานที่ หรือชื่อไฟล์)
            document.getElementById('searchInput').addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const locRows = document.querySelectorAll('.location-row');
                
                locRows.forEach(row => {
                    const targetId = row.getAttribute('data-target');
                    const collapseEl = document.querySelector(targetId);
                    const collapseRow = row.nextElementSibling;
                    const locName = row.querySelector('.loc-name').textContent.toLowerCase();
                    
                    let matchFile = false;
                    if (collapseEl) {
                        const fileRows = collapseEl.querySelectorAll('.file-row');
                        fileRows.forEach(fRow => {
                            const fname = fRow.querySelector('.file-name').textContent.toLowerCase();
                            if (fname.includes(query)) {
                                fRow.style.display = '';
                                matchFile = true;
                            } else {
                                fRow.style.display = (query === '') ? '' : 'none';
                            }
                        });
                    }

                    if (locName.includes(query) || matchFile) {
                        row.style.display = '';
                        collapseRow.style.display = '';
                        if (query !== '' && matchFile && collapseEl) {
                            bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }).show();
                        }
                    } else {
                        row.style.display = 'none';
                        collapseRow.style.display = 'none';
                    }
                });
            });

            // ปุ่มขยายทั้งหมด / ยุบทั้งหมด
            let allExpanded = false;
            document.getElementById('btnToggleAll').addEventListener('click', function() {
                allExpanded = !allExpanded;
                const collapses = document.querySelectorAll('.collapse');
                collapses.forEach(c => {
                    const inst = bootstrap.Collapse.getOrCreateInstance(c, { toggle: false });
                    if (allExpanded) {
                        inst.show();
                    } else {
                        inst.hide();
                    }
                });
                this.innerHTML = allExpanded ? '<i class="fas fa-compress-alt mr-1"></i> ยุบทั้งหมด' : '<i class="fas fa-expand-alt mr-1"></i> ขยายทั้งหมด';
            });

            // คลิกที่แถวสถานที่เพื่อเปิด/ปิด Accordion
            document.querySelectorAll('.location-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('button') || e.target.closest('a')) return;
                    const targetId = this.getAttribute('data-target');
                    const collapseEl = document.querySelector(targetId);
                    if (collapseEl) {
                        const inst = bootstrap.Collapse.getOrCreateInstance(collapseEl);
                        inst.toggle();
                    }
                });
            });

            // ดึงข้อมูลไปใส่ Modal เมื่อกดปุ่มแก้ไข
            document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) return;
                var id = button.getAttribute('data-id');
                var u_id = button.getAttribute('data-u_id');
                var filename = button.getAttribute('data-filename');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-u_id').value = u_id;
                document.getElementById('edit-path').value = 'uploads/' + filename;
                document.getElementById('preview-image').src = 'uploads/' + filename;

                // อัปเดตภาพตัวอย่างเมื่อเลือกไฟล์ใหม่
                document.getElementById('edit-filename').addEventListener('change', function(e) {
                    if (e.target.files && e.target.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function() {
                            document.getElementById('preview-image').src = reader.result;
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            });

            // ฟังก์ชันยืนยันการลบ
            function confirmDelete(m_id) {
                Swal.fire({
                    title: "ยืนยันการลบ?",
                    text: "คุณต้องการลบรูปภาพนี้หรือไม่?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ลบ",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete_spot_image.php?m_id=' + m_id;
                    }
                });
            }
        </script>

        <!-- Flash Alert -->
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
<?php
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'กรุณาเข้าสู่ระบบ!',
            icon: 'error'
        }).then(() => {
            window.location='frm_login.php'; 
        });
    </script>";
    exit;
}
?>
