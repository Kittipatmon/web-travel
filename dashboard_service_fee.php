<?php
include "connect.php";
session_start();
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {

    // ดึงข้อมูลจาก service_fee table
    $serviceFeeResult = $conn->query("SELECT s_id, service_name, fee, unit, u_id FROM service_fee");

?>
    <!DOCTYPE html>

    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>รายละเอียดสถานที่</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
        <style>
            .card:hover {
                transform: scale(1.02);
                transition: transform 0.3s ease-in-out;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }

            .content-wrapper {
                min-height: calc(100vh - 55px);
                overflow: visible;
            }

            body {
                overflow-y: auto;
            }

            .main-sidebar {
                background: linear-gradient(to bottom, #F89B9E, #C9C9FF);
            }

            /* .navbar-light {
                background-color: rgb(232, 231, 230);
            } */

            .navbar-light .navbar-nav .nav-link {
                color: #ff6f00;
            }

            .text-light {
                color: #fff !important;
                font-weight: bold;
            }

            .nav-link:hover {
                color: rgb(22, 16, 212) !important;
            }
        </style>
    </head>

    <body class="hold-transition sidebar-mini sidebar-mini-md ">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a class="nav-link " href="">หน้าหลัก</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a class="nav-link " href="#">คู่มือการใช้งาน</a>
                    </li>
                </ul>
            </nav>

            <aside class="main-sidebar elevation-4">
                <h3 class="brand-link" align="center">
                    <img src="assets/images/logovr.png" style="width:100%; height:60px; margin-top:10px; filter:drop-shadow(5px 0px 5px grey);">
                </h3>
                <hr class="hr-white">
                <div align="center">
                    <a class="nav-link text-light" href="logout.php"><i class="fas fa-sign-in-alt"></i> Logout</a>
                </div>
                <hr class="hr-white">
                <?php include "sidebar.php"; ?>
            </aside>

            <!-- show -->
            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">
                        <div class="container mt-5">
                            <h2 class="text-center mb-3" style="background:#76Bc43; padding:10px; border-radius:25px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff;">รายละเอียดค่าบริการ</h2>
                            <div class="text-end mb-3">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">อัปโหลดใหม่</button>
                            </div>
                            <div class="row">
                                <?php if ($serviceFeeResult->num_rows > 0) : ?>
                                    <div class="card mb-3 shadow-sm style=" border-radius: 15px;">
                                        <div class="table-responsive">
                                            <table id="myTable" class="display table table-striped" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">ลำดับ</th>
                                                        <th style="text-align:center;">ชื่อสถานที่</th>
                                                        <th style="text-align:center;">ชื่อบริการ</th>
                                                        <th style="text-align:center;">ค่าธรรมเนียม</th>
                                                        <th style="text-align:center;">หน่วยของค่าธรรมเนียม</th>
                                                        <th style="text-align:center;">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $count = 1;
                                                    while ($service_fee = $serviceFeeResult->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td style='text-align:center;'>" . $count++ . "</td>";
                                                        // ตรวจสอบว่า u_id ของ uploads และ service_fee ตรงกันหรือไม่
                                                        // Fetch the corresponding image_name from the uploads table
                                                        $u_id = $service_fee['u_id'];
                                                        $sql1 = "SELECT image_name FROM uploads WHERE u_id = '$u_id'";
                                                        $result1 = mysqli_query($conn, $sql1) or die("Can't query sql");
                                                        $rs1 = mysqli_fetch_array($result1);
                                                        // Display the image_name or a placeholder if not found
                                                        echo "<td>" . (!empty($rs1['image_name']) ? htmlspecialchars($rs1['image_name']) : '-') . "</td>";

                                                        echo "<td style='text-align:center;'>" . htmlspecialchars($service_fee['service_name']) . "</td>";
                                                        echo "<td style='text-align:center;'>" . htmlspecialchars($service_fee['fee']) . "</td>";
                                                        echo "<td style='text-align:center;'>" . htmlspecialchars($service_fee['unit']) . "</td>";

                                                        // ปุ่มแก้ไขและลบ
                                                        echo "<td style='text-align:center;'>";
                                                        echo "<div class='d-flex justify-content-center'>";
                                                        echo "<button class='btn btn-warning btn-sm me-2' data-bs-toggle='modal' data-bs-target='#editModal'
                                                               data-id='" . $service_fee['s_id'] . "'
                                                               data-u_id='" . htmlspecialchars($service_fee['u_id']) . "'
                                                               data-service_name='" . htmlspecialchars($service_fee['service_name']) . "'
                                                               data-fee='" . htmlspecialchars($service_fee['fee']) . "'
                                                               data-unit='" . htmlspecialchars($service_fee['unit']) . "'>
                                                               แก้ไข
                                                           </button>";
                                                        echo "<button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $service_fee['s_id'] . ")'>ลบ</button>";
                                                        echo "</div>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="alert alert-warning text-center">ไม่มีข้อมูลสถานที่</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Modal แก้ไข -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title text-center" id="editModalLabel" style="background:#e6b609; padding-top:10px; padding-bottom:10px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff; width: 100%;">แก้ไขข้อมูล</h2>
                        </div>
                        <div class="modal-body">
                            <form action="edit_service_fee.php" method="POST">
                                <input type="hidden" name="t_id" id="edit-id">

                                <div class="mb-3">
                                    <label class="form-label">ชื่อสถานที่</label>
                                    <select class="form-select" aria-label="Default select example" name="u_id" id="edit-u_id">
                                        <?php
                                        // Query to fetch all u_id and image_name from uploads
                                        $sql2 = "SELECT u_id, image_name FROM uploads";
                                        $result2 = mysqli_query($conn, $sql2);

                                        // Query to fetch all u_id already used in tourist_spot
                                        $used_uids = [];
                                        $usedResult = mysqli_query($conn, "SELECT u_id FROM service_fee");
                                        while ($usedRow = mysqli_fetch_assoc($usedResult)) {
                                            $used_uids[] = $usedRow['u_id'];
                                        }

                                        // Display all u_id options and mark the selected one
                                        while ($rs2 = mysqli_fetch_array($result2)) {
                                            $selected = ($rs2['u_id'] == $u_id) ? "selected" : "";
                                            echo "<option value=\"" . htmlspecialchars($rs2['u_id']) . "\" $selected>" . htmlspecialchars($rs2['image_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ชื่อบริการ</label>
                                    <input type="text" name="service_name" id="edit-service_name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ค่าธรรมเนียม</label>
                                    <input type="number" step="0.01" name="fee" id="edit-fee" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">หน่วยของค่าธรรมเนียม</label>
                                    <input type="text" name="unit" id="edit-unit" class="form-control" required>
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

            <!-- Modal อัปโหลดใหม่ -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title text-center" id="uploadModalLabel" style="background:#84c356; padding-top:10px; padding-bottom:10px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff; width: 100%;">อัปโหลดรูปภาพ</h2>
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                        </div>
                        <div class="modal-body">
                            <form action="add_service_fee.php" method="POST" enctype="multipart/form-data">

                                <!-- <div class="mb-3">
                                    <label class="form-label">ชื่อรูปภาพ</label>
                                    <input type="text" name="image_name" class="form-control" required>
                                </div> -->
                                <div class="mb-3">
                                    <label class="form-label">ชื่อสถานที่ <span style="color: red;"> *</span></label>
                                    <select class="form-select" aria-label="Default select example" name="u_id" id="u_id" required>
                                        <?php
                                        // Query to fetch all u_id and image_name from uploads
                                        $sql2 = "SELECT u_id, image_name FROM uploads";
                                        $result2 = mysqli_query($conn, $sql2);

                                        // Display all u_id options
                                        while ($rs2 = mysqli_fetch_array($result2)) {
                                            echo "<option value=\"" . htmlspecialchars($rs2['u_id']) . "\">" . htmlspecialchars($rs2['image_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ชื่อบริการ <span style="color: red;"> *</span></label>
                                    <input type="text" name="service_name" class="form-control" required placeholder="เช่น ค่าเข้าชม (ผู้ใหญ่/เด็ก)">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ค่าธรรมเนียม <span style="color: red;"> *</span></label>
                                    <input type="number" step="0.01" name="fee" class="form-control" required placeholder="เช่น จำนวนทศนิยม 100.00">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">หน่วยของค่าธรรมเนียม <span style="color: red;"> *</span></label>
                                    <input type="text" name="unit" class="form-control" required placeholder="เช่น บาท/คน, บาท/วัน, บาท/ครั้ง">
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
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
            // ดึงข้อมูลไปใส่ Modal เมื่อกดปุ่มแก้ไข
            document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var t_id = button.getAttribute('data-id');
                var u_id = button.getAttribute('data-u_id');
                var service_name = button.getAttribute('data-service_name');
                var fee = button.getAttribute('data-fee');
                var unit = button.getAttribute('data-unit');

                // ตั้งค่าฟิลด์ข้อมูล
                document.getElementById('edit-id').value = t_id;
                document.getElementById('edit-u_id').value = u_id;
                document.getElementById('edit-service_name').value = service_name;
                document.getElementById('edit-fee').value = fee;
                document.getElementById('edit-unit').value = unit;

            });
            // ฟังก์ชันยืนยันการลบ
            function confirmDelete(s_id) {
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
                            window.location.href = 'delete_service_fee.php?s_id=' + s_id;
                        });
                    }
                });
            }
        </script>

        <!-- JS Script Section -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
        <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

        <script>
            $(document).ready(function() {
                $("#myTable").DataTable();
            });
        </script>
    </body>

    </html>

<?php
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'กรุณาเข้าสู่ระบบ!',
            icon: 'error'
        }).then(() => {
            window.location='frm_login.php'; 
        });
    </script>";
    exit;
}
?>