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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/user_header.php'; ?>

    <div class="main-container">
        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 25px;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -80px; right: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">📋 Appointment Details</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0; position: relative; z-index: 1;">View your appointment information</p>
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

                    <!-- Payment Proof -->
                    <?php if ($appointment['fee'] > 0): ?>
                        <div class="details-card">
                            <h3>💳 Payment Proof</h3>
                            <div class="detail-grid">
                                <div class="detail-item full-width">
                                    <?php if (!empty($appointment['payment_proof'])): ?>
                                        <div class="payment-proof-container">
                                            <label>Your Uploaded Payment Proof:</label>
                                            <div class="payment-proof-image">
                                                <img src="<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($appointment['payment_proof']); ?>"
                                                    alt="Payment Proof"
                                                    onclick="openImageModal(this.src)">
                                                <div class="payment-proof-info">
                                                    <p><strong>Uploaded on:</strong> <?php echo !empty($appointment['payment_proof_uploaded_at']) ? date('M d, Y h:i A', strtotime($appointment['payment_proof_uploaded_at'])) : 'N/A'; ?></p>
                                                    <p class="info-text">✓ Your payment proof has been submitted. Please wait for admin verification.</p>
                                                    <div class="payment-proof-actions">
                                                        <a href="<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($appointment['payment_proof']); ?>"
                                                            target="_blank"
                                                            class="btn btn-sm btn-info">👁️ View Full Size</a>
                                                        <a href="<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($appointment['payment_proof']); ?>"
                                                            download
                                                            class="btn btn-sm btn-primary">⬇️ Download</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($appointment['status'] == 'approved'): ?>
                                        <div class="upload-prompt">
                                            <p class="text-muted">
                                                <span class="no-payment-icon">📄</span>
                                                No payment proof uploaded yet
                                            </p>
                                            <p class="info-text">Please upload your payment proof to complete the transaction.</p>
                                            <button onclick="window.location.href='dashboard.php'" class="btn btn-primary">
                                                📤 Upload Payment Proof
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">
                                            <span class="no-payment-icon">📄</span>
                                            Payment proof can be uploaded once your appointment is approved
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

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
                        <button onclick="downloadReceipt()" class="btn btn-primary">📥 Download Receipt</button>

                        <?php if ($appointment['status'] == 'pending'): ?>
                            <button onclick="confirmCancel(<?php echo $appointment['id']; ?>)" class="btn btn-danger">Cancel Appointment</button>
                        <?php endif; ?>

                        <?php if ($appointment['fee'] > 0 && $appointment['status'] == 'approved'): ?>
                            <button onclick="openPaymentModal(<?php echo $appointment['id']; ?>, <?php echo $appointment['fee']; ?>, '<?php echo addslashes($appointment['service_name']); ?>')" class="btn btn-success">💳 Make Payment</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal image-modal">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img class="modal-image-content" id="modalImage">
    </div>

    <!-- Cancel Confirmation Form (Hidden) -->
    <form id="cancelForm" method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=cancel" style="display: none;">
        <input type="hidden" name="appointment_id" id="cancel_appointment_id">
    </form>

    <script>
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'block';
            modalImg.src = imageSrc;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        function confirmCancel(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) {
                document.getElementById('cancel_appointment_id').value = appointmentId;
                document.getElementById('cancelForm').submit();
            }
        }

        function downloadReceipt() {
            // Create receipt HTML content
            const receiptHTML = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Receipt - #<?php echo str_pad($appointment['id'], 6, '0', STR_PAD_LEFT); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            padding: 20px; 
            background: #fff; 
            color: #333; 
            max-width: 600px; 
            margin: 0 auto;
            line-height: 1.6;
        }
        .receipt-header { 
            text-align: center; 
            padding-bottom: 20px; 
            border-bottom: 3px double #333;
            margin-bottom: 20px;
        }
        .receipt-header h1 { 
            font-size: 24px; 
            color: #2c3e50; 
            margin-bottom: 8px;
        }
        .receipt-header .site-name { 
            font-size: 18px;
            color: #7f8c8d; 
            font-weight: 600;
        }
        .receipt-header .date { 
            font-size: 14px; 
            color: #95a5a6;
            margin-top: 8px;
        }
        .receipt-section { 
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .receipt-section h2 { 
            font-size: 16px; 
            color: #2c3e50;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
        }
        .receipt-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { 
            font-weight: 600; 
            color: #7f8c8d;
            font-size: 14px;
        }
        .receipt-value { 
            color: #2c3e50;
            font-weight: 500;
            text-align: right;
            font-size: 14px;
        }
        .receipt-total { 
            background: #fff;
            padding: 15px;
            margin: 20px 0;
            border: 3px solid #27ae60;
            border-radius: 8px;
            text-align: center;
        }
        .receipt-total .label { 
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 600;
        }
        .receipt-total .amount { 
            font-size: 32px;
            color: #27ae60;
            font-weight: bold;
            margin-top: 5px;
        }
        .status-badge { 
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background: #ffa500; color: white; }
        .status-approved { background: #9b59b6; color: white; }
        .status-completed { background: #27ae60; color: white; }
        .status-cancelled, .status-rejected { background: #e74c3c; color: white; }
        .receipt-footer { 
            text-align: center; 
            margin-top: 30px;
            padding-top: 20px; 
            border-top: 3px double #333;
            color: #7f8c8d;
            font-size: 13px;
        }
        .thank-you {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
            margin-top: 15px;
        }
        .purpose-text {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #3498db;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        @media print {
            body { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h1>🧾 APPOINTMENT RECEIPT</h1>
        <div class="site-name"><?php echo SITE_NAME; ?></div>
        <div class="date">Generated: <?php echo date('F d, Y - h:i A'); ?></div>
    </div>

    <div class="receipt-section">
        <h2>📋 Receipt Information</h2>
        <div class="receipt-row">
            <span class="receipt-label">Receipt Number:</span>
            <span class="receipt-value">#<?php echo str_pad($appointment['id'], 6, '0', STR_PAD_LEFT); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Status:</span>
            <span class="receipt-value">
                <span class="status-badge status-<?php echo $appointment['status']; ?>">
                    <?php echo strtoupper($appointment['status']); ?>
                </span>
            </span>
        </div>
    </div>

    <div class="receipt-section">
        <h2>👤 Customer Information</h2>
        <div class="receipt-row">
            <span class="receipt-label">Name:</span>
            <span class="receipt-value"><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Email:</span>
            <span class="receipt-value"><?php echo htmlspecialchars($appointment['email']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Phone:</span>
            <span class="receipt-value"><?php echo htmlspecialchars($appointment['phone'] ?? 'N/A'); ?></span>
        </div>
    </div>

    <div class="receipt-section">
        <h2>🛎️ Service Details</h2>
        <div class="receipt-row">
            <span class="receipt-label">Service:</span>
            <span class="receipt-value"><?php echo htmlspecialchars($appointment['service_name']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Date:</span>
            <span class="receipt-value"><?php echo date('F d, Y (l)', strtotime($appointment['appointment_date'])); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Time:</span>
            <span class="receipt-value"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></span>
        </div>
        <?php if (!empty($appointment['queue_number'])): ?>
        <div class="receipt-row">
            <span class="receipt-label">Queue Number:</span>
            <span class="receipt-value"><?php echo htmlspecialchars($appointment['queue_number']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="receipt-total">
        <div class="label">Service Fee</div>
        <div class="amount">₱<?php echo number_format($appointment['fee'], 2); ?></div>
        <div style="margin-top: 10px; font-size: 14px; color: #7f8c8d;">
            Payment Status: 
            <?php if (!empty($appointment['payment_proof'])): ?>
                <span style="color: #27ae60; font-weight: bold;">✓ Proof Uploaded</span>
            <?php elseif ($appointment['fee'] == 0): ?>
                <span style="color: #7f8c8d; font-weight: bold;">Free Service</span>
            <?php else: ?>
                <span style="color: #e74c3c; font-weight: bold;">⚠ Pending</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="receipt-section">
        <h2>📝 Purpose of Appointment</h2>
        <div class="purpose-text"><?php echo nl2br(htmlspecialchars($appointment['purpose'])); ?></div>
    </div>

    <?php if (!empty($appointment['admin_notes'])): ?>
    <div class="receipt-section">
        <h2>📋 Admin Notes</h2>
        <div class="purpose-text" style="border-left-color: #ffc107; background: #fff3cd;">
            <?php echo nl2br(htmlspecialchars($appointment['admin_notes'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="receipt-footer">
        <p><strong>Important:</strong> Please keep this receipt for your records.</p>
        <p>Appointment Created: <?php echo date('M d, Y h:i A', strtotime($appointment['created_at'])); ?></p>
        <div class="thank-you">Thank you for using our service!</div>
    </div>
</body>
</html>`;

            // Create blob and download
            const blob = new Blob([receiptHTML], {
                type: 'text/html'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Appointment_Receipt_<?php echo str_pad($appointment['id'], 6, '0', STR_PAD_LEFT); ?>_<?php echo date('Ymd'); ?>.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            // Show success message
            alert('✓ Receipt downloaded successfully!\n\nYou can open it anytime on your phone or computer.');
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
            padding: 25px 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .status-header h2 {
            margin: 0 0 12px 0;
            color: #2d3748;
            font-size: 2rem;
            font-weight: 700;
        }

        .badge-lg {
            padding: 12px 24px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 50px;
        }

        .details-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .details-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .details-card h3 {
            margin: 0 0 25px 0;
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .details-card h3::before {
            content: '';
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 5px;
            height: 28px;
            border-radius: 3px;
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
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(149, 165, 166, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 172, 254, 0.4);
        }

        /* Payment Proof Styles */
        .payment-proof-container {
            width: 100%;
        }

        .payment-proof-image {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-top: 10px;
        }

        .payment-proof-image img {
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            border: 3px solid #e0e0e0;
            cursor: pointer;
            transition: transform 0.3s, border-color 0.3s;
        }

        .payment-proof-image img:hover {
            transform: scale(1.05);
            border-color: #667eea;
        }

        .payment-proof-info {
            flex: 1;
        }

        .payment-proof-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .upload-prompt {
            text-align: center;
            padding: 20px;
        }

        .upload-prompt .btn {
            margin-top: 15px;
        }

        .info-text {
            color: #27ae60;
            font-size: 0.95em;
            margin-top: 10px;
            padding: 10px;
            background: #d4edda;
            border-radius: 5px;
            border-left: 4px solid #27ae60;
        }

        .no-payment-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }

        /* Image Modal */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .image-modal .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .image-modal .close:hover,
        .image-modal .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-image-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 80vh;
            animation: zoom 0.3s;
        }

        @keyframes zoom {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
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

            .payment-proof-image {
                flex-direction: column;
            }

            .payment-proof-image img {
                max-width: 100%;
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

            .payment-proof-image img {
                max-width: 200px;
            }
        }
    </style>
</body>

</html>