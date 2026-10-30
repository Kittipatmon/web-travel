<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

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
$site_footer_desc = $footer_settings['footer_desc'] ?? 'เว็บท่องเที่ยว Virtual Vista PCRU รวมข้อมูลข่าวสารสถานที่ท่องเที่ยวในตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ สัมผัสธรรมชาติ จุดชมวิว ลานกางเต็นท์ และระบบท่องเที่ยวเสมือนจริง 360° จัดทริปท่องเที่ยวง่ายๆ ผ่านเว็บไซต์ของเรา';
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
  <title>ติดต่อเรา - Virtual Vista pcru.</title>

  <!-- Google Fonts: Prompt & Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- Theme CSS -->
  <link rel="stylesheet" href="assets/css/travel.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/modern-travel.css">

  <style>
    html, body {
      font-family: 'Prompt', 'Poppins', sans-serif;
      margin: 0 !important;
      padding: 0 !important;
      background-color: #faf7f2;
      color: #333;
    }

    .second-page-heading {
      background-image: url('assets/images/heading-bg-02.jpg');
      background-position: center center;
      background-size: cover;
      background-repeat: no-repeat;
      padding: 120px 0 90px 0;
      text-align: center;
      position: relative;
    }

    .second-page-heading::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.45);
    }

    .second-page-heading .container {
      position: relative;
      z-index: 2;
    }

    .second-page-heading h4 {
      font-size: 22px;
      font-weight: 600;
      color: #fff;
      margin-bottom: 12px;
    }

    .second-page-heading h2 {
      font-size: 38px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 18px;
    }

    .second-page-heading p {
      font-size: 16px;
      color: #f1f5f9;
      margin-bottom: 8px;
    }

    .second-page-heading .main-button {
      margin-top: 24px;
    }

    .second-page-heading .main-button a {
      background-color: #ff991b;
      color: #ffffff;
      padding: 12px 28px;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 153, 27, 0.4);
    }

    .second-page-heading .main-button a:hover {
      background-color: #e0820f;
      transform: translateY(-2px);
    }

    .reservation-info {
      margin-top: -40px;
      position: relative;
      z-index: 3;
      margin-bottom: 40px;
    }

    .info-item {
      background: #ffffff;
      border-radius: 16px;
      padding: 30px 24px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      height: 100%;
      transition: transform 0.3s ease;
    }

    .info-item:hover {
      transform: translateY(-5px);
    }

    .info-item i {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: rgba(255, 153, 27, 0.12);
      color: #ff991b;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 18px;
    }

    .info-item h4 {
      font-size: 19px;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 10px;
    }

    .info-item a, .info-item p {
      color: #64748b;
      font-size: 14.5px;
      text-decoration: none;
      line-height: 1.6;
      margin: 0;
    }

    .map-container-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
      margin-bottom: 60px;
      overflow: hidden;
    }

    .map-container-card iframe {
      border-radius: 14px;
      display: block;
      width: 100%;
      height: 450px;
      border: 0;
    }
  </style>
</head>

<body>

  <!-- ***** Header Area Start ***** -->
  <?php include('header(contact).php'); ?>
  <!-- ***** Header Area End ***** -->

  <!-- Hero Banner -->
  <div class="second-page-heading">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h4>
            <img src="assets/images/contact.png" alt="Contact Icon" style="width:28px; height:28px; margin-bottom:6px; object-fit: contain;"> ติดต่อเรา
          </h4>
          <h2>คณะวิทยาศาสตร์และเทคโนโลยี</h2>
          <p>เยี่ยมชมธรรมชาติและวิถีชีวิตชนบทผ่านเทคโนโลยีเสมือนจริง VR 360°</p>
          <p>เริ่มต้นทริปเสมือนจริงของคุณได้ที่นี่</p>
          <div class="main-button">
            <a href="https://www.facebook.com/SPmelonphetchabun" target="_blank">
              <i class="fab fa-facebook me-2"></i> SP Farm Stay facebook page
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Info Cards -->
  <div class="reservation-info">
    <div class="container">
      <div class="row g-4">
        <!-- Phone -->
        <div class="col-lg-4 col-md-6">
          <div class="info-item">
            <i class="fas fa-phone-alt"></i>
            <h4>เบอร์โทรศัพท์</h4>
            <a href="tel:056717122">056-717122</a>
          </div>
        </div>

        <!-- Website -->
        <div class="col-lg-4 col-md-6">
          <div class="info-item">
            <i class="fas fa-globe"></i>
            <h4>เว็บไซต์ทางการ</h4>
            <a href="https://sci.pcru.ac.th" target="_blank">sci.pcru.ac.th</a>
          </div>
        </div>

        <!-- Address -->
        <div class="col-lg-4 col-md-12">
          <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <h4>ที่อยู่</h4>
            <p>83 หมู่ 11 ถนนสระบุรี-หล่มสัก ตำบลสะเดียง อำเภอเมือง จังหวัดเพชรบูรณ์ 67000</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Google Map Section -->
  <div class="container">
    <div class="map-container-card">
      <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d19071.952367397782!2d101.135817!3d16.457917!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31203ba75feb6033%3A0x82fb44fd2d6f954d!2z4LiE4LiT4Liw4Lin4Li04LiX4Lii4Liy4Lio4Liy4Liq4LiV4Lij4LmM4LmB4Lil4Liw4LmA4LiX4LiE4LmC4LiZ4LmC4Lil4Lii4Li1IOC4oeC4q-C4suC4p-C4tOC4l-C4ouC4suC4peC4seC4ouC4o-C4suC4iuC4oOC4seC4j-C5gOC4nuC4iuC4o-C4muC4ueC4o-C4k-C5jA!5e1!3m2!1sen!2sus!4v1744441594288!5m2!1sen!2sus" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>

  <!-- 8. REAL FOOTER (ฟุตเตอร์สไตล์เดียวกับหน้าหลัก index.php ในโทนสีส้ม) -->
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
            <li><a href="all_destinations.php">ที่เที่ยวน่าสนใจ</a></li>
            <li><a href="all_destinations.php">ที่พัก & โฮมสเตย์</a></li>
            <li><a href="all_destinations.php">ทัวร์และกิจกรรม VR 360°</a></li>
            <li><a href="contact.php">เที่ยวกับเรา / ติดต่อเรา</a></li>
          </ul>
        </div>

        <!-- Col 3: คู่มือ & บริการ -->
        <div class="footer-grid-col">
          <h5 class="pipe-heading">คู่มือ & บริการ</h5>
          <ul class="footer-styled-links">
            <li><a href="contact.php">ติดต่อสอบถามข้อมูล</a></li>
            <li><a href="https://sci.pcru.ac.th" target="_blank">คณะวิทยาศาสตร์และเทคโนโลยี</a></li>
            <li><a href="https://www.pcru.ac.th" target="_blank">มหาวิทยาลัยราชภัฏเพชรบูรณ์</a></li>
            <li><a href="frm_login.php"><i class="fas fa-user-shield" style="font-size: 11px;"></i> เข้าสู่ระบบจัดการ (Admin)</a></li>
          </ul>
        </div>

        <!-- Col 4: เครือข่ายของเรา -->
        <div class="footer-grid-col">
          <h5 class="pipe-heading">เครือข่ายของเรา</h5>
          <ul class="footer-network-icon-links">
            <li><a href="all_destinations.php"><i class="fas fa-utensils"></i> แนะนำร้านอาหาร & วิถีชุมชน</a></li>
            <li><a href="all_destinations.php"><i class="fas fa-cloud-sun"></i> จุดชมวิว & ทะเลหมอก</a></li>
            <li><a href="all_destinations.php"><i class="fas fa-vr-cardboard"></i> ทัวร์สถานที่เสมือนจริง VR</a></li>
            <li><a href="all_destinations.php"><i class="fas fa-home"></i> ที่พัก & ฟาร์มโฮมสเตย์</a></li>
            <li><a href="all_destinations.php"><i class="fas fa-campground"></i> ลานกางเต็นท์ใกล้ชิดธรรมชาติ</a></li>
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

  <!-- Scripts: Only fast core scripts needed -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
