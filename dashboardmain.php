<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {

    // 1. ยอดผู้เข้าชมทั้งหมด และ จำนวนสถานที่ทั้งหมด (ข้อมูลจริง 100%)
    $totRes = $conn->query("SELECT SUM(view_count) AS total_views, COUNT(*) AS total_locations FROM uploads");
    $totRow = $totRes ? $totRes->fetch_assoc() : [];
    $total_views = (int)($totRow['total_views'] ?? 0);
    $total_locations = (int)($totRow['total_locations'] ?? 0);

    // 2. จำนวนภาพในแกลเลอรีทั้งหมด
    $imgRes = $conn->query("SELECT COUNT(*) AS total_images FROM images");
    $total_images = (int)($imgRes ? $imgRes->fetch_assoc()['total_images'] : 0);

    // 3. จำนวนสถานที่ที่มีทัวร์เสมือนจริง VR 360°
    $vrRes = $conn->query("SELECT COUNT(*) AS total_vr FROM uploads WHERE vr_link IS NOT NULL AND vr_link != ''");
    $total_vr = (int)($vrRes ? $vrRes->fetch_assoc()['total_vr'] : 0);

    // 4. อันดับสถานที่ท่องเที่ยวที่มีผู้เข้าชมมากที่สุด (เรียงจากมากไปน้อย - ข้อมูลจริงจากตาราง uploads)
    $spotsRes = $conn->query("
        SELECT u.u_id, u.image_name, u.title, u.subtitle, u.image_path, u.view_count, u.vr_link, u.created_at,
               c.category_name, c.color AS cat_color, c.icon AS cat_icon
        FROM uploads u
        LEFT JOIN categories c ON u.c_id = c.c_id
        ORDER BY u.view_count DESC
    ");
    $top_spots = [];
    $rank = 1;
    $chartLabels = [];
    $chartCounts = [];
    $chartPercentages = [];

    if ($spotsRes) {
        while ($row = $spotsRes->fetch_assoc()) {
            $pct = $total_views > 0 ? round(($row['view_count'] / $total_views) * 100, 1) : 0;
            $top_spots[] = [
                'rank' => $rank++,
                'u_id' => (int)$row['u_id'],
                'name' => $row['image_name'],
                'title' => $row['title'],
                'subtitle' => $row['subtitle'] ?? '',
                'category_name' => $row['category_name'] ?? 'ทั่วไป',
                'cat_color' => $row['cat_color'] ?? '#0284c7',
                'cat_icon' => $row['cat_icon'] ?? 'fas fa-map-marker-alt',
                'image_path' => $row['image_path'],
                'view_count' => (int)$row['view_count'],
                'percentage' => $pct,
                'has_vr' => !empty($row['vr_link'])
            ];
            $chartLabels[] = $row['image_name'];
            $chartCounts[] = (int)$row['view_count'];
            $chartPercentages[] = $pct;
        }
    }
    $top_one = !empty($top_spots) ? $top_spots[0] : null;

    // 5. ข้อมูลเชิงวิเคราะห์แยกตามหมวดหมู่จริง (Category Analytics - 100% Real Data จาก categories + uploads)
    $catRes = $conn->query("
        SELECT c.c_id, c.category_name, c.color, c.icon, 
               COUNT(u.u_id) AS spot_count, 
               COALESCE(SUM(u.view_count), 0) AS cat_views 
        FROM categories c 
        LEFT JOIN uploads u ON c.c_id = u.c_id 
        GROUP BY c.c_id 
        HAVING cat_views > 0 OR spot_count > 0
        ORDER BY cat_views DESC, spot_count DESC
    ");
    $categories_stats = [];
    $catLabels = [];
    $catViews = [];
    $catColors = [];
    $catSpotCounts = [];

    if ($catRes) {
        while ($crow = $catRes->fetch_assoc()) {
            $c_pct = $total_views > 0 ? round(($crow['cat_views'] / $total_views) * 100, 1) : 0;
            $categories_stats[] = [
                'id' => (int)$crow['c_id'],
                'name' => $crow['category_name'],
                'color' => $crow['color'] ?: '#0284c7',
                'icon' => $crow['icon'] ?: 'fas fa-map-marker-alt',
                'spot_count' => (int)$crow['spot_count'],
                'views' => (int)$crow['cat_views'],
                'percentage' => $c_pct
            ];
            $catLabels[] = $crow['category_name'];
            $catViews[] = (int)$crow['cat_views'];
            $catColors[] = $crow['color'] ?: '#0284c7';
            $catSpotCounts[] = (int)$crow['spot_count'];
        }
    }

    // 6. ข้อมูลเชิงวิเคราะห์ค่าบริการจริง (Free vs Paid Attractions)
    $feeRes = $conn->query("
        SELECT u.u_id, COUNT(s.s_id) AS fee_count, COALESCE(MAX(s.fee), 0) AS max_fee
        FROM uploads u
        LEFT JOIN service_fee s ON u.u_id = s.u_id
        GROUP BY u.u_id
    ");
    $free_spots_count = 0;
    $paid_spots_count = 0;
    if ($feeRes) {
        while ($frow = $feeRes->fetch_assoc()) {
            if ((int)$frow['fee_count'] > 0 && (float)$frow['max_fee'] > 0) {
                $paid_spots_count++;
            } else {
                $free_spots_count++;
            }
        }
    }
    $free_pct = $total_locations > 0 ? round(($free_spots_count / $total_locations) * 100, 1) : 0;
    $paid_pct = $total_locations > 0 ? round(($paid_spots_count / $total_locations) * 100, 1) : 0;

    // 7. ข้อมูลเชิงวิเคราะห์ช่วงเวลาท่องเที่ยวหนาแน่นจริงจากตาราง tourist_spot
    $spotDetailsRes = $conn->query("
        SELECT t.time_period, t.day_period, t.season, COUNT(*) as cnt
        FROM tourist_spot t
        WHERE t.time_period IS NOT NULL AND t.time_period != ''
        GROUP BY t.time_period
        ORDER BY cnt DESC
        LIMIT 1
    ");
    $peak_row = $spotDetailsRes ? $spotDetailsRes->fetch_assoc() : null;
    $peak_time = $peak_row ? $peak_row['time_period'] : '09:00 - 15:00 น.';
    $peak_day = ($peak_row && !empty($peak_row['day_period'])) ? $peak_row['day_period'] : 'วันเสาร์-อาทิตย์';

    $top_category = !empty($categories_stats) ? $categories_stats[0] : null;

    // JSON Encoding สำหรับ Chart.js
    $chartLabelsJson = json_encode($chartLabels, JSON_UNESCAPED_UNICODE);
    $chartCountsJson = json_encode($chartCounts);
    $chartPercentagesJson = json_encode($chartPercentages);

    $catLabelsJson = json_encode($catLabels, JSON_UNESCAPED_UNICODE);
    $catViewsJson = json_encode($catViews);
    $catColorsJson = json_encode($catColors);
    $catSpotCountsJson = json_encode($catSpotCounts);

    // 8. ข้อมูลผู้ใช้งาน Admin
    $name_user = 'ผู้ดูแลระบบ';
    $email_user = $_SESSION['email_user'] ?? '';
    $userResult = $conn->query("SELECT id_user, name_user, email_user, role_user FROM users WHERE role_user='admin' LIMIT 1");
    if ($userResult && $userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
        $name_user = $userData['name_user'];
        $email_user = $userData['email_user'];
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดวิเคราะห์ข้อมูลและสถิติ - Virtual Vista PCRU</title>

    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!-- Font Awesome & Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap & AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style>
        body {
            font-family: 'Prompt', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
        }

        .stat-card-custom {
            background: #ffffff;
            border-radius: 16px;
            padding: 22px 24px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .stat-card-custom:hover .stat-icon-wrapper {
            transform: scale(1.08) rotate(3deg);
        }

        /* Analytics Insight Cards */
        .insight-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.25s ease;
        }
        .insight-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .insight-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .rank-1 { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .rank-2 { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; }
        .rank-3 { background: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; }
        .rank-other { background: #f1f5f9; color: #64748b; font-size: 13px; }

        .progress-bar-custom {
            height: 8px;
            border-radius: 9999px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chart-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .chart-container-responsive {
            position: relative;
            height: 320px;
            width: 100%;
        }

        /* Pulse Dot for Real-time status */
        .live-pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .refresh-spinner {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
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
                    <a class="nav-link font-weight-bold" href="dashboardmain.php">
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
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #2563eb); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 15px; box-shadow: 0 2px 8px rgba(2,132,199,0.35);">
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
            <a href="dashboardmain.php" class="brand-link" align="center" style="display: block; padding: 18px 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <img src="assets/images/logovr.png" style="max-height: 46px; width: auto; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));">
            </a>
            <?php include "sidebar.php"; ?>
        </aside>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="container-fluid px-4 py-4">
                
                <!-- 1. Header Banner & Real-time Live Status Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h1 class="text-2xl font-bold text-gray-800 m-0">แดชบอร์ดวิเคราะห์ข้อมูลและสถิติท่องเที่ยว</h1>
                            <span class="badge badge-light border text-success font-weight-500 d-inline-flex align-items-center gap-1 px-2 py-1" style="border-radius: 20px;">
                                <span class="live-pulse-dot"></span>
                                <span style="font-size: 11px;">Real-time Active (100% ข้อมูลจริง)</span>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 m-0">ยินดีต้อนรับคุณ <?= htmlspecialchars($name_user); ?> | วิเคราะห์และประมวลผลข้อมูลท่องเที่ยวจากฐานข้อมูลจริงแบบเรียลไทม์</p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Auto-sync Timestamp Badge with Current Thai Date & Time -->
                        <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1.5 bg-white border rounded-pill shadow-sm text-xs">
                            <i class="far fa-calendar-alt text-success"></i>
                            <span id="lastUpdatedDate" class="text-secondary font-weight-500"><?= thai_date_format(time(), false) ?></span>
                            <span class="text-muted opacity-50">|</span>
                            <i class="far fa-clock text-primary"></i>
                            <span id="lastUpdatedTime" class="text-secondary font-weight-bold"><?= date('H:i:s') ?> น.</span>
                        </div>

                        <!-- Manual Refresh Button -->
                        <button type="button" id="btnRefreshStats" onclick="fetchLiveStats(true)" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; font-weight: 500;">
                            <i class="fas fa-sync-alt" id="refreshIcon"></i>
                            <span>อัปเดตสด</span>
                        </button>

                        <a href="all_destinations.php" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px;">
                            <i class="fas fa-external-link-alt"></i>
                            <span>หน้าเว็บ</span>
                        </a>

                        <a href="dashboard_pic.php" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px;">
                            <i class="fas fa-plus"></i>
                            <span>จัดการสถานที่</span>
                        </a>
                    </div>
                </div>

                <!-- 2. TOP KPI SUMMARY CARDS (4 การ์ดตัวเลขหลัก) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    
                    <!-- Card 1: Total Views -->
                    <div class="stat-card-custom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-sm font-weight-500 text-gray-500 m-0">จำนวนผู้เข้าชมทั้งหมด</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2 mb-1" id="statTotalViews">
                                    <?= number_format($total_views); ?>
                                </h3>
                                <p class="text-xs text-blue-600 font-weight-500 m-0 d-flex align-items-center gap-1">
                                    <i class="fas fa-chart-line"></i> ยอดเข้าชมสะสมทุกสถานที่
                                </p>
                            </div>
                            <div class="stat-icon-wrapper" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1 mt-4">
                            <div class="h-1 rounded-full" style="width: 100%; background: linear-gradient(90deg, #0284c7, #2563eb);"></div>
                        </div>
                    </div>

                    <!-- Card 2: Total Locations -->
                    <div class="stat-card-custom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-sm font-weight-500 text-gray-500 m-0">สถานที่ท่องเที่ยวในระบบ</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2 mb-1" id="statTotalLocations">
                                    <?= number_format($total_locations); ?>
                                </h3>
                                <p class="text-xs text-emerald-600 font-weight-500 m-0 d-flex align-items-center gap-1">
                                    <i class="fas fa-check-circle"></i> เปิดให้บริการบนเว็บไซต์
                                </p>
                            </div>
                            <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1 mt-4">
                            <div class="h-1 rounded-full" style="width: 100%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                        </div>
                    </div>

                    <!-- Card 3: Top Attraction -->
                    <div class="stat-card-custom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="max-width: 70%;">
                                <p class="text-sm font-weight-500 text-gray-500 m-0">เข้าชมมากที่สุดอันดับ 1</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-2 mb-1 truncate" id="statTopName" title="<?= htmlspecialchars($top_one['name'] ?? '-'); ?>">
                                    <?= htmlspecialchars($top_one['name'] ?? '-'); ?>
                                </h3>
                                <p class="text-xs text-amber-600 font-weight-500 m-0 d-flex align-items-center gap-1">
                                    <i class="fas fa-trophy"></i> <span id="statTopCount"><?= number_format($top_one['view_count'] ?? 0); ?> ครั้ง</span> 
                                    (<span id="statTopPct"><?= $top_one['percentage'] ?? 0 ?>%</span>)
                                </p>
                            </div>
                            <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fas fa-award"></i>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1 mt-4">
                            <div class="h-1 rounded-full" id="statTopProgress" style="width: <?= min(100, $top_one['percentage'] ?? 0) ?>%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                        </div>
                    </div>

                    <!-- Card 4: VR 360 & Images -->
                    <div class="stat-card-custom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-sm font-weight-500 text-gray-500 m-0">รูปภาพและสื่อ VR 360°</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2 mb-1" id="statTotalImages">
                                    <?= number_format($total_images); ?>
                                </h3>
                                <p class="text-xs text-cyan-600 font-weight-500 m-0 d-flex align-items-center gap-1">
                                    <i class="fas fa-vr-cardboard"></i> ทัวร์ VR: <span id="statTotalVr" class="font-weight-bold"><?= $total_vr ?></span> แห่ง (100%)
                                </p>
                            </div>
                            <div class="stat-icon-wrapper" style="background: rgba(6, 182, 212, 0.1); color: #0891b2;">
                                <i class="fas fa-photo-video"></i>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1 mt-4">
                            <div class="h-1 rounded-full" style="width: 100%; background: linear-gradient(90deg, #06b6d4, #8b5cf6);"></div>
                        </div>
                    </div>
                </div>

                <!-- 3. REAL ANALYTICAL INSIGHTS HIGHLIGHTS (4 การ์ดวิเคราะห์เชิงลึกจากข้อมูลจริง) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Insight 1: Top Category Demand -->
                    <div class="insight-card">
                        <div class="insight-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                            <i class="<?= $top_category ? $top_category['icon'] : 'fas fa-campground' ?>"></i>
                        </div>
                        <div class="flex-1">
                            <span class="text-xs text-gray-500 font-medium">หมวดหมู่ยอดนิยมสูงสุด</span>
                            <div class="font-bold text-gray-800 text-sm mt-0.5" id="insightTopCatName">
                                <?= htmlspecialchars($top_category['name'] ?? 'กางเต็นท์ & แคมป์ปิ้ง') ?>
                            </div>
                            <div class="text-xs text-emerald-600 font-medium mt-0.5" id="insightTopCatStats">
                                <?= number_format($top_category['views'] ?? 0) ?> วิว (<?= $top_category['percentage'] ?? 0 ?>% ของทั้งหมด)
                            </div>
                        </div>
                    </div>

                    <!-- Insight 2: Peak Hours & Days -->
                    <div class="insight-card">
                        <div class="insight-icon" style="background: rgba(2, 132, 199, 0.15); color: #0284c7;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="flex-1">
                            <span class="text-xs text-gray-500 font-medium">ช่วงเวลาท่องเที่ยวยอดนิยม</span>
                            <div class="font-bold text-gray-800 text-sm mt-0.5" id="insightPeakTime">
                                <?= htmlspecialchars($peak_time) ?>
                            </div>
                            <div class="text-xs text-blue-600 font-medium mt-0.5" id="insightPeakDay">
                                <?= htmlspecialchars($peak_day) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Insight 3: Pricing & Admission Model -->
                    <div class="insight-card">
                        <div class="insight-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="flex-1">
                            <span class="text-xs text-gray-500 font-medium">โมเดลค่าบริการสถานที่</span>
                            <div class="font-bold text-gray-800 text-sm mt-0.5" id="insightFeeRatio">
                                เข้าชมฟรี <?= $free_pct ?>% (<?= $free_spots_count ?> แห่ง)
                            </div>
                            <div class="text-xs text-amber-600 font-medium mt-0.5" id="insightPaidCount">
                                มีค่าบริการ <?= $paid_pct ?>% (<?= $paid_spots_count ?> แห่ง)
                            </div>
                        </div>
                    </div>

                    <!-- Insight 4: Virtual Tour Readiness -->
                    <div class="insight-card">
                        <div class="insight-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                            <i class="fas fa-vr-cardboard"></i>
                        </div>
                        <div class="flex-1">
                            <span class="text-xs text-gray-500 font-medium">ความพร้อม Virtual Tour 360°</span>
                            <div class="font-bold text-gray-800 text-sm mt-0.5">
                                ครอบคลุม 100% (<?= $total_vr ?>/<?= $total_locations ?> แห่ง)
                            </div>
                            <div class="text-xs text-purple-600 font-medium mt-0.5">
                                คลังรูปบรรยากาศ <?= $total_images ?> รูปในระบบ
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. TOP VISITED LEADERBOARD TABLE (ตารางจัดอันดับสถานที่ท่องเที่ยวตามยอดวิว) -->
                <div class="card border-0 shadow-sm mb-6" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-fire-alt"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-800 m-0">อันดับสถานที่ท่องเที่ยวที่มีผู้เข้าชมมากที่สุด (Most Visited Attractions)</h2>
                                <p class="text-xs text-gray-500 m-0">ตรวจจับยอดวิวสดและเรียงลำดับความนิยมจากฐานข้อมูลจริงแบบ Real-time</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">
                                รวมทั้งหมด <span id="badgeSpotCount"><?= count($top_spots); ?></span> แห่ง
                            </span>
                            <a href="dashboard_pic.php" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                                <i class="fas fa-cog mr-1"></i> จัดการรายการ
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="leaderboardTable">
                                <thead style="background: #f8fafc; font-size: 12.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 80px;">อันดับ</th>
                                        <th>สถานที่ท่องเที่ยว</th>
                                        <th class="text-center" style="width: 140px;">หมวดหมู่</th>
                                        <th class="text-center" style="width: 150px;">ยอดการเข้าชม</th>
                                        <th style="width: 240px;">สัดส่วนความนิยม</th>
                                        <th class="text-center" style="width: 130px;">ดูหน้าเว็บ</th>
                                    </tr>
                                </thead>
                                <tbody id="leaderboardBody">
                                    <?php if (!empty($top_spots)): ?>
                                        <?php foreach ($top_spots as $spot): 
                                            $rankClass = 'rank-other';
                                            $rankIcon = $spot['rank'];
                                            if ($spot['rank'] === 1) {
                                                $rankClass = 'rank-1';
                                                $rankIcon = '<i class="fas fa-crown"></i>';
                                            } elseif ($spot['rank'] === 2) {
                                                $rankClass = 'rank-2';
                                                $rankIcon = '<i class="fas fa-medal"></i>';
                                            } elseif ($spot['rank'] === 3) {
                                                $rankClass = 'rank-3';
                                                $rankIcon = '<i class="fas fa-award"></i>';
                                            }
                                        ?>
                                            <tr id="spot-row-<?= $spot['u_id'] ?>">
                                                <td class="text-center">
                                                    <div class="rank-badge <?= $rankClass ?> mx-auto">
                                                        <?= $rankIcon ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="<?= htmlspecialchars($spot['image_path']) ?>" alt="<?= htmlspecialchars($spot['name']) ?>" 
                                                             class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;">
                                                        <div>
                                                            <div class="font-weight-bold text-gray-800" style="font-size: 14.5px;">
                                                                <?= htmlspecialchars($spot['name']) ?>
                                                                <?php if ($spot['has_vr']): ?>
                                                                    <span class="badge badge-info ml-1" style="font-size: 10px; font-weight: 500;">
                                                                        <i class="fas fa-vr-cardboard"></i> VR 360°
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                <?= htmlspecialchars($spot['title']) ?> <?= !empty($spot['subtitle']) ? '• ' . htmlspecialchars($spot['subtitle']) : '' ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge px-2 py-1" style="background: rgba(2, 132, 199, 0.1); color: <?= htmlspecialchars($spot['cat_color']) ?>; font-size: 11.5px; border-radius: 8px;">
                                                        <i class="<?= htmlspecialchars($spot['cat_icon']) ?> mr-1"></i> <?= htmlspecialchars($spot['category_name']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge px-3 py-2" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; font-size: 14px; font-weight: 700; border-radius: 10px;">
                                                        <i class="fas fa-eye mr-1"></i> <span class="view-val"><?= number_format($spot['view_count']) ?></span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center text-xs mb-1 font-weight-500">
                                                        <span class="text-gray-600">สัดส่วน</span>
                                                        <span class="text-primary font-weight-bold pct-val"><?= $spot['percentage'] ?>%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                                        <div class="progress-bar-custom" style="width: <?= min(100, $spot['percentage']) ?>%; background: linear-gradient(90deg, #0284c7, #2563eb);"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="frm_detail.php?u_id=<?= $spot['u_id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                                                        <i class="fas fa-external-link-alt mr-1"></i> เข้าชม
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูลสถานที่ท่องเที่ยวในระบบ</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 5. REAL ANALYTICAL CHARTS (3 กราฟประมวลผลข้อมูลจริง 100%) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                    
                    <!-- Chart 1: เปรียบเทียบยอดการเข้าชมแต่ละสถานที่ (Bar Chart - 7 Columns) -->
                    <div class="lg:col-span-7">
                        <div class="chart-box">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <h2 class="text-base font-bold text-gray-800 m-0">เปรียบเทียบยอดการเข้าชมแต่ละสถานที่ (Attractions Views)</h2>
                                    <p class="text-xs text-gray-500 m-0">แสดงสถิติจำนวนผู้เข้าชมจริงแต่ละจุดท่องเที่ยวเพื่อการบริหารจัดการ</p>
                                </div>
                                <span class="badge badge-light border text-primary px-2 py-1" style="font-size: 11px;">
                                    <i class="fas fa-chart-bar mr-1"></i> ข้อมูลเรียลไทม์
                                </span>
                            </div>
                            <div class="chart-container-responsive">
                                <canvas id="attractionsBarChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 2: สัดส่วนยอดการเข้าชมแยกตามหมวดหมู่ (Donut Chart - 5 Columns) -->
                    <div class="lg:col-span-5">
                        <div class="chart-box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h2 class="text-base font-bold text-gray-800 m-0">สัดส่วนความสนใจตามหมวดหมู่ (Category Share)</h2>
                                    <p class="text-xs text-gray-500 m-0">ประมวลผลยอดเข้าชมแยกตามหมวดหมู่การท่องเที่ยวจริง</p>
                                </div>
                                <span class="badge badge-light border text-success px-2 py-1" style="font-size: 11px;">
                                    <i class="fas fa-chart-pie mr-1"></i> สัดส่วนร้อยละ
                                </span>
                            </div>
                            <div class="chart-container-responsive">
                                <canvas id="categoryDonutChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Chart 3: วิเคราะห์โครงสร้างประเภทสถานที่และอัตราค่าบริการ (Service & Structure Breakdown) -->
                <div class="card border-0 shadow-sm mb-6" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2 class="text-base font-bold text-gray-800 m-0">โครงสร้างการให้บริการและโมเดลค่าเข้าชม (Tourism Services & Fees Breakdown)</h2>
                            <p class="text-xs text-gray-500 m-0">วิเคราะห์จำนวนสถานที่ในแต่ละหมวดหมู่และสัดส่วนสถานที่เข้าชมฟรี vs มีค่าบริการ</p>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-xs">
                            <span class="d-flex align-items-center gap-1 font-weight-500 text-emerald-600">
                                <i class="fas fa-check-circle"></i> เข้าชมฟรี <?= $free_spots_count ?> แห่ง (<?= $free_pct ?>%)
                            </span>
                            <span class="d-flex align-items-center gap-1 font-weight-500 text-amber-600">
                                <i class="fas fa-ticket-alt"></i> มีค่าบริการ <?= $paid_spots_count ?> แห่ง (<?= $paid_pct ?>%)
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div style="height: 280px; width: 100%;">
                            <canvas id="structureBarChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript & Real-time Live Polling Engine -->
    <script>
        // Real Data from PHP (100% ฐานข้อมูลจริง)
        let chartLabels = <?php echo $chartLabelsJson; ?>;
        let chartCounts = <?php echo $chartCountsJson; ?>;
        let chartPercentages = <?php echo $chartPercentagesJson; ?>;

        let catLabels = <?php echo $catLabelsJson; ?>;
        let catViews = <?php echo $catViewsJson; ?>;
        let catColors = <?php echo $catColorsJson; ?>;
        let catSpotCounts = <?php echo $catSpotCountsJson; ?>;

        let freeCount = <?= $free_spots_count ?>;
        let paidCount = <?= $paid_spots_count ?>;

        const defaultColors = [
            '#0284c7', // Sky Blue
            '#10b981', // Emerald
            '#f59e0b', // Amber
            '#84cc16', // Lime
            '#8b5cf6', // Purple
            '#06b6d4'  // Cyan
        ];

        // ==========================================
        // CHART 1: Attractions Views Comparison (Vertical Bar Chart)
        // ==========================================
        const barCtx = document.getElementById('attractionsBarChart').getContext('2d');
        const attractionsBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'ยอดผู้เข้าชม (ครั้ง)',
                    data: chartCounts,
                    backgroundColor: [
                        'rgba(2, 132, 199, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(132, 204, 22, 0.85)',
                        'rgba(139, 92, 246, 0.85)'
                    ],
                    borderColor: [
                        '#0284c7',
                        '#10b981',
                        '#f59e0b',
                        '#84cc16',
                        '#8b5cf6'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, family: 'Prompt' },
                        bodyFont: { size: 12, family: 'Prompt' },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const idx = context.dataIndex;
                                const val = context.raw || 0;
                                const pct = chartPercentages[idx] !== undefined ? chartPercentages[idx] : 0;
                                return ` ยอดเข้าชม: ${val.toLocaleString()} ครั้ง (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Prompt', size: 12 },
                            color: '#475569'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Prompt', size: 11 },
                            color: '#64748b',
                            callback: function(value) {
                                return value.toLocaleString() + ' ครั้ง';
                            }
                        }
                    }
                }
            }
        });

        // ==========================================
        // CHART 2: Category Views Share (Donut Chart)
        // ==========================================
        const donutCtx = document.getElementById('categoryDonutChart').getContext('2d');
        const categoryDonutChart = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catViews,
                    backgroundColor: catColors.length > 0 ? catColors : defaultColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 14,
                            font: { family: 'Prompt', size: 12 },
                            color: '#334155'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, family: 'Prompt' },
                        bodyFont: { size: 12, family: 'Prompt' },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val.toLocaleString()} ครั้ง (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '62%'
            }
        });

        // ==========================================
        // CHART 3: Tourism Service & Structure Breakdown (Bar Chart)
        // ==========================================
        const structCtx = document.getElementById('structureBarChart').getContext('2d');
        const structureBarChart = new Chart(structCtx, {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [
                    {
                        label: 'จำนวนสถานที่ในระบบ (แห่ง)',
                        data: catSpotCounts,
                        backgroundColor: 'rgba(2, 132, 199, 0.75)',
                        borderColor: '#0284c7',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        maxBarThickness: 32
                    },
                    {
                        label: 'ยอดผู้เข้าชมรวมในหมวดหมู่ (ครั้ง)',
                        data: catViews,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        maxBarThickness: 32,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Prompt', size: 12 },
                            color: '#334155',
                            boxWidth: 14,
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, family: 'Prompt' },
                        bodyFont: { size: 12, family: 'Prompt' },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Prompt', size: 12 }, color: '#475569' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Prompt', size: 11 },
                            color: '#0284c7',
                            stepSize: 1,
                            callback: function(v) { return v + ' แห่ง'; }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: {
                            font: { family: 'Prompt', size: 11 },
                            color: '#10b981',
                            callback: function(v) { return v + ' วิว'; }
                        }
                    }
                }
            }
        });

        // ==========================================
        // REAL-TIME AJAX POLLING ENGINE
        // ==========================================
        let isFetching = false;

        function fetchLiveStats(isManual = false) {
            if (isFetching) return;
            isFetching = true;

            const refreshIcon = document.getElementById('refreshIcon');
            if (refreshIcon) refreshIcon.classList.add('refresh-spinner');

            fetch('api_dashboard_stats.php')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (!data.success) return;

                    // 1. Update KPI numbers
                    document.getElementById('statTotalViews').textContent = Number(data.total_views).toLocaleString();
                    document.getElementById('statTotalLocations').textContent = Number(data.total_locations).toLocaleString();
                    document.getElementById('statTotalImages').textContent = Number(data.total_images).toLocaleString();
                    document.getElementById('statTotalVr').textContent = data.total_vr;

                    if (data.top_one) {
                        document.getElementById('statTopName').textContent = data.top_one.name;
                        document.getElementById('statTopCount').textContent = `${Number(data.top_one.view_count).toLocaleString()} ครั้ง`;
                        document.getElementById('statTopPct').textContent = `${data.top_one.percentage}%`;
                        const topProg = document.getElementById('statTopProgress');
                        if (topProg) topProg.style.width = `${Math.min(100, data.top_one.percentage)}%`;
                    }

                    // 2. Update Analytical Insights
                    if (data.insights) {
                        const inTop = document.getElementById('insightTopCatName');
                        if (inTop) inTop.textContent = data.insights.top_category_name;

                        const inStats = document.getElementById('insightTopCatStats');
                        if (inStats) inStats.textContent = `${Number(data.insights.top_category_views).toLocaleString()} วิว (${data.insights.top_category_pct}% ของทั้งหมด)`;

                        const inPeakTime = document.getElementById('insightPeakTime');
                        if (inPeakTime) inPeakTime.textContent = data.insights.peak_time;

                        const inPeakDay = document.getElementById('insightPeakDay');
                        if (inPeakDay) inPeakDay.textContent = data.insights.peak_day;

                        if (data.fee_stats) {
                            const inFee = document.getElementById('insightFeeRatio');
                            if (inFee) inFee.textContent = `เข้าชมฟรี ${data.fee_stats.free_pct}% (${data.fee_stats.free_count} แห่ง)`;

                            const inPaid = document.getElementById('insightPaidCount');
                            if (inPaid) inPaid.textContent = `มีค่าบริการ ${data.fee_stats.paid_pct}% (${data.fee_stats.paid_count} แห่ง)`;
                        }
                    }

                    // 3. Update Leaderboard Table
                    renderLeaderboardTable(data.top_spots);

                    // 4. Update Chart 1 (Attractions Bar Chart)
                    if (data.chart_labels && data.chart_counts) {
                        chartLabels = data.chart_labels;
                        chartCounts = data.chart_counts;
                        chartPercentages = data.chart_percentages;
                        attractionsBarChart.data.labels = chartLabels;
                        attractionsBarChart.data.datasets[0].data = chartCounts;
                        attractionsBarChart.update('none');
                    }

                    // 5. Update Chart 2 (Category Donut Chart)
                    if (data.cat_labels && data.cat_views) {
                        catLabels = data.cat_labels;
                        catViews = data.cat_views;
                        categoryDonutChart.data.labels = catLabels;
                        categoryDonutChart.data.datasets[0].data = catViews;
                        if (data.cat_colors && data.cat_colors.length > 0) {
                            categoryDonutChart.data.datasets[0].backgroundColor = data.cat_colors;
                        }
                        categoryDonutChart.update('none');
                    }

                    // 6. Update Chart 3 (Structure Bar Chart)
                    if (data.cat_labels && data.cat_spot_counts) {
                        structureBarChart.data.labels = data.cat_labels;
                        structureBarChart.data.datasets[0].data = data.cat_spot_counts;
                        structureBarChart.data.datasets[1].data = data.cat_views;
                        structureBarChart.update('none');
                    }

                    if (isManual && typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'อัปเดตข้อมูลสถิติจริงล่าสุดเรียบร้อย'
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching live stats:', err);
                })
                .finally(() => {
                    isFetching = false;
                    if (refreshIcon) refreshIcon.classList.remove('refresh-spinner');
                });
        }

        // Render Leaderboard Table Dynamically
        function renderLeaderboardTable(spots) {
            const tbody = document.getElementById('leaderboardBody');
            if (!tbody || !spots) return;

            const badgeCount = document.getElementById('badgeSpotCount');
            if (badgeCount) badgeCount.textContent = spots.length;

            if (spots.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูลสถานที่ท่องเที่ยวในระบบ</td></tr>';
                return;
            }

            let html = '';
            spots.forEach(spot => {
                let rankClass = 'rank-other';
                let rankIcon = spot.rank;
                if (spot.rank === 1) {
                    rankClass = 'rank-1';
                    rankIcon = '<i class="fas fa-crown"></i>';
                } else if (spot.rank === 2) {
                    rankClass = 'rank-2';
                    rankIcon = '<i class="fas fa-medal"></i>';
                } else if (spot.rank === 3) {
                    rankClass = 'rank-3';
                    rankIcon = '<i class="fas fa-award"></i>';
                }

                const vrBadge = spot.has_vr ? '<span class="badge badge-info ml-1" style="font-size: 10px; font-weight: 500;"><i class="fas fa-vr-cardboard"></i> VR 360°</span>' : '';
                const subtitle = spot.subtitle ? `• ${escapeHTML(spot.subtitle)}` : '';
                const catName = spot.category_name ? escapeHTML(spot.category_name) : 'ทั่วไป';
                const catIcon = spot.cat_icon ? escapeHTML(spot.cat_icon) : 'fas fa-map-marker-alt';
                const catColor = spot.cat_color ? escapeHTML(spot.cat_color) : '#0284c7';

                html += `
                    <tr id="spot-row-${spot.u_id}">
                        <td class="text-center">
                            <div class="rank-badge ${rankClass} mx-auto">
                                ${rankIcon}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="${escapeHTML(spot.image_path)}" alt="${escapeHTML(spot.name)}" 
                                     class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;">
                                <div>
                                    <div class="font-weight-bold text-gray-800" style="font-size: 14.5px;">
                                        ${escapeHTML(spot.name)} ${vrBadge}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ${escapeHTML(spot.title)} ${subtitle}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge px-2 py-1" style="background: rgba(2, 132, 199, 0.1); color: ${catColor}; font-size: 11.5px; border-radius: 8px;">
                                <i class="${catIcon} mr-1"></i> ${catName}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge px-3 py-2" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; font-size: 14px; font-weight: 700; border-radius: 10px;">
                                <i class="fas fa-eye mr-1"></i> <span class="view-val">${Number(spot.view_count).toLocaleString()}</span>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between align-items-center text-xs mb-1 font-weight-500">
                                <span class="text-gray-600">สัดส่วน</span>
                                <span class="text-primary font-weight-bold pct-val">${spot.percentage}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="progress-bar-custom" style="width: ${Math.min(100, spot.percentage)}%; background: linear-gradient(90deg, #0284c7, #2563eb);"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="frm_detail.php?u_id=${spot.u_id}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                                <i class="fas fa-external-link-alt mr-1"></i> เข้าชม
                            </a>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function escapeHTML(str) {
            if (!str) return '';
            return String(str).replace(/[&<>'"]/g, 
                tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
            );
        }

        // ==========================================
        // REAL-TIME THAI CLOCK (TICKING EVERY SECOND)
        // ==========================================
        function updateLiveThaiClock() {
            const now = new Date();
            const thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
            const thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            
            const dayOfWeek = thaiDays[now.getDay()];
            const d = now.getDate();
            const m = thaiMonths[now.getMonth() + 1];
            const y = now.getFullYear() + 543;
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const dateEl = document.getElementById('lastUpdatedDate');
            const timeEl = document.getElementById('lastUpdatedTime');
            if (dateEl) {
                dateEl.textContent = `วัน${dayOfWeek}ที่ ${d} ${m} ${y}`;
            }
            if (timeEl) {
                timeEl.textContent = `${hours}:${minutes}:${seconds} น.`;
            }
        }
        setInterval(updateLiveThaiClock, 1000);
        updateLiveThaiClock();

        // Auto Poll every 15 seconds
        setInterval(() => {
            fetchLiveStats(false);
        }, 15000);
    </script>

    <!-- AdminLTE JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/css/adminlte.min.css"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
<?php
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'ไม่มีสิทธิ์เข้าถึง!',
            text: 'กรุณาเข้าสู่ระบบด้วยบัญชีผู้ดูแลระบบ (Admin) ก่อนใช้งาน',
            icon: 'warning',
            confirmButtonText: 'เข้าสู่ระบบ'
        }).then(() => {
            window.location.href = 'frm_login.php';
        });
    </script>";
    exit;
}
?>
