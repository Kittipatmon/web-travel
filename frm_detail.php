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
            background-color: #faf7f2;
            /* โทนอบอุ่นละมุนแบบหน้าหลัก/หน้าติดต่อ (#faf7f2) พร้อมคลื่นลวดลายแนวเทือกเขาและสายหมอกบางเบา สไตล์มินิมอล */
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(255, 153, 27, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(2, 132, 199, 0.04) 0%, transparent 45%),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 400' preserveAspectRatio='none' width='100%25' height='400'%3E%3Cpath d='M0,180 C320,120 540,240 860,170 C1140,110 1320,200 1440,160 L1440,400 L0,400 Z' fill='none' stroke='rgba(217, 119, 6, 0.04)' stroke-width='1.5'/%3E%3Cpath d='M0,230 C280,180 620,270 960,210 C1200,170 1360,220 1440,200 L1440,400 L0,400 Z' fill='none' stroke='rgba(217, 119, 6, 0.035)' stroke-width='1.5'/%3E%3Cpath d='M0,280 C380,240 680,310 1040,260 C1240,230 1360,270 1440,250 L1440,400 L0,400 Z' fill='none' stroke='rgba(2, 132, 199, 0.03)' stroke-width='1.5'/%3E%3C/svg%3E");
            background-repeat: repeat-y;
            background-size: 100% 400px;
            background-attachment: fixed;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ตกแต่งการ์ดเนื้อหาให้อ่านง่าย สบายตา สไตล์โมเดิร์นคลีน */
        .details-card {
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 8px 24px -2px rgba(0, 0, 0, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .details-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px -4px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .card-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 16px 24px;
            font-size: 19px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .booking-card .card-header {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
        }

        .gallery {
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 28px;
            box-shadow: 0 8px 24px -2px rgba(0, 0, 0, 0.04);
            margin-top: 35px;
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
            display: block;
            visibility: hidden;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.22s ease, transform 0.22s ease, visibility 0.22s ease;
            pointer-events: none;
            position: absolute;
            background-color: #ffffff;
            min-width: 160px;
            box-shadow: 0px 8px 20px 0px rgba(0, 0, 0, 0.18);
            border-radius: 10px;
            z-index: 1050;
            right: 0;
            top: 100%;
            margin-top: 8px;
            padding: 4px;
        }

        /* Invisible hover bridge */
        .dropdown-content::before {
            content: '';
            position: absolute;
            top: -14px;
            left: 0;
            right: 0;
            height: 14px;
            background: transparent;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-radius: 6px;
            transition: background-color 0.15s ease;
        }

        .dropdown-content a:hover {
            background-color: rgb(42, 141, 240);
            color: #ffffff;
        }

        .avatar-dropdown:hover .dropdown-content,
        .avatar-dropdown.show .dropdown-content {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
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

    if (isset($_GET['u_id']) && is_numeric($_GET['u_id'])) {
        $u_id = (int)$_GET['u_id']; // ตรวจสอบความปลอดภัย
        $stmt = $conn->prepare("SELECT * FROM uploads WHERE u_id = ?");
        $stmt->bind_param("i", $u_id);
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

    // ดึงข้อมูลสถานที่ที่ตรงกับ u_id
    $spotStmt = $conn->prepare("SELECT * FROM tourist_spot WHERE u_id = ?");
    $spotStmt->bind_param("i", $u_id);
    $spotStmt->execute();
    $spot = $spotStmt->get_result()->fetch_assoc();

    // ดึงข้อมูลช่วงเวลายอดนิยมที่ตรงกับ u_id
    $popularTimesStmt = $conn->prepare("SELECT time_period, day_period, season FROM tourist_spot WHERE u_id = ?");
    $popularTimesStmt->bind_param("i", $u_id);
    $popularTimesStmt->execute();
    $popularTimesResult = $popularTimesStmt->get_result();

    // ดึงข้อมูลบริการและค่าธรรมเนียมที่ตรงกับ u_id
    $feeStmt = $conn->prepare("SELECT service_name, fee, unit FROM service_fee WHERE u_id = ?");
    $feeStmt->bind_param("i", $u_id);
    $feeStmt->execute();
    $feeResult = $feeStmt->get_result();

    // ดึงข้อมูลภาพที่ตรงกับ u_id
    $imageStmt = $conn->prepare("SELECT filename FROM images WHERE u_id = ?");
    $imageStmt->bind_param("i", $u_id);
    $imageStmt->execute();
    $imageResult = $imageStmt->get_result();
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

                            <p>
                                <a href="<?= htmlspecialchars($upload['contact_link']) ?>" style="text-decoration: none;">
                                    <?= htmlspecialchars($upload['contact']) ?>&nbsp;&nbsp;&nbsp;(คลิกเพื่อดูข้อมูล)
                                </a>
                            </p>


                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">👥</div>
                        <div class="info-text">
                            <h3>จำนวนผู้เข้าชม</h3>
                            <p>
                                <?= isset($spot['visitor_limit']) && $spot['visitor_limit'] > 0 ? htmlspecialchars($spot['visitor_limit']) : 'ไม่ระบุ' ?>
                                (แนะนำไม่เกิน <?= isset($spot['recommended_visitors']) && $spot['recommended_visitors'] > 0 ? htmlspecialchars($spot['recommended_visitors']) : 'ไม่ระบุ' ?> คนต่อรอบ)
                            </p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">🏞️</div>
                        <div class="info-text">
                            <h3>ขนาดพื้นที่</h3>
                            <p>
                                <?= isset($spot['area']) && $spot['area'] > 0 ? htmlspecialchars($spot['area']) : 'ไม่ระบุ' ?>
                                ตารางกิโลเมตร
                            </p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">⏰</div>
                        <div class="info-text">
                            <h3>เวลาทำการ</h3>
                            <p>
                                <?= isset($spot['date_time']) ? htmlspecialchars($spot['date_time']) : 'ไม่ระบุ' ?>
                                <?= isset($spot['open_time']) ? htmlspecialchars($spot['open_time']) : 'ไม่ระบุ' ?> -
                                <?= isset($spot['close_time']) ? htmlspecialchars($spot['close_time']) : 'ไม่ระบุ' ?>
                            </p>
                        </div>
                    </div>

                    <div class="description">
                        <p><?= htmlspecialchars($upload['content']) ?></p>
                    </div>

                    <div class="popular-times">
                        <h3>ช่วงเวลายอดนิยม</h3>
                        <div class="times-container">
                            <?php if ($popularTimesResult && $popularTimesResult->num_rows > 0) { ?>
                                <?php while ($row = $popularTimesResult->fetch_assoc()) { ?>
                                    <?php if (!empty($row['time_period'])) { ?>
                                        <span class="time-slot"><?= htmlspecialchars($row['time_period']) ?></span>
                                    <?php } ?>
                                    <?php if (!empty($row['day_period'])) { ?>
                                        <span class="time-slot"><?= htmlspecialchars($row['day_period']) ?></span>
                                    <?php } ?>
                                    <?php if (!empty($row['season'])) { ?>
                                        <span class="time-slot"><?= htmlspecialchars($row['season']) ?></span>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <p>ไม่มีข้อมูลช่วงเวลายอดนิยม</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-card booking-card">
                <div class="card-header">บริการและค่าธรรมเนียม</div>
                <div class="card-body">
                    <div class="pricing">
                        <?php if ($feeResult && $feeResult->num_rows > 0) { ?>
                            <?php while ($row = $feeResult->fetch_assoc()) { ?>
                                <div class="pricing-item">
                                    <span class="pricing-item-name"><?= htmlspecialchars($row['service_name']) ?></span>
                                    <span class="pricing-item-value"><?= htmlspecialchars($row['fee']) ?> <?= htmlspecialchars($row['unit']) ?></span>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p>ไม่มีข้อมูลค่าธรรมเนียม</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="gallery">
            <h2>ภาพจากสถานที่</h2>
            <div class="gallery-grid">
                <?php if ($imageResult && $imageResult->num_rows > 0) { ?>
                    <?php while ($row = $imageResult->fetch_assoc()) { ?>
                        <div class="gallery-item">
                            <img src="<?= htmlspecialchars('uploads/' . $row['filename']) ?>" alt="<?= htmlspecialchars($row['filename']) ?>">
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>ไม่มีภาพจากสถานที่</p>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php
    $conn->close();
    ?>

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