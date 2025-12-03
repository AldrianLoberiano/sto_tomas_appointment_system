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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content">
            <div class="page-header">
                <h1>Appointments Management</h1>
                <p>Manage all appointment bookings</p>
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

            <div class="action-buttons">
                <button onclick="openAppointmentModal()" class="btn btn-primary">➕ New Appointment</button>
            </div>

            <div class="section">
                <h2>All Appointments</h2>
                <div class="table-container">
                    <table id="appointmentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td><?php echo $row['id']; ?></td>
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
                                        <a href="#" onclick="viewAppointment(<?php echo $row['id']; ?>)" class="btn btn-sm btn-info">View</a>
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
    </style>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>