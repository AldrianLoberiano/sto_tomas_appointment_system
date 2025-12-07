<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../models/Service.php';
require_once __DIR__ . '/../../models/User.php';

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$filter_period = $_GET['period'] ?? 'this_month';
$filter_service = $_GET['service'] ?? 'all';
$filter_status = $_GET['status'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Date range based on period
$where_date = "";
switch ($filter_period) {
    case 'today':
        $where_date = "AND DATE(a.appointment_date) = CURDATE()";
        break;
    case 'this_week':
        $where_date = "AND YEARWEEK(a.appointment_date) = YEARWEEK(CURDATE())";
        break;
    case 'this_month':
        $where_date = "AND MONTH(a.appointment_date) = MONTH(CURDATE()) AND YEAR(a.appointment_date) = YEAR(CURDATE())";
        break;
    case 'this_year':
        $where_date = "AND YEAR(a.appointment_date) = YEAR(CURDATE())";
        break;
    case 'custom':
        if ($start_date && $end_date) {
            $where_date = "AND a.appointment_date BETWEEN '$start_date' AND '$end_date'";
        }
        break;
}

// Service filter
$where_service = ($filter_service !== 'all') ? "AND a.service_id = " . intval($filter_service) : "";

// Status filter
$where_status = ($filter_status !== 'all') ? "AND a.status = '$filter_status'" : "";

// Get statistics
$query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN a.status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
          FROM appointments a
          WHERE 1=1 $where_date $where_service $where_status";
$stmt = $db->prepare($query);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get appointments by service
$query = "SELECT s.service_name, COUNT(*) as count
          FROM appointments a
          JOIN services s ON a.service_id = s.id
          WHERE 1=1 $where_date $where_status
          GROUP BY a.service_id, s.service_name
          ORDER BY count DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$by_service = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get appointments by status
$query = "SELECT a.status, COUNT(*) as count
          FROM appointments a
          WHERE 1=1 $where_date $where_service
          GROUP BY a.status
          ORDER BY count DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$by_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get daily appointments (last 30 days or custom range)
$query = "SELECT DATE(a.appointment_date) as date, COUNT(*) as count
          FROM appointments a
          WHERE 1=1 $where_date $where_service $where_status
          GROUP BY DATE(a.appointment_date)
          ORDER BY date DESC
          LIMIT 30";
$stmt = $db->prepare($query);
$stmt->execute();
$daily_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top users
$query = "SELECT u.first_name, u.last_name, u.email, COUNT(*) as count
          FROM appointments a
          JOIN users u ON a.user_id = u.id
          WHERE 1=1 $where_date $where_service $where_status
          GROUP BY a.user_id, u.first_name, u.last_name, u.email
          ORDER BY count DESC
          LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$top_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all services for filter
$service = new Service($db);
$services = $service->read();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .filter-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }

        .filter-group select,
        .filter-group input {
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .report-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-section h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 20px;
        }

        .chart-container {
            margin: 20px 0;
            position: relative;
            height: 400px;
        }

        .chart-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .chart-box h2 {
            margin: 0 0 25px 0;
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chart-box h2::before {
            content: '';
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 5px;
            height: 28px;
            border-radius: 3px;
        }

        @media (max-width: 1024px) {
            .chart-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
        }

        .summary-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 8px 20px rgba(240, 147, 251, 0.3);
        }

        .summary-card:nth-child(2):hover {
            box-shadow: 0 12px 30px rgba(240, 147, 251, 0.4);
        }

        .summary-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 8px 20px rgba(79, 172, 254, 0.3);
        }

        .summary-card:nth-child(3):hover {
            box-shadow: 0 12px 30px rgba(79, 172, 254, 0.4);
        }

        .summary-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            box-shadow: 0 8px 20px rgba(67, 233, 123, 0.3);
        }

        .summary-card:nth-child(4):hover {
            box-shadow: 0 12px 30px rgba(67, 233, 123, 0.4);
        }

        .summary-card:nth-child(5) {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            box-shadow: 0 8px 20px rgba(250, 112, 154, 0.3);
        }

        .summary-card:nth-child(5):hover {
            box-shadow: 0 12px 30px rgba(250, 112, 154, 0.4);
        }

        .summary-card:nth-child(6) {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }

        .summary-card h3 {
            font-size: 32px;
            margin: 0;
        }

        .summary-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        @media print {

            .sidebar,
            .filter-section,
            .export-buttons,
            .btn {
                display: none !important;
            }

            .main-container {
                margin: 0;
                padding: 0;
            }

            .content {
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 25px;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -80px; right: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">📈 Reports & Analytics</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0; position: relative; z-index: 1;">View insights and generate reports</p>
            </div>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 35px;">
                <div class="stat-card" style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #3498db; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(52,152,219,0.1) 0%, rgba(52,152,219,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">📊</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Total Records</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #3498db; line-height: 1;"><?php echo $stats['total']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">In selected period</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #fff9f0 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #f39c12; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(243,156,18,0.15) 0%, rgba(243,156,18,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">⏳</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Pending</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #f39c12; line-height: 1;"><?php echo $stats['pending']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Awaiting action</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f3fb 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #9b59b6; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(155,89,182,0.15) 0%, rgba(155,89,182,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">✅</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Approved</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #9b59b6; line-height: 1;"><?php echo $stats['approved']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Ready to serve</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9f4 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #27ae60; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(39,174,96,0.15) 0%, rgba(39,174,96,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">✔️</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Completed</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #27ae60; line-height: 1;"><?php echo $stats['completed']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Successfully served</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="period">Time Period</label>
                            <select name="period" id="period" onchange="toggleCustomDate(); autoSubmitForm();">
                                <option value="today" <?php echo $filter_period === 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="this_week" <?php echo $filter_period === 'this_week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="this_month" <?php echo $filter_period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="this_year" <?php echo $filter_period === 'this_year' ? 'selected' : ''; ?>>This Year</option>
                                <option value="custom" <?php echo $filter_period === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                        </div>

                        <div class="filter-group" id="start_date_group" style="display: <?php echo $filter_period === 'custom' ? 'flex' : 'none'; ?>;">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
                        </div>

                        <div class="filter-group" id="end_date_group" style="display: <?php echo $filter_period === 'custom' ? 'flex' : 'none'; ?>;">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
                        </div>

                        <div class="filter-group">
                            <label for="service">Service</label>
                            <select name="service" id="service" onchange="autoSubmitForm();">
                                <option value="all">All Services</option>
                                <?php while ($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo $filter_service == $row['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['service_name'] ?? ''); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" onchange="autoSubmitForm();">
                                <option value="all">All Status</option>
                                <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $filter_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="rejected" <?php echo $filter_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn" onclick="window.print()" style="margin-top: 15px;">🖨️ Print Report</button>
                </form>
            </div>

            <!-- Summary Statistics -->
            <div class="summary-grid">
                <div class="summary-card">
                    <h3><?php echo $stats['total']; ?></h3>
                    <p>Total Appointments</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo $stats['pending']; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo $stats['approved']; ?></h3>
                    <p>Approved</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo $stats['completed']; ?></h3>
                    <p>Completed</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo $stats['rejected']; ?></h3>
                    <p>Rejected</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo $stats['cancelled']; ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="chart-wrapper">
                <!-- Appointments by Service (Bar Chart) -->
                <div class="chart-box">
                    <h2>Appointments by Service</h2>
                    <div class="chart-container">
                        <canvas id="serviceBarChart"></canvas>
                    </div>
                </div>

                <!-- Appointments by Status (Pie Chart) -->
                <div class="chart-box">
                    <h2>Appointments by Status</h2>
                    <div class="chart-container">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="chart-wrapper">
                <!-- Appointments by Service (Pie Chart) -->
                <div class="chart-box">
                    <h2>Service Distribution</h2>
                    <div class="chart-container">
                        <canvas id="servicePieChart"></canvas>
                    </div>
                </div>

                <!-- Appointments by Status (Bar Chart) -->
                <div class="chart-box">
                    <h2>Status Overview</h2>
                    <div class="chart-container">
                        <canvas id="statusBarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="report-section">
                <h2>Top 10 Users by Appointments</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Total Appointments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rank = 1;
                            foreach ($top_users as $user):
                            ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                    <td><?php echo $user['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($top_users)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #7f8c8d;">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Appointments -->
            <div class="report-section">
                <h2>Daily Appointments (Last 30 Days)</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Number of Appointments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daily_appointments as $item): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($item['date'])); ?></td>
                                    <td><?php echo $item['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($daily_appointments)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; color: #7f8c8d;">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleCustomDate() {
            const period = document.getElementById('period').value;
            const startDateGroup = document.getElementById('start_date_group');
            const endDateGroup = document.getElementById('end_date_group');

            if (period === 'custom') {
                startDateGroup.style.display = 'flex';
                endDateGroup.style.display = 'flex';
            } else {
                startDateGroup.style.display = 'none';
                endDateGroup.style.display = 'none';
            }
        }

        function autoSubmitForm() {
            const form = document.querySelector('.filter-section form');
            form.submit();
        }

        // Add onchange event to date inputs for auto-submit
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (startDate) {
                startDate.addEventListener('change', autoSubmitForm);
            }
            if (endDate) {
                endDate.addEventListener('change', autoSubmitForm);
            }
        });

        // Prepare data for charts
        const serviceData = {
            labels: [<?php
                        echo !empty($by_service) ? implode(', ', array_map(function ($item) {
                            return '"' . addslashes($item['service_name']) . '"';
                        }, $by_service)) : '';
                        ?>],
            values: [<?php
                        echo !empty($by_service) ? implode(', ', array_column($by_service, 'count')) : '0';
                        ?>]
        };

        const statusData = {
            labels: [<?php
                        echo !empty($by_status) ? implode(', ', array_map(function ($item) {
                            return '"' . ucfirst($item['status']) . '"';
                        }, $by_status)) : '';
                        ?>],
            values: [<?php
                        echo !empty($by_status) ? implode(', ', array_column($by_status, 'count')) : '0';
                        ?>]
        };

        // Status colors
        const statusColors = {
            'Pending': '#f39c12',
            'Approved': '#3498db',
            'Completed': '#27ae60',
            'Rejected': '#e74c3c',
            'Cancelled': '#95a5a6'
        };

        // Service colors (generate dynamic colors)
        const serviceColors = [
            '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c',
            '#34495e', '#16a085', '#27ae60', '#2980b9', '#8e44ad',
            '#2c3e50', '#f1c40f', '#e67e22', '#95a5a6', '#d35400'
        ];

        // Chart.js default configuration
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.plugins.legend.position = 'bottom';
        Chart.defaults.plugins.legend.labels.padding = 15;

        // Service Bar Chart
        const serviceBarCtx = document.getElementById('serviceBarChart').getContext('2d');
        const serviceBarChart = new Chart(serviceBarCtx, {
            type: 'bar',
            data: {
                labels: serviceData.labels,
                datasets: [{
                    label: 'Number of Appointments',
                    data: serviceData.values,
                    backgroundColor: serviceColors.slice(0, serviceData.labels.length),
                    borderColor: serviceColors.slice(0, serviceData.labels.length).map(color => color),
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Appointments: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });

        // Service Pie Chart
        const servicePieCtx = document.getElementById('servicePieChart').getContext('2d');
        const servicePieChart = new Chart(servicePieCtx, {
            type: 'pie',
            data: {
                labels: serviceData.labels,
                datasets: [{
                    data: serviceData.values,
                    backgroundColor: serviceColors.slice(0, serviceData.labels.length),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Status Pie Chart
        const statusPieCtx = document.getElementById('statusPieChart').getContext('2d');
        const statusPieChart = new Chart(statusPieCtx, {
            type: 'pie',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.values,
                    backgroundColor: statusData.labels.map(label => statusColors[label] || '#95a5a6'),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Status Bar Chart
        const statusBarCtx = document.getElementById('statusBarChart').getContext('2d');
        const statusBarChart = new Chart(statusBarCtx, {
            type: 'bar',
            data: {
                labels: statusData.labels,
                datasets: [{
                    label: 'Number of Appointments',
                    data: statusData.values,
                    backgroundColor: statusData.labels.map(label => statusColors[label] || '#95a5a6'),
                    borderColor: statusData.labels.map(label => statusColors[label] || '#95a5a6'),
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Appointments: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
    </script>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>