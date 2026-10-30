<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- Original Theme CSS Files -->
<link rel="stylesheet" href="assets/css/travel.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/modern-travel.css">

<style>
    /* Reset html and body margin & padding so header sticks flush to the very top edge */
    html, body {
        margin: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* Exact Header from Image 2 & Image 1 */
    .header-area {
        background-color: #ff991b !important;
        height: 65px !important;
        position: sticky !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 1000 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        padding: 0 !important;
        margin: 0 !important;
    }

    .header-area .main-nav {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        height: 65px !important;
        background: transparent !important;
        width: 100%;
        margin: 0 !important;
        padding: 0 15px !important;
    }

    .header-area .main-nav .logo {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .header-area .main-nav .logo img {
        height: 42px;
        width: auto;
        object-fit: contain;
    }

    .header-area .main-nav .nav {
        display: flex !important;
        align-items: center !important;
        gap: 24px !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .header-area .main-nav .nav li {
        display: inline-flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .header-area .main-nav .nav li a.nav-link-item {
        color: #ffffff !important;
        font-family: 'Prompt', 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 30px;
        transition: all 0.2s ease;
    }

    .header-area .main-nav .nav li a.nav-link-item:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Active Capsule Pill like in Image 1 */
    .header-area .main-nav .nav li a.nav-link-item.active {
        background-color: rgba(255, 255, 255, 0.35) !important;
        color: #ffffff !important;
        padding: 6px 20px;
        border-radius: 50px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    /* Remove legacy top dash/line above icon on active menu */
    .header-area .main-nav .nav li a::after,
    .header-area .main-nav .nav li a::before,
    .header-area .main-nav .nav li a.active::after,
    .header-area .main-nav .nav li a.active::before {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
    }

    .header-area .main-nav .nav li a.nav-link-item img {
        width: 20px;
        height: 20px;
        object-fit: contain;
        margin: 0 !important;
        display: block;
    }

    /* Profile Avatar dropdown in Header */
    .avatar-dropdown {
        position: relative;
        display: inline-flex;
        align-items: center;
        margin-left: 4px;
    }

    .avatar-dropdown .profile-btn {
        color: #ffffff !important;
        font-family: 'Prompt', 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 10px;
        border-radius: 30px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .avatar-dropdown .profile-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .avatar-dropdown .profile-btn img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        object-fit: cover;
        margin: 0 !important;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #ffffff;
        min-width: 160px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        right: 0;
        top: 100%;
        margin-top: 8px;
        z-index: 1050;
        overflow: hidden;
    }

    .dropdown-content a {
        color: #333333 !important;
        padding: 12px 18px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        font-family: 'Prompt', sans-serif;
    }

    .dropdown-content a:hover {
        background-color: #fff3e0;
        color: #ff991b !important;
    }

    .avatar-dropdown:hover .dropdown-content {
        display: block;
    }
</style>

<header class="header-area header-sticky">
    <div class="container-fluid px-lg-5">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo (Left) ***** -->
                    <a href="index.php" class="logo">
                        <img src="assets/images/logovr.png" alt="Virtual Vista pcru.">
                    </a>

                    <!-- ***** Menu Items (Right) matching Image 2 & Image 1 ***** -->
                    <ul class="nav">
                        <li>
                            <a href="index.php" class="nav-link-item <?= ($current_page === 'index.php') ? 'active' : '' ?>">
                                <img src="assets/images/home.png" alt="Home"> Home
                            </a>
                        </li>
                        <li>
                            <a href="all_destinations.php" class="nav-link-item <?= ($current_page === 'all_destinations.php') ? 'active' : '' ?>">
                                <i class="fas fa-th-large" style="font-size: 16px;"></i> ดูทั้งหมด
                            </a>
                        </li>
                        <li>
                            <a href="contact.php" class="nav-link-item <?= ($current_page === 'contact.php') ? 'active' : '' ?>">
                                <img src="assets/images/contact.png" alt="Contact Us"> Contact Us
                            </a>
                        </li>

                        <!-- Profile Dropdown -->
                        <div class="avatar-dropdown">
                            <div class="profile-btn">
                                <img src="assets/images/avatra.png" alt="Profile"> Profile
                            </div>
                            <div class="dropdown-content">
                                <?php if (isset($_SESSION['role_user']) && $_SESSION['role_user'] === 'admin'): ?>
                                    <a href="dashboardmain.php"><i class="fas fa-chart-pie text-warning"></i> แดชบอร์ด</a>
                                    <a href="logout.php"><i class="fas fa-sign-out-alt text-danger"></i> ออกจากระบบ</a>
                                <?php else: ?>
                                    <a href="frm_login.php"><i class="fas fa-sign-in-alt text-warning"></i> เข้าสู่ระบบ</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </ul>

                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>
