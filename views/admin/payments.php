<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$filter_status = $_GET['status'] ?? 'all';
$filter_payment = $_GET['payment'] ?? 'all';

// Build WHERE clause
$where_clauses = ["s.fee > 0"];

if ($filter_status !== 'all') {
    $where_clauses[] = "a.status = :status";
}

if ($filter_payment !== 'all') {
    if ($filter_payment === 'paid') {
        $where_clauses[] = "a.status = 'completed'";
    } elseif ($filter_payment === 'pending') {
        $where_clauses[] = "a.status = 'approved'";
    } elseif ($filter_payment === 'waiting') {
        $where_clauses[] = "a.status = 'pending'";
    }
}

$where_sql = implode(' AND ', $where_clauses);

// Get appointments with payment information
$query = "SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            s.service_name,
            s.fee,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            a.created_at
          FROM appointments a
          JOIN services s ON a.service_id = s.id
          JOIN users u ON a.user_id = u.id
          WHERE $where_sql
          ORDER BY a.created_at DESC";

$stmt = $db->prepare($query);
if ($filter_status !== 'all') {
    $stmt->bindParam(':status', $filter_status);
}
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$stats = [
    'total_paid' => 0,
    'total_pending' => 0,
    'total_amount' => 0,
    'count_paid' => 0,
    'count_pending' => 0,
];

$stats_query = "SELECT 
                  SUM(CASE WHEN a.status = 'completed' THEN s.fee ELSE 0 END) as total_paid,
                  SUM(CASE WHEN a.status = 'approved' THEN s.fee ELSE 0 END) as total_pending,
                  SUM(s.fee) as total_amount,
                  COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as count_paid,
                  COUNT(CASE WHEN a.status = 'approved' THEN 1 END) as count_pending
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE s.fee > 0";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content">
            <div class="page-header">
                <h1>Payment Management</h1>
                <p>Track and manage appointment payments</p>
            </div>

            <!-- Payment Statistics -->
            <div class="stats-grid">
                <div class="stat-card paid">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <h3>₱<?php echo number_format($stats['total_paid'], 2); ?></h3>
                        <p>Total Paid (<?php echo $stats['count_paid']; ?> payments)</p>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3>₱<?php echo number_format($stats['total_pending'], 2); ?></h3>
                        <p>Pending Payment (<?php echo $stats['count_pending']; ?> appointments)</p>
                    </div>
                </div>
                <div class="stat-card total">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>₱<?php echo number_format($stats['total_amount'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <form method="GET" action="" class="filter-form" id="filterForm">
                    <div class="filter-group">
                        <label for="status">Appointment Status:</label>
                        <select name="status" id="status" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $filter_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="payment">Payment Status:</label>
                        <select name="payment" id="payment" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" <?php echo $filter_payment === 'all' ? 'selected' : ''; ?>>All Payments</option>
                            <option value="paid" <?php echo $filter_payment === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="pending" <?php echo $filter_payment === 'pending' ? 'selected' : ''; ?>>Payment Due</option>
                            <option value="waiting" <?php echo $filter_payment === 'waiting' ? 'selected' : ''; ?>>Awaiting Approval</option>
                        </select>
                    </div>
                    <a href="payments.php" class="btn">Reset</a>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="section">
                <div class="section-header">
                    <h2>Payment Records</h2>
                    <button onclick="window.print()" class="btn">🖨️ Print</button>
                </div>

                <?php if (empty($appointments)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">💳</div>
                        <h3>No Payment Records Found</h3>
                        <p>No appointments with fees match your filter criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table id="paymentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Resident</th>
                                    <th>Contact</th>
                                    <th>Service</th>
                                    <th>Appointment Date</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td>#<?php echo $appointment['id']; ?></td>
                                        <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($appointment['email']); ?><br>
                                            <small><?php echo htmlspecialchars($appointment['phone'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?><br>
                                            <small><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></small>
                                        </td>
                                        <td><strong style="color: #27ae60;">₱<?php echo number_format($appointment['fee'], 2); ?></strong></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($appointment['status'] === 'completed'): ?>
                                                <span class="payment-badge paid">✓ Paid</span>
                                            <?php elseif ($appointment['status'] === 'approved'): ?>
                                                <span class="payment-badge pending">⏳ Payment Due</span>
                                            <?php elseif ($appointment['status'] === 'pending'): ?>
                                                <span class="payment-badge waiting">⏰ Awaiting Approval</span>
                                            <?php else: ?>
                                                <span class="payment-badge cancelled">✗ <?php echo ucfirst($appointment['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm">View</a>
                                            <?php if ($appointment['status'] === 'approved'): ?>
                                                <button onclick="markAsPaid(<?php echo $appointment['id']; ?>)" class="btn btn-sm btn-success">Mark Paid</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Payment Methods Info -->
            <div class="section info-section">
                <h2>Accepted Payment Methods</h2>
                <div class="payment-methods-grid">
                    <div class="payment-method-card">
                        <h3>💵 Cash Payment</h3>
                        <p>Accepted at the Barangay Office during office hours</p>
                        <small>8:00 AM - 5:00 PM, Monday to Friday</small>
                    </div>
                    <div class="payment-method-card">
                        <h3>📱 GCash</h3>
                        <p><strong>Number:</strong> 09123456789</p>
                        <small>Require proof of payment upload</small>
                    </div>
                    <div class="payment-method-card">
                        <h3>💳 PayMaya</h3>
                        <p>Online payment platform</p>
                        <small>Require screenshot as proof</small>
                    </div>
                    <div class="payment-method-card">
                        <h3>🏦 Bank Transfer</h3>
                        <p>Available for online payments</p>
                        <small>Require deposit slip or receipt</small>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function markAsPaid(appointmentId) {
            if (confirm('Mark this appointment as paid and completed?')) {
                // You can implement AJAX call here to update status
                window.location.href = `../appointments.php?action=complete&id=${appointmentId}`;
            }
        }

        // Search functionality
        function searchTable() {
            const input = document.getElementById('searchInput');
            if (input) {
                const filter = input.value.toUpperCase();
                const table = document.getElementById('paymentsTable');
                const tr = table.getElementsByTagName('tr');

                for (let i = 1; i < tr.length; i++) {
                    const td = tr[i].getElementsByTagName('td');
                    let found = false;

                    for (let j = 0; j < td.length; j++) {
                        if (td[j]) {
                            const txtValue = td[j].textContent || td[j].innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                found = true;
                                break;
                            }
                        }
                    }

                    tr[i].style.display = found ? '' : 'none';
                }
            }
        }
    </script>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #3498db;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .stat-card.paid {
            border-left-color: #27ae60;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }

        .stat-card.pending {
            border-left-color: #f39c12;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }

        .stat-card.total {
            border-left-color: #3498db;
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        }

        .stat-icon {
            font-size: 48px;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-info p {
            margin: 5px 0 0 0;
            color: #555;
            font-size: 14px;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        .filter-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-group select:hover {
            border-color: #3498db;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            margin: 0;
        }

        .payment-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .payment-badge.paid {
            background: #d4edda;
            color: #155724;
        }

        .payment-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .payment-badge.waiting {
            background: #d1ecf1;
            color: #0c5460;
        }

        .payment-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-success {
            background-color: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background-color: #229954;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #7f8c8d;
        }

        .info-section {
            background: #f8f9fa;
            border-left: 4px solid #e74c3c;
        }

        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .payment-method-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .payment-method-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #3498db;
        }

        .payment-method-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .payment-method-card p {
            color: #555;
            margin-bottom: 10px;
        }

        .payment-method-card small {
            color: #7f8c8d;
            font-style: italic;
        }

        /* Table hover effects */
        #paymentsTable tbody tr {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #paymentsTable tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* Button hover effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-sm:hover {
            transform: translateY(-1px);
        }

        @media print {

            .sidebar,
            .stats-grid,
            .filter-section,
            .section-header button,
            .info-section,
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

            .page-header {
                margin-bottom: 20px;
                text-align: center;
            }

            .section {
                box-shadow: none;
                padding: 0;
            }

            table {
                font-size: 12px;
            }

            .section-header h2 {
                text-align: center;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }
        }
    </style>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>