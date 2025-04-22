<?php
session_start();
include "connect.php";
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
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/travel.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <!--
-->

  </head>

  <body>
    <!-- ***** Preloader Start ***** -->
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
    <!-- ***** Preloader End ***** -->

    <!-- ***** Header Area Start ***** -->
    <?php include "header.php" ?>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Main Banner Area Start ***** -->
    <section id="section-1">
      <div class="content-slider">
        <input type="radio" id="banner1" class="sec-1-input" name="banner" checked>
        <input type="radio" id="banner2" class="sec-1-input" name="banner">
        <input type="radio" id="banner3" class="sec-1-input" name="banner">
        <input type="radio" id="banner4" class="sec-1-input" name="banner">
        <div class="slider">
          <div id="top-banner-1" class="banner">
            <div class="banner-inner-wrapper header-text">
              <div class="main-caption">
                <h2>เที่ยวให้สุด แล้วหยุดที่ความทรงจำดีๆ:</h2>
                <h1>SP ฟาร์มสเตย์ เพชรบูรณ์</h1>
                <div class="border-button"><a href="https://sci.pcru.ac.th/room/vr/napa/sp/" >รับชม VR</a></div>
              </div>
              <!-- <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <div class="more-info">
                    <div class="row">
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-user"></i>
                        <h4><span>ผู้เข้าชม:</span><br>628 ครั้ง</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-globe"></i>
                        <h4><span>Territory:</span><br>275.400 KM<em>2</em></h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-home"></i>
                        <h4><span>AVG Price:</span><br>$946.000</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <div class="main-button">
                          <a href="https://sci.pcru.ac.th/room/vr/napa/sp/">รับชม VR</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            </div>
          </div>
          <div id="top-banner-2" class="banner">
            <div class="banner-inner-wrapper header-text">
              <div class="main-caption">
                <h2>ไปที่ใหม่ๆ เพื่อค้นพบตัวเองในมุมที่ไม่เคยรู้จัก:</h2>
                <h1>ลานชมดาว</h1>
                <div class="border-button"><a href="https://sci.pcru.ac.th/room/vr/napa/ชมดาว/" style="font-size: 20px; font-weight:bold;">รับชม VR</a></div>
              </div>
              <!-- <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <div class="more-info">
                    <div class="row">
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-user"></i>
                        <h4><span>Population:</span><br>8.66 M</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-globe"></i>
                        <h4><span>Territory:</span><br>41.290 KM<em>2</em></h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-home"></i>
                        <h4><span>AVG Price:</span><br>$1.100.200</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <div class="main-button">
                          <a href="https://sci.pcru.ac.th/room/vr/napa/%e0%b8%8a%e0%b8%a1%e0%b8%94%e0%b8%b2%e0%b8%a7/">รับชม VR</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            </div>
          </div>
          <div id="top-banner-3" class="banner">
            <div class="banner-inner-wrapper header-text">
              <div class="main-caption">
                <h2>อยากพักใจ… ให้ไปเที่ยว:</h2>
                <h1>ลานจามจุรี</h1>
                <div class="border-button"><a href="https://sci.pcru.ac.th/room/vr/napa/ลานจามจุรี/">รับชม VR</a></div>
              </div>
              <!-- <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <div class="more-info">
                    <div class="row">
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-user"></i>
                        <h4><span>Population:</span><br>67.41 M</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-globe"></i>
                        <h4><span>Territory:</span><br>551.500 KM<em>2</em></h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-home"></i>
                        <h4><span>AVG Price:</span><br>$425.600</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <div class="main-button">
                          <a href="https://sci.pcru.ac.th/room/vr/napa/%e0%b8%a5%e0%b8%b2%e0%b8%99%e0%b8%88%e0%b8%b2%e0%b8%a1%e0%b8%88%e0%b8%b8%e0%b8%a3%e0%b8%b5/">รับชม VR</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            </div>
          </div>
          <div id="top-banner-4" class="banner">
            <div class="banner-inner-wrapper header-text">
              <div class="main-caption">
                <h2>ธรรมชาติสวยงามเสมอ เมื่อเราออกไปสัมผัส:</h2>
                <h1>ห้วยน้ำริน</h1>
                <div class="border-button"><a href="https://sci.pcru.ac.th/room/vr/napa/ห้วยน้ำริน/">รับชม VR</a></div>
              </div>
              <!-- <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <div class="more-info">
                    <div class="row">
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-user"></i>
                        <h4><span>Population:</span><br>69.86 M</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-globe"></i>
                        <h4><span>Territory:</span><br>513.120 KM<em>2</em></h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-home"></i>
                        <h4><span>AVG Price:</span><br>$165.450</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <div class="main-button">
                          <a href="https://sci.pcru.ac.th/room/vr/napa/%e0%b8%ab%e0%b9%89%e0%b8%a7%e0%b8%a2%e0%b8%99%e0%b9%89%e0%b8%b3%e0%b8%a3%e0%b8%b4%e0%b8%99/">รับชม VR</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            </div>
          </div>
        </div>
        <nav>
          <div class="controls">
            <label for="banner1"><span class="progressbar"><span class="progressbar-fill"></span></span><span class="text">1</span></label>
            <label for="banner2"><span class="progressbar"><span class="progressbar-fill"></span></span><span class="text">2</span></label>
            <label for="banner3"><span class="progressbar"><span class="progressbar-fill"></span></span><span class="text">3</span></label>
            <label for="banner4"><span class="progressbar"><span class="progressbar-fill"></span></span><span class="text">4</span></label>
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
        <div class="row">
          <div class="col-lg-8">
            <div class="items">
              <div class="row">
                <div class="col-lg-12">
                  <div class="item">
                    <div class="row">
                      <div class="col-lg-4 col-sm-5">
                        <div class="image">
                          <img src="assets/images/sp.jpg" width="229" height="234" alt="">
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-7">
                        <div class="right-content">
                          <h4>Sp ฟาร์มโฮมสเตย์นาป่า</h4>
                          <span>แหล่งท่องเที่ยวและแหล่งเรียนรู้</span>
                          <div class="main-button">
                            <a href="https://sci.pcru.ac.th/room/vr/napa/sp/">รับชม VR</a>
                          </div>
                          <p>SP ฟาร์มสเตย์ ในตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ เป็นแหล่งท่องเที่ยวเชิงเกษตรและโฮมสเตย์ เปิดตั้งแต่ปี 2560 ให้นักท่องเที่ยวได้เรียนรู้การเกษตรแบบผสมผสาน และสัมผัสวิถีชีวิตชุมชน นอกจากนี้ยังสนับสนุนให้ชาวบ้านนำสินค้ามาจำหน่ายเพื่อสร้างรายได้ในชุมชน โดยเน้นการท่องเที่ยวแบบมีส่วนร่วมและใช้เทคโนโลยีเสมือนจริงในการประชาสัมพันธ์</p>
                          <ul class="info">
                            <li><i class="fa fa-user"></i>ผู้เข้าชม:&nbsp;628&nbsp;ครั้ง </li>
                            <li><i class="fa fa-globe"></i> SP Farm Stay facebookpage</li>
                          </ul>
                          <div class="text-button">
                            <a href="https://www.facebook.com/SPmelonphetchabun">(ข้อมูลการติดต่อ)<i class="fa fa-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="item">
                    <div class="row">
                      <div class="col-lg-4 col-sm-5">
                        <div class="image">
                          <img src="assets/images/ลานชมดาว.jpg" width="229" height="234" alt="">
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-7">
                        <div class="right-content">
                          <h4>ลานชมดาว</h4>
                          <span>แหล่งท่องเที่ยวและจุดกางเต็นท์</span>
                          <div class="main-button">
                            <a href="https://sci.pcru.ac.th/room/vr/napa/%e0%b8%8a%e0%b8%a1%e0%b8%94%e0%b8%b2%e0%b8%a7/" target="_blank">รับชม VR</a>
                          </div>
                          <p>เป็นจุดชมดาวยอดนิยมที่โอบล้อมด้วยธรรมชาติอันเงียบสงบและอากาศเย็นสบายตลอดปี ที่นี่สามารถมองเห็น ท้องฟ้าที่เต็มไปด้วยดวงดาว ได้อย่างชัดเจน โดยเฉพาะในคืนฟ้าเปิดที่ไม่มีมลภาวะทางแสง ทำให้สามารถสังเกตกลุ่มดาว ทางช้างเผือก และบางครั้งอาจได้เห็นดาวตก นอกจากนี้ ลานชมดาวยังเป็นจุดแวะพักสำหรับนักเดินป่าที่มาเยือนน้ำตกตาดหมอก และเป็นสถานที่เหมาะสำหรับการตั้งแคมป์ ถ่ายภาพดาราศาสตร์ และสัมผัสบรรยากาศธรรมชาติยามค่ำคืนอย่างเต็มที่</p>
                          <ul class="info">
                            <li><i class="fa fa-user"></i>ผู้เข้าชม:&nbsp;628&nbsp;ครั้ง</li>
                            <!-- <li><i class="fa fa-globe"></i> อุทยานแห่งชาติตาดหมอก - Tat Mok National Park</li> -->
                            <li><i class="fa fa-home"></i> อุทยานแห่งชาติตาดหมอก - Tat Mok National Park</li>
                          </ul>
                          <div class="text-button">
                            <a href="https://www.facebook.com/TatmokNationalPark">(ข้อมูลการติดต่อ)<i class="fa fa-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="item last-item">
                    <div class="row">
                      <div class="col-lg-4 col-sm-5">
                        <div class="image">
                          <img src="assets/images/ลานจามจุรี.jpg" width="229" height="234" alt="">
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-7">
                        <div class="right-content">
                          <h4>ลานจามจุรี</h4>
                          <span>แหล่งท่องเที่ยวและจุดกางเต็นท์</span>
                          <div class="main-button">
                            <a href="https://sci.pcru.ac.th/room/vr/napa/%e0%b8%a5%e0%b8%b2%e0%b8%99%e0%b8%88%e0%b8%b2%e0%b8%a1%e0%b8%88%e0%b8%b8%e0%b8%a3%e0%b8%b5/" traget="_blank">รับชม VR</a>
                          </div>
                          <p>เป็นพื้นที่พักผ่อนที่โดดเด่นด้วยต้นจามจุรีขนาดใหญ่ แผ่กิ่งก้านให้ร่มเงากว้างขวาง สร้างบรรยากาศร่มรื่นและเย็นสบายตลอดทั้งปี นักท่องเที่ยวสามารถนั่งพักผ่อน ปิกนิก หรือถ่ายภาพท่ามกลางธรรมชาติที่เขียวขจี นอกจากนี้ยังเป็นจุดแวะพักยอดนิยมของผู้ที่เดินทางไปยัง น้ำตกตาดหมอก และเส้นทางศึกษาธรรมชาติของอุทยาน บรรยากาศเงียบสงบ เหมาะสำหรับการพักผ่อนก่อนเดินทางต่อไปยังจุดหมายปลายทางอื่นภายในอุทยาน</p>
                          <ul class="info">
                            <li><i class="fa fa-user"></i>ผู้เข้าชม:&nbsp;628&nbsp;ครั้ง</li>
                            <li><i class="fa fa-globe"></i> อุทยานแห่งชาติตาดหมอก - Tat Mok National Park</li>
                            <!-- <li><i class="fa fa-home"></i> </li> -->
                          </ul>
                          <div class="text-button">
                            <a href="https://www.facebook.com/TatmokNationalPark">(ข้อมูลการติดต่อ)<i class="fa fa-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- test 4  -->
                <div class="col-lg-12">
                  <div class="item last-item">
                    <div class="row">
                      <div class="col-lg-4 col-sm-5">
                        <div class="image">
                          <img src="assets/images/ห้วยน้ำริน.png" width="229" height="234" alt="">
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-7">
                        <div class="right-content">
                          <h4>จุดชมวิวห้วยน้ำริน</h4>
                          <span>แหล่งท่องเที่ยวและจุดชมวิวที่สวยงาม</span>
                          <div class="main-button">
                            <a href="https://sci.pcru.ac.th/room/vr/napa/ห้วยน้ำริน/" traget="_blank">รับชม VR</a>
                          </div>
                          <p>เป็นสถานที่ท่องเที่ยวธรรมชาติที่ตั้งอยู่ในอำเภอเมือง จังหวัดเพชรบูรณ์ เป็นจุดชมวิวที่มีทิวทัศน์สวยงามของภูเขาและหุบเขา โดยเฉพาะในช่วงเช้าหรือเย็น นักท่องเที่ยวสามารถชมทะเลหมอกที่ปกคลุมทั่วบริเวณได้อย่างชัดเจน และสามารถถ่ายภาพธรรมชาติที่งดงามได้ที่นี่ นอกจากนี้ยังเป็นจุดที่เหมาะแก่การพักผ่อนและสัมผัสความสงบของธรรมชาติอย่างเต็มที่</p>
                          <ul class="info">
                            <li><i class="fa fa-user"></i>ผู้เข้าชม:&nbsp;628&nbsp;ครั้ง</li>
                            <li><i class="fa fa-globe"></i> อุทยานแห่งชาติตาดหมอก - Tat Mok National Park</li>
                            <!-- <li><i class="fa fa-home"></i> </li> -->
                          </ul>
                          <div class="text-button">
                            <a href="https://www.facebook.com/TatmokNationalPark">(ข้อมูลการติดต่อ)<i class="fa fa-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- test 4  -->
                <!-- <div class="col-lg-12">
                <ul class="page-numbers">
                  <li><a href="#"><i class="fa fa-arrow-left"></i></a></li>
                  <li><a href="#">1</a></li>
                  <li class="active"><a href="#">2</a></li>
                  <li><a href="#">3</a></li>
                  <li><a href="#"><i class="fa fa-arrow-right"></i></a></li>
                </ul>
              </div> -->
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="side-bar-map">
              <div class="row">
                <div class="col-lg-12">
                  <div id="map">
                    <img src="assets/images/เส้นทางการท่องเที่ยว.png" width="100%" height="100%" frameborder="0" allowfullscreen=""></img>
                    <style>
                      .side-bar-map {
                        transition: transform 0.2s;
                      }
                      .side-bar-map:hover {
                        transform: scale(1.05);
                      }
                    </style>
                    <!-- <iframe src="assets/images/เส้นทางง.png" width="100%" height="550px" frameborder="0" allowfullscreen=""></iframe> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
            <p> © 2025 kittipat Manuch Wichuta Kongpol &<a href="#" target="_blank"> Virtual Vista</a>
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

  </body>

  </html>