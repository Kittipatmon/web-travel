<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {

    // ดึงข้อมูลสถานที่ท่องเที่ยวเชื่อมโยงกับ uploads
    $spotQuery = "
        SELECT t.*, u.image_name, u.image_path, u.title, u.subtitle 
        FROM tourist_spot t 
        LEFT JOIN uploads u ON t.u_id = u.u_id 
        ORDER BY t.t_id ASC
    ";
    $spotResult = $conn->query($spotQuery);
    $totalSpots = $spotResult ? $spotResult->num_rows : 0;
?>
    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>รายละเอียดสถานที่ท่องเที่ยว</title>
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

            .detail-row {
                transition: background-color 0.2s ease;
                cursor: pointer;
            }

            .detail-row:hover {
                background-color: #f1f5f9 !important;
            }

            .btn-toggle-detail {
                border-radius: 8px;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
            }

            .toggle-icon {
                transition: transform 0.25s ease;
                display: inline-block;
            }

            .btn-toggle-detail[aria-expanded="true"] .toggle-icon,
            .detail-row.is-open .toggle-icon {
                transform: rotate(180deg);
            }

            .subcard-container {
                background: #f8fafc;
                border-bottom: 2px solid #e2e8f0;
                padding: 18px 24px;
            }

            .info-badge {
                border-radius: 8px;
                font-size: 12px;
                font-weight: 500;
                padding: 4px 10px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
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
                                    <i class="fas fa-map-marked-alt mr-2"></i>รายละเอียดสถานที่ท่องเที่ยว
                                </h3>
                                <p class="m-0 text-white-50 text-sm mt-1">
                                    จัดการข้อมูลเวลาทำการ, จำนวนผู้เข้าชม, ขนาดพื้นที่, และช่วงเวลายอดนิยมของสถานที่ท่องเที่ยว
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-light px-3 py-2 font-weight-bold shadow-sm" 
                                        style="border-radius: 10px; color: #059669;"
                                        data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-plus-circle mr-1"></i> เพิ่มรายละเอียดสถานที่
                                </button>
                            </div>
                        </div>

                        <!-- Card Table -->
                        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="m-0 font-weight-bold text-gray-800" style="font-size: 16px;">
                                        <i class="fas fa-list-alt text-success mr-2"></i>ตารางข้อมูลรายละเอียดสถานที่
                                    </h5>
                                    <span class="badge px-3 py-2" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 13px; font-weight: 600; border-radius: 8px;">
                                        ทั้งหมด <?= number_format($totalSpots) ?> สถานที่
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="ค้นหาสถานที่ หรือวันทำการ...">
                                    </div>
                                    <button type="button" id="btnToggleAll" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                                        <i class="fas fa-expand-alt mr-1"></i> ขยายข้อมูลทั้งหมด
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0" id="spotTable">
                                        <thead class="bg-light text-muted text-uppercase" style="font-size: 12.5px; letter-spacing: 0.5px;">
                                            <tr>
                                                <th style="width: 60px;" class="text-center py-3">ลำดับ</th>
                                                <th class="py-3">สถานที่ท่องเที่ยว</th>
                                                <th style="width: 210px;" class="py-3">เวลาทำการ & วันเปิด</th>
                                                <th style="width: 190px;" class="py-3">ความจุ & ขนาดพื้นที่</th>
                                                <th style="width: 210px;" class="py-3">ช่วงยอดนิยม & ฤดูกาล</th>
                                                <th style="width: 220px;" class="text-center py-3">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no = 1;
                                            if ($spotResult && $spotResult->num_rows > 0): 
                                                while ($spot = $spotResult->fetch_assoc()):
                                                    $t_id = $spot['t_id'];
                                                    $u_id = $spot['u_id'];
                                                    $locName = !empty($spot['image_name']) ? $spot['image_name'] : 'ไม่ระบุชื่อสถานที่';
                                                    $locImage = !empty($spot['image_path']) ? $spot['image_path'] : 'assets/images/default.jpg';
                                                    $title = !empty($spot['title']) ? $spot['title'] : '';
                                                    $subtitle = !empty($spot['subtitle']) ? $spot['subtitle'] : '';

                                                    // Format Open/Close Time
                                                    $openTimeFmt = !empty($spot['open_time']) ? substr($spot['open_time'], 0, 5) : '-';
                                                    $closeTimeFmt = !empty($spot['close_time']) ? substr($spot['close_time'], 0, 5) : '-';
                                                    $timeText = ($openTimeFmt !== '-' && $closeTimeFmt !== '-') ? "{$openTimeFmt} - {$closeTimeFmt} น." : '-';

                                                    // Format Area
                                                    $areaNum = (float)($spot['area'] ?? 0);
                                                    $areaText = $areaNum > 0 ? number_format($areaNum) . ' ตร.ม.' : 'ไม่ระบุขนาด';

                                                    // Format Capacity
                                                    $visitorLimit = !empty($spot['visitor_limit']) ? $spot['visitor_limit'] : 'ไม่จำกัด';
                                                    $recVisitor = !empty($spot['recommended_visitors']) && $spot['recommended_visitors'] != '0' ? number_format($spot['recommended_visitors']) . ' คน' : '-';

                                                    // Format Peak Times
                                                    $timePeriod = !empty($spot['time_period']) ? $spot['time_period'] : '';
                                                    $dayPeriod = !empty($spot['day_period']) ? $spot['day_period'] : '';
                                                    $season = !empty($spot['season']) ? $spot['season'] : '';
                                            ?>
                                                <!-- Main Row -->
                                                <tr class="detail-row" data-target="#collapse-detail-<?= $t_id ?>" style="background-color: #fff;">
                                                    <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="<?= htmlspecialchars($locImage) ?>" 
                                                                 alt="<?= htmlspecialchars($locName) ?>" 
                                                                 class="rounded shadow-sm" 
                                                                 style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                                            <div>
                                                                <span class="font-weight-bold text-gray-800 d-block loc-name" style="font-size: 15px;">
                                                                    <?= htmlspecialchars($locName) ?>
                                                                </span>
                                                                <?php if ($title || $subtitle): ?>
                                                                    <small class="text-muted d-block" style="font-size: 12px;">
                                                                        <?= htmlspecialchars($title) ?> <?= $subtitle ? '• ' . htmlspecialchars($subtitle) : '' ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <div>
                                                                <span class="info-badge bg-light text-dark border">
                                                                    <i class="far fa-calendar-alt text-primary"></i> 
                                                                    <span class="date-time-val"><?= htmlspecialchars($spot['date_time'] ?: 'ไม่ระบุ') ?></span>
                                                                </span>
                                                            </div>
                                                            <small class="text-muted text-nowrap mt-1">
                                                                <i class="far fa-clock text-success mr-1"></i> <?= $timeText ?>
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <span class="text-sm font-weight-500 text-gray-800 text-nowrap">
                                                                <i class="fas fa-users text-info mr-1"></i> <?= htmlspecialchars($visitorLimit) ?>
                                                            </span>
                                                            <small class="text-muted text-nowrap">
                                                                <i class="fas fa-vector-square text-secondary mr-1"></i> <?= $areaText ?>
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <?php if ($season): ?>
                                                                <div>
                                                                    <span class="info-badge" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                                                                        <i class="fas fa-leaf"></i> <?= htmlspecialchars($season) ?>
                                                                    </span>
                                                                </div>
                                                            <?php else: ?>
                                                                <div>
                                                                    <span class="info-badge text-muted" style="background: #f1f5f9;">
                                                                        <i class="fas fa-leaf"></i> ตลอดทั้งปี
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if ($timePeriod || $dayPeriod): ?>
                                                                <small class="text-muted text-nowrap mt-1">
                                                                    <i class="fas fa-fire text-danger mr-1"></i>
                                                                    <?= htmlspecialchars($timePeriod ?: '-') ?> 
                                                                    <?= $dayPeriod ? ' (' . htmlspecialchars($dayPeriod) . ')' : '' ?>
                                                                </small>
                                                            <?php else: ?>
                                                                <small class="text-muted mt-1">-</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-center" style="white-space: nowrap;">
                                                        <div class="d-inline-flex align-items-center justify-content-center gap-1" style="white-space: nowrap;">
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-toggle-detail px-2 py-1 text-nowrap" 
                                                                    data-bs-toggle="collapse" data-bs-target="#collapse-detail-<?= $t_id ?>" 
                                                                    aria-expanded="false" aria-controls="collapse-detail-<?= $t_id ?>" title="คลิกดูสรุปรายละเอียดเต็ม">
                                                                <i class="fas fa-chevron-down mr-1 toggle-icon"></i> รายละเอียด
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-warning font-weight-bold px-2 py-1 shadow-xs" 
                                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                                    data-id="<?= $spot['t_id'] ?>"
                                                                    data-u_id="<?= htmlspecialchars($spot['u_id']) ?>"
                                                                    data-visitor_limit="<?= htmlspecialchars($spot['visitor_limit']) ?>"
                                                                    data-recommended_visitors="<?= htmlspecialchars($spot['recommended_visitors']) ?>"
                                                                    data-area="<?= htmlspecialchars($spot['area']) ?>"
                                                                    data-date_time="<?= htmlspecialchars($spot['date_time']) ?>"
                                                                    data-open_time="<?= htmlspecialchars($spot['open_time']) ?>"
                                                                    data-close_time="<?= htmlspecialchars($spot['close_time']) ?>"
                                                                    data-time_period="<?= htmlspecialchars($spot['time_period']) ?>"
                                                                    data-day_period="<?= htmlspecialchars($spot['day_period']) ?>"
                                                                    data-season="<?= htmlspecialchars($spot['season']) ?>"
                                                                    title="แก้ไขข้อมูล">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger px-2 py-1 shadow-xs" 
                                                                    onclick="confirmDelete(<?= $spot['t_id'] ?>)" title="ลบข้อมูล">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Sub-item Collapse Card (คลี่ดูรายละเอียดฉบับเต็ม) -->
                                                <tr class="collapse-row">
                                                    <td colspan="6" class="p-0 border-0">
                                                        <div class="collapse" id="collapse-detail-<?= $t_id ?>">
                                                            <div class="subcard-container">
                                                                <div class="bg-white rounded-3 p-3 shadow-sm border">
                                                                    <div class="row g-3 align-items-center">
                                                                        <!-- Card Image & Info -->
                                                                        <div class="col-md-4 border-end-md">
                                                                            <div class="d-flex gap-3 align-items-center">
                                                                                <img src="<?= htmlspecialchars($locImage) ?>" 
                                                                                     class="rounded-3 shadow-sm" 
                                                                                     style="width: 72px; height: 72px; object-fit: cover;">
                                                                                <div>
                                                                                    <h6 class="font-weight-bold m-0 text-gray-800"><?= htmlspecialchars($locName) ?></h6>
                                                                                    <span class="badge bg-light text-primary border mt-1">รหัสอ้างอิง #<?= $u_id ?></span>
                                                                                    <p class="text-xs text-muted m-0 mt-1"><?= htmlspecialchars($title) ?></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Card Detail 1: Capacity & Area -->
                                                                        <div class="col-md-4 border-end-md">
                                                                            <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-2">
                                                                                <i class="fas fa-users-cog text-info mr-1"></i> ข้อมูลพื้นที่ & ผู้เข้าชม
                                                                            </h6>
                                                                            <ul class="list-unstyled m-0 text-xs text-gray-700 space-y-1">
                                                                                <li class="d-flex justify-content-between py-1 border-bottom">
                                                                                    <span class="text-muted">ความจุสูงสุด:</span>
                                                                                    <span class="font-weight-bold"><?= htmlspecialchars($visitorLimit) ?></span>
                                                                                </li>
                                                                                <li class="d-flex justify-content-between py-1 border-bottom">
                                                                                    <span class="text-muted">ผู้เข้าชมแนะนำ:</span>
                                                                                    <span class="font-weight-bold"><?= $recVisitor ?></span>
                                                                                </li>
                                                                                <li class="d-flex justify-content-between py-1">
                                                                                    <span class="text-muted">ขนาดพื้นที่:</span>
                                                                                    <span class="font-weight-bold"><?= $areaText ?></span>
                                                                                </li>
                                                                            </ul>
                                                                        </div>

                                                                        <!-- Card Detail 2: Time & Peak -->
                                                                        <div class="col-md-4">
                                                                            <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-2">
                                                                                <i class="fas fa-business-time text-success mr-1"></i> เวลาทำการ & สถิติความนิยม
                                                                            </h6>
                                                                            <ul class="list-unstyled m-0 text-xs text-gray-700 space-y-1">
                                                                                <li class="d-flex justify-content-between py-1 border-bottom">
                                                                                    <span class="text-muted">วันเปิดให้บริการ:</span>
                                                                                    <span class="font-weight-bold"><?= htmlspecialchars($spot['date_time'] ?: 'ทุกวัน') ?></span>
                                                                                </li>
                                                                                <li class="d-flex justify-content-between py-1 border-bottom">
                                                                                    <span class="text-muted">เวลาเปิด-ปิด:</span>
                                                                                    <span class="font-weight-bold text-success"><?= $timeText ?></span>
                                                                                </li>
                                                                                <li class="d-flex justify-content-between py-1 border-bottom">
                                                                                    <span class="text-muted">ช่วงเวลายอดนิยม:</span>
                                                                                    <span class="font-weight-bold text-danger"><?= htmlspecialchars($timePeriod ?: '-') ?></span>
                                                                                </li>
                                                                                <li class="d-flex justify-content-between py-1">
                                                                                    <span class="text-muted">ฤดูกาลยอดนิยม:</span>
                                                                                    <span class="font-weight-bold text-primary"><?= htmlspecialchars($season ?: 'ตลอดทั้งปี') ?></span>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php 
                                                endwhile;
                                            else:
                                            ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                        <i class="fas fa-folder-open mr-1"></i> ไม่พบข้อมูลรายละเอียดสถานที่ท่องเที่ยวในระบบ
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
                                <i class="fas fa-edit mr-2"></i>แก้ไขรายละเอียดสถานที่ท่องเที่ยว
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="edit_tourist_spot.php" method="POST" style="display: flex; flex-direction: column; height: 100%; margin: 0;">
                            <div class="modal-body px-4 py-3" style="overflow-y: auto;">
                                <input type="hidden" name="t_id" id="edit-id">

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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">จำนวนผู้เข้าชมสูงสุด <span class="text-danger">*</span></label>
                                        <input type="text" name="visitor_limit" id="edit-visitor_limit" class="form-control" required placeholder="เช่น ไม่จำกัด หรือ 500">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">จำนวนผู้เข้าชมแนะนำ (คน)</label>
                                        <input type="number" name="recommended_visitors" id="edit-recommended_visitors" class="form-control" placeholder="เช่น 200">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">ขนาดพื้นที่ (ตร.ม.) <span class="text-danger">*</span></label>
                                        <input type="number" step="any" name="area" id="edit-area" class="form-control" required placeholder="เช่น 5000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">วันที่เปิดให้บริการ <span class="text-danger">*</span></label>
                                        <input type="text" name="date_time" id="edit-date_time" class="form-control" required placeholder="เช่น ทุกวัน, เสาร์-อาทิตย์">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">เวลาเปิด <span class="text-danger">*</span></label>
                                        <input type="time" name="open_time" id="edit-open_time" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">เวลาปิด <span class="text-danger">*</span></label>
                                        <input type="time" name="close_time" id="edit-close_time" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">ช่วงเวลายอดนิยม</label>
                                        <input type="text" name="time_period" id="edit-time_period" class="form-control" placeholder="เช่น 09:00 - 12:00">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">ช่วงวันยอดนิยม</label>
                                        <input type="text" name="day_period" id="edit-day_period" class="form-control" placeholder="เช่น วันเสาร์-อาทิตย์">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">ฤดูกาลยอดนิยม</label>
                                    <input type="text" name="season" id="edit-season" class="form-control" placeholder="เช่น ฤดูหนาว, ตลอดปี">
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
                                <i class="fas fa-plus-circle mr-2"></i>เพิ่มรายละเอียดสถานที่ท่องเที่ยวใหม่
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="add_tourist_spot.php" method="POST" style="display: flex; flex-direction: column; height: 100%; margin: 0;">
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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">จำนวนผู้เข้าชมสูงสุด <span class="text-danger">*</span></label>
                                        <input type="text" name="visitor_limit" class="form-control" required placeholder="เช่น ไม่จำกัด หรือ 500">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">จำนวนผู้เข้าชมแนะนำ (คน)</label>
                                        <input type="number" name="recommended_visitors" class="form-control" placeholder="เช่น 200">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">ขนาดพื้นที่ (ตร.ม.) <span class="text-danger">*</span></label>
                                        <input type="number" step="any" name="area" class="form-control" required placeholder="เช่น 5000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">วันที่เปิดให้บริการ <span class="text-danger">*</span></label>
                                        <input type="text" name="date_time" class="form-control" required placeholder="เช่น ทุกวัน, เสาร์-อาทิตย์">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">เวลาเปิด <span class="text-danger">*</span></label>
                                        <input type="time" name="open_time" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">เวลาปิด <span class="text-danger">*</span></label>
                                        <input type="time" name="close_time" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">ช่วงเวลายอดนิยม</label>
                                        <input type="text" name="time_period" class="form-control" placeholder="เช่น 09:00 - 12:00">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">ช่วงวันยอดนิยม</label>
                                        <input type="text" name="day_period" class="form-control" placeholder="เช่น วันเสาร์-อาทิตย์">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">ฤดูกาลยอดนิยม</label>
                                    <input type="text" name="season" class="form-control" placeholder="เช่น ฤดูหนาว, ตลอดปี">
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3" style="border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i> ยกเลิก
                                </button>
                                <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                                    <i class="fas fa-save mr-1"></i> บันทึกข้อมูล
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
            // Quick Search Filter (ค้นหาสถานที่ หรือวันทำการ)
            document.getElementById('searchInput').addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.detail-row');
                
                rows.forEach(row => {
                    const targetId = row.getAttribute('data-target');
                    const collapseRow = row.nextElementSibling;
                    const locName = row.querySelector('.loc-name').textContent.toLowerCase();
                    const dateTime = row.querySelector('.date-time-val').textContent.toLowerCase();
                    
                    if (locName.includes(query) || dateTime.includes(query)) {
                        row.style.display = '';
                        collapseRow.style.display = '';
                    } else {
                        row.style.display = 'none';
                        collapseRow.style.display = 'none';
                    }
                });
            });

            // ปุ่มขยายข้อมูลทั้งหมด / ยุบทั้งหมด
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
                this.innerHTML = allExpanded ? '<i class="fas fa-compress-alt mr-1"></i> ยุบข้อมูลทั้งหมด' : '<i class="fas fa-expand-alt mr-1"></i> ขยายข้อมูลทั้งหมด';
            });

            // คลิกที่แถวเพื่อเปิด/ปิด Accordion การ์ดรายละเอียด
            document.querySelectorAll('.detail-row').forEach(row => {
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
                var t_id = button.getAttribute('data-id');
                var u_id = button.getAttribute('data-u_id');
                var visitor_limit = button.getAttribute('data-visitor_limit');
                var recommended_visitors = button.getAttribute('data-recommended_visitors');
                var area = button.getAttribute('data-area');
                var date_time = button.getAttribute('data-date_time');
                var open_time = button.getAttribute('data-open_time');
                var close_time = button.getAttribute('data-close_time');
                var time_period = button.getAttribute('data-time_period');
                var day_period = button.getAttribute('data-day_period');
                var season = button.getAttribute('data-season');

                document.getElementById('edit-id').value = t_id;
                document.getElementById('edit-u_id').value = u_id;
                document.getElementById('edit-visitor_limit').value = visitor_limit;
                document.getElementById('edit-recommended_visitors').value = (recommended_visitors && recommended_visitors !== '0') ? recommended_visitors : '';
                document.getElementById('edit-area').value = area;
                document.getElementById('edit-date_time').value = date_time;
                document.getElementById('edit-open_time').value = open_time;
                document.getElementById('edit-close_time').value = close_time;
                document.getElementById('edit-time_period').value = time_period;
                document.getElementById('edit-day_period').value = day_period;
                document.getElementById('edit-season').value = season;
            });

            // ฟังก์ชันยืนยันการลบ
            function confirmDelete(t_id) {
                Swal.fire({
                    title: "ยืนยันการลบ?",
                    text: "คุณต้องการลบข้อมูลรายละเอียดสถานที่นี้หรือไม่?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ลบ",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete_tourist_spot.php?t_id=' + t_id;
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
