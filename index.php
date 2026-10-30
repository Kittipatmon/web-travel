<?php
session_start();
include "connect.php";

// ดึงรายการข้อมูลทั้งหมดสำหรับโปสเตอร์สไลเดอร์แบนเนอร์ด้านบน
$banner_stmt = $conn->query("SELECT * FROM uploads ORDER BY created_at DESC");
$slider_uploads = $banner_stmt ? $banner_stmt->fetch_all(MYSQLI_ASSOC) : [];

// ดึงข้อมูลโปสเตอร์โปรโมทที่เปิดใช้งาน
$poster_stmt = $conn->query("SELECT * FROM posters WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC");
$promo_posters = $poster_stmt ? $poster_stmt->fetch_all(MYSQLI_ASSOC) : [];

// รับค่าการค้นหาและหมวดหมู่สำหรับส่วนด้านล่าง
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';

// การแบ่งหน้า (Pagination)
$per_page = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $per_page;

// สร้างเงื่อนไข Query
$where_clauses = [];
$params = [];
$types = "";

if ($search !== '') {
    $where_clauses[] = "(image_name LIKE ? OR subtitle LIKE ? OR content LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($cat !== '' && $cat !== 'all') {
    if ($cat === 'vr') {
        $where_clauses[] = "(vr_link IS NOT NULL AND vr_link != '')";
    } elseif ($cat === 'ที่พัก') {
        $where_clauses[] = "(subtitle LIKE '%โฮมสเตย์%' OR subtitle LIKE '%พัก%' OR content LIKE '%โฮมสเตย์%' OR content LIKE '%ที่พัก%')";
    } elseif ($cat === 'เทศกาล') {
        $where_clauses[] = "(content LIKE '%เทศกาล%' OR subtitle LIKE '%เทศกาล%' OR content LIKE '%กิจกรรม%' OR content LIKE '%ชม%')";
    } elseif ($cat === 'เปิดใหม่') {
        // รายการล่าสุด ไม่ต้องกรองเพิ่ม ให้ดึงตามลำดับ created_at DESC
    } elseif ($cat === 'แหล่งเรียนรู้' || $cat === 'ร้านอาหารแนะนำ' || $cat === 'ร้านอาหาร') {
        $where_clauses[] = "(subtitle LIKE '%เรียนรู้%' OR content LIKE '%เรียนรู้%' OR subtitle LIKE '%ฟาร์ม%' OR content LIKE '%ฟาร์ม%')";
    } else {
        $where_clauses[] = "(subtitle LIKE ? OR title LIKE ? OR image_name LIKE ?)";
        $cat_param = "%" . $cat . "%";
        $params[] = $cat_param;
        $params[] = $cat_param;
        $params[] = $cat_param;
        $types .= "sss";
    }
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// นับจำนวนรายการทั้งหมด
$count_sql = "SELECT COUNT(*) AS total FROM uploads $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_result = $count_stmt->get_result()->fetch_assoc();
$total = $total_result['total'];
$total_pages = ceil($total / $per_page);

// ดึงข้อมูลสถานที่ท่องเที่ยวสำหรับการ์ดด้านล่าง
$data_sql = "SELECT * FROM uploads $where_sql ORDER BY created_at DESC LIMIT ?, ?";
$data_stmt = $conn->prepare($data_sql);
$pagination_types = $types . "ii";
$pagination_params = array_merge($params, [$start, $per_page]);
$data_stmt->bind_param($pagination_types, ...$pagination_params);
$data_stmt->execute();
$uploads = $data_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ฟังก์ชันสำหรับ Render การ์ดสถานที่ท่องเที่ยวและการแบ่งหน้า
function renderAttractionsGrid($uploads, $cat, $page, $total_pages, $search = '') {
    ?>
    <div class="travel-3col-grid">
        <?php if (!empty($uploads)): ?>
            <?php foreach ($uploads as $upload): ?>
                <div class="travel-item-card" id="card-<?= htmlspecialchars($upload['u_id']) ?>">
                    <div class="item-thumb-box">
                        <img src="<?= htmlspecialchars($upload['image_path']) ?>" alt="<?= htmlspecialchars($upload['image_name']) ?>" loading="lazy">
                        
                        <!-- Photo Title Overlay as seen in reference image -->
                        <div class="thumb-title-overlay">
                            <h4><?= htmlspecialchars($upload['image_name']) ?></h4>
                        </div>

                        <?php if (!empty($upload['vr_link'])): ?>
                            <a href="<?= htmlspecialchars($upload['vr_link']) ?>" class="item-badge-vr vr-link" data-id="<?= $upload['u_id'] ?>" target="_blank" title="รับชม VR 360°">
                                <i class="fas fa-vr-cardboard"></i> VR
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="item-body">
                        <h3 class="item-title"><?= htmlspecialchars($upload['image_name']) ?>: <?= htmlspecialchars($upload['subtitle']) ?></h3>

                        <p class="item-desc">
                            <?= htmlspecialchars(mb_substr($upload['content'], 0, 75, 'UTF-8')) ?>...
                        </p>

                        <div class="item-footer">
                            <div class="item-views">
                                <?= date('F d, Y', strtotime($upload['created_at'] ?? 'now')) ?>
                            </div>

                            <div class="item-actions">
                                <a href="frm_detail.php?u_id=<?= htmlspecialchars($upload['u_id']) ?>" class="btn-card-view">
                                    <i class="far fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search-location fa-3x text-muted mb-3"></i>
                <h4 class="text-secondary">ไม่พบสถานที่ท่องเที่ยวที่ตรงกับการค้นหา</h4>
                <p class="text-muted">ลองเลือกหมวดหมู่อื่น หรือกดดูสถานที่ทั้งหมด</p>
                <a href="javascript:void(0)" onclick="filterCategory('all', 1, '')" class="btn btn-warning mt-2 text-white" style="border-radius: 50px; padding: 7px 24px; background: #ff991b;">
                    ดูทั้งหมด
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination (เมื่อมีมากกว่า 1 หน้า) -->
    <?php if ($total_pages > 1): ?>
        <div class="portal-pagination">
            <?php if ($page > 1): ?>
                <a href="javascript:void(0)" onclick="filterCategory('<?= htmlspecialchars($cat) ?>', <?= $page - 1 ?>, '<?= htmlspecialchars(addslashes($search)) ?>')">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="javascript:void(0)" onclick="filterCategory('<?= htmlspecialchars($cat) ?>', <?= $i ?>, '<?= htmlspecialchars(addslashes($search)) ?>')"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="javascript:void(0)" onclick="filterCategory('<?= htmlspecialchars($cat) ?>', <?= $page + 1 ?>, '<?= htmlspecialchars(addslashes($search)) ?>')">&raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php
}

// ถ้าเป็นการร้องขอแบบ AJAX ให้ส่งเฉพาะ HTML ในกรอบการ์ดแล้วจบการทำงานทันที (ไม่รีเฟรชทั้งหน้า ไม่เลื่อนจอ)
if (isset($_GET['ajax'])) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    renderAttractionsGrid($uploads, $cat, $page, $total_pages, $search);
    exit;
}

// ดึง 1 สถานที่เด่นสำหรับ Spotlight Section
$spotlight_stmt = $conn->query("SELECT * FROM uploads ORDER BY created_at DESC");
$spotlight_list = $spotlight_stmt ? $spotlight_stmt->fetch_all(MYSQLI_ASSOC) : [];

// ดึงรายการสถานที่ทั้งหมดสำหรับแสดงเป็นการ์ดภาพด้านล่าง
$all_spots_stmt = $conn->query("SELECT * FROM uploads ORDER BY u_id ASC LIMIT 4");
$mini_spots = $all_spots_stmt ? $all_spots_stmt->fetch_all(MYSQLI_ASSOC) : [];

// ดึงยอดผู้เข้าชมรวมสำหรับแสดงผลที่ Footer
$total_views_stmt = $conn->query("SELECT SUM(view_count) AS total_views FROM uploads");
$total_views_row = $total_views_stmt ? $total_views_stmt->fetch_assoc() : null;
$total_views_count = $total_views_row && $total_views_row['total_views'] ? (int)$total_views_row['total_views'] : 28;

// ดึงการตั้งค่า Footer จากฐานข้อมูล site_settings
$footer_settings = [];
$settings_stmt = $conn->query("SELECT setting_key, setting_value FROM site_settings");
if ($settings_stmt) {
    while ($s_row = $settings_stmt->fetch_assoc()) {
        $footer_settings[$s_row['setting_key']] = $s_row['setting_value'];
    }
}
$site_footer_copyright = $footer_settings['footer_copyright'] ?? '© 2569 Virtual Vista PCRU. All Rights Reserved.';
$site_footer_brand = $footer_settings['footer_brand_title'] ?? 'VIRTUAL VISTA';
$site_footer_sub = $footer_settings['footer_brand_subtitle'] ?? 'PCRU.AC.TH / NAPA TRAVEL';
$site_footer_desc = $footer_settings['footer_desc'] ?? 'เว็บท่องเที่ยว Virtual Vista PCRU รวมข้อมูลข่าวสารสถานที่ท่องเที่ยวในตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ สัมผัสธรรมชาติ จุดชมวิว ลานกางเต็นท์ และระบบท่องเที่ยวเสมือนจริง 360° จัดทริปท่องเที่ยวง่ายๆ ผ่านเว็บไซต์ของเรา';
$site_footer_fb = $footer_settings['footer_fb_url'] ?? 'https://www.facebook.com';
$site_footer_tw = $footer_settings['footer_tw_url'] ?? 'https://twitter.com';
$site_footer_yt = $footer_settings['footer_yt_url'] ?? 'https://youtube.com';
$site_footer_mail = $footer_settings['footer_email_url'] ?? 'contact.php';

// บันทึกการเข้าชมผ่านพารามิเตอร์ u_id
if (isset($_GET['u_id'])) {
    $u_id = (int)$_GET['u_id'];
    if (!isset($_SESSION['view_link' . $u_id])) {
        $query = "UPDATE uploads SET view_count = view_count + 1 WHERE u_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $u_id);
        $stmt->execute();
        $_SESSION['view_link' . $u_id] = true;
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Virtual Vista - แหล่งท่องเที่ยวตำบลนาป่า เพชรบูรณ์</title>

    <!-- Google Fonts: Prompt & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/travel.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Modern Styles for Content below Banner -->
    <link rel="stylesheet" href="assets/css/modern-travel.css">
</head>

<body>

    <!-- Preloader -->
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <!-- 1. NAVIGATION (Original Navigation - ไม่ปรับเปลี่ยน) -->
    <?php include "header(index).php"; ?>

    <!-- 2. MAIN BANNER SLIDER (โปสเตอร์เดิม - ไม่ปรับเปลี่ยน) -->
    <section id="section-1">
      <div class="content-slider">
        <?php foreach ($slider_uploads as $index => $upload): ?>
          <input type="radio" id="banner<?= $index + 1 ?>" class="sec-1-input" name="banner" <?= $index === 0 ? 'checked' : '' ?>>
        <?php endforeach; ?>
        <div class="slider">
          <?php foreach ($slider_uploads as $index => $upload): ?>
            <div id="top-banner-<?= $index + 1 ?>" class="banner" style="background: url('<?= htmlspecialchars($upload['image_path']) ?>') no-repeat center center; background-size: cover;">
              <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5);"></div>
              <div class="banner-inner-wrapper header-text" style="position: relative; z-index: 1;">
                <div class="main-caption">
                  <h2><?= htmlspecialchars($upload['subtitle']) ?>:</h2>
                  <h1><?= htmlspecialchars($upload['image_name']) ?></h1>
                  <div class="border-button">
                    <a href="<?= htmlspecialchars($upload['vr_link']) ?>" target="_blank">รับชม VR</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <nav>
          <div class="controls">
            <?php foreach ($slider_uploads as $index => $upload): ?>
              <label for="banner<?= $index + 1 ?>">
                <span class="progressbar">
                  <span class="progressbar-fill"></span>
                </span>
                <span class="text"><?= $index + 1 ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </nav>
      </div>
    </section>
    <!-- ***** Main Banner Area End ***** -->

    <!-- 3. SEARCH & CATEGORY ICONS (ลักษณะตามรูป ที่เที่ยวน่าสนใจ ที่เพชรบูรณ์) -->
    <section class="search-filter-section">
        <div class="container">
            <h2 class="search-title">
                ที่เที่ยวน่าสนใจ ที่เพชรบูรณ์
            </h2>

            <!-- Search Bar Form (Pill Shape with Dropdown & Search Button) -->
            <form action="index.php#attractions-section" method="GET" class="search-pill-container">
                <input type="text" name="search" placeholder="ชื่อสถานที่ท่องเที่ยว . . ." value="<?= htmlspecialchars($search) ?>">
                
                <div class="search-dropdown-box">
                    <select name="cat">
                        <option value="all" <?= ($cat == '' || $cat == 'all') ? 'selected' : '' ?>>ทั้งหมด</option>
                        <option value="จุดชมวิว" <?= $cat == 'จุดชมวิว' ? 'selected' : '' ?>>จุดชมวิว</option>
                        <option value="จุดกางเต็นท์" <?= $cat == 'จุดกางเต็นท์' ? 'selected' : '' ?>>กางเต็นท์</option>
                        <option value="ที่พัก" <?= $cat == 'ที่พัก' ? 'selected' : '' ?>>ที่พัก/โฮมสเตย์</option>
                        <option value="แหล่งเรียนรู้" <?= $cat == 'แหล่งเรียนรู้' ? 'selected' : '' ?>>ฟาร์มสเตย์</option>
                        <option value="vr" <?= $cat == 'vr' ? 'selected' : '' ?>>VR 360°</option>
                    </select>
                    <i class="fas fa-chevron-down chevron-icon"></i>
                </div>

                <button type="submit" class="search-pill-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>

            <!-- Dotted Divider Line (เส้นประคั่นกลางตามรูป) -->
            <div class="search-dotted-divider"></div>

            <!-- Category Icons Row (6 หมวดหมู่ตามรูปภาพเป๊ะๆ) -->
            <div class="category-squircle-row">
                <!-- 1. ที่เที่ยว (Dark Blue) -->
                <a href="javascript:void(0)" onclick="filterCategory('all')" data-cat="all" class="cat-squircle-item <?= ($cat == '' || $cat == 'all') ? 'active' : '' ?>">
                    <div class="cat-squircle-box sq-blue">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <span class="cat-label-text">ที่เที่ยว</span>
                </a>

                <!-- 2. เทศกาล (Red) -->
                <a href="javascript:void(0)" onclick="filterCategory('เทศกาล')" data-cat="เทศกาล" class="cat-squircle-item <?= $cat == 'เทศกาล' ? 'active' : '' ?>">
                    <div class="cat-squircle-box sq-red">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <span class="cat-label-text">เทศกาล</span>
                </a>

                <!-- 3. เปิดใหม่ (Purple) -->
                <a href="javascript:void(0)" onclick="filterCategory('เปิดใหม่')" data-cat="เปิดใหม่" class="cat-squircle-item <?= $cat == 'เปิดใหม่' ? 'active' : '' ?>">
                    <div class="cat-squircle-box sq-purple">
                        <i class="fas fa-bell"></i>
                    </div>
                    <span class="cat-label-text">เปิดใหม่</span>
                </a>

                <!-- 4. กางเต็นท์ (Green) -->
                <a href="javascript:void(0)" onclick="filterCategory('จุดกางเต็นท์')" data-cat="จุดกางเต็นท์" class="cat-squircle-item <?= $cat == 'จุดกางเต็นท์' ? 'active' : '' ?>">
                    <div class="cat-squircle-box sq-green">
                        <i class="fas fa-campground"></i>
                    </div>
                    <span class="cat-label-text">กางเต็นท์</span>
                </a>

                <!-- 5. ที่พัก (Yellow) -->
                <a href="javascript:void(0)" onclick="filterCategory('ที่พัก')" data-cat="ที่พัก" class="cat-squircle-item <?= $cat == 'ที่พัก' ? 'active' : '' ?>">
                    <div class="cat-squircle-box sq-yellow">
                        <i class="fas fa-bed"></i>
                    </div>
                    <span class="cat-label-text">ที่พัก</span>
                </a>

                <!-- 6. ทริปเที่ยว (Orange) - ซ่อนไว้ก่อนชั่วคราว
                <a href="travel_route.php" class="cat-squircle-item">
                    <div class="cat-squircle-box sq-orange">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <span class="cat-label-text">ทริปเที่ยว</span>
                </a>
                -->
            </div>
        </div>
    </section>

    <!-- 4. ATTRACTIONS GRID (การ์ดสถานที่ท่องเที่ยว 3 คอลัมน์) -->
    <section class="attractions-portal-section" id="attractions-section">
        <div class="container">
            <div class="portal-white-container">
                
                <!-- Section Header Bar -->
                <div class="portal-header-bar">
                    <div class="portal-title-left">
                        <div class="icon-circle">
                            <i class="fas fa-umbrella-beach"></i>
                        </div>
                        <div>
                            <p class="sub">TRAVEL AND ACTIVITY</p>
                            <h2>ท่องเที่ยวและกิจกรรม</h2>
                        </div>
                    </div>

                    <!-- Category Menu Links on Top Right (คลิกแล้วรีเฟรชเฉพาะในกรอบ ไม่โหลดทั้งหน้า) -->
                    <div class="portal-menu-links">
                        <a href="javascript:void(0)" onclick="filterCategory('all')" data-cat="all" class="<?= ($cat == '' || $cat == 'all') ? 'active' : '' ?>">เที่ยว</a>
                        <a href="javascript:void(0)" onclick="filterCategory('จุดชมวิว')" data-cat="จุดชมวิว" class="<?= $cat == 'จุดชมวิว' ? 'active' : '' ?>">ท่องเที่ยว</a>
                        <a href="javascript:void(0)" onclick="filterCategory('vr')" data-cat="vr" class="<?= $cat == 'vr' ? 'active' : '' ?>">ทัวร์และกิจกรรม</a>
                        <a href="javascript:void(0)" onclick="filterCategory('ที่พัก')" data-cat="ที่พัก" class="<?= $cat == 'ที่พัก' ? 'active' : '' ?>">ที่พักHotel</a>
                        <a href="javascript:void(0)" onclick="filterCategory('แหล่งเรียนรู้')" data-cat="แหล่งเรียนรู้" class="<?= $cat == 'แหล่งเรียนรู้' ? 'active' : '' ?>">ร้านอาหารแนะนำ</a>
                        <!-- <a href="travel_route.php">ไฮไลท์</a> -->
                    </div>
                </div>

                <!-- Attractions Grid Container (รีเฉพาะในกรอบนี้แบบ Smooth AJAX) -->
                <div id="attractionsGridContainer">
                    <?php renderAttractionsGrid($uploads, $cat, $page, $total_pages, $search); ?>
                </div>

                <!-- Bottom Centered "ดูทั้งหมด" Pill Button (ไปยังหน้าดูทั้งหมดที่รวมทุกอย่าง) -->
                <div class="portal-bottom-btn-box">
                    <a href="all_destinations.php" class="btn-portal-view-all">ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i></a>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. PROMOTIONAL TRAVEL POSTER BANNER (โปสเตอร์โปรโมทการท่องเที่ยวจังหวัดเพชรบูรณ์) -->
    <?php if (!empty($promo_posters)): ?>
    <div class="container" id="promo-section">
        <div class="promo-poster-slider-wrapper">
            <div class="promo-poster-carousel" id="promoPosterCarousel">
                <?php foreach ($promo_posters as $p_idx => $poster): 
                    $p_link = !empty($poster['link_url']) ? htmlspecialchars($poster['link_url']) : 'all_destinations.php';
                ?>
                    <div class="promo-poster-slide <?= $p_idx === 0 ? 'active' : '' ?>" data-slide="<?= $p_idx ?>">
                        <img src="<?= htmlspecialchars($poster['image_path']) ?>" alt="<?= htmlspecialchars($poster['title']) ?>" class="poster-graphic-image" onerror="this.src='assets/images/default.jpg'">
                        <div class="poster-hotspots-overlay">
                            <a href="<?= $p_link ?>" class="hotspot-link-full" title="<?= htmlspecialchars($poster['title']) ?>"></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Dots & Navigation Controls for Posters if more than 1 -->
            <?php if (count($promo_posters) > 1): ?>
                <div class="promo-poster-controls d-flex justify-content-center align-items-center gap-3 mt-3">
                    <button type="button" class="btn btn-sm btn-light border rounded-circle shadow-xs" onclick="prevPosterSlide()" style="width: 32px; height: 32px;">
                        <i class="fas fa-chevron-left text-muted"></i>
                    </button>
                    <div class="promo-poster-dots d-flex gap-2">
                        <?php foreach ($promo_posters as $p_idx => $poster): ?>
                            <span class="poster-dot <?= $p_idx === 0 ? 'active' : '' ?>" onclick="goToPosterSlide(<?= $p_idx ?>)" style="cursor: pointer; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: <?= $p_idx === 0 ? '#ff991b' : '#cbd5e1' ?>; transition: all 0.3s ease;"></span>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border rounded-circle shadow-xs" onclick="nextPosterSlide()" style="width: 32px; height: 32px;">
                        <i class="fas fa-chevron-right text-muted"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 6. FEATURED SPOTLIGHT SLIDER (อันล่างเลื่อนได้ ตามรูปภาพตัวอย่าง) -->
    <?php if (!empty($spotlight_list)): ?>
    <section class="spotlight-slider-section">
        <div class="container">
            <div class="spotlight-carousel-card" id="spotlightCarousel">
                
                <?php foreach ($spotlight_list as $s_index => $s_item): ?>
                    <div class="spotlight-slide-item <?= $s_index === 0 ? 'active' : '' ?>" data-index="<?= $s_index ?>">
                        <!-- Left Media Box with Slide Chevrons -->
                        <div class="spotlight-media-col">
                            <img src="<?= htmlspecialchars($s_item['image_path']) ?>" alt="<?= htmlspecialchars($s_item['image_name']) ?>">
                            <div class="media-overlay">
                                <h3><?= htmlspecialchars($s_item['image_name']) ?></h3>
                                <p><?= htmlspecialchars($s_item['subtitle']) ?> - ตำบลนาป่า จ.เพชรบูรณ์</p>
                            </div>

                            <!-- Left / Right Slide Arrow Buttons -->
                            <button type="button" class="spot-nav-arrow spot-nav-prev" onclick="prevSpotlightSlide()" title="ก่อนหน้า">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="spot-nav-arrow spot-nav-next" onclick="nextSpotlightSlide()" title="ถัดไป">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Right Details Box with Dashed Line & Blue Button -->
                        <div class="spotlight-info-col">
                            <h3 class="info-title">
                                <?= htmlspecialchars($s_item['image_name']) ?> | <?= htmlspecialchars($s_item['subtitle']) ?>
                            </h3>

                            <!-- Dashed Divider Line as in reference image -->
                            <div class="spotlight-dashed-line"></div>

                            <p class="info-content">
                                <?= htmlspecialchars($s_item['content']) ?>
                            </p>

                            <div class="spotlight-actions-row">
                                <a href="frm_detail.php?u_id=<?= htmlspecialchars($s_item['u_id']) ?>" class="btn-spotlight-blue">
                                    ดูรายละเอียด
                                </a>
                                <?php if (!empty($s_item['vr_link'])): ?>
                                    <a href="<?= htmlspecialchars($s_item['vr_link']) ?>" class="btn-spotlight-vr vr-link" data-id="<?= $s_item['u_id'] ?>" target="_blank">
                                        <i class="fas fa-vr-cardboard"></i> ชม VR
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <!-- Slide Indicator Dots -->
            <?php if (count($spotlight_list) > 1): ?>
                <div class="spotlight-dots-row">
                    <?php foreach ($spotlight_list as $d_idx => $d_item): ?>
                        <div class="spotlight-dot <?= $d_idx === 0 ? 'active' : '' ?>" onclick="goToSpotlightSlide(<?= $d_idx ?>)"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

    <!-- 7. PHOTO MINI CARDS (การ์ดรูปภาพจุดเช็คอินแนะนำ 4 ช่อง) -->
    <?php if (!empty($mini_spots)): ?>
    <section class="spotlight-grid-4cards">
        <div class="container">
            <div class="section-clean-heading">
                <h3>จุดเช็คอินแนะนำในตำบลนาป่า</h3>
                <p>สถานที่ท่องเที่ยวและจุดพักผ่อนธรรมชาติที่ไม่ควรพลาด</p>
            </div>

            <div class="photo-cards-4col">
                <?php foreach ($mini_spots as $m_spot): ?>
                    <div class="photo-mini-card" onclick="window.location.href='frm_detail.php?u_id=<?= $m_spot['u_id'] ?>'">
                        <img src="<?= htmlspecialchars($m_spot['image_path']) ?>" alt="<?= htmlspecialchars($m_spot['image_name']) ?>">
                        <div class="overlay-caption">
                            <h5><?= htmlspecialchars($m_spot['image_name']) ?></h5>
                            <span><?= htmlspecialchars($m_spot['subtitle']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 8. REAL FOOTER (ฟุตเตอร์สไตล์ตามรูปตัวอย่าง ในโทนสีส้ม) -->
    <footer class="portal-footer orange-theme-footer">
        <!-- SVG Background Artwork: Organic Wave Landscapes, Elephant on Left, Airplane on Right -->
        <div class="footer-bg-artwork" aria-hidden="true">
            <svg viewBox="0 0 1440 380" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Rolling hills / wave landscape layers (Light translucent curves) -->
                <path d="M0,80 C320,150 480,40 820,110 C1120,170 1280,70 1440,100 L1440,380 L0,380 Z" fill="rgba(255, 255, 255, 0.15)" />
                <path d="M0,170 C280,120 540,220 860,160 C1140,110 1320,190 1440,150 L1440,380 L0,380 Z" fill="rgba(255, 255, 255, 0.10)" />
                <path d="M0,250 C380,210 680,290 1020,230 C1220,190 1360,240 1440,220 L1440,380 L0,380 Z" fill="rgba(255, 255, 255, 0.18)" />

                <!-- Elephant Silhouette (ช้างไทย) On Left -->
                <g transform="translate(70, 150) scale(0.48)" fill="rgba(255, 255, 255, 0.20)">
                    <path d="M180,60 C210,60 230,80 250,110 C270,140 280,180 280,220 C280,240 270,260 260,270 C250,280 240,290 240,310 C240,330 250,340 250,350 L230,350 C230,340 220,330 220,310 C220,290 230,270 230,250 C210,250 200,260 190,280 L190,350 L160,350 L160,280 C150,280 140,280 130,280 L130,350 L100,350 L100,260 C80,260 60,250 50,230 C40,210 30,180 30,150 C30,110 50,80 80,70 C100,60 150,60 180,60 Z M190,130 C198,130 205,123 205,115 C205,107 198,100 190,100 C182,100 175,107 175,115 C175,123 182,130 190,130 Z" />
                    <circle cx="210" cy="140" r="45" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="4" />
                </g>

                <!-- Airplane Silhouette (เครื่องบินบินเฉียงขึ้น) On Right -->
                <g transform="translate(1120, 60) rotate(-18) scale(0.68)" fill="rgba(255, 255, 255, 0.22)">
                    <path d="M250,110 L190,110 L140,20 L110,20 L135,110 L60,110 L45,80 L20,80 L30,125 L20,170 L45,170 L60,140 L135,140 L110,230 L140,230 L190,140 L250,140 C270,140 290,130 290,125 C290,120 270,110 250,110 Z" />
                </g>
            </svg>
        </div>

        <div class="container footer-relative-content">
            <!-- Top Bar: Logo on Left, FOLLOW US with Colored Social Buttons on Right -->
            <div class="footer-top-row">
                <div class="footer-brand-header">
                    <div class="footer-logo-text">
                        <span class="brand-main"><?= htmlspecialchars($site_footer_brand) ?></span>
                        <span class="brand-domain"><?= htmlspecialchars($site_footer_sub) ?></span>
                    </div>
                </div>

                <div class="footer-follow-box">
                    <span class="follow-text">FOLLOW US</span>
                    <div class="social-pill-group">
                        <a href="<?= htmlspecialchars($site_footer_fb) ?>" target="_blank" class="social-square-btn fb" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?= htmlspecialchars($site_footer_tw) ?>" target="_blank" class="social-square-btn tw" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="<?= htmlspecialchars($site_footer_yt) ?>" target="_blank" class="social-square-btn yt" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="<?= htmlspecialchars($site_footer_mail) ?>" class="social-square-btn mail" title="Contact Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-horizontal-line"></div>

            <!-- 4 Main Columns (with vertical white pipe bar headers) -->
            <div class="footer-columns-grid">
                <!-- Col 1: Project Info & Visitor Badge -->
                <div class="footer-grid-col">
                    <h5 class="pipe-heading"><?= htmlspecialchars($site_footer_brand) ?></h5>
                    <p class="footer-desc-text">
                        <?= nl2br(htmlspecialchars($site_footer_desc)) ?>
                    </p>
                    <div class="footer-vis-counter-badge">
                        <span class="vis-chart-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="vis-label">Vis. Today</span>
                        <span class="vis-number"><?= $total_views_count ?></span>
                    </div>
                </div>

                <!-- Col 2: เที่ยวกับเรา -->
                <div class="footer-grid-col">
                    <h5 class="pipe-heading">เที่ยวกับเรา</h5>
                    <ul class="footer-styled-links">
                        <li><a href="index.php">หน้าแรก</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('all')">ที่เที่ยวน่าสนใจ</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('ที่พัก')">ที่พัก & โฮมสเตย์</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('vr')">ทัวร์และกิจกรรม VR 360°</a></li>
                        <li><a href="about.php">เที่ยวกับเรา / เกี่ยวกับโครงการ</a></li>
                    </ul>
                </div>

                <!-- Col 3: คู่มือ & บริการ -->
                <div class="footer-grid-col">
                    <h5 class="pipe-heading">คู่มือ & บริการ</h5>
                    <ul class="footer-styled-links">
                        <li><a href="contact.php">ติดต่อสอบถามข้อมูล</a></li>
                        <!-- <li><a href="travel_route.php">โปรโมทเส้นทางท่องเที่ยว</a></li> -->
                        <li><a href="https://sci.pcru.ac.th" target="_blank">คณะวิทยาศาสตร์และเทคโนโลยี</a></li>
                        <li><a href="https://www.pcru.ac.th" target="_blank">มหาวิทยาลัยราชภัฏเพชรบูรณ์</a></li>
                        <li><a href="frm_login.php"><i class="fas fa-user-shield" style="font-size: 11px;"></i> เข้าสู่ระบบจัดการ (Admin)</a></li>
                    </ul>
                </div>

                <!-- Col 4: เครือข่ายของเรา (with icons matching reference image) -->
                <div class="footer-grid-col">
                    <h5 class="pipe-heading">เครือข่ายของเรา</h5>
                    <ul class="footer-network-icon-links">
                        <li><a href="javascript:void(0)" onclick="filterCategory('แหล่งเรียนรู้')"><i class="fas fa-utensils"></i> แนะนำร้านอาหาร & วิถีชุมชน</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('จุดชมวิว')"><i class="fas fa-cloud-sun"></i> จุดชมวิว & ทะเลหมอก</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('vr')"><i class="fas fa-vr-cardboard"></i> ทัวร์สถานที่เสมือนจริง VR</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('ที่พัก')"><i class="fas fa-home"></i> ที่พัก & ฟาร์มโฮมสเตย์</a></li>
                        <li><a href="javascript:void(0)" onclick="filterCategory('จุดกางเต็นท์')"><i class="fas fa-campground"></i> ลานกางเต็นท์ใกล้ชิดธรรมชาติ</a></li>
                        <!-- <li><a href="travel_route.php"><i class="fas fa-map-marked-alt"></i> แผนที่เส้นทางท่องเที่ยวตำบลนาป่า</a></li> -->
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar & Scroll-To-Top Button -->
            <div class="footer-bottom-flex">
                <div class="bottom-copyright-text">
                    <?= htmlspecialchars($site_footer_copyright) ?>
                </div>
                
                <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn-footer-scroll-top" title="กลับขึ้นด้านบน">
                    <i class="fas fa-chevron-up"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- Script ควบคุมโปสเตอร์สไลเดอร์เดิม -->
    <script>
      function bannerSwitcher() {
        next = $('.sec-1-input').filter(':checked').next('.sec-1-input');
        if (next.length) next.prop('checked', true);
        else $('.sec-1-input').first().prop('checked', true);
      }

      var bannerTimer = setInterval(bannerSwitcher, 5000);

      $('nav .controls label').click(function() {
        clearInterval(bannerTimer);
        bannerTimer = setInterval(bannerSwitcher, 5000);
      });
    </script>

    <!-- Script จัดการโหลดหมวดหมู่และการค้นหาแบบ AJAX (รีเฉพาะในกรอบ ไม่โหลดทั้งหน้า ไม่เลื่อนจอ) -->
    <script>
      let currentFilterCat = <?= json_encode($cat) ?> || 'all';
      let currentFilterSearch = <?= json_encode($search) ?> || '';
      let currentFilterPage = <?= (int)$page ?>;

      function filterCategory(cat, page, search) {
        if (cat === undefined || cat === null || cat === '') cat = 'all';
        if (page === undefined || page === null) page = 1;
        if (search === undefined || search === null) {
          const searchInput = document.querySelector('input[name="search"]');
          search = searchInput ? searchInput.value.trim() : '';
        }

        currentFilterCat = cat;
        currentFilterSearch = search;
        currentFilterPage = page;

        const container = document.getElementById('attractionsGridContainer');
        if (!container) return;

        // นุ่มนวลเฉพาะเนื้อหาภายในการ์ด
        container.style.transition = 'opacity 0.2s ease';
        container.style.opacity = '0.35';

        // อัปเดตสถานะ Active ของแท็บหมวดหมู่ด้านบนในกรอบ
        document.querySelectorAll('.portal-menu-links a').forEach(el => {
          const linkCat = el.getAttribute('data-cat');
          if (linkCat) {
            el.classList.toggle('active', (cat === 'all' && linkCat === 'all') || (cat !== 'all' && linkCat === cat));
          }
        });

        // อัปเดตสถานะ Active ของไอคอนหมวดหมู่ Squircle 6 อัน
        document.querySelectorAll('.cat-squircle-item').forEach(el => {
          const itemCat = el.getAttribute('data-cat');
          if (itemCat) {
            el.classList.toggle('active', (cat === 'all' && itemCat === 'all') || (cat !== 'all' && itemCat === cat));
          }
        });

        // ซิงค์ค่าตัวเลือก Dropdown ในแถบค้นหาให้ตรงกัน
        const selectEl = document.querySelector('select[name="cat"]');
        if (selectEl) {
          selectEl.value = (cat === '' ? 'all' : cat);
        }

        // ดึงข้อมูลผ่าน AJAX ให้โหลดเฉพาะ HTML ในกรอบ
        const queryParams = new URLSearchParams({
          ajax: '1',
          cat: cat,
          page: page,
          search: search
        });

        fetch('index.php?' + queryParams.toString())
          .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.text();
          })
          .then(html => {
            container.innerHTML = html;
            container.style.opacity = '1';

            // ผูก Event บันทึกยอดชม VR สำหรับการ์ดที่โหลดมาใหม่
            attachVrLinkEvents();

            // อัปเดต URL เงียบๆ โดยไม่รีเฟรชหน้า และไม่เลื่อนตำแหน่งหน้าจอ
            try {
              const cleanUrl = new URL(window.location.href);
              if (cat && cat !== 'all') {
                cleanUrl.searchParams.set('cat', cat);
              } else {
                cleanUrl.searchParams.delete('cat');
              }
              if (search) {
                cleanUrl.searchParams.set('search', search);
              } else {
                cleanUrl.searchParams.delete('search');
              }
              if (page > 1) {
                cleanUrl.searchParams.set('page', page);
              } else {
                cleanUrl.searchParams.delete('page');
              }
              cleanUrl.hash = '';
              window.history.replaceState({}, '', cleanUrl.toString());
            } catch (e) {}
          })
          .catch(err => {
            console.error('AJAX Load Error:', err);
            container.style.opacity = '1';
          });
      }

      // ผูก Event ค้นหาและ VR ติดตามการดู
      function attachVrLinkEvents() {
        document.querySelectorAll('.vr-link').forEach(link => {
          if (!link.dataset.hasVrTracker) {
            link.dataset.hasVrTracker = "true";
            link.addEventListener('click', function() {
              const u_id = this.getAttribute('data-id');
              if (u_id) {
                fetch('update_view.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: 'u_id=' + encodeURIComponent(u_id)
                }).catch(err => console.error(err));
              }
            });
          }
        });
      }

      document.addEventListener('DOMContentLoaded', function() {
        // ดักฟอร์มค้นหา ให้ทำงานผ่าน AJAX เฉพาะในกรอบ
        const searchForm = document.querySelector('.search-pill-container');
        if (searchForm) {
          searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('input[name="search"]');
            const catSelect = this.querySelector('select[name="cat"]');
            const q = searchInput ? searchInput.value.trim() : '';
            const selectedCat = catSelect ? catSelect.value : 'all';
            filterCategory(selectedCat, 1, q);
          });
        }

        // เมื่อเลือก Dropdown ในแถบค้นหา ให้กรองทันทีเฉพาะในกรอบ
        const catSelect = document.querySelector('select[name="cat"]');
        if (catSelect) {
          catSelect.addEventListener('change', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const q = searchInput ? searchInput.value.trim() : '';
            filterCategory(this.value, 1, q);
          });
        }

        attachVrLinkEvents();
      });
    </script>

    <!-- Script ควบคุม Spotlight Slider (อันล่างเลื่อนได้) -->
    <script>
      let currentSpotIdx = 0;
      const spotSlides = document.querySelectorAll('.spotlight-slide-item');
      const spotDots = document.querySelectorAll('.spotlight-dot');
      const totalSpotSlides = spotSlides.length;

      function renderSpotlightSlide(idx) {
        if (totalSpotSlides <= 1) return;
        if (idx >= totalSpotSlides) currentSpotIdx = 0;
        else if (idx < 0) currentSpotIdx = totalSpotSlides - 1;
        else currentSpotIdx = idx;

        spotSlides.forEach((slide, i) => {
          slide.classList.toggle('active', i === currentSpotIdx);
        });
        spotDots.forEach((dot, i) => {
          dot.classList.toggle('active', i === currentSpotIdx);
        });
      }

      function nextSpotlightSlide() {
        renderSpotlightSlide(currentSpotIdx + 1);
      }

      function prevSpotlightSlide() {
        renderSpotlightSlide(currentSpotIdx - 1);
      }

      function goToSpotlightSlide(idx) {
        renderSpotlightSlide(idx);
      }

      // Auto-slide every 6 seconds (หยุดชั่วคราวเมื่อชี้เมาส์)
      let spotAutoTimer = setInterval(nextSpotlightSlide, 6000);
      const carouselEl = document.getElementById('spotlightCarousel');
      if (carouselEl) {
        carouselEl.addEventListener('mouseenter', () => clearInterval(spotAutoTimer));
        carouselEl.addEventListener('mouseleave', () => {
          clearInterval(spotAutoTimer);
          spotAutoTimer = setInterval(nextSpotlightSlide, 6000);
        });
      }
    </script>

    <!-- Script ควบคุม Graphic Poster Slider (โปสเตอร์เลื่อนได้) -->
    <script>
      let currentPosterIdx = 0;
      const posterSlides = document.querySelectorAll('.promo-poster-slide');
      const posterDots = document.querySelectorAll('.promo-poster-dots .poster-dot');

      function switchPosterSlide(idx) {
        if (!posterSlides.length) return;
        currentPosterIdx = (idx + posterSlides.length) % posterSlides.length;
        posterSlides.forEach((slide, i) => {
          slide.classList.toggle('active', i === currentPosterIdx);
        });
        posterDots.forEach((dot, i) => {
          dot.classList.toggle('active', i === currentPosterIdx);
          dot.style.background = (i === currentPosterIdx) ? '#ff991b' : '#cbd5e1';
        });
      }

      function nextPosterSlide() { switchPosterSlide(currentPosterIdx + 1); }
      function prevPosterSlide() { switchPosterSlide(currentPosterIdx - 1); }
      function goToPosterSlide(idx) { switchPosterSlide(idx); }

      let posterAutoTimer = setInterval(() => switchPosterSlide(currentPosterIdx + 1), 5000);
      const posterCarouselEl = document.getElementById('promoPosterCarousel');
      if (posterCarouselEl) {
        posterCarouselEl.addEventListener('mouseenter', () => clearInterval(posterAutoTimer));
        posterCarouselEl.addEventListener('mouseleave', () => {
          clearInterval(posterAutoTimer);
          posterAutoTimer = setInterval(() => switchPosterSlide(currentPosterIdx + 1), 5000);
        });
      }
    </script>

</body>

</html>
