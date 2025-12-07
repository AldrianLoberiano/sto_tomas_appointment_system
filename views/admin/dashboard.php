<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../controllers/AppointmentController.php';

$appointmentController = new AppointmentController();
$statistics = $appointmentController->getStatistics();

// Get today's appointments count
$database = new Database();
$db = $database->getConnection();
$today = date('Y-m-d');
$todayQuery = "SELECT COUNT(*) as today_count FROM appointments WHERE appointment_date = :today";
$todayStmt = $db->prepare($todayQuery);
$todayStmt->bindParam(':today', $today);
$todayStmt->execute();
$todayResult = $todayStmt->fetch(PDO::FETCH_ASSOC);
$todayCount = $todayResult['today_count'];

// Get time-based greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good morning";
    $greetingIcon = "☀️";
} elseif ($hour < 18) {
    $greeting = "Good afternoon";
    $greetingIcon = "🌤️";
} else {
    $greeting = "Good evening";
    $greetingIcon = "🌙";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 25px;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -80px; right: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%; z-index: 0;"></div>
                <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%; z-index: 0;"></div>
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">Admin Dashboard</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0; position: relative; z-index: 1; font-weight: 500;">
                    <?php echo $greetingIcon; ?> <?php echo $greeting; ?>, <strong><?php echo $_SESSION['full_name']; ?></strong>!
                    <?php if ($todayCount > 0): ?>
                        <span style="display: inline-block; margin-top: 8px; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; backdrop-filter: blur(10px);">
                            📅 <strong><?php echo $todayCount; ?></strong> appointment<?php echo $todayCount > 1 ? 's' : ''; ?> scheduled for today
                        </span>
                    <?php else: ?>
                        <span style="display: inline-block; margin-top: 8px; background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 20px;">
                            ✨ No appointments scheduled for today
                        </span>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card stat-total">
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon">📊</div>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total Appointments</p>
                        <h3 class="stat-number"><?php echo $statistics['total']; ?></h3>
                        <div class="stat-footer">All time records</div>
                    </div>
                </div>

                <div class="stat-card stat-pending">
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon">⏳</div>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Pending Approval</p>
                        <h3 class="stat-number"><?php echo $statistics['pending']; ?></h3>
                        <div class="stat-footer">Awaiting review</div>
                    </div>
                    <?php if ($statistics['pending'] > 0): ?>
                        <div class="stat-badge">Action Required</div>
                    <?php endif; ?>
                </div>

                <div class="stat-card stat-approved">
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon">✅</div>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Approved</p>
                        <h3 class="stat-number"><?php echo $statistics['approved']; ?></h3>
                        <div class="stat-footer">Ready for service</div>
                    </div>
                </div>

                <div class="stat-card stat-completed">
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon">✔️</div>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Completed</p>
                        <h3 class="stat-number"><?php echo $statistics['completed']; ?></h3>
                        <div class="stat-footer">Successfully served</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections">
                <div class="section" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); margin-top: 25px;">
                    <h2 style="color: #2d3748; font-size: 1.8rem; margin: 0 0 25px 0; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                        <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 6px; height: 35px; border-radius: 3px; display: inline-block;"></span>
                        📋 Recent Appointments
                    </h2>
                    <div class="table-container" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <table>
                            <thead>
                                <tr>
                                    <th>Queue #</th>
                                    <th>Resident</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $appointments = $appointmentController->getAll();
                                $count = 0;
                                while ($row = $appointments->fetch(PDO::FETCH_ASSOC) and $count < 10):
                                    $count++;
                                ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($row['queue_number'])): ?>
                                                <span class="badge badge-info" style="font-size: 1em; padding: 8px 12px;">
                                                    <?php echo $row['queue_number']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: #95a5a6;">Not Assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                        <td><?php echo $row['service_name']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status']; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button onclick="viewAppointment(<?php echo $row['id']; ?>)" class="btn btn-sm btn-info" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">👁️ View</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
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

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js?v=<?php echo time(); ?>"></script>
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
    </script>

    <style>
        /* Enhanced Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border-left: 4px solid #3498db;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(52, 152, 219, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .stat-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%);
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .stat-icon {
            font-size: 2rem;
            line-height: 1;
        }

        .stat-info {
            position: relative;
            z-index: 1;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 8px 0;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-footer {
            font-size: 0.85rem;
            color: #95a5a6;
            font-weight: 500;
        }

        .stat-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e74c3c;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Card-specific colors */
        .stat-total {
            border-left-color: #3498db;
        }

        .stat-total .stat-icon-wrapper {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.15) 0%, rgba(52, 152, 219, 0.05) 100%);
        }

        .stat-total .stat-number {
            color: #3498db;
        }

        .stat-pending {
            border-left-color: #f39c12;
            background: linear-gradient(135deg, #ffffff 0%, #fff9f0 100%);
        }

        .stat-pending .stat-icon-wrapper {
            background: linear-gradient(135deg, rgba(243, 156, 18, 0.15) 0%, rgba(243, 156, 18, 0.05) 100%);
        }

        .stat-pending .stat-number {
            color: #f39c12;
        }

        .stat-pending::before {
            background: radial-gradient(circle, rgba(243, 156, 18, 0.1) 0%, transparent 70%);
        }

        .stat-approved {
            border-left-color: #9b59b6;
            background: linear-gradient(135deg, #ffffff 0%, #f8f3fb 100%);
        }

        .stat-approved .stat-icon-wrapper {
            background: linear-gradient(135deg, rgba(155, 89, 182, 0.15) 0%, rgba(155, 89, 182, 0.05) 100%);
        }

        .stat-approved .stat-number {
            color: #9b59b6;
        }

        .stat-approved::before {
            background: radial-gradient(circle, rgba(155, 89, 182, 0.1) 0%, transparent 70%);
        }

        .stat-completed {
            border-left-color: #27ae60;
            background: linear-gradient(135deg, #ffffff 0%, #f0f9f4 100%);
        }

        .stat-completed .stat-icon-wrapper {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.15) 0%, rgba(39, 174, 96, 0.05) 100%);
        }

        .stat-completed .stat-number {
            color: #27ae60;
        }

        .stat-completed::before {
            background: radial-gradient(circle, rgba(39, 174, 96, 0.1) 0%, transparent 70%);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon-wrapper {
                width: 50px;
                height: 50px;
            }

            .stat-icon {
                font-size: 1.5rem;
            }

            .stat-number {
                font-size: 2rem;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Hover Effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Table row hover */
        .table-container table tbody tr {
            transition: all 0.2s ease;
        }

        .table-container table tbody tr:hover {
            background: #ecf0f1;
            transform: translateX(3px);
            cursor: pointer;
        }

        /* Badge hover */
        .badge {
            transition: all 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* Action buttons hover */
        .action-buttons .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        /* Link hover */
        a {
            transition: all 0.2s ease;
        }

        a:hover {
            opacity: 0.8;
        }

        /* Stat card enhanced hover */
        .stat-card:hover .stat-icon-wrapper {
            transform: rotate(5deg) scale(1.1);
        }

        .stat-card:hover .stat-number {
            transform: scale(1.05);
        }

        .stat-icon-wrapper {
            transition: all 0.3s ease;
        }

        .stat-number {
            transition: all 0.3s ease;
        }

        /* Modern table styling */
        .table-container table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-container table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .table-container table thead th {
            color: white;
            padding: 18px 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
        }

        .table-container table tbody tr {
            transition: all 0.3s ease;
            background: white;
        }

        .table-container table tbody tr:nth-child(even) {
            background: #f8f9ff;
        }

        .table-container table tbody tr:hover {
            background: linear-gradient(90deg, #f0f4ff 0%, #e8ecff 100%);
            transform: translateX(5px);
            box-shadow: -4px 0 0 #667eea;
        }

        .table-container table tbody td {
            padding: 18px 15px;
            border-bottom: 1px solid #e8e9f3;
        }

        /* Modern badge styling */
        .badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            text-transform: capitalize;
        }

        .badge-pending {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge-approved {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .badge-completed {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .badge-cancelled,
        .badge-rejected {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Modern button styling */
        .btn-sm {
            padding: 8px 18px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .btn-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-info:hover::before {
            left: 100%;
        }

        .btn-info:hover {
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        /* Alert styling */
        .alert {
            border-radius: 15px;
            padding: 18px 24px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        /* Enhanced stat cards with modern gradients */
        .stat-card {
            background: white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            border-radius: 20px;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 18px;
        }

        .stat-icon {
            font-size: 2.3rem;
        }

        .stat-number {
            font-size: 2.8rem;
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
</body>

</html>