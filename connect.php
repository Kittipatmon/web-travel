<?php
// ตั้งค่าเขตเวลาประเทศไทย (Asia/Bangkok)
date_default_timezone_set('Asia/Bangkok');

// กำหนดค่าการเชื่อมต่อฐานข้อมูล (รองรับทั้ง Local XAMPP และเมื่ออัปโหลดขึ้น Web Hosting)
$server = getenv('DB_HOST') ?: "localhost";
$user   = getenv('DB_USER') ?: "root";
$pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ?: "travel";

$conn = mysqli_connect($server, $user, $pass, $dbname);

if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว (Connection failed): " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
@mysqli_query($conn, "SET time_zone = '+07:00'");

// ฟังก์ชันแปลงวันที่และเวลาเป็นภาษาไทย (พ.ศ.)
if (!function_exists('thai_date_format')) {
    function thai_date_format($datetime = null, $showTime = true) {
        $timestamp = $datetime ? (is_numeric($datetime) ? (int)$datetime : strtotime($datetime)) : time();
        if (!$timestamp) return '-';
        
        $thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        
        $dayOfWeek = $thaiDays[date('w', $timestamp)];
        $d = date('j', $timestamp);
        $m = $thaiMonths[(int)date('n', $timestamp)];
        $y = (int)date('Y', $timestamp) + 543;
        
        $dateStr = "วัน{$dayOfWeek}ที่ {$d} {$m} {$y}";
        if ($showTime) {
            $t = date('H:i:s', $timestamp);
            return "{$dateStr} เวลา {$t} น.";
        }
        return $dateStr;
    }
}
