<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

// ดึงข้อมูลหมวดหมู่ทั้งหมด
$cat_stmt = $conn->query("SELECT * FROM categories ORDER BY c_id ASC");
$categories = $cat_stmt ? $cat_stmt->fetch_all(MYSQLI_ASSOC) : [];

// ดึงข้อมูลสถานที่ท่องเที่ยวทั้งหมด (ดึงทั้งหมด) พร้อมหมวดหมู่
$stmt = $conn->query("
    SELECT u.*, c.category_name, c.icon AS cat_icon, c.color AS cat_color 
    FROM uploads u 
    LEFT JOIN categories c ON u.c_id = c.c_id 
    ORDER BY u.created_at DESC
");
$uploads = $stmt ? $stmt->fetch_all(MYSQLI_ASSOC) : [];

// ยอดการเข้าชมรวมสำหรับแสดงที่ Footer
$total_views_stmt = $conn->query("SELECT SUM(view_count) AS total_views FROM uploads");
$total_views_row = $total_views_stmt ? $total_views_stmt->fetch_assoc() : null;
$total_views_count = $total_views_row && $total_views_row['total_views'] ? (int)$total_views_row['total_views'] : 0;

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
$site_footer_desc = $footer_settings['footer_desc'] ?? 'เว็บท่องเที่ยว Virtual Vista PCRU รวมข้อมูลข่าวสารสถานที่ท่องเที่ยวในตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ สัมผัสธรรมชาติ จุดชมวิว ลานกางเต็นท์ และระบบท่องเที่ยวเสมือนจริง 360°';
$site_footer_fb = $footer_settings['footer_fb_url'] ?? 'https://www.facebook.com';
$site_footer_tw = $footer_settings['footer_tw_url'] ?? 'https://twitter.com';
$site_footer_yt = $footer_settings['footer_yt_url'] ?? 'https://youtube.com';
$site_footer_mail = $footer_settings['footer_email_url'] ?? 'contact.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ดูทั้งหมด - ท่องเที่ยวและกิจกรรม Virtual Vista</title>

    <!-- Google Fonts: Poppins & Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Original Theme CSS Files -->
    <link rel="stylesheet" href="assets/css/travel.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/modern-travel.css">

    <style>
        body {
            font-family: 'Prompt', 'Poppins', sans-serif;
            background-color: #faf7f2;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .search-filter-section {
            margin-top: 35px;
            margin-bottom: 25px;
        }

        .attractions-portal-section {
            margin-top: 15px;
            margin-bottom: 50px;
        }
    </style>
</head>

<body>

    <!-- 1. NAVIGATION (ส่วนหัวดั้งเดิมตรงตามรูปที่ 2 และ 1 เป๊ะๆ) -->
    <?php include "header(index).php"; ?>

    <!-- 2. SEARCH BAR (แถบค้นหาดั้งเดิมตามแบบหน้าแรก) -->
    <section class="search-filter-section">
        <div class="container">
            <h2 class="search-title">
                ที่เที่ยวน่าสนใจ ที่เพชรบูรณ์ (ทั้งหมด)
            </h2>

            <!-- Search Bar Form (Pill Shape with Dropdown & Search Button) -->
            <div class="search-pill-container">
                <input type="text" id="searchInput" placeholder="ชื่อสถานที่ท่องเที่ยว . . .">
                
                <div class="search-dropdown-box">
                    <select id="catSelect" onchange="filterCategory(this.value)">
                        <option value="all">ทั้งหมด</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category_name']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                        <option value="vr">VR 360°</option>
                    </select>
                    <i class="fas fa-chevron-down chevron-icon"></i>
                </div>

                <button type="button" class="search-pill-btn" onclick="executeFilter()">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </section>

    <!-- 3. ATTRACTIONS GRID (กรอบขาวและการ์ด 3 คอลัมน์เหมือน index.php 100%) -->
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

                    <!-- Category Menu Links on Top Right -->
                    <div class="portal-menu-links">
                        <a href="javascript:void(0)" onclick="filterCategory('all', this)" class="active" data-cat="all">เที่ยว</a>
                        <a href="javascript:void(0)" onclick="filterCategory('จุดชมวิว', this)" data-cat="จุดชมวิว">ท่องเที่ยว</a>
                        <a href="javascript:void(0)" onclick="filterCategory('vr', this)" data-cat="vr">ทัวร์และกิจกรรม</a>
                        <a href="javascript:void(0)" onclick="filterCategory('ที่พัก', this)" data-cat="ที่พัก">ที่พักHotel</a>
                        <a href="javascript:void(0)" onclick="filterCategory('แหล่งเรียนรู้', this)" data-cat="แหล่งเรียนรู้">ร้านอาหารแนะนำ</a>
                    </div>
                </div>

                <!-- Attractions Grid: Render All Cards -->
                <div id="attractionsGridContainer">
                    <div class="travel-3col-grid">
                        <?php if (!empty($uploads)): ?>
                            <?php foreach ($uploads as $upload): 
                                $u_id = (int)$upload['u_id'];
                                $name = htmlspecialchars($upload['image_name'] ?? '');
                                $path = !empty($upload['image_path']) ? htmlspecialchars($upload['image_path']) : 'assets/images/default.jpg';
                                $subtitle = htmlspecialchars($upload['subtitle'] ?? '');
                                $content = htmlspecialchars($upload['content'] ?? '');
                                $vr_link = htmlspecialchars($upload['vr_link'] ?? '');
                                $created_at = !empty($upload['created_at']) ? date('F d, Y', strtotime($upload['created_at'])) : date('F d, Y');
                                $category_name = htmlspecialchars($upload['category_name'] ?? '');
                                $search_kw = strtolower($name . ' ' . $subtitle . ' ' . $content . ' ' . $category_name);
                            ?>
                                <div class="travel-item-card dest-card" data-keywords="<?= $search_kw ?>" data-has-vr="<?= !empty($vr_link) ? '1' : '0' ?>" id="card-<?= $u_id ?>">
                                    <div class="item-thumb-box">
                                        <img src="<?= $path ?>" alt="<?= $name ?>" loading="lazy" onerror="this.onerror=null; this.src='assets/images/default.jpg';">
                                        
                                        <!-- Photo Title Overlay as seen in reference image -->
                                        <div class="thumb-title-overlay">
                                            <h4><?= $name ?></h4>
                                        </div>

                                        <?php if (!empty($vr_link)): ?>
                                            <a href="<?= $vr_link ?>" class="item-badge-vr vr-link" target="_blank" title="รับชม VR 360°">
                                                <i class="fas fa-vr-cardboard"></i> VR
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <div class="item-body">
                                        <h3 class="item-title"><?= $name ?>: <?= $subtitle ?></h3>

                                        <p class="item-desc">
                                            <?= htmlspecialchars(mb_substr($content, 0, 75, 'UTF-8')) ?>...
                                        </p>

                                        <div class="item-footer">
                                            <div class="item-views">
                                                <?= $created_at ?>
                                            </div>

                                            <div class="item-actions">
                                                <a href="frm_detail.php?u_id=<?= $u_id ?>" class="btn-card-view">
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
                                <h4 class="text-secondary">ไม่พบสถานที่ท่องเที่ยว</h4>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4. REAL FOOTER (ฟุตเตอร์ดั้งเดิมของเว็บไซต์) -->
    <footer class="portal-footer orange-theme-footer">
        <!-- SVG Background Artwork: Organic Wave Landscapes, Elephant on Left, Airplane on Right -->
        <div class="footer-bg-artwork" aria-hidden="true">
            <svg viewBox="0 0 1440 380" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,80 C320,150 480,40 820,110 C1120,170 1280,70 1440,100 L1440,380 L0,380 Z" fill="rgba(255, 255, 255, 0.15)" />
                <path d="M0,170 C280,120 540,220 860,160 C1140,110 1320,190 1440,150 L1440,380 L0,380 Z" fill="rgba(255, 255, 255, 0.10)" />
                <path d="M0,250 C380,210 680,290 1020,230 C1220,190 1360,240 1440,220 L1440,380 L0,380 Z" fill="rgba(255, 255, 255, 0.18)" />

                <!-- Elephant Silhouette On Left -->
                <g transform="translate(70, 150) scale(0.48)" fill="rgba(255, 255, 255, 0.20)">
                    <path d="M180,60 C210,60 230,80 250,110 C270,140 280,180 280,220 C280,240 270,260 260,270 C250,280 240,290 240,310 C240,330 250,340 250,350 L230,350 C230,340 220,330 220,310 C220,290 230,270 230,250 C210,250 200,260 190,280 L190,350 L160,350 L160,280 C150,280 140,280 130,280 L130,350 L100,350 L100,260 C80,260 60,250 50,230 C40,210 30,180 30,150 C30,110 50,80 80,70 C100,60 150,60 180,60 Z" />
                </g>

                <!-- Airplane Silhouette On Right -->
                <g transform="translate(1120, 60) rotate(-18) scale(0.68)" fill="rgba(255, 255, 255, 0.22)">
                    <path d="M250,110 L190,110 L140,20 L110,20 L135,110 L60,110 L45,80 L20,80 L30,125 L20,170 L45,170 L60,140 L135,140 L110,230 L140,230 L190,140 L250,140 C270,140 290,130 290,125 C290,120 270,110 250,110 Z" />
                </g>
            </svg>
        </div>

        <div class="container footer-relative-content">
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
                        <a href="<?= htmlspecialchars($site_footer_fb) ?>" target="_blank" class="social-square-btn fb"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= htmlspecialchars($site_footer_tw) ?>" target="_blank" class="social-square-btn tw"><i class="fab fa-twitter"></i></a>
                        <a href="<?= htmlspecialchars($site_footer_yt) ?>" target="_blank" class="social-square-btn yt"><i class="fab fa-youtube"></i></a>
                        <a href="<?= htmlspecialchars($site_footer_mail) ?>" class="social-square-btn mail"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-horizontal-line"></div>

            <div class="footer-columns-grid">
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

                <div class="footer-grid-col">
                    <h5 class="pipe-heading">เที่ยวกับเรา</h5>
                    <ul class="footer-styled-links">
                        <li><a href="index.php">หน้าแรก</a></li>
                        <li><a href="all_destinations.php">ที่เที่ยวน่าสนใจ (ดูทั้งหมด)</a></li>
                        <li><a href="index.php#attractions-section">ที่พัก & โฮมสเตย์</a></li>
                        <li><a href="about.php">เที่ยวกับเรา / เกี่ยวกับโครงการ</a></li>
                    </ul>
                </div>

                <div class="footer-grid-col">
                    <h5 class="pipe-heading">คู่มือ & บริการ</h5>
                    <ul class="footer-styled-links">
                        <li><a href="contact.php">ติดต่อสอบถามข้อมูล</a></li>
                        <li><a href="https://sci.pcru.ac.th" target="_blank">คณะวิทยาศาสตร์และเทคโนโลยี</a></li>
                        <li><a href="https://www.pcru.ac.th" target="_blank">มหาวิทยาลัยราชภัฏเพชรบูรณ์</a></li>
                        <li><a href="frm_login.php">เข้าสู่ระบบจัดการ (Admin)</a></li>
                    </ul>
                </div>

                <div class="footer-grid-col">
                    <h5 class="pipe-heading">เครือข่ายของเรา</h5>
                    <ul class="footer-network-icon-links">
                        <li><a href="index.php"><i class="fas fa-utensils"></i> แนะนำร้านอาหาร & วิถีชุมชน</a></li>
                        <li><a href="index.php"><i class="fas fa-cloud-sun"></i> จุดชมวิว & ทะเลหมอก</a></li>
                        <li><a href="index.php"><i class="fas fa-vr-cardboard"></i> ทัวร์สถานที่เสมือนจริง VR</a></li>
                        <li><a href="index.php"><i class="fas fa-home"></i> ที่พัก & ฟาร์มโฮมสเตย์</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-copyright-bar">
                <p><?= htmlspecialchars($site_footer_copyright) ?></p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedCat = 'all';

        function filterCategory(cat, el) {
            selectedCat = cat;

            // ปรับสถานะ Active บนแท็บเมนูด้านบนขวา
            document.querySelectorAll('.portal-menu-links a').forEach(a => a.classList.remove('active'));
            if (el) {
                el.classList.add('active');
            } else {
                const target = document.querySelector(`.portal-menu-links a[data-cat="${cat}"]`);
                if (target) target.classList.add('active');
            }

            // ซิงค์กับ dropdown
            const dd = document.getElementById('catSelect');
            if (dd && dd.value !== cat) dd.value = cat;

            executeFilter();
        }

        // ค้นหาแบบเรียลไทม์
        document.getElementById('searchInput').addEventListener('input', executeFilter);

        function executeFilter() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();

            document.querySelectorAll('.travel-3col-grid .dest-card').forEach(card => {
                const kw = card.getAttribute('data-keywords') || '';
                const hasVr = card.getAttribute('data-has-vr') === '1';

                let matchCat = true;
                if (selectedCat === 'vr') {
                    matchCat = hasVr;
                } else if (selectedCat !== 'all') {
                    matchCat = kw.includes(selectedCat.toLowerCase());
                }

                const matchQuery = (query === '') || kw.includes(query);

                card.style.display = (matchCat && matchQuery) ? '' : 'none';
            });
        }
    </script>
</body>

</html>
