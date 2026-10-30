<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 1. ยอดผู้เข้าชมรวมทั้งหมด และ จำนวนสถานที่ทั้งหมด
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

// 4. อันดับสถานที่ท่องเที่ยวที่มีผู้เข้าชมมากที่สุด (เรียงจากมากไปน้อย - ข้อมูลจริง)
$spotsRes = $conn->query("
    SELECT u.u_id, u.image_name, u.title, u.subtitle, u.image_path, u.view_count, u.vr_link, u.created_at,
           c.category_name, c.color AS cat_color, c.icon AS cat_icon
    FROM uploads u
    LEFT JOIN categories c ON u.c_id = c.c_id
    ORDER BY u.view_count DESC
");
$top_spots = [];
$chart_labels = [];
$chart_counts = [];
$chart_percentages = [];
$rank = 1;
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
        $chart_labels[] = $row['image_name'];
        $chart_counts[] = (int)$row['view_count'];
        $chart_percentages[] = $pct;
    }
}
$top_one = !empty($top_spots) ? $top_spots[0] : null;

// 5. สถิติเชิงวิเคราะห์แยกตามหมวดหมู่จริง (Category Analytics - 100% Real Data)
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
$cat_labels = [];
$cat_views = [];
$cat_colors = [];
$cat_spot_counts = [];
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
        $cat_labels[] = $crow['category_name'];
        $cat_views[] = (int)$crow['cat_views'];
        $cat_colors[] = $crow['color'] ?: '#0284c7';
        $cat_spot_counts[] = (int)$crow['spot_count'];
    }
}

// 6. โครงสร้างค่าบริการจริง (Free vs Paid Attractions)
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
$fee_stats = [
    'free_count' => $free_spots_count,
    'paid_count' => $paid_spots_count,
    'free_pct' => $total_locations > 0 ? round(($free_spots_count / $total_locations) * 100, 1) : 0,
    'paid_pct' => $total_locations > 0 ? round(($paid_spots_count / $total_locations) * 100, 1) : 0
];

// 7. ช่วงเวลาและวันยอดนิยมจากฐานข้อมูลจริง
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
$peak_day = ($peak_row && !empty($peak_row['day_period'])) ? $peak_row['day_period'] : 'วันเสาร์-อาทิตย์ / ทุกวัน';

// ข้อมูลเชิงวิเคราะห์ (Insights)
$top_cat = !empty($categories_stats) ? $categories_stats[0] : null;
$insights = [
    'top_category_name' => $top_cat ? $top_cat['name'] : 'ทั่วไป',
    'top_category_views' => $top_cat ? $top_cat['views'] : 0,
    'top_category_pct' => $top_cat ? $top_cat['percentage'] : 0,
    'peak_time' => $peak_time,
    'peak_day' => $peak_day,
    'free_spots_ratio' => "{$free_spots_count}/{$total_locations} แห่ง ({$fee_stats['free_pct']}%)",
    'vr_readiness_pct' => $total_locations > 0 ? round(($total_vr / $total_locations) * 100, 1) : 0
];

echo json_encode([
    'success' => true,
    'timestamp' => date('H:i:s น.'),
    'date' => date('d/m/Y'),
    'date_thai' => thai_date_format(time(), false),
    'datetime_thai' => thai_date_format(time(), true),
    'total_views' => $total_views,
    'total_locations' => $total_locations,
    'total_images' => $total_images,
    'total_vr' => $total_vr,
    'top_one' => $top_one,
    'top_spots' => $top_spots,
    'chart_labels' => $chart_labels,
    'chart_counts' => $chart_counts,
    'chart_percentages' => $chart_percentages,
    'categories_stats' => $categories_stats,
    'cat_labels' => $cat_labels,
    'cat_views' => $cat_views,
    'cat_colors' => $cat_colors,
    'cat_spot_counts' => $cat_spot_counts,
    'fee_stats' => $fee_stats,
    'insights' => $insights
], JSON_UNESCAPED_UNICODE);
