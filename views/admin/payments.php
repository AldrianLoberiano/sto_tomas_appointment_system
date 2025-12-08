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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 25px;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -80px; right: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">💳 Payment Management</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0; position: relative; z-index: 1;">Track and manage service payments</p>
            </div>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 35px;">
                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9f4 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #27ae60; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(39,174,96,0.15) 0%, rgba(39,174,96,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">💰</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Total Paid</p>
                    <h3 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: #27ae60; line-height: 1;">₱<?php echo number_format($stats['total_paid'], 2); ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;"><?php echo $stats['count_paid']; ?> payments</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #fff9f0 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #f39c12; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(243,156,18,0.15) 0%, rgba(243,156,18,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">⏳</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Pending Payment</p>
                    <h3 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: #f39c12; line-height: 1;">₱<?php echo number_format($stats['total_pending'], 2); ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;"><?php echo $stats['count_pending']; ?> pending</div>
                </div>

                <div class="stat-card" style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #3498db; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(52,152,219,0.1) 0%, rgba(52,152,219,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">📊</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Total Revenue</p>
                    <h3 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: #3498db; line-height: 1;">₱<?php echo number_format($stats['total_amount'], 2); ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">All transactions</div>
                </div>
            </div>


            <!-- Filters -->
            <div class="filter-section" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); margin-bottom: 30px;">
                <h2 style="color: #2d3748; font-size: 1.5rem; margin: 0 0 20px 0; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 5px; height: 28px; border-radius: 3px;"></span>
                    🔍 Filter Payments
                </h2>
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
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Payment Records</h2>
                    <button onclick="window.print()" class="btn btn-info" style="padding: 10px 24px; border-radius: 25px;">🖨️ Print</button>
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
                                            <button onclick="viewAppointment(<?php echo $appointment['id']; ?>)" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">View</button>
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

    <!-- View Appointment Modal -->
    <div id="viewAppointmentModal" class="modal">
        <div class="modal-content modal-content-large">
            <span class="close" onclick="closeViewModal()">&times;</span>
            <div class="modal-header">
                <h2 id="viewModalTitle">📋 Appointment Details</h2>
            </div>
            <div class="modal-body" style="padding: 30px 40px;">
                <div id="appointmentDetailsContent">
                    <div style="text-align: center; padding: 40px;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">⏳</div>
                        <p style="color: #7f8c8d;">Loading appointment details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewAppointment(id) {
            const modal = document.getElementById('viewAppointmentModal');
            const content = document.getElementById('appointmentDetailsContent');

            modal.style.display = 'block';

            fetch('<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=get&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const appt = data.appointment;
                        content.innerHTML = `
                            <div style="margin-bottom: 25px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; color: white;">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                                    <div>
                                        <h3 style="margin: 0 0 8px 0; font-size: 1.5rem;">Appointment #${appt.id}</h3>
                                        ${appt.queue_number ? `<p style="margin: 0; opacity: 0.9;">Queue Number: <strong>${appt.queue_number}</strong></p>` : ''}
                                    </div>
                                    <span class="badge badge-${appt.status}" style="font-size: 1rem; padding: 10px 20px;">
                                        ${appt.status.charAt(0).toUpperCase() + appt.status.slice(1)}
                                    </span>
                                </div>
                            </div>

                            <div style="display: grid; gap: 25px;">
                                <div style="background: #f8f9ff; padding: 25px; border-radius: 15px; border-left: 4px solid #667eea;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>👤</span> Resident Information
                                    </h3>
                                    <div style="display: grid; gap: 12px;">
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Name:</strong>
                                            <span style="color: #2d3748;">${appt.first_name} ${appt.last_name}</span>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Email:</strong>
                                            <span style="color: #2d3748;">${appt.email}</span>
                                        </div>
                                        ${appt.phone ? `
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Phone:</strong>
                                            <span style="color: #2d3748;">${appt.phone}</span>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>

                                <div style="background: #f0f9f4; padding: 25px; border-radius: 15px; border-left: 4px solid #27ae60;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>🛎️</span> Service Information
                                    </h3>
                                    <div style="display: grid; gap: 12px;">
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Service:</strong>
                                            <span style="color: #2d3748;">${appt.service_name}</span>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Fee:</strong>
                                            <span style="color: #27ae60; font-weight: 600;">₱${parseFloat(appt.fee).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                        </div>
                                        ${appt.description ? `
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Description:</strong>
                                            <span style="color: #2d3748;">${appt.description}</span>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>

                                <div style="background: #fff9f0; padding: 25px; border-radius: 15px; border-left: 4px solid #f39c12;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>📅</span> Schedule
                                    </h3>
                                    <div style="display: grid; gap: 12px;">
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Date:</strong>
                                            <span style="color: #2d3748;">${new Date(appt.appointment_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                                            <strong style="color: #7f8c8d;">Time:</strong>
                                            <span style="color: #2d3748;">${appt.appointment_time}</span>
                                        </div>
                                    </div>
                                </div>

                                ${appt.purpose ? `
                                <div style="background: #f8f3fb; padding: 25px; border-radius: 15px; border-left: 4px solid #9b59b6;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>📝</span> Purpose
                                    </h3>
                                    <p style="color: #2d3748; margin: 0; line-height: 1.6;">${appt.purpose}</p>
                                </div>
                                ` : ''}

                                ${appt.notes ? `
                                <div style="background: #f5f5f5; padding: 25px; border-radius: 15px; border-left: 4px solid #95a5a6;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>📄</span> Additional Notes
                                    </h3>
                                    <p style="color: #2d3748; margin: 0; line-height: 1.6;">${appt.notes}</p>
                                </div>
                                ` : ''}

                                <div style="display: flex; gap: 12px; flex-wrap: wrap; padding-top: 10px;">
                                    ${appt.status === 'pending' ? `
                                        <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="${appt.id}">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success" style="padding: 12px 24px; border-radius: 25px;">
                                                ✓ Approve Appointment
                                            </button>
                                        </form>
                                        <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="${appt.id}">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-danger" style="padding: 12px 24px; border-radius: 25px;">
                                                ✗ Reject Appointment
                                            </button>
                                        </form>
                                    ` : ''}
                                    ${appt.status === 'approved' ? `
                                        <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="${appt.id}">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-success" style="padding: 12px 24px; border-radius: 25px;">
                                                ✓ Mark as Completed
                                            </button>
                                        </form>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    } else {
                        content.innerHTML = `
                            <div style="text-align: center; padding: 40px;">
                                <div style="font-size: 3rem; margin-bottom: 10px;">❌</div>
                                <p style="color: #e74c3c; font-size: 1.1rem; font-weight: 500;">${data.message || 'Failed to load appointment details'}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    content.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <div style="font-size: 3rem; margin-bottom: 10px;">⚠️</div>
                            <p style="color: #e74c3c; font-size: 1.1rem; font-weight: 500;">Error loading appointment details</p>
                            <p style="color: #7f8c8d; font-size: 0.9rem;">${error.message}</p>
                        </div>
                    `;
                });
        }

        function closeViewModal() {
            document.getElementById('viewAppointmentModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('viewAppointmentModal');
            if (event.target == modal) {
                closeViewModal();
            }
        }

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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content-large {
            max-width: 800px;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1;
        }

        .close:hover {
            color: #333;
        }

        .modal-header {
            padding: 30px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
        }

        .modal-header h2 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
    </style>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>