<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../controllers/AppointmentController.php';

// Get appointment ID from URL
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Appointment ID is required';
    header('Location: appointments.php');
    exit();
}

$appointmentController = new AppointmentController();
$appointment = $appointmentController->getById($_GET['id']);

// Check if appointment exists
if (!$appointment) {
    $_SESSION['error'] = 'Appointment not found';
    header('Location: appointments.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content">
            <div class="page-header">
                <h1>📋 Appointment Details</h1>
                <p>View and manage appointment information</p>
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

            <div class="section">
                <div class="appointment-details-container">
                    <!-- Status and Action Header -->
                    <div class="status-header">
                        <div>
                            <h2>Appointment #<?php echo $appointment['id']; ?></h2>
                            <span class="badge badge-<?php echo $appointment['status']; ?> badge-lg">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                        </div>
                        <div class="quick-actions">
                            <?php if ($appointment['status'] == 'pending'): ?>
                                <button onclick="updateStatus(<?php echo $appointment['id']; ?>, 'approved')" class="btn btn-success">✓ Approve</button>
                                <button onclick="updateStatus(<?php echo $appointment['id']; ?>, 'rejected')" class="btn btn-danger">✗ Reject</button>
                            <?php elseif ($appointment['status'] == 'approved'): ?>
                                <button onclick="updateStatus(<?php echo $appointment['id']; ?>, 'completed')" class="btn btn-success">✓ Complete</button>
                            <?php endif; ?>
                            <button onclick="openEditModal()" class="btn btn-primary">✏️ Edit</button>
                        </div>
                    </div>

                    <!-- Service Information -->
                    <div class="details-card">
                        <h3>🛎️ Service Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Service Name:</label>
                                <span><?php echo htmlspecialchars($appointment['service_name']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Service Fee:</label>
                                <span class="fee-amount">₱<?php echo number_format($appointment['fee'], 2); ?></span>
                            </div>
                            <div class="detail-item full-width">
                                <label>Description:</label>
                                <span><?php echo htmlspecialchars($appointment['description'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item full-width">
                                <label>Requirements:</label>
                                <span><?php echo htmlspecialchars($appointment['requirements'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Schedule -->
                    <div class="details-card">
                        <h3>📅 Schedule Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Date:</label>
                                <span><?php echo date('F d, Y (l)', strtotime($appointment['appointment_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Time:</label>
                                <span><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Queue Number:</label>
                                <?php if (!empty($appointment['queue_number'])): ?>
                                    <span class="queue-number"><?php echo htmlspecialchars($appointment['queue_number']); ?></span>
                                <?php else: ?>
                                    <button onclick="assignQueue(<?php echo $appointment['id']; ?>)" class="btn btn-sm btn-info">Assign Queue</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Resident Information -->
                    <div class="details-card">
                        <h3>👤 Resident Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Name:</label>
                                <span><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span><?php echo htmlspecialchars($appointment['email']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span><?php echo htmlspecialchars($appointment['phone'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item full-width">
                                <label>Address:</label>
                                <span><?php echo htmlspecialchars($appointment['address'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Purpose and Notes -->
                    <div class="details-card">
                        <h3>📝 Additional Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item full-width">
                                <label>Purpose:</label>
                                <p class="text-content"><?php echo nl2br(htmlspecialchars($appointment['purpose'])); ?></p>
                            </div>
                            <?php if (!empty($appointment['notes'])): ?>
                                <div class="detail-item full-width">
                                    <label>Resident Notes:</label>
                                    <p class="text-content"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="detail-item full-width">
                                <label>Admin Notes:</label>
                                <?php if (!empty($appointment['admin_notes'])): ?>
                                    <p class="text-content admin-note"><?php echo nl2br(htmlspecialchars($appointment['admin_notes'])); ?></p>
                                <?php else: ?>
                                    <p class="text-muted">No admin notes</p>
                                <?php endif; ?>
                                <button onclick="openNotesModal()" class="btn btn-sm btn-secondary" style="margin-top: 10px;">Edit Admin Notes</button>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="details-card">
                        <h3>⏰ Timestamps</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Created:</label>
                                <span><?php echo date('M d, Y h:i A', strtotime($appointment['created_at'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Last Updated:</label>
                                <span><?php echo date('M d, Y h:i A', strtotime($appointment['updated_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons-container">
                        <a href="appointments.php" class="btn btn-secondary">← Back to Appointments</a>
                        <button onclick="window.print()" class="btn btn-info">🖨️ Print</button>
                        <button onclick="confirmDelete(<?php echo $appointment['id']; ?>)" class="btn btn-danger">🗑️ Delete</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Appointment</h2>
            <form method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update">
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_date">Appointment Date *</label>
                        <input type="date" id="edit_date" name="appointment_date" value="<?php echo $appointment['appointment_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_time">Appointment Time *</label>
                        <input type="time" id="edit_time" name="appointment_time" value="<?php echo $appointment['appointment_time']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_status">Status *</label>
                    <select id="edit_status" name="status" required>
                        <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $appointment['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="rejected" <?php echo $appointment['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_queue">Queue Number</label>
                    <input type="text" id="edit_queue" name="queue_number" value="<?php echo htmlspecialchars($appointment['queue_number'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="edit_admin_notes">Admin Notes</label>
                    <textarea id="edit_admin_notes" name="admin_notes" rows="4"><?php echo htmlspecialchars($appointment['admin_notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" onclick="closeEditModal()" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Notes Modal -->
    <div id="notesModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeNotesModal()">&times;</span>
            <h2>Update Admin Notes</h2>
            <form method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status">
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                <input type="hidden" name="status" value="<?php echo $appointment['status']; ?>">

                <div class="form-group">
                    <label for="notes_admin_notes">Admin Notes</label>
                    <textarea id="notes_admin_notes" name="admin_notes" rows="6" placeholder="Enter admin notes..."><?php echo htmlspecialchars($appointment['admin_notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Notes</button>
                <button type="button" onclick="closeNotesModal()" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Queue Assignment Modal -->
    <div id="queueModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeQueueModal()">&times;</span>
            <h2>Assign Queue Number</h2>
            <form method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update">
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                <input type="hidden" name="appointment_date" value="<?php echo $appointment['appointment_date']; ?>">
                <input type="hidden" name="appointment_time" value="<?php echo $appointment['appointment_time']; ?>">
                <input type="hidden" name="status" value="<?php echo $appointment['status']; ?>">
                <input type="hidden" name="admin_notes" value="<?php echo htmlspecialchars($appointment['admin_notes'] ?? ''); ?>">

                <div class="form-group">
                    <label for="queue_number">Queue Number *</label>
                    <input type="text" id="queue_number" name="queue_number" placeholder="e.g., A-001" required>
                </div>

                <button type="submit" class="btn btn-primary">Assign Queue</button>
                <button type="button" onclick="closeQueueModal()" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="statusForm" method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=update_status" style="display: none;">
        <input type="hidden" name="appointment_id" id="status_appointment_id">
        <input type="hidden" name="status" id="status_value">
    </form>

    <form id="deleteForm" method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=delete" style="display: none;">
        <input type="hidden" name="appointment_id" id="delete_appointment_id">
    </form>

    <script>
        function openEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openNotesModal() {
            document.getElementById('notesModal').style.display = 'block';
        }

        function closeNotesModal() {
            document.getElementById('notesModal').style.display = 'none';
        }

        function assignQueue(appointmentId) {
            document.getElementById('queueModal').style.display = 'block';
        }

        function closeQueueModal() {
            document.getElementById('queueModal').style.display = 'none';
        }

        function updateStatus(appointmentId, status) {
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);
            if (confirm(`Are you sure you want to ${statusText.toLowerCase()} this appointment?`)) {
                document.getElementById('status_appointment_id').value = appointmentId;
                document.getElementById('status_value').value = status;
                document.getElementById('statusForm').submit();
            }
        }

        function confirmDelete(appointmentId) {
            if (confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
                document.getElementById('delete_appointment_id').value = appointmentId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Print styles
        window.onbeforeprint = function() {
            document.querySelector('.action-buttons-container').style.display = 'none';
            document.querySelector('.quick-actions').style.display = 'none';
        };

        window.onafterprint = function() {
            document.querySelector('.action-buttons-container').style.display = 'flex';
            document.querySelector('.quick-actions').style.display = 'flex';
        };
    </script>

    <style>
        .appointment-details-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .status-header h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge-lg {
            padding: 10px 20px;
            font-size: 1.1em;
            font-weight: bold;
        }

        .details-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .details-card h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            font-size: 1.2em;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-item.full-width {
            grid-column: 1 / -1;
        }

        .detail-item label {
            font-size: 0.9em;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .detail-item span,
        .detail-item p {
            color: #2c3e50;
            font-size: 1em;
        }

        .text-content {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            line-height: 1.6;
            margin: 0;
        }

        .text-muted {
            color: #95a5a6;
            font-style: italic;
        }

        .admin-note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        .fee-amount {
            color: #27ae60;
            font-weight: bold;
            font-size: 1.2em;
        }

        .queue-number {
            background: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.1em;
            display: inline-block;
        }

        .action-buttons-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-info {
            background-color: #3498db;
            color: white;
        }

        .btn-info:hover {
            background-color: #2980b9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        @media (max-width: 768px) {

            .detail-grid,
            .form-row {
                grid-template-columns: 1fr;
            }

            .status-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .quick-actions {
                width: 100%;
            }

            .quick-actions .btn {
                flex: 1;
            }

            .action-buttons-container {
                flex-direction: column;
            }

            .action-buttons-container .btn {
                width: 100%;
            }
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .appointment-details-container,
            .appointment-details-container * {
                visibility: visible;
            }

            .appointment-details-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .details-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</body>

</html>