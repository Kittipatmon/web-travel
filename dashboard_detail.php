<?php
include "connect.php";
session_start();
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {

    // ดึงข้อมูลสถานที่ท่องเที่ยว
    $spotResult = $conn->query("SELECT t_id, u_id, visitor_limit, recommended_visitors, area, open_time, close_time, date_time,	time_period, day_period, season FROM tourist_spot");

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
                            <h2 class="text-center mb-3" style="background:#76Bc43; padding:10px; border-radius:25px; box-shadow: 0 3px 6px rgba(0,0,0,0.15); color:#ffffff;">รายละเอียดสถานที่</h2>
                            <div class="text-end mb-3">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">อัปโหลดใหม่</button>
                            </div>
                            <div class="row">
                                <?php if ($spotResult->num_rows > 0) : ?>
                                    <div class="card mb-3 shadow-sm style=" border-radius: 15px;">
                                        <div class="table-responsive">
                                            <table id="myTable" class="display table table-striped" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">ลำดับ</th>
                                                        <th style="text-align:center;">ชื่อสถานที่</th>
                                                        <th style="text-align:center;">จำนวนผู้เข้าชมสูงสุด</th>
                                                        <th style="text-align:center;">จำนวนผู้เข้าชมแนะนำ</th>
                                                        <th style="text-align:center;">ขนาดพื้นที่ (ตร.ม.)</th>
                                                        <th style="text-align:center;">วันที่เปิด</th>
                                                        <th style="text-align:center;">เวลาเปิด-ปิด</th>
                                                        <th style="text-align:center;">ช่วงเวลายอดนิยม</th>
                                                        <th style="text-align:center;">ช่วงวันยอดนิยม</th>
                                                        <th style="text-align:center;">ฤดูกาลยอดนิยม</th>
                                                        <th style="text-align:center;">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $count = 1;
                                                    while ($tourist_spot = $spotResult->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $count++ . "</td>";

                                                        // ตรวจสอบว่า u_id ของ uploads และ tourist_spot ตรงกันหรือไม่
                                                        // Fetch the corresponding image_name from the uploads table
                                                        $u_id = $tourist_spot['u_id'];
                                                        $sql1 = "SELECT image_name FROM uploads WHERE u_id = '$u_id'";
                                                        $result1 = mysqli_query($conn, $sql1) or die("Can't query sql");
                                                        $rs1 = mysqli_fetch_array($result1);
                                                        // Display the image_name or a placeholder if not found
                                                        echo "<td>" . (!empty($rs1['image_name']) ? htmlspecialchars($rs1['image_name']) : '-') . "</td>";

                                                        echo "<td>" . htmlspecialchars($tourist_spot['visitor_limit']) . "</td>";
                                                        echo "<td>" . (!empty($tourist_spot['recommended_visitors']) ? htmlspecialchars($tourist_spot['recommended_visitors']) : '-') . "</td>";
                                                        echo "<td>" . htmlspecialchars($tourist_spot['area']) . "</td>";
                                                        // แสดงวัน-เปิด
                                                        echo "<td>" . (!empty($tourist_spot['date_time']) ? htmlspecialchars($tourist_spot['date_time']) : '-') . "</td>";
                                                        echo "<td>" . htmlspecialchars($tourist_spot['open_time']) . " - " . htmlspecialchars($tourist_spot['close_time']) . "</td>";
                                                        // แสดงช่วงเวลายอดนิยม
                                                        echo "<td>" . (!empty($tourist_spot['time_period']) ? htmlspecialchars($tourist_spot['time_period']) : '-') . "</td>";
                                                        // แสดงช่วงวันยอดนิยม
                                                        echo "<td>" . (!empty($tourist_spot['day_period']) ? htmlspecialchars($tourist_spot['day_period']) : '-') . "</td>";
                                                        // แสดงฤดูกาลยอดนิยม  
                                                        echo "<td>" . (!empty($tourist_spot['season']) ? htmlspecialchars($tourist_spot['season']) : '-') . "</td>";


                                                        // ปุ่มแก้ไขและลบ
                                                        echo "<td>";
                                                        echo "<div class='d-flex justify-content-between'>";
                                                        echo "<button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal'
                                                               data-id='" . $tourist_spot['t_id'] . "'
                                                               data-u_id='" . htmlspecialchars($tourist_spot['u_id']) . "'
                                                               data-visitor_limit='" . htmlspecialchars($tourist_spot['visitor_limit']) . "'
                                                               data-recommended_visitors='" . htmlspecialchars($tourist_spot['recommended_visitors']) . "'
                                                               data-area='" . htmlspecialchars($tourist_spot['area']) . "'
                                                               data-date_time='" . htmlspecialchars($tourist_spot['date_time']) . "'
                                                               data-open_time='" . htmlspecialchars($tourist_spot['open_time']) . "'
                                                               data-close_time='" . htmlspecialchars($tourist_spot['close_time']) . "'
                                                               data-time_period='" . htmlspecialchars($tourist_spot['time_period']) . "'
                                                               data-day_period='" . htmlspecialchars($tourist_spot['day_period']) . "'
                                                               data-season='" . htmlspecialchars($tourist_spot['season']) . "'>
                                                               แก้ไข
                                                           </button>";
                                                        echo "<button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $tourist_spot['t_id'] . ")'>ลบ</button>";
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
                            <form action="edit_tourist_spot.php" method="POST">
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
                                        $usedResult = mysqli_query($conn, "SELECT u_id FROM tourist_spot");
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
                                    <label class="form-label">จำนวนผู้เข้าชม <span style="color: red;"> *(พิมพ์ตัวอักษรหรือตัวเลขได้)</span></label>
                                    <input type="text" name="visitor_limit" id="edit-visitor_limit" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">จำนวนผู้เข้าชมแนะนำ</label>
                                    <input type="text" name="recommended_visitors" id="edit-recommended_visitors" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ขนาดพื้นที่ (ตร.ม.)</label>
                                    <input type="text" name="area" id="edit-area" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">วันที่เปิดให้บริการ</label>
                                    <input type="text" name="date_time" id="edit-date_time" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">เวลาเปิด <span style="color: red;"> *</span></label>
                                    <input type="time" name="open_time" id="edit-open_time" class="form-control" required title="กรุณากรอกเวลาในรูปแบบ ชั่งโมง : นาที (24 ชั่วโมง)">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">เวลาปิด <span style="color: red;"> *</span></label>
                                    <input type="time" name="close_time" id="edit-close_time" class="form-control" required title="กรุณากรอกเวลาในรูปแบบ ชั่งโมง : นาที (24 ชั่วโมง)">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ช่วงเวลายอดนิยม <span style="color: red;"> (เช่น "09:00 - 11:00")</span></label>
                                    <input type="text" name="time_period" id="edit-time_period" class="form-control" placeholder="เช่น 09:00 - 11:00">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ช่วงวันยอดนิยม <span style="color: red;"> (เช่น "วันเสาร์-อาทิตย์")</span></label>
                                    <input type="text" name="day_period" id="edit-day_period" class="form-control" placeholder="เช่น วันเสาร์-อาทิตย์">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ฤดูกาลยอดนิยม <span style="color: red;"> (เช่น "ต.ค.- ธ.ค. (ฤดูท่องเที่ยว)")</span></label>
                                    <input type="text" name="season" id="edit-season" class="form-control" placeholder="เช่น ต.ค.- ธ.ค. (ฤดูท่องเที่ยว)">
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
                            <form action="add_tourist_spot.php" method="POST" enctype="multipart/form-data">

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

                                        // Query to fetch all u_id already used in tourist_spot
                                        $used_uids = [];
                                        $usedResult = mysqli_query($conn, "SELECT u_id FROM tourist_spot");
                                        while ($usedRow = mysqli_fetch_assoc($usedResult)) {
                                            $used_uids[] = $usedRow['u_id'];
                                        }

                                        // Display only unused u_id options
                                        while ($rs2 = mysqli_fetch_array($result2)) {
                                            if (!in_array($rs2['u_id'], $used_uids)) {
                                                echo "<option value=\"" . htmlspecialchars($rs2['u_id']) . "\">" . htmlspecialchars($rs2['image_name']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">จำนวนผู้เข้าชม <span style="color: red;"> *(พิมพ์ตัวอักษรหรือตัวเลขได้)</span></label>
                                    <input type="text" name="visitor_limit" class="form-control" required pattern="[A-Za-z0-9ก-๙\s]*" title="กรุณาพิมพ์ตัวอักษรหรือตัวเลข">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">จำนวนผู้เข้าชมแนะนำ</label>
                                    <input type="text" name="recommended_visitors" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ขนาดพื้นที่ (ตร.ม.)</label>
                                    <input type="text" name="area" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">เปิดวัน</label>
                                    <input type="text" name="date_time" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">เวลาเปิด<span style="color: red;"> *</span></label>
                                    <input type="time" name="open_time" class="form-control" required title="กรุณากรอกเวลาในรูปแบบ ชั่งโมง : นาที (24 ชั่วโมง)">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">เวลาปิด<span style="color: red;"> *</span></label>
                                    <input type="time" name="close_time" class="form-control" required title="กรุณากรอกเวลาในรูปแบบ ชั่งโมง : นาที (24 ชั่วโมง)">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ช่วงเวลายอดนิยม <span style="color: red;"> (เช่น "09:00 - 11:00")</span></label>
                                    <input type="text" name="time_period" class="form-control" placeholder="เช่น 09:00 - 11:00">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ช่วงวันยอดนิยม <span style="color: red;"> (เช่น "วันเสาร์-อาทิตย์")</span></label>
                                    <input type="text" name="day_period" class="form-control" placeholder="เช่น วันเสาร์-อาทิตย์">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ฤดูกาลยอดนิยม <span style="color: red;"> (เช่น "ต.ค.- ธ.ค. (ฤดูท่องเที่ยว)")</span></label>
                                    <input type="text" name="season" class="form-control" placeholder="เช่น ต.ค.- ธ.ค. (ฤดูท่องเที่ยว)">
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
                var visitor_limit = button.getAttribute('data-visitor_limit');
                var recommended_visitors = button.getAttribute('data-recommended_visitors');
                var area = button.getAttribute('data-area');
                var date_time = button.getAttribute('data-date_time');
                var open_time = button.getAttribute('data-open_time');
                var close_time = button.getAttribute('data-close_time');
                var time_period = button.getAttribute('data-time_period');
                var day_period = button.getAttribute('data-day_period');
                var season = button.getAttribute('data-season');


                // ตั้งค่าฟิลด์ข้อมูล
                document.getElementById('edit-id').value = t_id;
                document.getElementById('edit-u_id').value = u_id;
                document.getElementById('edit-visitor_limit').value = visitor_limit;
                document.getElementById('edit-recommended_visitors').value = recommended_visitors;
                document.getElementById('edit-area').value = area;
                document.getElementById('edit-date_time').value = date_time;
                document.getElementById('edit-open_time').value = open_time;
                document.getElementById('edit-close_time').value = close_time;
                document.getElementById('edit-time_period').value = time_period;
                document.getElementById('edit-day_period').value = day_period;
                document.getElementById('edit-season').value = season;
            });
            // ฟังก์ชันยืนยันการลบ
            function confirmDelete(t_id) {
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
                            window.location.href = 'delete_tourist_spot.php?t_id=' + t_id;
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