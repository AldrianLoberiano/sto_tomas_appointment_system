<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Service.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../controllers/AppointmentController.php';

$database = new Database();
$db = $database->getConnection();

$appointmentController = new AppointmentController();
$service = new Service($db);
$user = new User($db);

$services = $service->readActive();
$users = $user->read();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - <?php echo SITE_NAME; ?></title>
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
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">📅 Appointments Management</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0; position: relative; z-index: 1;">Manage all appointment bookings</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="action-buttons" style="margin-bottom: 25px;">
                <button onclick="openAppointmentModal()" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 14px 32px; border-radius: 50px; font-size: 1.1rem; font-weight: 600; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Appointment
                </button>
            </div>

            <?php
            $stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM appointments";
            $stats_stmt = $db->prepare($stats_query);
            $stats_stmt->execute();
            $appt_stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 35px;">
                <div class="stat-card" style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #3498db; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(52,152,219,0.1) 0%, rgba(52,152,219,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">📊</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Total Appointments</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #3498db; line-height: 1;"><?php echo $appt_stats['total']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">All bookings</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #fff9f0 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #f39c12; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(243,156,18,0.15) 0%, rgba(243,156,18,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">⏳</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Pending</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #f39c12; line-height: 1;"><?php echo $appt_stats['pending']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Awaiting approval</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f3fb 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #9b59b6; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(155,89,182,0.15) 0%, rgba(155,89,182,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">✅</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Approved</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #9b59b6; line-height: 1;"><?php echo $appt_stats['approved']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Ready for service</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9f4 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #27ae60; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(39,174,96,0.15) 0%, rgba(39,174,96,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">✔️</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Completed</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #27ae60; line-height: 1;"><?php echo $appt_stats['completed']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Successfully served</div>
                </div>
            </div>

            <div class="section" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                <h2 style="color: #2d3748; font-size: 1.8rem; margin: 0 0 25px 0; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 6px; height: 35px; border-radius: 3px;"></span>
                    All Appointments
                </h2>
                <div class="table-container" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <table id="appointmentsTable">
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
                            while ($row = $appointments->fetch(PDO::FETCH_ASSOC)):
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
                                        <button onclick="viewAppointment(<?php echo $row['id']; ?>)" class="btn btn-sm btn-info" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">View</button>
                                        <?php if ($row['status'] == 'pending'): ?>
                                            <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        <?php elseif ($row['status'] == 'approved'): ?>
                                            <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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

    <!-- New Appointment Modal -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content modal-content-large">
            <span class="close" onclick="closeAppointmentModal()">&times;</span>
            <div class="modal-header">
                <h2>Create New Appointment</h2>
                <h3>Book appointment for a resident</h3>
            </div>

            <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=create" method="POST" id="adminAppointmentForm">
                <div class="form-group">
                    <label for="user_id">Resident *</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Select a resident</option>
                        <?php
                        $users_temp = $user->read();
                        while ($user_row = $users_temp->fetch(PDO::FETCH_ASSOC)):
                            if ($user_row['role'] == 'resident'):
                        ?>
                                <option value="<?php echo $user_row['id']; ?>">
                                    <?php echo $user_row['first_name'] . ' ' . $user_row['last_name']; ?> (<?php echo $user_row['email']; ?>)
                                </option>
                        <?php
                            endif;
                        endwhile;
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="service_id">Service *</label>
                    <select id="service_id" name="service_id" required>
                        <option value="">Select a service</option>
                        <?php
                        $services_temp = $service->readActive();
                        while ($service_row = $services_temp->fetch(PDO::FETCH_ASSOC)):
                        ?>
                            <option value="<?php echo $service_row['id']; ?>"
                                data-fee="<?php echo $service_row['fee']; ?>"
                                data-description="<?php echo htmlspecialchars($service_row['description']); ?>">
                                <?php echo $service_row['service_name']; ?> - ₱<?php echo number_format($service_row['fee'], 2); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="serviceInfo" class="service-info" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
                    <strong>Description:</strong>
                    <p id="serviceDescription" style="margin: 5px 0 10px 0;"></p>
                    <strong>Fee:</strong>
                    <p id="serviceFee" style="margin: 5px 0;"></p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="appointment_date">Appointment Date *</label>
                        <input type="date" id="appointment_date" name="appointment_date" required
                            min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                            max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                    </div>

                    <div class="form-group">
                        <label for="appointment_time">Appointment Time *</label>
                        <input type="time" id="appointment_time" name="appointment_time" required
                            min="08:00" max="17:00">
                        <small>Office hours: 8:00 AM - 5:00 PM</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose *</label>
                    <textarea id="purpose" name="purpose" rows="3" required
                        placeholder="Purpose of appointment"></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="2"
                        placeholder="Additional notes or instructions"></textarea>
                </div>

                <div class="form-group">
                    <label for="admin_notes">Admin Notes</label>
                    <textarea id="admin_notes" name="admin_notes" rows="2"
                        placeholder="Internal admin notes"></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="approved" selected>Approved</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Create Appointment</button>
            </form>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    <script>
        // Modal functions
        function openAppointmentModal() {
            document.getElementById('appointmentModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeAppointmentModal() {
            document.getElementById('appointmentModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('appointmentModal');
            if (event.target == modal) {
                closeAppointmentModal();
            }
        }

        // Service selection handler
        document.getElementById('service_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const serviceInfo = document.getElementById('serviceInfo');

            if (selectedOption.value) {
                document.getElementById('serviceDescription').textContent = selectedOption.getAttribute('data-description');
                document.getElementById('serviceFee').textContent = '₱' + parseFloat(selectedOption.getAttribute('data-fee')).toFixed(2);
                serviceInfo.style.display = 'block';
            } else {
                serviceInfo.style.display = 'none';
            }
        });

        function viewAppointment(id) {
            window.location.href = 'view_appointment.php?id=' + id;
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAppointmentModal();
            }
        });
    </script>

    <style>
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
            max-width: 700px;
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

        .modal-header h3 {
            color: #7f8c8d;
            font-weight: normal;
            font-size: 1rem;
            margin: 0;
        }

        .modal-content form {
            padding: 20px 40px 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        /* Hover Effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-sm:hover {
            transform: translateY(-2px) scale(1.05);
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

        /* Modal close button hover */
        .close {
            transition: all 0.2s ease;
        }

        .close:hover {
            transform: rotate(90deg);
            color: #e74c3c;
        }

        /* Input hover */
        .form-group input:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: #95a5a6;
        }

        /* Trendy table styling */
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
        }

        .table-container table tbody tr:nth-child(even) {
            background: #f8f9ff;
        }

        .table-container table tbody tr:hover {
            background: linear-gradient(90deg, #f0f4ff 0%, #e8ecff 100%);
            box-shadow: -4px 0 0 #667eea;
        }

        .table-container table tbody td {
            padding: 18px 15px;
            border-bottom: 1px solid #e8e9f3;
        }

        /* Trendy badges */
        .badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
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

        /* Modern buttons */
        .btn-sm {
            border-radius: 25px;
            font-weight: 600;
            padding: 8px 18px;
        }

        .btn-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-info:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 15px;
            padding: 18px 24px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .alert-error {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
    </style>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    <script>
        function viewAppointment(id) {
            const modal = document.getElementById('viewAppointmentModal');
            const content = document.getElementById('appointmentDetailsContent');

            modal.style.display = 'block';

            // Fetch appointment details
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
                                <!-- Resident Information -->
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

                                <!-- Service Information -->
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

                                <!-- Appointment Schedule -->
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
                                <!-- Purpose -->
                                <div style="background: #f8f3fb; padding: 25px; border-radius: 15px; border-left: 4px solid #9b59b6;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>📝</span> Purpose
                                    </h3>
                                    <p style="color: #2d3748; margin: 0; line-height: 1.6;">${appt.purpose}</p>
                                </div>
                                ` : ''}

                                ${appt.notes ? `
                                <!-- Notes -->
                                <div style="background: #f5f5f5; padding: 25px; border-radius: 15px; border-left: 4px solid #95a5a6;">
                                    <h3 style="color: #2d3748; margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                                        <span>📄</span> Additional Notes
                                    </h3>
                                    <p style="color: #2d3748; margin: 0; line-height: 1.6;">${appt.notes}</p>
                                </div>
                                ` : ''}

                                <!-- Action Buttons -->
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

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('viewAppointmentModal');
            if (event.target == modal) {
                closeViewModal();
            }
        }
    </script>
</body>

</html>