<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();

require_once __DIR__ . '/../../controllers/AppointmentController.php';

$appointmentController = new AppointmentController();
$appointments = $appointmentController->getUserAppointments($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/user_header.php'; ?>

    <div class="main-container">
        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 0;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 0;"></div>
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">My Dashboard</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.2rem; margin: 0; position: relative; z-index: 1;">👋 Welcome back, <strong><?php echo $_SESSION['full_name']; ?></strong>!</p>
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

            <div class="action-buttons" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <a href="new_appointment.php" class="btn btn-primary new-appt-btn" style="font-size: 1.1rem; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 50px; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); position: relative; overflow: hidden; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                    <span style="position: relative; z-index: 2; display: flex; align-items: center; font-weight: 600; letter-spacing: 0.5px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <line x1="12" y1="14" x2="12" y2="18"></line>
                            <line x1="10" y1="16" x2="14" y2="16"></line>
                        </svg>
                        Book New Appointment
                    </span>
                </a>
                <div class="filter-container" style="display: flex; gap: 12px; align-items: center; background: white; padding: 12px 20px; border-radius: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <select id="statusFilter" class="filter-select" style="padding: 10px 18px; border: 2px solid #e8e9f3; border-radius: 25px; font-size: 14px; cursor: pointer; transition: all 0.3s; background: #f8f9ff; font-weight: 500; color: #4a5568;">
                        <option value="all">🔍 All Status</option>
                        <option value="pending">⏳ Pending</option>
                        <option value="approved">✅ Approved</option>
                        <option value="completed">✔️ Completed</option>
                        <option value="cancelled">❌ Cancelled</option>
                        <option value="rejected">⛔ Rejected</option>
                    </select>
                </div>
            </div>

            <div class="section" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); backdrop-filter: blur(10px);">
                <h2 style="color: #2d3748; font-size: 1.8rem; margin: 0 0 25px 0; display: flex; align-items: center; gap: 12px;">
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 6px; height: 35px; border-radius: 3px; display: inline-block;"></span>
                    My Appointments
                </h2>
                <div class="table-container" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <table>
                        <thead>
                            <tr>
                                <th>Queue #</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $appointments->fetch(PDO::FETCH_ASSOC)): ?>
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
                                    <td><?php echo $row['service_name']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                                    <td>₱<?php echo number_format($row['fee'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status']; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="openViewAppointmentModal(<?php echo $row['id']; ?>)" class="btn btn-sm btn-info">View</button>
                                        <?php if ($row['fee'] > 0 && $row['status'] == 'approved' && $row['payment_method'] !== 'cash'): ?>
                                            <?php if (empty($row['payment_proof'])): ?>
                                                <button onclick="openUploadProofModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['service_name']); ?>', <?php echo $row['fee']; ?>)" class="btn btn-sm btn-success">📤 Upload Proof</button>
                                            <?php else: ?>
                                                <span class="badge badge-completed" style="font-size: 0.8em;">✓ Proof Uploaded</span>
                                            <?php endif; ?>
                                        <?php elseif ($row['fee'] > 0 && $row['status'] == 'approved' && $row['payment_method'] === 'cash'): ?>
                                            <span class="badge" style="background: #28a745; color: white; font-size: 0.8em;">💵 Cash Payment</span>
                                        <?php endif; ?>
                                        <?php if ($row['status'] == 'pending'): ?>
                                            <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=cancel" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this appointment?')">Cancel</button>
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

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="closePaymentModal()">&times;</span>
            <h2>💳 Payment Options</h2>

            <div class="payment-summary">
                <h3>Payment Summary</h3>
                <div class="summary-item">
                    <span>Appointment ID:</span>
                    <strong id="payment_appointment_id_display"></strong>
                </div>
                <div class="summary-item">
                    <span>Service:</span>
                    <strong id="payment_service"></strong>
                </div>
                <div class="summary-item">
                    <span>Amount Due:</span>
                    <strong id="payment_amount_display" style="color: #27ae60; font-size: 1.3em;"></strong>
                </div>
            </div>

            <div class="payment-methods">
                <h3>Select Payment Method</h3>

                <div class="payment-method-card" onclick="selectPaymentMethod('cash')">
                    <div class="method-icon">💵</div>
                    <div class="method-info">
                        <h4>Cash Payment</h4>
                        <p>Pay at Barangay Office</p>
                        <small>Office Hours: Mon-Fri, 8:00 AM - 5:00 PM</small>
                    </div>
                    <div class="method-arrow">→</div>
                </div>

                <div class="payment-method-card" onclick="selectPaymentMethod('gcash')">
                    <div class="method-icon">📱</div>
                    <div class="method-info">
                        <h4>GCash</h4>
                        <p>Pay via GCash Mobile App</p>
                        <small>Instant verification required</small>
                    </div>
                    <div class="method-arrow">→</div>
                </div>

                <div class="payment-method-card" onclick="selectPaymentMethod('paymaya')">
                    <div class="method-icon">💳</div>
                    <div class="method-info">
                        <h4>PayMaya</h4>
                        <p>Pay via PayMaya Mobile App</p>
                        <small>Instant verification required</small>
                    </div>
                    <div class="method-arrow">→</div>
                </div>

                <div class="payment-method-card" onclick="selectPaymentMethod('bank')">
                    <div class="method-icon">🏦</div>
                    <div class="method-info">
                        <h4>Bank Transfer</h4>
                        <p>Transfer to Barangay Bank Account</p>
                        <small>Submit deposit slip for verification</small>
                    </div>
                    <div class="method-arrow">→</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div id="paymentDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="closePaymentDetailsModal()">&times;</span>
            <h2 id="payment_method_title"></h2>

            <div id="payment_instructions" class="payment-instructions"></div>

            <form id="paymentForm" method="POST" action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=confirm_payment" enctype="multipart/form-data">
                <input type="hidden" id="payment_appointment_id" name="appointment_id">
                <input type="hidden" id="payment_method" name="payment_method">
                <input type="hidden" id="payment_amount" name="amount">

                <div id="online_payment_fields" style="display:none;">
                    <div class="form-group">
                        <label for="reference_number">Reference/Transaction Number *</label>
                        <input type="text" id="reference_number" name="reference_number" placeholder="Enter reference number">
                    </div>

                    <div class="form-group">
                        <label for="payment_proof">Upload Proof of Payment *</label>
                        <input type="file" id="payment_proof" name="payment_proof" accept="image/*,.pdf">
                        <small>Upload screenshot or receipt (JPG, PNG, PDF - Max 2MB)</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="payment_notes">Notes (Optional)</label>
                    <textarea id="payment_notes" name="notes" rows="3" placeholder="Additional information..."></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <span id="submit_button_text">Confirm Payment</span>
                    </button>
                    <button type="button" onclick="closePaymentDetailsModal()" class="btn" style="width: 100%; margin-top: 10px;">Back</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Payment Proof Modal -->
    <div id="uploadProofModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="closeUploadProofModal()">&times;</span>
            <h2>📤 Upload Payment Proof</h2>

            <div class="payment-summary">
                <h3>Appointment Details</h3>
                <div class="summary-item">
                    <span>Appointment ID:</span>
                    <strong id="proof_appointment_id_display"></strong>
                </div>
                <div class="summary-item">
                    <span>Service:</span>
                    <strong id="proof_service"></strong>
                </div>
                <div class="summary-item">
                    <span>Amount:</span>
                    <strong id="proof_amount_display" style="color: #27ae60; font-size: 1.3em;"></strong>
                </div>
            </div>

            <form id="uploadProofForm" method="POST" action="<?php echo SITE_URL; ?>/controllers/upload_payment_proof.php" enctype="multipart/form-data">
                <input type="hidden" id="proof_appointment_id" name="appointment_id">

                <div class="upload-instructions">
                    <h4>📋 Instructions:</h4>
                    <ul>
                        <li>Upload a clear photo or screenshot of your payment receipt</li>
                        <li>Accepted formats: JPG, JPEG, PNG, PDF</li>
                        <li>Maximum file size: 5MB</li>
                        <li>Make sure payment details are visible</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="payment_proof_file">Select Payment Proof Image *</label>
                    <input type="file"
                        id="payment_proof_file"
                        name="payment_proof"
                        accept="image/jpeg,image/jpg,image/png,application/pdf"
                        required
                        onchange="previewProofImage(event)">
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">
                        Supported formats: JPG, PNG, PDF (Max: 5MB)
                    </small>
                </div>

                <div id="proof_preview" class="proof-preview" style="display:none;">
                    <h4>Preview:</h4>
                    <img id="proof_preview_img" src="" alt="Payment Proof Preview">
                </div>

                <div class="form-group">
                    <label for="proof_notes">Additional Notes (Optional)</label>
                    <textarea id="proof_notes" name="notes" rows="3" placeholder="e.g., Reference number, payment date, etc."></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        📤 Upload Payment Proof
                    </button>
                    <button type="button" onclick="closeUploadProofModal()" class="btn" style="width: 100%; margin-top: 10px;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div id="viewAppointmentModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <span class="close" onclick="closeViewAppointmentModal()">&times;</span>
            <div id="appointmentDetailsContent">
                <div style="text-align: center; padding: 40px;">
                    <div class="loading-spinner"></div>
                    <p>Loading appointment details...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAppointmentId = null;
        let currentAmount = 0;
        let currentService = '';

        function openViewAppointmentModal(appointmentId) {
            document.getElementById('viewAppointmentModal').style.display = 'block';

            // Fetch appointment details via AJAX
            fetch('<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=get&id=' + appointmentId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAppointmentDetails(data.appointment);
                    } else {
                        document.getElementById('appointmentDetailsContent').innerHTML =
                            '<div class="alert alert-error">Failed to load appointment details.</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('appointmentDetailsContent').innerHTML =
                        '<div class="alert alert-error">Error loading appointment details.</div>';
                });
        }

        function closeViewAppointmentModal() {
            document.getElementById('viewAppointmentModal').style.display = 'none';
        }

        function displayAppointmentDetails(appointment) {
            const statusBadgeClass = 'badge-' + appointment.status;
            const paymentProofSection = appointment.payment_proof ?
                `<div class="detail-item full-width">
                    <label>Payment Proof:</label>
                    <span>
                        <a href="<?php echo SITE_URL; ?>/uploads/payment_proofs/${appointment.payment_proof}" 
                           target="_blank" class="btn btn-sm btn-info">View Payment Proof</a>
                    </span>
                </div>` : '';

            const content = `
                <div class="appointment-details-container">
                    <!-- Status Badge -->
                    <div class="status-header">
                        <h2>📋 Appointment #${appointment.id}</h2>
                        <span class="badge ${statusBadgeClass} badge-lg">
                            ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                        </span>
                    </div>

                    <!-- Service Information -->
                    <div class="details-card">
                        <h3>🛎️ Service Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Service Name:</label>
                                <span>${appointment.service_name}</span>
                            </div>
                            <div class="detail-item">
                                <label>Service Fee:</label>
                                <span class="fee-amount">₱${parseFloat(appointment.fee).toFixed(2)}</span>
                            </div>
                            <div class="detail-item full-width">
                                <label>Description:</label>
                                <span>${appointment.description || 'N/A'}</span>
                            </div>
                            <div class="detail-item full-width">
                                <label>Requirements:</label>
                                <span>${appointment.requirements || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Schedule -->
                    <div class="details-card">
                        <h3>📅 Schedule Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Date:</label>
                                <span>${new Date(appointment.appointment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            <div class="detail-item">
                                <label>Time:</label>
                                <span>${appointment.appointment_time}</span>
                            </div>
                            ${appointment.queue_number ? `
                            <div class="detail-item">
                                <label>Queue Number:</label>
                                <span class="badge badge-info">${appointment.queue_number}</span>
                            </div>` : ''}
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="details-card">
                        <h3>👤 Personal Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Name:</label>
                                <span>${appointment.first_name} ${appointment.last_name}</span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span>${appointment.email}</span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span>${appointment.phone || 'N/A'}</span>
                            </div>
                            <div class="detail-item">
                                <label>Address:</label>
                                <span>${appointment.address || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="details-card">
                        <h3>📝 Purpose of Appointment</h3>
                        <div class="purpose-section">
                            ${appointment.purpose}
                        </div>
                    </div>

                    ${appointment.admin_notes ? `
                    <div class="details-card">
                        <h3>📋 Admin Notes</h3>
                        <div class="admin-notes">
                            ${appointment.admin_notes}
                        </div>
                    </div>` : ''}

                    <!-- Payment Information -->
                    ${appointment.fee > 0 ? `
                    <div class="details-card">
                        <h3>💳 Payment Information</h3>
                        <div class="detail-grid">
                            ${paymentProofSection}
                            ${appointment.payment_proof_uploaded_at ? `
                            <div class="detail-item">
                                <label>Uploaded At:</label>
                                <span>${new Date(appointment.payment_proof_uploaded_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                            </div>` : ''}
                        </div>
                    </div>` : ''}

                    <!-- Timestamps -->
                    <div class="details-card">
                        <h3>🕐 Timestamps</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Created:</label>
                                <span>${new Date(appointment.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                            <div class="detail-item">
                                <label>Last Updated:</label>
                                <span>${new Date(appointment.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons-container" style="margin-top: 20px;">
                        <button onclick="closeViewAppointmentModal()" class="btn btn-secondary">← Close</button>
                        <button onclick="downloadReceiptFromModal(${appointment.id})" class="btn btn-primary">📥 Download Receipt</button>
                        ${appointment.status === 'pending' ? 
                            `<button onclick="confirmCancelFromModal(${appointment.id})" class="btn btn-danger">Cancel Appointment</button>` : ''}
                        ${appointment.fee > 0 && appointment.status === 'approved' && !appointment.payment_proof && appointment.payment_method !== 'cash' ? 
                            `<button onclick="closeViewAppointmentModal(); openUploadProofModal(${appointment.id}, '${appointment.service_name}', ${appointment.fee})" class="btn btn-success">💳 Upload Payment Proof</button>` : ''}
                        ${appointment.fee > 0 && appointment.payment_method === 'cash' ? 
                            `<div style="padding: 10px; background: #d4edda; border-radius: 6px; margin-top: 10px; width: 100%;">
                                <span style="color: #155724;">💵 <strong>Cash Payment:</strong> Pay at office with your appointment confirmation. No proof upload required.</span>
                            </div>` : ''}
                    </div>
                </div>
            `;

            document.getElementById('appointmentDetailsContent').innerHTML = content;
        }

        function confirmCancelFromModal(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=cancel';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'appointment_id';
                input.value = appointmentId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function downloadReceiptFromModal(appointmentId) {
            // Fetch appointment data again to ensure we have all details
            fetch('<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=get&id=' + appointmentId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        generateAndDownloadReceipt(data.appointment);
                    } else {
                        alert('Failed to generate receipt. Please try again.');
                    }
                })
                .catch(error => {
                    alert('Error generating receipt. Please try again.');
                });
        }

        function generateAndDownloadReceipt(appointment) {
            const today = new Date();
            const appointmentDate = new Date(appointment.appointment_date);
            const createdAt = new Date(appointment.created_at);

            // Format dates
            const generatedDate = today.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) + ' - ' +
                today.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            const formattedAppointmentDate = appointmentDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'long'
            });
            const formattedCreatedAt = createdAt.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) + ' ' +
                createdAt.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

            const receiptNumber = String(appointment.id).padStart(6, '0');
            const filename = `Appointment_Receipt_${receiptNumber}_${today.getFullYear()}${String(today.getMonth()+1).padStart(2,'0')}${String(today.getDate()).padStart(2,'0')}.html`;

            // Payment status
            let paymentStatus = '';
            if (appointment.payment_proof) {
                paymentStatus = '<span style="color: #27ae60; font-weight: bold;">✓ Proof Uploaded</span>';
            } else if (appointment.fee == 0) {
                paymentStatus = '<span style="color: #7f8c8d; font-weight: bold;">Free Service</span>';
            } else {
                paymentStatus = '<span style="color: #e74c3c; font-weight: bold;">⚠ Pending</span>';
            }

            const queueNumberSection = appointment.queue_number ? `
        <div class="receipt-row">
            <span class="receipt-label">Queue Number:</span>
            <span class="receipt-value">${appointment.queue_number}</span>
        </div>` : '';

            const adminNotesSection = appointment.admin_notes ? `
    <div class="receipt-section">
        <h2>📋 Admin Notes</h2>
        <div class="purpose-text" style="border-left-color: #ffc107; background: #fff3cd;">
            ${appointment.admin_notes.replace(/\n/g, '<br>')}
        </div>
    </div>` : '';

            const receiptHTML = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Receipt - #${receiptNumber}</title>
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
        <div class="date">Generated: ${generatedDate}</div>
    </div>

    <div class="receipt-section">
        <h2>📋 Receipt Information</h2>
        <div class="receipt-row">
            <span class="receipt-label">Receipt Number:</span>
            <span class="receipt-value">#${receiptNumber}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Status:</span>
            <span class="receipt-value">
                <span class="status-badge status-${appointment.status}">
                    ${appointment.status.toUpperCase()}
                </span>
            </span>
        </div>
    </div>

    <div class="receipt-section">
        <h2>👤 Customer Information</h2>
        <div class="receipt-row">
            <span class="receipt-label">Name:</span>
            <span class="receipt-value">${appointment.first_name} ${appointment.last_name}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Email:</span>
            <span class="receipt-value">${appointment.email}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Phone:</span>
            <span class="receipt-value">${appointment.phone || 'N/A'}</span>
        </div>
    </div>

    <div class="receipt-section">
        <h2>🛎️ Service Details</h2>
        <div class="receipt-row">
            <span class="receipt-label">Service:</span>
            <span class="receipt-value">${appointment.service_name}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Date:</span>
            <span class="receipt-value">${formattedAppointmentDate}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Time:</span>
            <span class="receipt-value">${appointment.appointment_time}</span>
        </div>
        ${queueNumberSection}
    </div>

    <div class="receipt-total">
        <div class="label">Service Fee</div>
        <div class="amount">₱${parseFloat(appointment.fee).toFixed(2)}</div>
        <div style="margin-top: 10px; font-size: 14px; color: #7f8c8d;">
            Payment Status: ${paymentStatus}
        </div>
    </div>

    <div class="receipt-section">
        <h2>📝 Purpose of Appointment</h2>
        <div class="purpose-text">${appointment.purpose.replace(/\n/g, '<br>')}</div>
    </div>

    ${adminNotesSection}

    <div class="receipt-footer">
        <p><strong>Important:</strong> Please keep this receipt for your records.</p>
        <p>Appointment Created: ${formattedCreatedAt}</p>
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
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            // Show success message
            alert('✓ Receipt downloaded successfully!\n\nYou can open it anytime on your phone or computer.');
        }

        function openPaymentModal(appointmentId, amount, serviceName) {
            currentAppointmentId = appointmentId;
            currentAmount = amount;
            currentService = serviceName;

            document.getElementById('payment_appointment_id_display').textContent = '#' + appointmentId;
            document.getElementById('payment_service').textContent = serviceName;
            document.getElementById('payment_amount_display').textContent = '₱' + parseFloat(amount).toFixed(2);
            document.getElementById('paymentModal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function selectPaymentMethod(method) {
            document.getElementById('payment_appointment_id').value = currentAppointmentId;
            document.getElementById('payment_method').value = method;
            document.getElementById('payment_amount').value = currentAmount;

            const onlineFields = document.getElementById('online_payment_fields');
            const instructions = document.getElementById('payment_instructions');
            const submitText = document.getElementById('submit_button_text');
            const refNumber = document.getElementById('reference_number');
            const proofFile = document.getElementById('payment_proof');

            let title = '';
            let instructionHTML = '';

            if (method === 'cash') {
                title = '💵 Cash Payment Instructions';
                instructionHTML = `
                    <div class="instruction-card">
                        <h4>How to Pay:</h4>
                        <ol>
                            <li>Visit the Barangay Office during office hours</li>
                            <li>Proceed to the cashier/payment counter</li>
                            <li>Present your Appointment ID: <strong>#${currentAppointmentId}</strong></li>
                            <li>Pay the amount of <strong>₱${parseFloat(currentAmount).toFixed(2)}</strong></li>
                            <li>Get your official receipt</li>
                        </ol>
                        <div class="info-box">
                            <strong>Office Hours:</strong><br>
                            Monday to Friday<br>
                            8:00 AM - 5:00 PM
                        </div>
                    </div>
                `;
                onlineFields.style.display = 'none';
                submitText.textContent = 'I Understand';
                refNumber.required = false;
                proofFile.required = false;
            } else if (method === 'gcash') {
                title = '📱 GCash Payment Instructions';
                instructionHTML = `
                    <div class="instruction-card">
                        <div class="info-box" style="background: #007DFE; color: white; margin-bottom: 15px;">
                            <strong style="display: block; font-size: 1.1em; margin-bottom: 5px;">GCash Number:</strong>
                            <span style="font-size: 1.3em;">0912-345-6789</span><br>
                            <strong>Account Name:</strong> Barangay Sto. Tomas
                        </div>
                        <h4>How to Pay:</h4>
                        <ol>
                            <li>Open your GCash app</li>
                            <li>Select "Send Money"</li>
                            <li>Enter the GCash number above</li>
                            <li>Enter amount: <strong>₱${parseFloat(currentAmount).toFixed(2)}</strong></li>
                            <li>Add reference: <strong>APPT-${currentAppointmentId}</strong></li>
                            <li>Complete the transaction</li>
                            <li>Take a screenshot of the confirmation</li>
                            <li>Upload the screenshot below</li>
                        </ol>
                    </div>
                `;
                onlineFields.style.display = 'block';
                submitText.textContent = 'Submit Payment Proof';
                refNumber.required = true;
                proofFile.required = true;
            } else if (method === 'paymaya') {
                title = '💳 PayMaya Payment Instructions';
                instructionHTML = `
                    <div class="instruction-card">
                        <div class="info-box" style="background: #00D632; color: white; margin-bottom: 15px;">
                            <strong style="display: block; font-size: 1.1em; margin-bottom: 5px;">PayMaya Number:</strong>
                            <span style="font-size: 1.3em;">0912-345-6789</span><br>
                            <strong>Account Name:</strong> Barangay Sto. Tomas
                        </div>
                        <h4>How to Pay:</h4>
                        <ol>
                            <li>Open your PayMaya app</li>
                            <li>Select "Send Money" or "Pay"</li>
                            <li>Enter the PayMaya number above</li>
                            <li>Enter amount: <strong>₱${parseFloat(currentAmount).toFixed(2)}</strong></li>
                            <li>Add message: <strong>APPT-${currentAppointmentId}</strong></li>
                            <li>Complete the transaction</li>
                            <li>Take a screenshot of the confirmation</li>
                            <li>Upload the screenshot below</li>
                        </ol>
                    </div>
                `;
                onlineFields.style.display = 'block';
                submitText.textContent = 'Submit Payment Proof';
                refNumber.required = true;
                proofFile.required = true;
            } else if (method === 'bank') {
                title = '🏦 Bank Transfer Instructions';
                instructionHTML = `
                    <div class="instruction-card">
                        <div class="info-box" style="margin-bottom: 15px;">
                            <strong>Bank Name:</strong> Philippine National Bank (PNB)<br>
                            <strong>Account Name:</strong> Barangay Sto. Tomas<br>
                            <strong>Account Number:</strong> 1234-5678-9012<br>
                            <strong>Branch:</strong> Main Branch
                        </div>
                        <h4>How to Pay:</h4>
                        <ol>
                            <li>Go to your bank or use online banking</li>
                            <li>Transfer to the account above</li>
                            <li>Amount: <strong>₱${parseFloat(currentAmount).toFixed(2)}</strong></li>
                            <li>Reference: <strong>APPT-${currentAppointmentId}</strong></li>
                            <li>Get your transaction reference number</li>
                            <li>Take photo of deposit slip or screenshot</li>
                            <li>Upload the proof below</li>
                        </ol>
                    </div>
                `;
                onlineFields.style.display = 'block';
                submitText.textContent = 'Submit Payment Proof';
                refNumber.required = true;
                proofFile.required = true;
            }

            document.getElementById('payment_method_title').innerHTML = title;
            instructions.innerHTML = instructionHTML;

            closePaymentModal();
            document.getElementById('paymentDetailsModal').style.display = 'block';
        }

        function closePaymentDetailsModal() {
            document.getElementById('paymentDetailsModal').style.display = 'none';
            document.getElementById('paymentForm').reset();
        }

        // Upload Proof Modal Functions
        function openUploadProofModal(appointmentId, serviceName, amount) {
            document.getElementById('proof_appointment_id').value = appointmentId;
            document.getElementById('proof_appointment_id_display').textContent = '#' + appointmentId;
            document.getElementById('proof_service').textContent = serviceName;
            document.getElementById('proof_amount_display').textContent = '₱' + parseFloat(amount).toFixed(2);
            document.getElementById('uploadProofModal').style.display = 'block';
        }

        function closeUploadProofModal() {
            document.getElementById('uploadProofModal').style.display = 'none';
            document.getElementById('uploadProofForm').reset();
            document.getElementById('proof_preview').style.display = 'none';
        }

        function previewProofImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('proof_preview');
            const previewImg = document.getElementById('proof_preview_img');

            if (file) {
                // Check file size (5MB)
                if (file.size > 5242880) {
                    alert('File size must be less than 5MB.');
                    event.target.value = '';
                    preview.style.display = 'none';
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload a JPG, PNG, or PDF file.');
                    event.target.value = '';
                    preview.style.display = 'none';
                    return;
                }

                // Show preview for images only
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.id === 'paymentModal') {
                closePaymentModal();
            }
            if (event.target.id === 'paymentDetailsModal') {
                closePaymentDetailsModal();
            }
            if (event.target.id === 'uploadProofModal') {
                closeUploadProofModal();
            }
        }

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const method = document.getElementById('payment_method').value;

            if (method !== 'cash') {
                const refNumber = document.getElementById('reference_number').value;
                const proofFile = document.getElementById('payment_proof').files[0];

                if (!refNumber || !proofFile) {
                    e.preventDefault();
                    alert('Please provide both reference number and payment proof.');
                    return false;
                }

                // Check file size
                if (proofFile.size > 2097152) {
                    e.preventDefault();
                    alert('File size must be less than 2MB.');
                    return false;
                }
            }

            return confirm('Submit payment information?');
        });

        // Upload Proof Form Validation
        document.getElementById('uploadProofForm').addEventListener('submit', function(e) {
            const file = document.getElementById('payment_proof_file').files[0];

            if (!file) {
                e.preventDefault();
                alert('Please select a payment proof image.');
                return false;
            }

            if (file.size > 5242880) {
                e.preventDefault();
                alert('File size must be less than 5MB.');
                return false;
            }

            return confirm('Upload this payment proof?');
        });
    </script>

    <style>
        .payment-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .payment-summary h3 {
            margin: 0 0 15px 0;
            font-size: 1.1em;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-item:last-child {
            border-bottom: none;
            padding-top: 12px;
            margin-top: 5px;
        }

        .payment-methods h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .payment-method-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method-card:hover {
            border-color: #3498db;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
            transform: translateY(-2px);
        }

        .method-icon {
            font-size: 2.5em;
            margin-right: 20px;
        }

        .method-info {
            flex: 1;
        }

        .method-info h4 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 1.1em;
        }

        .method-info p {
            margin: 0 0 5px 0;
            color: #555;
        }

        .method-info small {
            color: #7f8c8d;
            font-size: 0.85em;
        }

        .method-arrow {
            font-size: 1.5em;
            color: #3498db;
            margin-left: 15px;
        }

        .payment-instructions {
            margin-bottom: 25px;
        }

        .instruction-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .instruction-card h4 {
            color: #2c3e50;
            margin: 15px 0 10px 0;
        }

        .instruction-card ol {
            padding-left: 20px;
            line-height: 1.8;
        }

        .instruction-card ol li {
            margin-bottom: 8px;
            color: #555;
        }

        .info-box {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            line-height: 1.6;
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
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            line-height: 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: #000;
        }

        /* Upload Proof Modal Styles */
        .upload-instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }

        .upload-instructions h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 1.1em;
        }

        .upload-instructions ul {
            margin: 0;
            padding-left: 20px;
        }

        .upload-instructions li {
            margin: 8px 0;
            color: #555;
            line-height: 1.5;
        }

        .proof-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .proof-preview h4 {
            margin: 0 0 15px 0;
            color: #2c3e50;
        }

        .proof-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            border: 2px dashed #3498db;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }

        input[type="file"]:hover {
            background: #e3f2fd;
            border-color: #2980b9;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .appointment-details-container {
            padding: 20px 0;
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
        }

        .status-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.8em;
        }

        .badge-lg {
            font-size: 1.1em;
            padding: 10px 20px;
        }

        .details-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .details-card h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            font-size: 1.3em;
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
            font-weight: 600;
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .detail-item span {
            color: #2c3e50;
            font-size: 1em;
        }

        .fee-amount {
            color: #27ae60;
            font-weight: bold;
            font-size: 1.3em !important;
        }

        .purpose-section,
        .admin-notes {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .admin-notes {
            border-left-color: #f39c12;
            background: #fff3cd;
        }

        .action-buttons-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .payment-method-card {
                padding: 15px;
            }

            .method-icon {
                font-size: 2em;
                margin-right: 15px;
            }

            .modal-content {
                width: 95%;
                margin: 10px auto;
                padding: 20px;
            }

            .proof-preview img {
                max-height: 200px;
            }

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

        /* Card hover */
        .card {
            transition: all 0.3s ease;
        }

        /* Detail items hover */
        .detail-item {
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            background: #f8f9fa;
            transform: translateX(2px);
        }

        /* Action buttons hover enhancement */
        .action-buttons-container .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        /* Link hover */
        a {
            transition: all 0.2s ease;
        }

        a:hover {
            opacity: 0.8;
        }

        /* Filter select hover */
        .filter-select:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            background: white;
            transform: translateY(-2px);
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            background: white;
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
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(235, 51, 73, 0.4);
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

        .alert-error {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        /* Trendy New Appointment Button */
        .new-appt-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 100%);
            transition: left 0.6s ease;
        }

        .new-appt-btn:hover::before {
            left: 100%;
        }

        .new-appt-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
        }

        .new-appt-btn:active {
            transform: translateY(-1px) scale(0.98);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        .new-appt-btn svg {
            transition: transform 0.3s ease;
        }

        .new-appt-btn:hover svg {
            transform: rotate(15deg) scale(1.1);
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            }

            50% {
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            }
        }

        .new-appt-btn {
            animation: pulse-glow 3s infinite;
        }

        .new-appt-btn:hover {
            animation: none;
        }
    </style>

    <script>
        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.table-container tbody tr');

            tableRows.forEach(row => {
                if (filterValue === 'all') {
                    row.style.display = '';
                } else {
                    const statusCell = row.querySelector('td:nth-child(6)');
                    if (statusCell) {
                        const statusBadge = statusCell.querySelector('.badge');
                        if (statusBadge) {
                            const statusText = statusBadge.textContent.toLowerCase().trim();
                            if (statusText === filterValue) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>