<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
/* ===================================================
   MODERN ADMINLTE SIDEBAR & NAVBAR SYSTEM
   =================================================== */

/* Main Sidebar Container - Dark Navy / Slate Modern Theme */
.main-sidebar {
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.25) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
    z-index: 1040 !important;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-left 0.3s ease-in-out !important;
}

/* Brand Logo Area */
.main-sidebar .brand-link {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding: 18px 14px !important;
    background: rgba(15, 23, 42, 0.7) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    overflow: hidden !important;
    transition: padding 0.3s ease !important;
}
.main-sidebar .brand-link img {
    max-height: 44px !important;
    width: auto !important;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4)) !important;
    transition: transform 0.3s ease, max-height 0.3s ease, max-width 0.3s ease !important;
}
.main-sidebar .brand-link:hover img {
    transform: scale(1.04);
}

/* Hide legacy inline HRs & old redundant logout link */
.main-sidebar hr.hr-white,
.main-sidebar hr {
    display: none !important;
}
.main-sidebar div[align="center"] {
    display: none !important;
}

/* Sidebar Body */
.main-sidebar .sidebar {
    padding: 14px 10px 30px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

/* Sidebar Section Headers */
.sidebar-nav-header {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    color: #94a3b8 !important;
    padding: 14px 12px 6px !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    white-space: nowrap !important;
    transition: opacity 0.2s ease !important;
}

/* Nav Menu Links */
.sidebar-modern-menu {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 0 12px 0 !important;
}

.sidebar-modern-item {
    margin-bottom: 5px !important;
}

.sidebar-modern-link {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 10px 14px !important;
    border-radius: 10px !important;
    color: #cbd5e1 !important;
    text-decoration: none !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: transparent;
    border: 1px solid transparent;
    white-space: nowrap !important;
}

.sidebar-modern-link i.nav-icon {
    font-size: 17px !important;
    width: 24px !important;
    text-align: center !important;
    color: #94a3b8 !important;
    transition: all 0.2s ease !important;
    flex-shrink: 0 !important;
}

.sidebar-modern-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    transform: translateX(3px);
}

.sidebar-modern-link:hover i.nav-icon {
    color: #38bdf8 !important;
}

/* Active Menu Item */
.sidebar-modern-link.active {
    color: #ffffff !important;
    background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%) !important;
    border-color: rgba(255, 255, 255, 0.18) !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4) !important;
    font-weight: 600 !important;
}

.sidebar-modern-link.active i.nav-icon {
    color: #ffffff !important;
}

/* Bottom Action Buttons */
.sidebar-bottom-actions {
    margin-top: 20px !important;
    padding-top: 14px !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}

.sidebar-action-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 14px !important;
    border-radius: 10px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
}

.sidebar-action-btn i {
    font-size: 15px !important;
    flex-shrink: 0 !important;
}

.sidebar-action-btn.btn-web {
    color: #38bdf8 !important;
    background: rgba(56, 189, 248, 0.1) !important;
    border: 1px solid rgba(56, 189, 248, 0.25) !important;
}
.sidebar-action-btn.btn-web:hover {
    background: rgba(56, 189, 248, 0.22) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.sidebar-action-btn.btn-logout {
    color: #f87171 !important;
    background: rgba(239, 68, 68, 0.1) !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;
}
.sidebar-action-btn.btn-logout:hover {
    background: #ef4444 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35) !important;
}

/* ===================================================
   MINI / COLLAPSE MODE (พับเก็บ - แสดงเฉพาะไอคอน)
   =================================================== */

/* ซ่อนตัวหนังสือทั้งหมดเมื่อพับ Sidebar */
body.sidebar-collapse .sidebar-nav-header,
body.sidebar-mini.sidebar-collapse .sidebar-nav-header,
body.sidebar-mini-md.sidebar-collapse .sidebar-nav-header {
    display: none !important;
}

body.sidebar-collapse .sidebar-modern-link span,
body.sidebar-mini.sidebar-collapse .sidebar-modern-link span,
body.sidebar-mini-md.sidebar-collapse .sidebar-modern-link span,
body.sidebar-collapse .sidebar-action-btn span,
body.sidebar-mini.sidebar-collapse .sidebar-action-btn span,
body.sidebar-mini-md.sidebar-collapse .sidebar-action-btn span {
    display: none !important;
}

/* จัดไอคอนให้อยู่กึ่งกลางพอดีสวยงาม */
body.sidebar-collapse .sidebar-modern-link,
body.sidebar-mini.sidebar-collapse .sidebar-modern-link,
body.sidebar-mini-md.sidebar-collapse .sidebar-modern-link {
    justify-content: center !important;
    padding: 12px 0 !important;
    width: 100% !important;
    border-radius: 10px !important;
    text-align: center !important;
    transform: none !important;
}

body.sidebar-collapse .sidebar-modern-link i.nav-icon,
body.sidebar-mini.sidebar-collapse .sidebar-modern-link i.nav-icon,
body.sidebar-mini-md.sidebar-collapse .sidebar-modern-link i.nav-icon {
    font-size: 20px !important;
    width: auto !important;
    margin: 0 !important;
    display: inline-block !important;
}

body.sidebar-collapse .sidebar-action-btn,
body.sidebar-mini.sidebar-collapse .sidebar-action-btn,
body.sidebar-mini-md.sidebar-collapse .sidebar-action-btn {
    justify-content: center !important;
    padding: 11px 0 !important;
    width: 100% !important;
    transform: none !important;
}

body.sidebar-collapse .sidebar-action-btn i,
body.sidebar-mini.sidebar-collapse .sidebar-action-btn i,
body.sidebar-mini-md.sidebar-collapse .sidebar-action-btn i {
    font-size: 18px !important;
    margin: 0 !important;
}

/* ย่อขนาดโลโก้ในโหมดพับ */
body.sidebar-collapse .main-sidebar .brand-link,
body.sidebar-mini.sidebar-collapse .main-sidebar .brand-link,
body.sidebar-mini-md.sidebar-collapse .main-sidebar .brand-link {
    padding: 14px 6px !important;
}

body.sidebar-collapse .main-sidebar .brand-link img,
body.sidebar-mini.sidebar-collapse .main-sidebar .brand-link img,
body.sidebar-mini-md.sidebar-collapse .main-sidebar .brand-link img {
    max-height: 26px !important;
    max-width: 36px !important;
    object-fit: contain !important;
}

/* กำหนดขนาดกว้าง Mini Sidebar */
body.sidebar-collapse .main-sidebar,
body.sidebar-mini.sidebar-collapse .main-sidebar,
body.sidebar-mini-md.sidebar-collapse .main-sidebar {
    width: 4.6rem !important;
}

/* ===================================================
   LAYOUT MARGINS & OVERLAP PREVENTION
   =================================================== */

.content-wrapper {
    background-color: #f8fafc !important;
    min-height: calc(100vh - 57px) !important;
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.main-header.navbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
    padding: 10px 24px !important;
    z-index: 1030 !important;
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.main-header .nav-link {
    color: #475569 !important;
    font-weight: 500 !important;
    font-size: 14px !important;
    padding: 8px 14px !important;
    border-radius: 8px !important;
    transition: all 0.2s !important;
}

.main-header .nav-link:hover {
    color: #0284c7 !important;
    background: #f1f5f9 !important;
}

@media (min-width: 768px) {
    /* ตอนกางออกปกติ */
    body:not(.sidebar-collapse) .content-wrapper,
    body:not(.sidebar-collapse) .main-header {
        margin-left: 250px !important;
    }

    /* ตอนพับเก็บ (Mini) ให้เนื้อหาขยับตามระยะ 4.6rem พอดีเป๊ะ ไม่ทับ */
    body.sidebar-collapse .content-wrapper,
    body.sidebar-collapse .main-header {
        margin-left: 4.6rem !important;
    }
}
</style>

<div class="sidebar">
    <nav class="mt-2">
        <!-- 1. หมวดหมู่สถิติและภาพรวม -->
        <div class="sidebar-nav-header">
            <i class="fas fa-chart-bar text-info" style="font-size: 12px;"></i> สถิติและภาพรวม
        </div>
        <ul class="sidebar-modern-menu">
            <li class="sidebar-modern-item">
                <a href="dashboardmain.php" class="sidebar-modern-link <?= ($currentPage == 'dashboardmain.php') ? 'active' : '' ?>" title="แดชบอร์ดสถิติ">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <span>แดชบอร์ดสถิติ</span>
                </a>
            </li>
        </ul>

        <!-- 2. หมวดหมู่จัดการสถานที่ท่องเที่ยว -->
        <div class="sidebar-nav-header">
            <i class="fas fa-map-marked-alt text-success" style="font-size: 12px;"></i> จัดการสถานที่ท่องเที่ยว
        </div>
        <ul class="sidebar-modern-menu">
            <li class="sidebar-modern-item">
                <a href="dashboard_pic.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_pic.php') ? 'active' : '' ?>" title="รายการอัปโหลด">
                    <i class="nav-icon fas fa-images"></i>
                    <span>รายการอัปโหลด</span>
                </a>
            </li>
            <li class="sidebar-modern-item">
                <a href="dashboard_category.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_category.php') ? 'active' : '' ?>" title="จัดการหมวดหมู่">
                    <i class="nav-icon fas fa-tags"></i>
                    <span>จัดการหมวดหมู่</span>
                </a>
            </li>
            <li class="sidebar-modern-item">
                <a href="dashboard_poster.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_poster.php') ? 'active' : '' ?>" title="จัดการโปสเตอร์โปรโมท">
                    <i class="nav-icon fas fa-bullhorn"></i>
                    <span>จัดการโปสเตอร์โปรโมท</span>
                </a>
            </li>
            <li class="sidebar-modern-item">
                <a href="dashboard_detail.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_detail.php') ? 'active' : '' ?>" title="รายละเอียดสถานที่">
                    <i class="nav-icon fas fa-map-marker-alt"></i>
                    <span>รายละเอียดสถานที่</span>
                </a>
            </li>
            <li class="sidebar-modern-item">
                <a href="dashboard_service_fee.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_service_fee.php') ? 'active' : '' ?>" title="รายละเอียดค่าบริการ">
                    <i class="nav-icon fas fa-receipt"></i>
                    <span>รายละเอียดค่าบริการ</span>
                </a>
            </li>
            <li class="sidebar-modern-item">
                <a href="dashboard_spot_image.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_spot_image.php') ? 'active' : '' ?>" title="รูปภาพจุดท่องเที่ยว">
                    <i class="nav-icon fas fa-camera-retro"></i>
                    <span>รูปภาพจุดท่องเที่ยว</span>
                </a>
            </li>
            <li class="sidebar-modern-item">
                <a href="dashboard_footer.php" class="sidebar-modern-link <?= ($currentPage == 'dashboard_footer.php') ? 'active' : '' ?>" title="จัดการ Footer">
                    <i class="nav-icon fas fa-shoe-prints"></i>
                    <span>จัดการ Footer</span>
                </a>
            </li>
        </ul>

        <!-- 3. ทางลัดไปยังหน้าเว็บ & ออกจากระบบ -->
        <div class="sidebar-bottom-actions">
            <a href="index.php" target="_blank" class="sidebar-action-btn btn-web" title="ไปหน้าเว็บไซต์หลัก">
                <i class="fas fa-external-link-alt"></i>
                <span>ไปหน้าเว็บไซต์หลัก</span>
            </a>
            <a href="logout.php" class="sidebar-action-btn btn-logout" title="ออกจากระบบ">
                <i class="fas fa-sign-out-alt"></i>
                <span>ออกจากระบบ</span>
            </a>
        </div>
    </nav>
</div>
