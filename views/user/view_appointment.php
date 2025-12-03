<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();

require_once __DIR__ . '/../../controllers/AppointmentController.php';

// Get appointment ID from URL
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Appointment ID is required';
    header('Location: dashboard.php');
    exit();
}

$appointmentController = new AppointmentController();
$appointment = $appointmentController->getById($_GET['id']);

// Check if appointment exists and belongs to user
if (!$appointment || $appointment['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Appointment not found or you do not have permission to view it';
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/user_header.php'; ?>

    <div class="main-container">
        <main class="content">
            <div class="page-header">
                <h1>📋 Appointment Details</h1>
                <p>View your appointment information</p>
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
                    <!-- Status Badge -->
                    <div class="status-header">
                        <h2>Appointment #<?php echo $appointment['id']; ?></h2>
                        <span class="badge badge-<?php echo $appointment['status']; ?> badge-lg">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
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
                            <?php if (!empty($appointment['queue_number'])): ?>
                                <div class="detail-item">
                                    <label>Queue Number:</label>
                                    <span class="queue-number"><?php echo htmlspecialchars($appointment['queue_number']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="details-card">
                        <h3>👤 Your Information</h3>
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
                                    <label>Your Notes:</label>
                                    <p class="text-content"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($appointment['admin_notes'])): ?>
                                <div class="detail-item full-width">
                                    <label>Admin Notes:</label>
                                    <p class="text-content admin-note"><?php echo nl2br(htmlspecialchars($appointment['admin_notes'])); ?></p>
                                </div>
                            <?php endif; ?>
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
                        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>

                        <?php if ($appointment['status'] == 'pending'): ?>
                            <button onclick="confirmCancel(<?php echo $appointment['id']; ?>)" class="btn btn-danger">Cancel Appointment</button>
                        <?php endif; ?>

                        <?php if ($appointment['fee'] > 0 && $appointment['status'] == 'approved'): ?>
                            <button onclick="openPaymentModal(<?php echo $appointment['id']; ?>, <?php echo $appointment['fee']; ?>, '<?php echo addslashes($appointment['service_name']); ?>')" class="btn btn-success">💳 Make Payment</button>
                        <?php endif; ?>

                        <button onclick="window.print()" class="btn btn-info">🖨️ Print</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Cancel Confirmation Form (Hidden) -->
    <form id="cancelForm" method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=cancel" style="display: none;">
        <input type="hidden" name="appointment_id" id="cancel_appointment_id">
    </form>

    <script>
        function confirmCancel(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) {
                document.getElementById('cancel_appointment_id').value = appointmentId;
                document.getElementById('cancelForm').submit();
            }
        }

        // Print styles
        window.onbeforeprint = function() {
            document.querySelector('.action-buttons-container').style.display = 'none';
        };

        window.onafterprint = function() {
            document.querySelector('.action-buttons-container').style.display = 'flex';
        };
    </script>

    <style>
        .appointment-details-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .status-header h2 {
            margin: 0;
            color: #2c3e50;
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
            border-bottom: 2px solid #3498db;
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
            background: #3498db;
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

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .status-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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