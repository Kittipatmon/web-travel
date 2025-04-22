<?php
include "connect.php"; // Include your database connection file
session_start();
if (isset($_SESSION["role_user"]) && $_SESSION["role_user"] == "admin") {

    // Query to fetch view_count grouped by month for the current year
    $monthlyVisitorQuery = "
    SELECT MONTH(created_at) AS month, SUM(view_count) AS visitors 
    FROM uploads 
    WHERE YEAR(created_at) = YEAR(CURRENT_DATE()) 
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at)";
    $monthlyVisitorResult = $conn->query($monthlyVisitorQuery);

    $visitorStats = [];
    if ($monthlyVisitorResult && $monthlyVisitorResult->num_rows > 0) {
        while ($row = $monthlyVisitorResult->fetch_assoc()) {
            $visitorStats[] = [
                'month' => date('M', mktime(0, 0, 0, $row['month'], 10)), // Convert month number to short name
                'visitors' => $row['visitors']
            ];
        }
    }

    // Convert the data for charts
    $visitorStatsJson = json_encode($visitorStats);

    // Query to fetch image_name and total view_count from the uploads table, sorted by view_count descending
    $imageDataQuery = "SELECT image_name AS name, SUM(view_count) AS count FROM uploads GROUP BY image_name ORDER BY count DESC";
    $imageDataResult = $conn->query($imageDataQuery);

    $locationData = [];
    if ($imageDataResult && $imageDataResult->num_rows > 0) {
        while ($row = $imageDataResult->fetch_assoc()) {
            $locationData[] = [
                'name' => $row['name'],
                'count' => $row['count']
            ];
        }
    }

    // Convert the data for charts
    $locationDataJson = json_encode($locationData);

    // Query to get the total number of locations
    $totalLocationsQuery = "SELECT COUNT(*) AS total_locations FROM uploads";
    $totalLocationsResult = $conn->query($totalLocationsQuery);
    $totalLocations = $totalLocationsResult->fetch_assoc()['total_locations'] ?? 0;

?>

    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>แดชบอร์ดวิเคราะห์ข้อมูล</title>

        <!-- Tailwind CSS via CDN -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

        <!-- Boxicons -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- Custom Styles -->
        <style>
            body {
                font-family: 'Prompt', sans-serif;
            }

            .card {
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }

            .stats-card {
                border-radius: 12px;
                overflow: hidden;
            }

            .chart-container {
                position: relative;
                margin: auto;
                height: 300px;
                width: 100%;
            }

            .achievement-badge {
                transition: all 0.3s ease;
            }

            .achievement-badge:hover {
                transform: scale(1.05);
            }
        </style>
        <style>
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

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto">
                <!-- Top Navigation -->
                <div class="bg-white shadow-sm">
                    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center">
                            <button class="md:hidden mr-4 text-gray-600">
                                <i class='bx bx-menu text-2xl'></i>
                            </button>
                            <div>
                                <?php
                                // ดึงข้อมูลผู้ใช้งาน
                                $userResult = $conn->query("SELECT id_user, name_user, email_user, role_user FROM users");
                                if ($userResult && $userResult->num_rows > 0) {
                                    $userData = $userResult->fetch_assoc();
                                    $name_user = $userData['name_user'];
                                    $email_user = $userData['email_user'];
                                    $role_user = $userData['role_user'];
                                }

                                ?>

                                <div class="text-sm text-gray-500">ยินดีต้อนรับ สวัสดี</div>
                                <div class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($name_user); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div class="container mx-auto px-4 py-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6 ">แดชบอร์ดวิเคราะห์ข้อมูล</h1>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <!-- Visitors Card -->
                        <div class="card bg-white rounded-lg shadow-sm p-6 ">
                            <div class="flex justify-between items-start">
                                <?php
                                // Query to get the total view count for the current and previous month
                                $currentMonthQuery = "SELECT SUM(view_count) AS total_views FROM uploads WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
                                $previousMonthQuery = "SELECT SUM(view_count) AS total_views FROM uploads WHERE MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURRENT_DATE())";

                                $currentMonthResult = $conn->query($currentMonthQuery);
                                $previousMonthResult = $conn->query($previousMonthQuery);

                                $currentMonthViews = $currentMonthResult->fetch_assoc()['total_views'] ?? 0;
                                $previousMonthViews = $previousMonthResult->fetch_assoc()['total_views'] ?? 0;

                                // Calculate percentage change
                                $percentageChange = $previousMonthViews > 0 ? (($currentMonthViews - $previousMonthViews) / $previousMonthViews) * 100 : 0;
                                ?>

                                <div>
                                    <p class="text-sm text-gray-500">จำนวนผู้เข้าชมทั้งหมด</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo number_format($currentMonthViews); ?></h3>
                                    <p class="text-xs <?php echo $percentageChange >= 0 ? 'text-green-500' : 'text-red-500'; ?> mt-1">
                                        <i class='bx <?php echo $percentageChange >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt'; ?>'></i>
                                        <?php echo number_format(abs($percentageChange), 2); ?>%
                                        <?php echo $percentageChange >= 0 ? 'จากเดือนที่แล้ว' : 'ลดลงจากเดือนที่แล้ว'; ?>
                                    </p>
                                </div>
                                <div class="bg-blue-500 bg-opacity-10 p-3 rounded-full">
                                    <i class='bx bx-group text-blue-500 text-2xl'></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-100 rounded-full h-1">
                                    <div class="bg-blue-500 h-1 rounded-full" style="width: <?php echo min(100, max(0, $percentageChange + 50)); ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Locations Card -->
                        <div class="card bg-white rounded-lg shadow-sm p-6 " style="animation-delay: 0.2s">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">จำนวนสถานที่</p>
                                    <h3 class="text-2xl font-bold mt-1"><?php echo number_format($totalLocations); ?></h3>
                                    <p class="text-xs text-green-500 mt-1">
                                        <i class='bx bx-plus'></i> เพิ่ม 5 แห่งในเดือนนี้
                                    </p>
                                </div>
                                <div class="bg-green-500 bg-opacity-10 p-3 rounded-full">
                                    <i class='bx bx-map text-green-500 text-2xl'></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-100 rounded-full h-1">
                                    <div class="bg-green-500 h-1 rounded-full" style="width: 60%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Visitor Statistics Chart -->
                        <div class="card bg-white rounded-lg shadow-sm p-6 " style="animation-delay: 0.5s">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">สถิติการเข้าชม</h2>
                                <div class="flex space-x-2">
                                    <button class="time-period px-3 py-1 text-xs rounded-md bg-blue-500 text-white" data-period="yearly">รายปี</button>
                                    <button class="time-period px-3 py-1 text-xs rounded-md bg-gray-100 text-gray-600" data-period="monthly">รายเดือน</button>
                                    <button class="time-period px-3 py-1 text-xs rounded-md bg-gray-100 text-gray-600" data-period="quarterly">รายไตรมาส</button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="visitorChart"></canvas>
                            </div>
                        </div>

                        <!-- Location Statistics Chart -->
                        <div class="card bg-white rounded-lg shadow-sm p-6" style="animation-delay: 0.6s">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">จำนวนการเข้าชมแต่ละสถานที่</h2>
                                <button class="text-sm text-blue-500 hover:underline">ดูทั้งหมด</button>
                            </div>
                            <div class="chart-container">
                                <canvas id="locationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script>
            // Initialize visitor chart
            const visitorCtx = document.getElementById('visitorChart').getContext('2d');
            const visitorData = <?php echo $visitorStatsJson; ?>;

            const visitorChart = new Chart(visitorCtx, {
                type: 'line',
                data: {
                    labels: visitorData.map(item => item.month),
                    datasets: [{
                        label: 'จำนวนผู้เข้าชม',
                        data: visitorData.map(item => item.visitors),
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Initialize location chart
            const locationCtx = document.getElementById('locationChart').getContext('2d');
            const locationData = <?php echo $locationDataJson; ?>;

            const locationChart = new Chart(locationCtx, {
                type: 'bar',
                data: {
                    labels: locationData.map(item => item.name),
                    datasets: [{
                        label: 'จำนวนการเข้าชม',
                        data: locationData.map(item => item.count),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Time period buttons
            document.querySelectorAll('.time-period').forEach(button => {
                button.addEventListener('click', () => {
                    // Reset all buttons to default style
                    document.querySelectorAll('.time-period').forEach(btn => {
                        btn.classList.remove('bg-blue-500', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-600');
                    });

                    // Apply active style to clicked button
                    button.classList.remove('bg-gray-100', 'text-gray-600');
                    button.classList.add('bg-blue-500', 'text-white');

                    // In a real application, you would fetch data for the selected period here
                    // For demo purposes, let's just apply some visual changes
                    const period = button.dataset.period;

                    // Example of updating chart data based on period
                    // This is a simplified example - in a real app you'd fetch new data
                    if (period === 'monthly') {
                        // Simulate monthly data
                        visitorChart.data.datasets[0].borderColor = 'rgba(16, 185, 129, 1)';
                        visitorChart.data.datasets[0].backgroundColor = 'rgba(16, 185, 129, 0.1)';
                    } else if (period === 'quarterly') {
                        // Simulate quarterly data
                        visitorChart.data.datasets[0].borderColor = 'rgba(245, 158, 11, 1)';
                        visitorChart.data.datasets[0].backgroundColor = 'rgba(245, 158, 11, 0.1)';
                    } else {
                        // Yearly data (default)
                        visitorChart.data.datasets[0].borderColor = 'rgba(99, 102, 241, 1)';
                        visitorChart.data.datasets[0].backgroundColor = 'rgba(99, 102, 241, 0.1)';
                    }

                    visitorChart.update();
                });
            });

            // Add sidebar toggle for mobile
            document.querySelector('.bx-menu').addEventListener('click', () => {
                const sidebar = document.querySelector('.sidebar');
                sidebar.classList.toggle('hidden');
            });

            // Add some dynamic elements
            // Removed random visitor count updates to ensure accurate data representation
        </script>
        <!-- JS Script Section -->
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
            icon: 'error'
        }).then(() => {
            window.location='frm_login.php'; 
        });
    </script>";
    exit;
}
?>