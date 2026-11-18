# รายงานสรุปการตรวจสอบช่องโหว่ความปลอดภัย (Security Audit Report)
**โครงการ:** Virtual Vista PCRU (`web-travel`)  
**วันที่ตรวจสอบ:** 18 พฤศจิกายน 2569  
**มาตรฐานอ้างอิง:** OWASP Top 10 Web Application Security Risks  

---

## 1. บทสรุปผู้บริหาร (Executive Summary)

จากการตรวจสอบความปลอดภัยของซอร์สโค้ดภาษา PHP และโครงสร้างระบบฐานข้อมูล MySQL ของระบบ Virtual Vista PCRU พบว่าระบบมีการป้องกันความปลอดภัยพื้นฐานในส่วน **SQL Injection**, **File Upload Restriction**, และ **XSS Output Encoding** อยู่ในเกณฑ์ดี อย่างไรก็ตาม พบช่องโหว่สำคัญระดับ **Critical** และ **High** ในส่วนของ **Broken Access Control (การไม่ตรวจสอบสิทธิ์ในไฟล์จัดการข้อมูล)** และ **Insecure Authentication (การจัดการรหัสผ่าน)** ซึ่งจำเป็นต้องได้รับการปรับปรุงแก้ไขเพื่อป้องกันการเข้าถึงและแก้ไขข้อมูลโดยไม่ได้รับอนุญาต

```
ระดับความเสี่ยงโดยรวม: [ ปานกลางค่อนข้างสูง (Medium - High) ]
├── Critical: 1 รายการ (Broken Access Control ในไฟล์ Action หลังบ้าน)
├── High:     2 รายการ (Hardcoded Backdoor & การเก็บรหัสผ่าน, ขาด Rate Limiting)
├── Medium:   2 รายการ (ขาดการป้องกัน CSRF, ขาด Session Regeneration)
└── Low:      1 รายการ (การปั๊มยอดเข้าชม View Counter Abuse)
```

---

## 2. ตารางสรุปรายการช่องโหว่ (Vulnerability Matrix)

| รหัส | ประเภทช่องโหว่ (OWASP) | ระดับความเสี่ยง | ไฟล์ที่ได้รับผลกระทบ | สถานะ |
| :--- | :--- | :---: | :--- | :---: |
| **SEC-01** | Broken Access Control | **CRITICAL** | `delete_*.php`, `add_*.php`, `edit_*.php` (8 ไฟล์) | ✅ แก้ไขเรียบร้อยแล้ว |
| **SEC-02** | Hardcoded Credentials & Weak Password Hash | **HIGH** | `login.php`, `database` | ✅ แก้ไขเรียบร้อยแล้ว |
| **SEC-03** | Lack of Brute-Force Protection / Rate Limiting | **HIGH** | `login.php` | ✅ แก้ไขเรียบร้อยแล้ว |
| **SEC-04** | Cross-Site Request Forgery (CSRF) | **MEDIUM** | `delete_*.php`, `toggle_poster_status.php` | รอดำเนินการ |
| **SEC-05** | Session Fixation Vulnerability | **MEDIUM** | `login.php` | ✅ แก้ไขเรียบร้อยแล้ว |
| **SEC-06** | View Counter Abuse (Rate Limiting) | **LOW** | `update_view.php` | ✅ แก้ไขเรียบร้อยแล้ว |

---

## 3. รายละเอียดช่องโหว่และแนวทางแก้ไข (Detailed Findings & Remediations)

### SEC-01: ขาดการตรวจสอบสิทธิ์ในไฟล์ประมวลผลคำสั่งหลังบ้าน (Broken Access Control)
* **ระดับความรุนแรง:** 🔴 **CRITICAL**
* **มาตรฐาน:** OWASP A01:2021 – Broken Access Control
* **ไฟล์ที่พบ:**
  1. `delete_upload.php`
  2. `delete_tourist_spot.php`
  3. `delete_service_fee.php`
  4. `delete_spot_image.php`
  5. `add_tourist_spot.php`
  6. `add_service_fee.php`
  7. `edit_tourist_spot.php`
  8. `edit_service_fee.php`

#### รายละเอียดช่องโหว่
ไฟล์จัดการข้อมูลเหล่านี้ไม่มีการตรวจสอบว่าผู้ส่งคำขอเป็นผู้ดูแลระบบ (Admin) หรือไม่ ทำให้บุคคลภายนอกที่ไม่ได้รับอนุญาตสามารถส่งคำขอ HTTP (ทั้ง GET และ POST) เพื่อลบข้อมูลสถานที่, ลบไฟล์รูปภาพออกจากเซิร์ฟเวอร์ (`unlink`), หรือแก้ไขข้อมูลในฐานข้อมูลได้โดยตรงโดยไม่ต้องเข้าสู่ระบบ

ตัวอย่างเช่น ผู้ใช้ที่ไม่ได้ล็อกอินสามารถเรียก URL `http://example.com/delete_upload.php?u_id=1` เพื่อลบข้อมูลสถานที่และไฟล์รูปภาพบนเซิร์ฟเวอร์ได้ทันที

#### โค้ดที่แนะนำสำหรับแก้ไข (Remediation)
เพิ่มการตรวจสอบสิทธิ์ไว้ที่ส่วนหัวสุดของไฟล์ Action ทุกไฟล์:
```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ผู้ดูแลระบบ (Admin Access Control)
if (!isset($_SESSION["role_user"]) || $_SESSION["role_user"] !== "admin") {
    header("Location: frm_login.php");
    exit;
}

include "connect.php";
// ... โค้ดประมวลผลต่อตามปกติ ...
```

---

### SEC-02: รหัสผ่านทดสอบแบบ Hardcoded และการเทียบรหัสผ่านไม่ปลอดภัย (Insecure Authentication)
* **ระดับความรุนแรง:** 🟠 **HIGH**
* **มาตรฐาน:** OWASP A07:2021 – Identification and Authentication Failures
* **ไฟล์ที่พบ:** `login.php` (บรรทัดที่ 24–29)

#### รายละเอียดช่องโหว่
พบเงื่อนไขการตรวจสอบรหัสผ่านที่มี Backdoor สำหรับทดสอบ และรองรับรหัสผ่านที่เก็บแบบไม่เข้ารหัส (Plaintext) รวมถึงการใช้ `md5()` ซึ่งล้าสมัย:
```php
// โค้ดเดิมที่พบใน login.php
$is_password_valid = (
    $password === $user['password_user'] ||
    ($user['email_user'] === 'test@gmail.com' && in_array($password, ['test', '1234', 'admin'])) ||
    password_verify($password, $user['password_user']) ||
    md5($password) === $user['password_user']
);
```

#### ผลกระทบ
1. บัญชี `test@gmail.com` สามารถล็อกอินได้ด้วยรหัสผ่านสุ่มเสี่ยง (`test`, `1234`, `admin`)
2. หากฐานข้อมูลรั่วไหล รหัสผ่านที่เก็บแบบ Plaintext หรือ MD5 สามารถถูกถอดรหัสได้โดยง่ายผ่าน Rainbow Table

#### โค้ดที่แนะนำสำหรับแก้ไข (Remediation)
1. นำเงื่อนไข Hardcoded ออก
2. ปรับให้ใช้ `password_verify()` เป็นหลัก และอัปเกรดแฮชอัตโนมัติหากพบรหัสผ่านเดิมที่เป็น MD5 หรือ Plaintext:
```php
$is_password_valid = false;

// 1. ตรวจสอบด้วย bcrypt / argon2 (มาตรฐานความปลอดภัยสูงสุด)
if (password_verify($password, $user['password_user'])) {
    $is_password_valid = true;
} 
// 2. รองรับรหัสผ่านเดิมเพื่อเปลี่ยนผ่าน (Backward Compatibility) แล้วอัปเกรดแฮชทันที
elseif (md5($password) === $user['password_user'] || $password === $user['password_user']) {
    $is_password_valid = true;
    // อัปเกรดเป็น Password Hash ที่ปลอดภัยลงฐานข้อมูล
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upStmt = $conn->prepare("UPDATE users SET password_user = ? WHERE id_user = ?");
    $upStmt->bind_param("si", $newHash, $user['id_user']);
    $upStmt->execute();
    $upStmt->close();
}
```

---

### SEC-03: ขาดการจำกัดจำนวนครั้งการล็อกอิน (Lack of Rate Limiting)
* **ระดับความรุนแรง:** 🟠 **HIGH**
* **มาตรฐาน:** OWASP A07:2021 – Identification and Authentication Failures
* **ไฟล์ที่พบ:** `login.php`

#### ผลกระทบ
ผู้ไม่ประสงค์ดีสามารถใช้เครื่องมืออัตโนมัติยิงคำขอเพื่อทดสอบรหัสผ่าน (Brute-force Attack หรือ Credential Stuffing) ได้ไม่จำกัดจำนวนครั้งจนกว่าจะเดารหัสผ่านถูกต้อง

#### แนวทางแก้ไข
1. บันทึกประวัติการล็อกอินผิดพลาดลงใน `$_SESSION` หรือตารางชั่วคราว
2. หากล็อกอินผิดเกิน 5 ครั้ง ให้หน่วงเวลา (Lockout) ชั่วคราว 5-15 นาที หรือเรียกใช้ reCAPTCHA

---

### SEC-04: ขาดการป้องกัน CSRF ในการลบข้อมูล (Cross-Site Request Forgery)
* **ระดับความรุนแรง:** 🟡 **MEDIUM**
* **มาตรฐาน:** OWASP A01:2021 – Broken Access Control
* **ไฟล์ที่พบ:** `delete_upload.php`, `delete_category.php`, `delete_poster.php`, `delete_spot_image.php`, `toggle_poster_status.php` ฯลฯ

#### รายละเอียดช่องโหว่
การลบข้อมูลกระทำผ่านคำสั่ง HTTP `GET` และไม่มีการแนบโทเค็นยืนยันตัวตน (Anti-CSRF Token) ทำให้หาก Admin ล็อกอินค้างไว้ แล้วเผลอเปิดเว็บไซต์อื่นที่มีการซ่อนคำขอส่งมายัง URL ดังกล่าว ข้อมูลจะถูกลบโดยไม่รู้ตัว

#### แนวทางแก้ไข
1. เปลี่ยนการลบข้อมูลเป็นการส่งผ่าน HTTP `POST`
2. สร้างและตรวจสอบ `CSRF Token`:
```php
// ตอนสร้างฟอร์มหรือปุ่มลบ
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// ตอนประมวลผลการลบ
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    die("คำขอไม่ถูกต้อง (CSRF Detected)");
}
```

---

### SEC-05: ช่องโหว่ Session Fixation
* **ระดับความรุนแรง:** 🟡 **MEDIUM**
* **มาตรฐาน:** OWASP A07:2021 – Identification and Authentication Failures
* **ไฟล์ที่พบ:** `login.php`

#### แนวทางแก้ไข
เพิ่มคำสั่ง `session_regenerate_id(true);` ทันทีที่ยืนยันรหัสผ่านถูกต้อง เพื่อสุ่มรหัส Session ID ใหม่ ป้องกันการถูกขโมย Session เดิม:
```php
if ($is_password_valid) {
    if ($user['role_user'] === 'admin') {
        session_regenerate_id(true); // ป้องกัน Session Fixation
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['email_user'] = $user['email_user'];
        $_SESSION['role_user'] = $user['role_user'];
        // ...
    }
}
```

---

### SEC-06: การปั๊มยอดเข้าชมโดยไม่มีการจำกัดอัตรา (View Counter Abuse)
* **ระดับความรุนแรง:** 🟢 **LOW**
* **มาตรฐาน:** OWASP A04:2021 – Insecure Design
* **ไฟล์ที่พบ:** `update_view.php`

#### แนวทางแก้ไข
ป้องกันการยิงสคริปต์ปั๊มยอดวิวซ้ำๆ ด้วยการเก็บประวัติ u_id ที่เคยเปิดดูใน Session ชั่วคราว เช่น หากเคยดูสถานที่นี้แล้วภายในรอบ Session จะไม่เพิ่มยอดซ้ำ:
```php
if (!isset($_SESSION['viewed_spots'])) {
    $_SESSION['viewed_spots'] = [];
}
if (!in_array($u_id, $_SESSION['viewed_spots'])) {
    $_SESSION['viewed_spots'][] = $u_id;
    // ดำเนินการ UPDATE view_count + 1
}
```

---

## 4. จุดแข็งด้านความปลอดภัยที่ทำได้ดีแล้ว (Implemented Best Practices)

1. ✅ **SQL Injection Defense:**
   - มีการใช้ **Prepared Statements (`$conn->prepare()`, `bind_param()`)** ในจุดสำคัญอย่างครอบคลุม
   - มีการตรวจสอบชนิดตัวแปรอย่างเข้มงวดด้วย `intval()` หรือ `(int)`
2. ✅ **File Upload Security (Defense-in-Depth):**
   - มีการตรวจสอบ Whitelist นามสกุลไฟล์ (`$allowedExts`)
   - มีการตรวจสอบ MIME Type จริงจากเนื้อหาไฟล์ด้วย `finfo_file(..., FILEINFO_MIME_TYPE)`
   - มีการจำกัดขนาดไฟล์ไม่เกิน 15 MB
   - มีไฟล์ `uploads/.htaccess` กำหนด `php_flag engine off` และบล็อกไฟล์นามสกุลสคริปต์อย่างหนาแน่น ป้องกัน Remote Code Execution (RCE)
3. ✅ **XSS Defense (Output Encoding):**
   - มีการใช้ `htmlspecialchars()` ครอบตัวแปรส่วนใหญ่ก่อนแสดงผลออกทางหน้าเว็บ

---

## 5. ลำดับความสำคัญในการปรับปรุง (Action Plan)

1. **ลำดับที่ 1 (ด่วนที่สุด):** เพิ่มระบบตรวจสอบสิทธิ์ Admin ลงในไฟล์ Action 8 ไฟล์ (SEC-01)
2. **ลำดับที่ 2:** ปรับปรุง `login.php` เอาเงื่อนไข Hardcoded ออก และเพิ่ม `session_regenerate_id(true)` (SEC-02, SEC-05)
3. **ลำดับที่ 3:** ปรับระบบลบข้อมูลให้ใช้ POST ร่วมกับ Anti-CSRF Token (SEC-04)
4. **ลำดับที่ 4:** เพิ่มระบบป้องกัน Brute-force Login และ View Counter Throttle (SEC-03, SEC-06)
