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

    @media (min-width: 992px) {
        .header-area .main-nav .nav {
            display: flex !important;
            align-items: center !important;
            gap: 24px !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }
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
        display: block;
        visibility: hidden;
        opacity: 0;
        transform: translateY(6px);
        transition: opacity 0.22s ease, transform 0.22s ease, visibility 0.22s ease;
        pointer-events: none;
        position: absolute;
        background-color: #ffffff;
        min-width: 165px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
        border-radius: 12px;
        right: 0;
        top: 100%;
        margin-top: 8px;
        padding: 4px;
        z-index: 1050;
    }

    /* สะพานเชื่อมการ Hover (Invisible hover bridge) ป้องกันไม่ให้เมนูปิดเวลาเลื่อนเมาส์ลงมา */
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
        color: #333333 !important;
        padding: 12px 18px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        font-family: 'Prompt', sans-serif;
        border-radius: 8px;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .dropdown-content a:hover {
        background-color: #fff3e0;
        color: #ff991b !important;
    }

    .avatar-dropdown:hover .dropdown-content,
    .avatar-dropdown.show .dropdown-content {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* Hide mobile menu trigger on desktop */
    @media (min-width: 992px) {
        .header-area .main-nav .menu-trigger {
            display: none !important;
        }
    }

    /* Mobile Responsive Header Styles (<= 991px) */
    @media (max-width: 991px) {
        .header-area {
            height: 65px !important;
            min-height: 65px !important;
            max-height: 65px !important;
            padding: 0 !important;
            margin: 0 !important;
            position: sticky !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            background-color: #ff991b !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
            display: block !important;
            z-index: 1000 !important;
        }

        .header-area .container-fluid,
        .header-area .container,
        .header-area .row,
        .header-area .col-12 {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            position: static !important;
            height: 65px !important;
        }

        .header-area .main-nav {
            width: 100% !important;
            height: 65px !important;
            padding: 0 16px !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            position: relative !important;
            box-sizing: border-box !important;
            background: transparent !important;
            overflow: visible !important;
        }

        .header-area .main-nav .logo {
            position: static !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            z-index: 10002 !important;
        }

        .header-area .main-nav .logo img {
            height: 38px !important;
            width: auto !important;
            margin: 0 !important;
        }

        /* Hamburger button positioned on the right of header bar */
        .header-area .main-nav .menu-trigger {
            display: block !important;
            position: absolute !important;
            right: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            left: auto !important;
            width: 32px !important;
            height: 32px !important;
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer !important;
            z-index: 10002 !important;
            text-indent: -9999em;
        }

        .header-area .main-nav .menu-trigger span,
        .header-area .main-nav .menu-trigger span:before,
        .header-area .main-nav .menu-trigger span:after {
            background-color: #ffffff !important;
            display: block !important;
            position: absolute !important;
            width: 26px !important;
            height: 3px !important;
            border-radius: 3px !important;
            left: 3px !important;
            transition: all 0.25s ease-in-out !important;
        }

        .header-area .main-nav .menu-trigger span {
            top: 14px !important;
        }

        .header-area .main-nav .menu-trigger span:before {
            content: "" !important;
            top: -8px !important;
        }

        .header-area .main-nav .menu-trigger span:after {
            content: "" !important;
            top: 8px !important;
        }

        /* Hamburger transform to X when active */
        .header-area .main-nav .menu-trigger.active span {
            background-color: transparent !important;
        }

        .header-area .main-nav .menu-trigger.active span:before {
            top: 0 !important;
            transform: rotate(45deg) !important;
            background-color: #ffffff !important;
        }

        .header-area .main-nav .menu-trigger.active span:after {
            top: 0 !important;
            transform: rotate(-45deg) !important;
            background-color: #ffffff !important;
        }

        /* Mobile Dropdown Menu Card */
        .header-area .main-nav .nav {
            position: absolute !important;
            top: 65px !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            margin-top: 0 !important;
            padding: 8px 0 !important;
            background: #ffffff !important;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.12) !important;
            border-radius: 0 0 16px 16px !important;
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0 !important;
            display: none;
            z-index: 10000 !important;
            list-style: none !important;
            box-sizing: border-box !important;
            float: none !important;
        }

        .header-area.header-sticky .nav {
            margin-top: 0 !important;
        }

        .header-area .main-nav .nav li {
            width: 100% !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: transparent !important;
        }

        .header-area .main-nav .nav li:last-child {
            border-bottom: none !important;
        }

        .header-area .main-nav .nav li a.nav-link-item {
            width: 100% !important;
            height: 48px !important;
            line-height: 48px !important;
            display: flex !important;
            align-items: center !important;
            color: #334155 !important;
            padding: 0 20px !important;
            border-radius: 0 !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            gap: 12px !important;
            background: transparent !important;
            text-decoration: none !important;
            box-sizing: border-box !important;
            transition: background-color 0.2s ease, color 0.2s ease !important;
        }

        .header-area .main-nav .nav li a.nav-link-item img {
            width: 20px !important;
            height: 20px !important;
            object-fit: contain !important;
            margin: 0 !important;
        }

        .header-area .main-nav .nav li a.nav-link-item i {
            width: 20px !important;
            text-align: center !important;
            font-size: 16px !important;
        }

        .header-area .main-nav .nav li a.nav-link-item:hover,
        .header-area .main-nav .nav li a.nav-link-item.active {
            background-color: #fff7ed !important;
            color: #ff991b !important;
            box-shadow: none !important;
        }

        /* Profile Row & Submenu in Mobile Dropdown */
        .header-area .main-nav .nav li.avatar-li {
            border-bottom: none !important;
            padding: 0 !important;
        }

        .header-area .main-nav .nav .avatar-dropdown {
            width: 100% !important;
            margin: 0 !important;
            display: block !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .profile-btn {
            width: 100% !important;
            height: 48px !important;
            line-height: 48px !important;
            display: flex !important;
            align-items: center !important;
            color: #334155 !important;
            padding: 0 20px !important;
            border-radius: 0 !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            gap: 12px !important;
            background: transparent !important;
            border-bottom: 1px solid #f1f5f9 !important;
            box-sizing: border-box !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .profile-btn img {
            width: 24px !important;
            height: 24px !important;
            border-radius: 50% !important;
            border: 1.5px solid #ff991b !important;
            margin: 0 !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .dropdown-content {
            position: static !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: none !important;
            pointer-events: auto !important;
            background-color: #fafbfc !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border-top: none !important;
            width: 100% !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .dropdown-content::before {
            display: none !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .dropdown-content a {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 10px 20px 10px 48px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            text-decoration: none !important;
            box-sizing: border-box !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .dropdown-content a:last-child {
            border-bottom: none !important;
        }

        .header-area .main-nav .nav .avatar-dropdown .dropdown-content a:hover {
            background-color: #fff3e0 !important;
            color: #ff991b !important;
        }
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

                        <!-- Profile Dropdown wrapped inside li for semantic and responsive layout -->
                        <li class="avatar-li">
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
                        </li>
                    </ul>

                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>

<script>
(function() {
    function setupMobileNav() {
        var trigger = document.querySelector('.header-area .menu-trigger');
        var nav = document.querySelector('.header-area .main-nav .nav');
        if (!trigger || !nav) return;

        // If jQuery is available, unbind previous handlers to avoid multiple triggers
        if (window.jQuery) {
            window.jQuery(trigger).off('click');
        }

        // Click handler for hamburger / X button
        trigger.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();

            var willBeOpen = !trigger.classList.contains('active');
            if (willBeOpen) {
                trigger.classList.add('active');
                if (window.jQuery) {
                    window.jQuery(nav).stop(true, true).slideDown(200);
                } else {
                    nav.style.display = 'flex';
                }
            } else {
                trigger.classList.remove('active');
                if (window.jQuery) {
                    window.jQuery(nav).stop(true, true).slideUp(200);
                } else {
                    nav.style.display = 'none';
                }
            }
        };

        // Close dropdown when clicking anywhere outside
        document.addEventListener('click', function(e) {
            if (trigger.classList.contains('active')) {
                if (!trigger.contains(e.target) && !nav.contains(e.target)) {
                    trigger.classList.remove('active');
                    if (window.jQuery) {
                        window.jQuery(nav).stop(true, true).slideUp(200);
                    } else {
                        nav.style.display = 'none';
                    }
                }
            }
        });

        // Close dropdown when clicking any navigation link (on mobile)
        var navLinks = nav.querySelectorAll('a');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) {
                    trigger.classList.remove('active');
                    if (window.jQuery) {
                        window.jQuery(nav).stop(true, true).slideUp(200);
                    } else {
                        nav.style.display = 'none';
                    }
                }
            });
        });
        // Click-to-toggle for Profile dropdown on desktop
        var avatarDropdown = document.querySelector('.avatar-dropdown');
        var profileBtn = document.querySelector('.avatar-dropdown .profile-btn');
        if (avatarDropdown && profileBtn) {
            profileBtn.onclick = function(e) {
                if (window.innerWidth > 991) {
                    e.preventDefault();
                    e.stopPropagation();
                    avatarDropdown.classList.toggle('show');
                }
            };
            document.addEventListener('click', function(e) {
                if (!avatarDropdown.contains(e.target)) {
                    avatarDropdown.classList.remove('show');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMobileNav);
    } else {
        setupMobileNav();
    }

    // Re-bind after window load in case jQuery/custom.js loaded later
    window.addEventListener('load', setupMobileNav);
})();
</script>
