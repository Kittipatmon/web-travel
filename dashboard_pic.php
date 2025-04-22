<?php
include "connect.php";
session_start();
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {
    // เชื่อมต่อฐานข้อมูล
    $stmt = $conn->query("SELECT * FROM uploads ORDER BY created_at DESC");
    $uploads = $stmt->fetch_all(MYSQLI_ASSOC);
?>

    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>รายการอัปโหลด</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css" rel="stylesheet">
        <!-- fontawesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">

        <style>
            .card:hover {
                transform: scale(1.02);
                transition: transform 0.3s ease-in-out;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }
        </style>
    </head>
    <!--code แก้ไข -->

    <!-- <body class="hold-transition sidebar-mini "> -->

    <!-- <body class="hold-transition sidebar-mini sidebar-collapse"> -->

    <body class="hold-transition sidebar-mini sidebar-mini-md ">


        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                                class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a class="nav-link" href="">หน้าหลัก</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link">คู่มือการใช้งาน</a>
                    </li>
                </ul>
            </nav>
            <aside class="main-sidebar elevation-4 ">
                <h3 class="brand-link" align="center"><img src="assets/images/logovr.png" style="width:100%; height:60px; margin-top:10px; filter:drop-shadow(5px 0px 5px grey);"></h3>
                <hr class="hr-white">
                <div align="center">
                    <a class="nav-link text-light" href="logout.php"><i class="fas fa-sign-in-alt"></i>
                        Logout</a>
                </div>
                <hr class="hr-white">
                <style>
                    .content-wrapper {
                        min-height: calc(100vh - 55px);
                        overflow: visible;
                    }

                    body {
                        overflow-y: auto;
                    }

                    /* Change main sidebar color to orange gradient */
                    .main-sidebar {
                        background: linear-gradient(to bottom, #F89B9E, #C9C9FF);
                    }

                    /* Style for the brand-link to add underline below image */
                    /* .brand-link {
                    border-bottom: 3px solid #ff4500;
                    padding-bottom: 10px;
                } */

                    Adjust navbar colors to match orange theme .navbar-light {
                        background-color: rgb(232, 231, 230);
                    }

                    .navbar-light .navbar-nav .nav-link {
                        color: #ff6f00;
                    }

                    /* Style for the login link */
                    .text-light {
                        color: #fff !important;
                        font-weight: bold;
                    }

                    /* Hover effects for better user experience */
                    .nav-link:hover {
                        color: rgb(22, 16, 212) !important;
                    }
                </style>
                <?php
                include "sidebar.php";
                ?>
            </aside>

            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">
                        <div class="container mt-5">
                            <h2 class="text-center mb-3" style="background:#76Bc43; padding-top:10px; padding-bottom:10px; border-radius:25px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff;">รายการอัปโหลด</h2>
                            <div class="text-end mb-3">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">อัปโหลดใหม่</button>
                            </div>
                            <div class="row">
                                <?php foreach ($uploads as $upload): ?>
                                    <div class="col-md-4">
                                        <div class="card mb-4">
                                            <img src="<?= htmlspecialchars($upload['image_path']) ?>" style="height: 200px; width: 100%; object-fit: cover;
                                            " class="card-img-top " alt="<?= htmlspecialchars($upload['image_name']) ?>">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3 ">
                                                    <b><u><?= htmlspecialchars($upload['image_name']) ?></u></b>
                                                </h5>
                                                <p class="card-text">
                                                    <?php
                                                    $content = htmlspecialchars($upload['content']);
                                                    if (mb_strlen($content) > 100) {
                                                        $shortContent = mb_substr($content, 0, 100) . '...';
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
                                                                    const shortContent = fullContent.substring(0, 100) + '...';
                                                                    parent.innerHTML = shortContent + ' <a href="#" class="text-primary toggle-link" data-full-content="' + fullContent + '">เพิ่มเติม</a>';
                                                                }
                                                            }
                                                        });
                                                    </script>
                                                <p class="card-text"><small class="text-muted">อัปโหลดเมื่อ <?= date('d/m/Y H:i น.', strtotime($upload['created_at'])) ?></small></p>
                                                <div class="d-flex justify-content-between">
                                                    <!-- ปุ่มแก้ไข -->
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-id="<?= $upload['u_id'] ?>"
                                                        data-name="<?= htmlspecialchars($upload['image_name']) ?>"
                                                        data-path="<?= htmlspecialchars($upload['image_path']) ?>"
                                                        data-contact="<?= htmlspecialchars($upload['contact']) ?>"
                                                        data-title="<?= htmlspecialchars($upload['title']) ?>"
                                                        data-subtitle="<?= htmlspecialchars($upload['subtitle']) ?>"
                                                        data-contact_link="<?= htmlspecialchars($upload['contact_link']) ?>"
                                                        data-vr_link="<?= htmlspecialchars($upload['vr_link']) ?>"
                                                        data-content="<?= htmlspecialchars($upload['content']) ?>">
                                                        แก้ไข
                                                    </button>

                                                    <!-- ปุ่มลบ -->
                                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $upload['u_id'] ?>)">
                                                        ลบ
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>


            <div class="sidebar" style="margin-top: -10px">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="">
                                <i class="fas fa-sign-in-alt"></i> ล็อกอิน
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-cog"></i> &nbsp;
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('staff-logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                            <form id="staff-logout-form" action="{{ route('staff.logout') }}"
                                method="POST" class="d-none">
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>


            <!--code แก้ไข -->
            <!-- Modal แก้ไข -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title text-center" id="editModalLabel" style="background:#e6b609; padding-top:10px; padding-bottom:10px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff; width: 100%;">แก้ไขข้อมูล</h2>
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                        </div>
                        <div class="modal-body">
                            <form action="edit_update.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="u_id" id="edit-id">
                                <input type="hidden" name="old_image_path" id="edit-path">

                                <div class="mb-3">
                                    <label class="form-label">ชื่อรูปภาพ</label>
                                    <input type="text" name="image_name" id="edit-name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ชื่อสถานที่(ตำแหน่ง)</label>
                                    <input type="text" name="title" id="edit-title" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">คำอธิบายสถานที่(แบบย่อ)</label>
                                    <input type="text" name="subtitle" id="edit-subtitle" class="form-control" required>
                                </div>

                                <!-- แสดงรูปเดิม -->
                                <div class="mb-3">
                                    <label class="form-label">รูปปัจจุบัน</label>
                                    <img id="preview-image" src="" class="img-fluid rounded mb-2" width="60" height="60">
                                </div>

                                <!-- อัปโหลดไฟล์ใหม่ -->
                                <div class="mb-3">
                                    <label class="form-label">เลือกไฟล์รูปใหม่</label>
                                    <input type="file" name="image" class="form-control" id="edit-file">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ช่องทางติดต่อ</label>
                                    <input type="text" name="contact" id="edit-contact" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ลิ้งค์ ติดต่อ</label>
                                    <input type="text" name="contact_link" id="edit-contact_link" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ลิ้งค์ VR</label>
                                    <input type="text" name="vr_link" id="edit-vr_link" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">เนื้อหา</label>
                                    <textarea name="content" id="edit-content" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                                    <div class="ms-auto">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // ดึงข้อมูลไปใส่ Modal เมื่อกดปุ่มแก้ไข
                document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var u_id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var title = button.getAttribute('data-title');
                    var subtitle = button.getAttribute('data-subtitle');
                    var path = button.getAttribute('data-path');
                    var content = button.getAttribute('data-content');
                    var contact = button.getAttribute('data-contact');
                    var contact_link = button.getAttribute('data-contact_link');
                    var vr_link = button.getAttribute('data-vr_link');

                    // ตั้งค่าฟิลด์ข้อมูล
                    document.getElementById('edit-id').value = u_id;
                    document.getElementById('edit-name').value = name;
                    document.getElementById('edit-title').value = title;
                    document.getElementById('edit-subtitle').value = subtitle;
                    document.getElementById('edit-path').value = path;
                    document.getElementById('edit-content').value = content;
                    document.getElementById('edit-contact').value = contact;
                    document.getElementById('preview-image').src = path; // แสดงรูปเดิม
                    document.getElementById('edit-contact_link').value = contact_link;
                    document.getElementById('edit-vr_link').value = vr_link;

                    // อัปเดตภาพตัวอย่างเมื่อเลือกไฟล์ใหม่
                    document.getElementById('edit-file').addEventListener('change', function(event) {
                        var reader = new FileReader();
                        reader.onload = function() {
                            document.getElementById('preview-image').src = reader.result; // แสดงรูปใหม่
                        }
                        reader.readAsDataURL(event.target.files[0]);
                    });
                });
                // ฟังก์ชันยืนยันการลบ
                function confirmDelete(u_id) {
                    Swal.fire({
                        title: "ยืนยันการลบ?",
                        text: "คุณต้องการลบรายการนี้หรือไม่?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "ลบ",
                        cancelButtonText: "ยกเลิก",
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: "ลบข้อมูลเรียบร้อยแล้ว!",
                                text: "ข้อมูลถูกลบเรียบร้อยแล้ว",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'delete_upload.php?u_id=' + u_id;
                            });
                        }
                    });
                }
            </script>
            <!-- Modal แก้ไข -->
            <!-- ------------------------------------------------------------------------------------------------------------------ -->

            <!-- Modal อัปโหลดใหม่ -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title text-center" id="uploadModalLabel" style="background:#84c356; padding-top:10px; padding-bottom:10px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff; width: 100%;">อัปโหลดรูปภาพ</h2>
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                        </div>
                        <div class="modal-body">
                            <form action="add_upload.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อรูปภาพ</label>
                                    <input type="text" name="image_name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ชื่อสถานที่(ตำแหน่ง)</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">คำอธิบายสถานที่(แบบย่อ)</label>
                                    <input type="text" name="subtitle" class="form-control" required>
                                </div>

                                <!-- แสดงรูปตัวอย่าง -->
                                <div class="mb-3 text-center">
                                    <label class="form-label">ตัวอย่างรูป</label>
                                    <img id="upload-preview" src="default-placeholder.png" class="img-fluid rounded mb-2" width="200">
                                </div>

                                <!-- อัปโหลดไฟล์ -->
                                <div class="mb-3">
                                    <label class="form-label">เลือกไฟล์รูป</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" id="upload-file" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ช่องทางติดต่อ</label>
                                    <input type="text" name="contact" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ลิ้งค์ ติดต่อ</label>
                                    <input type="text" name="contact_link" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ลิ้งค์ VR</label>
                                    <input type="text" name="vr_link" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">เนื้อหา</label>
                                    <textarea name="content" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">อัปโหลด</button>
                                    <div class="ms-auto">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // แสดงตัวอย่างรูปที่อัปโหลดใหม่
            document.getElementById('upload-file').addEventListener('change', function(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    document.getElementById('upload-preview').src = reader.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            });
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

    </body>

    </html>
<?php
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
      Swal.fire({
          title: 'กรุณาเข้าสู่ระบบ!',
          text: '',
          icon: 'error'
      }).then(() => {
          window.location='frm_login.php'; 
      });
  </script>";
    // หยุดการประมวลผลของโค้ดต่อไป
    exit;
}
?>