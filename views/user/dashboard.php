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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/user_header.php'; ?>

    <div class="main-container">
        <main class="content">
            <div class="page-header">
                <h1>My Dashboard</h1>
                <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
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
                <a href="new_appointment.php" class="btn btn-primary">📅 New Appointment</a>
            </div>

            <div class="section">
                <h2>My Appointments</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td><?php echo $row['id']; ?></td>
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
                                        <a href="view_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">View</a>
                                        <?php if ($row['fee'] > 0 && $row['status'] == 'approved'): ?>
                                            <?php if (empty($row['payment_proof'])): ?>
                                                <button onclick="openUploadProofModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['service_name']); ?>', <?php echo $row['fee']; ?>)" class="btn btn-sm btn-success">📤 Upload Proof</button>
                                            <?php else: ?>
                                                <span class="badge badge-completed" style="font-size: 0.8em;">✓ Proof Uploaded</span>
                                            <?php endif; ?>
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

    <script>
        let currentAppointmentId = null;
        let currentAmount = 0;
        let currentService = '';

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

        // Auto-refresh dashboard every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milliseconds = 30 seconds

        // Optional: Add visual indicator for next refresh
        let countdown = 30;
        setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                countdown = 30;
            }
        }, 1000);
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
        }
    </style>
</body>

</html>