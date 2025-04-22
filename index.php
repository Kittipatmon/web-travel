<?php
session_start();
include "connect.php";

// ใช้ $conn แทน $pdo
$stmt = $conn->query("SELECT * FROM uploads ORDER BY created_at DESC");
$uploads = $stmt->fetch_all(MYSQLI_ASSOC);

// ตั้งค่าจำนวนข้อมูลที่ต้องการแสดงต่อหน้า
$per_page = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

// นับจำนวนรายการทั้งหมด
$total_query = $conn->query("SELECT COUNT(*) AS total FROM uploads");
$total_result = $total_query->fetch_assoc();
$total = $total_result['total'];
$total_pages = ceil($total / $per_page);

// ดึงข้อมูลจากฐานข้อมูลแบบจำกัด
$stmt = $conn->prepare("SELECT * FROM uploads ORDER BY created_at DESC LIMIT ?,?");
$stmt->bind_param("ii", $start, $per_page);
$stmt->execute();
$uploads = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ตรวจสอบว่ามีการส่งค่าพารามิเตอร์ 'id' หรือไม่
if (isset($_GET['u_id'])) {
  $u_id = (int)$_GET['u_id']; // แปลงเป็น integer เพื่อความปลอดภัย

  // ตรวจสอบว่าใน session ยังไม่เคยคลิก
  if (!isset($_SESSION['view_link' . $u_id])) {
    // เรียกการเพิ่มค่าการนับในฐานข้อมูล
    $query = "UPDATE uploads SET view_count = view_count + 1 WHERE u_id = ?";
    // ใช้ $conn แทน $pdo
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $u_id); // bind ค่า id ที่เป็นแบบ integer
    $stmt->execute();
    // เก็บสถานะใน session ว่าได้คลิกแล้ว
    $_SESSION['view_link' . $u_id] = true;
  }
}
?>


<!DOCTYPE html>
<lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title> Virtual Vista pcru.</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-hover-dropdown/2.2.1/bootstrap-hover-dropdown.min.js">
    <!--
-->
    <link rel="stylesheet" href="assets/css/travel.css">
    <link rel="stylesheet" href="assets/css/style.css">

  </head>


  <body>
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

    <?php include "header(index).php"; ?>

    <section id="section-1">
      <div class="content-slider">
        <?php foreach ($uploads as $index => $upload): ?>
          <input type="radio" id="banner<?= $index + 1 ?>" class="sec-1-input" name="banner" <?= $index === 0 ? 'checked' : '' ?>>
        <?php endforeach; ?>
        <div class="slider">
          <?php foreach ($uploads as $index => $upload): ?>
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
            <?php foreach ($uploads as $index => $upload): ?>
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

    <div class="visit-country">
      <div class="container">
        <div class="row">
          <div class="col-lg-10">
            <div class="section-heading">
              <h2><img src="assets/images/point.png" alt="" style="width: 5%; height: 5%;"> ยินดีต้อนรับเข้าสู่ SP ฟาร์มสเตย์ นาป่า เพชรบูรณ์</h2>
              <p>สัมผัสกับธรรมชาติเเละวิถีชีวติที่เรียบง่าย ท่ามกลางขุนเขาตำบลนาป่า เที่ยวชมเเหล่งท่องเที่ยวในรูปเเบบเสมือนจริง</p>
            </div>
          </div>
        </div>
        <div class="container">
          <div class="row">
            <div class="col-lg-8">
              <div class="attractions-container">
                <!-- test -->
                <?php foreach ($uploads as $upload): ?>
                  <div class="attraction-card" id="card-<?= htmlspecialchars($upload['u_id']) ?>">
                    <div class="attraction-content">
                      <div class="attraction-image-container">
                        <div class="attraction-image">
                          <img src="<?= htmlspecialchars($upload['image_path']) ?>" alt="SP ฟาร์มโฮมสเตย์นาป่า">
                        </div>
                      </div>
                      <div class="attraction-details">
                        <h2 class="attraction-title"><?= htmlspecialchars($upload['image_name']) ?></h2>
                        <span class="attraction-subtitle"><?= htmlspecialchars($upload['subtitle']) ?></span>

                        <p class="attraction-description">
                          <?php
                          $content = htmlspecialchars($upload['content']);
                          if (mb_strlen($content) > 200) {
                            $shortContent = mb_substr($content, 0, 200) . '...';
                            echo $shortContent;
                            echo ' <a href="#" class="text-primary toggle-link" data-full-content="' . htmlspecialchars($content) . '">เพิ่มเติม</a>';
                          } else {
                            echo $content;
                          }
                          ?>

                          <script>
                            document.addEventListener('click', function(event) {
                              if (event.target.classList.contains('toggle-link')) {
                                event.preventDefault();
                                const link = event.target;
                                const parent = link.parentElement;
                                const fullContent = link.getAttribute('data-full-content');
                                if (link.textContent === "เพิ่มเติม") {
                                  parent.innerHTML = fullContent + ' <a href="#" class="text-primary toggle-link" data-full-content="' + fullContent + '">ย่อ</a>';
                                } else {
                                  const shortContent = fullContent.substring(0, 200) + '...';
                                  parent.innerHTML = shortContent + ' <a href="#" class="text-primary toggle-link" data-full-content="' + fullContent + '">เพิ่มเติม</a>';
                                }
                                behavior: 'smooth'
                              }
                            });
                          </script>
                        </p>

                        <ul class="attraction-info-list">
                          <li class="attraction-info-item"><i class="fas fa-user"></i> ผู้เข้าชม: <?= htmlspecialchars($upload['view_count']) ?> ครั้ง</li>
                          <li class="attraction-info-item"><i class="fas fa-globe"></i> <?= htmlspecialchars($upload['contact']) ?></li>
                        </ul>

                        <div class="attraction-buttons">
                          <div class="vr-button">
                            <a href="<?= htmlspecialchars($upload['vr_link']) ?>" class="vr-link" data-id="<?= $upload['u_id'] ?>" target="_blank">
                              <i class="fas fa-vr-cardboard"></i> รับชม VR
                            </a>
                          </div>

                          <div class="contact-button">
                            <a href="frm_detail.php?u_id=<?= htmlspecialchars($upload['u_id']) ?>">ดูข้อมูลเพิ่มเติม <i class="fas fa-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
                <!-- test -->
              </div>
            </div>

            <!-- ส่วนแผนที่ด้านขวา -->
            <div class="col-lg-4">
              <div class="side-bar-map">
                <div class="row">
                  <div class="col-lg-12">
                    <div id="map">
                      <img src="assets/images/เส้นทางการท่องเที่ยว.png" width="100%" height="100%" frameborder="0" allowfullscreen=""></img>
                      <!-- <iframe src="assets/images/เส้นทางง.png" width="100%" height="550px" frameborder="0" allowfullscreen=""></iframe> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 mt-5">
              <ul class="page-numbers">
                <?php if ($page > 1): ?>
                  <li><a href="?page=<?= $page - 1 ?>">&laquo;</a></li>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);

                if ($start_page > 1): ?>
                  <li><a href="?page=1">1</a></li>
                  <?php if ($start_page > 2): ?>
                    <li style="color:orange;">...</li>
                  <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                  <li <?= $i == $page ? 'class="active"' : '' ?>><a href="?page=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                  <?php if ($end_page < $total_pages - 1): ?>
                    <li style="color:orange;">...</li>
                  <?php endif; ?>
                  <li><a href="?page=<?= $total_pages ?>"><?= $total_pages ?></a></li>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                  <li><a href="?page=<?= $page + 1 ?>">&raquo;</a></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ***** Visit Country Area End ***** -->

    <div class="call-to-action">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <h2>Are You Looking To Travel ?</h2>
            <h4>View details by clicking the button.</h4>
          </div>
          <div class="col-lg-4">
            <div class="border-button">
              <a href="#">เส้นทางท่องเที่ยว</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer>
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <p> kittipat Manuch Wichuta Kongpol &<a href="#" target="_blank"> Virtual Vista © 2025</a>
              <br>Design: <a href="sci.pcru.ac.th" target="_blank" title="คณะวิทยาศาสตร์และเทคโนโลยี">คณะวิทยาศาสตร์และเทคโนโลยี</a>
            </p>
          </div>
        </div>
      </div>
    </footer>


    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/wow.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>

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

      // Scroll to top on page reload
      $(document).ready(function() {
        $(this).scrollTop(0);
      });
    </script>

    <!-- นับการเข้าชมเว็ป -->
    <!-- <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        // ตรวจสอบว่ามีการคลิกปุ่ม vr-button หรือไม่
        const vrButtons = document.querySelectorAll('.vr-button a');
        vrButtons.forEach(button => {
          button.addEventListener('click', (event) => {
            event.preventDefault(); // ป้องกันการเปลี่ยนหน้า
            const url = button.getAttribute('href');
            const id = button.closest('.attraction-card').dataset.id; // ดึง id จาก data-id ของ attraction-card

            if (!sessionStorage.getItem('view_count' + id)) {
              sessionStorage.setItem('view_count' + id, 'true');

              // ส่งคำขอ AJAX เพื่ออัปเดต view_count
              fetch('index copy.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                    id: id
                  })
                })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    console.log('View count updated successfully.');
                  } else {
                    console.error('Failed to update view count.');
                  }
                })
                .catch(error => console.error('Error:', error));
            }

            // เปลี่ยนหน้าไปยังลิงก์ VR
            window.location.href = url;
          });
        });
      });
    </script> -->

    <script>
      //เมื่อคลิกที่ขอบเขตของการ์ดหน้าเว็บจะค่อยๆ เลื่อนอย่างราบรื่นไปที่การ์ดที่ถูกคลิกทันที
      $(document).ready(function() {
        $('.attraction-card').on('click', function() {
          const cardId = $(this).attr('id');
          document.getElementById(cardId).scrollIntoView({
            behavior: 'smooth',
            block: 'center' // เปลี่ยนจาก 'start' เป็น 'center'
          });
        });
      });
    </script>



    <script>
      // เมื่อโหลดหน้าเว็บเสร็จสมบูรณ์
      document.addEventListener('DOMContentLoaded', () => {
        // เลือกทุกลิงก์ที่มี class 'vr-link'
        document.querySelectorAll('.vr-link').forEach(link => {
          // เพิ่ม event listener สำหรับการคลิก
          link.addEventListener('click', function(e) {
            const u_id = this.getAttribute('data-id'); // ดึงค่า u_id จาก attribute

            // ส่งคำขอไปยังไฟล์ update_view.php เพื่ออัปเดตจำนวนการเข้าชม
            fetch('update_view.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `u_id=${encodeURIComponent(u_id)}` // ส่งข้อมูล u_id
              }).then(response => response.json())
              .then(data => {
                console.log(data.message); // แสดงข้อความใน console เพื่อเช็คผลลัพธ์
              });
          });
        });
      });
    </script>

  </body>

  </html>