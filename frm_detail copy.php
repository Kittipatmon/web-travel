<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสถานที่ท่องเที่ยว</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #13547a 0%, #80d0c7 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .nav-menu {
            display: flex;
            gap: 30px;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            position: relative;
            padding: 5px 0;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: white;
            transition: width 0.3s ease;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .auth-buttons {
            display: flex;
            gap: 12px;
        }

        .auth-buttons button {
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signup-btn {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .signup-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .login-btn {
            background-color: #ffd166;
            color: #333;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-btn:hover {
            background-color: #ffbe0b;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 400px;
            overflow: hidden;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
        }

        .hero-content h1 {
            font-size: 36px;
            margin-bottom: 8px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 18px;
            max-width: 600px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .location-type {
            display: inline-block;
            background-color: #ef476f;
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 14px;
            margin-bottom: 12px;
        }

        /* Main Content */
        .main-content {
            margin: 40px auto;
        }

        .details-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .details-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .details-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #06d6a0;
            color: white;
            padding: 15px 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 25px;
        }

        .info-item {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background-color: #f1f9ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .info-text h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #555;
        }

        .info-text p {
            font-size: 18px;
            color: #333;
        }

        .description {
            line-height: 1.8;
            color: #555;
            margin-top: 20px;
            font-size: 16px;
        }

        .booking-card .card-header {
            background-color: #118ab2;
        }

        .pricing {
            margin-bottom: 25px;
        }

        .pricing-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #ddd;
        }

        .pricing-item:last-child {
            border-bottom: none;
        }

        .pricing-item-name {
            font-weight: 500;
        }

        .pricing-item-value {
            font-weight: 600;
            color: #118ab2;
        }

        .book-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #06d6a0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .book-btn:hover {
            background-color: #05c393;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 214, 160, 0.4);
        }

        .popular-times {
            margin-top: 30px;
        }

        .popular-times h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #555;
        }

        .times-container {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .time-slot {
            background-color: #f1f9ff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            color: #118ab2;
            font-weight: 500;
        }

        .gallery {
            margin-top: 50px;
        }

        .gallery h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .gallery h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #06d6a0;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .gallery-item {
            height: 200px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        @media (max-width: 900px) {
            .details-container {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                display: none;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero {
                height: 300px;
            }

            .hero-content h1 {
                font-size: 28px;
            }
        }
    </style>
    <style>
        .main-nav {
            display: flex;
            gap: 30px;
        }

        .main-nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            position: relative;
            padding: 5px 0;
        }

        .main-nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: white;
            transition: width 0.3s ease;
        }

        .main-nav a:hover::after {
            width: 100%;
        }

        .avatar-dropdown {
            position: relative;
            top: 25%;
            right: -80px;
            /* Adjusted to move further right */
            transform: translateY(-50%);
            display: inline-block;
            margin-left: 10px;
            /* ปรับตามระยะห่างที่ต้องการ */

        }

        .avatar-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            margin-right: 0px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            right: 0;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: rgb(42, 141, 240);
        }

        .avatar-dropdown:hover .dropdown-content {
            display: block;
        }

        @media (max-width: 992px) {
            .avatar-dropdown {
                position: static;
                transform: none;
                margin-top: 10px;
                float: none;
                text-align: center;
                display: contents;
            }

            .dropdown-content {
                position: static;
                box-shadow: none;
            }
        }

        @media (max-width: 768px) {
            .avatar-dropdown {
                position: static;
                transform: none;
                margin-top: 10px;
                float: none;
                text-align: center;
            }

            .dropdown-content {
                position: static;
                box-shadow: none;
            }
        }

        /* Back Button Styles */
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.85);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .back-button svg {
            width: 24px;
            height: 24px;
            fill: #13547a;
        }

        @media (max-width: 768px) {
            .back-button {
                width: 40px;
                height: 40px;
                top: 15px;
                left: 15px;
            }

            .back-button svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>

<body>
    <?php
    include "connect.php";

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int)$_GET['id']; // ตรวจสอบความปลอดภัย
        $stmt = $conn->prepare("SELECT * FROM uploads WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $upload = $stmt->get_result()->fetch_assoc();

        if (!$upload) {
            echo "<p>ไม่พบข้อมูลสถานที่ท่องเที่ยว</p>";
            exit;
        }
    } else {
        echo "<p>กรุณาเลือกสถานที่ท่องเที่ยว</p>";
        exit;
    }

    // ดึงข้อมูลสถานที่
    $spotResult = $conn->query("SELECT * FROM tourist_spot LIMIT 1");
    $spot = $spotResult->fetch_assoc();

    // ดึงข้อมูลช่วงเวลายอดนิยม
    $timeResult = $conn->query("SELECT * FROM popular_time");

    // ดึงข้อมูลบริการและค่าธรรมเนียม
    $feeResult = $conn->query("SELECT * FROM service_fee");

    // ดึงภาพสถานที่
    $imageResult = $conn->query("SELECT * FROM spot_image");
    ?>
    <!-- Back Button -->
    <a href="javascript:history.back()" class="back-button" title="กลับไปหน้าก่อนหน้า">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
        </svg>
    </a>

    <section class="hero">
        <img src="<?= htmlspecialchars($upload['image_path']) ?>" alt="<?= htmlspecialchars($upload['image_name']) ?>">
        <div class="hero-content">
            <span class="location-type"><?= htmlspecialchars($upload['title']) ?></span>
            <h1><?= htmlspecialchars($upload['image_name']) ?></h1>
            <p><?= htmlspecialchars($upload['subtitle']) ?></p>
        </div>
    </section>

    <div class="container main-content">
        <div class="details-container">
            <div class="details-card">
                <div class="card-header">รายละเอียดสถานที่</div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-text">
                            <h3>ที่อยู่</h3>
                            <p><?= htmlspecialchars($upload['contact']) ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">👥</div>
                        <div class="info-text">
                            <h3>จำนวนผู้เข้าชม</h3>
                            <p>ไม่จำกัด (แนะนำไม่เกิน 100 คนต่อรอบ)</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🏞️</div>
                        <div class="info-text">
                            <h3>ขนาดพื้นที่</h3>
                            <p>340 ตารางกิโลเมตร</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">⏰</div>
                        <div class="info-text">
                            <h3>เวลาทำการ</h3>
                            <p>เปิดทุกวัน 08:00 - 18:00 น.</p>
                        </div>
                    </div>

                    <div class="description">
                        <p><?= htmlspecialchars($upload['content']) ?></p>
                    </div>

                    <div class="popular-times">
                        <h3>ช่วงเวลายอดนิยม</h3>
                        <div class="times-container">
                            <span class="time-slot">09:00 - 11:00</span>
                            <span class="time-slot">15:00 - 17:00</span>
                            <span class="time-slot">วันเสาร์-อาทิตย์</span>
                            <span class="time-slot">ต.ค. - ธ.ค. (ฤดูท่องเที่ยว)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-card booking-card">
                <div class="card-header">บริการและค่าธรรมเนียม</div>
                <div class="card-body">
                    <div class="pricing">
                        <div class="pricing-item">
                            <span class="pricing-item-name">ค่าเข้าชม (ผู้ใหญ่)</span>
                            <span class="pricing-item-value">40 บาท/คน</span>
                        </div>
                        <div class="pricing-item">
                            <span class="pricing-item-name">ค่าเข้าชม (เด็ก)</span>
                            <span class="pricing-item-value">20 บาท/คน</span>
                        </div>
                        <div class="pricing-item">
                            <span class="pricing-item-name">บริการรถนำเที่ยว</span>
                            <span class="pricing-item-value">500 บาท/คัน</span>
                        </div>
                        <div class="pricing-item">
                            <span class="pricing-item-name">บริการไกด์นำเที่ยว</span>
                            <span class="pricing-item-value">300 บาท/กลุ่ม</span>
                        </div>
                        <div class="pricing-item">
                            <span class="pricing-item-name">บริการเต็นท์พัก</span>
                            <span class="pricing-item-value">250 บาท/หลัง/คืน</span>
                        </div>
                    </div>

                    <button class="book-btn">จองเข้าชมสถานที่</button>
                </div>
            </div>
        </div>

        <div class="gallery">
            <h2>ภาพจากสถานที่</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="/api/placeholder/300/200" alt="ภาพบรรยากาศ 1">
                </div>
                <div class="gallery-item">
                    <img src="/api/placeholder/300/200" alt="ภาพบรรยากาศ 2">
                </div>
                <div class="gallery-item">
                    <img src="/api/placeholder/300/200" alt="ภาพบรรยากาศ 3">
                </div>
                <div class="gallery-item">
                    <img src="/api/placeholder/300/200" alt="ภาพบรรยากาศ 4">
                </div>
            </div>
        </div>
    </div>

    <script>
        // If you need JavaScript for the back button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.back-button');
            backButton.addEventListener('click', function() {
                window.history.href = 'javascript:history.back()';
            });
        });
    </script>
</body>

</html>