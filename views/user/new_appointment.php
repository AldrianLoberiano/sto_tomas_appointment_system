<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();
$service = new Service($db);
$services = $service->readActive();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Appointment - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/user_header.php'; ?>

    <div class="main-container">
        <main class="content">
            <div class="page-header">
                <h1>Book New Appointment</h1>
                <p>Schedule your visit to the barangay</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="section">
                <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=create" method="POST" id="appointmentForm">
                    <div class="form-group">
                        <label for="service_id">Service *</label>
                        <select id="service_id" name="service_id" required>
                            <option value="">Select a service</option>
                            <?php while ($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $row['id']; ?>"
                                    data-fee="<?php echo $row['fee']; ?>"
                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                    data-requirements="<?php echo htmlspecialchars($row['requirements']); ?>"
                                    data-processing="<?php echo htmlspecialchars($row['processing_time']); ?>">
                                    <?php echo $row['service_name']; ?> - ₱<?php echo number_format($row['fee'], 2); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div id="serviceInfo" class="service-info" style="display: none;">
                        <div class="info-card">
                            <h3>Service Information</h3>
                            <div class="info-item">
                                <strong>Description:</strong>
                                <p id="serviceDescription"></p>
                            </div>
                            <div class="info-item">
                                <strong>Requirements:</strong>
                                <p id="serviceRequirements"></p>
                            </div>
                            <div class="info-item">
                                <strong>Processing Time:</strong>
                                <p id="serviceProcessing"></p>
                            </div>
                            <div class="info-item">
                                <strong>Fee:</strong>
                                <p id="serviceFee"></p>
                            </div>
                        </div>
                    </div>

                    <div class="datetime-selection">
                        <div class="form-group">
                            <label>Select Appointment Date *</label>
                            <div id="calendar" class="calendar-container"></div>
                            <input type="hidden" id="appointment_date" name="appointment_date" required>
                            <small>You can book appointments 1-14 days in advance. Weekends are highlighted in orange.</small>
                        </div>

                        <div class="form-group">
                            <label>Select Appointment Time *</label>
                            <div id="timeSlots" class="time-slots-container">
                                <p style="text-align: center; color: #7f8c8d; padding: 20px;">Please select a date first</p>
                            </div>
                            <input type="hidden" id="appointment_time" name="appointment_time" required>
                            <small>Office hours: 8:00 AM - 5:00 PM (Select your preferred time slot)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="purpose">Purpose *</label>
                        <textarea id="purpose" name="purpose" rows="4" required
                            placeholder="Please describe the purpose of your appointment"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                            placeholder="Any additional information you'd like to provide"></textarea>
                    </div>

                    <!-- Payment Section -->
                    <div id="paymentSection" class="payment-section" style="display: none;">
                        <h3>💳 Payment Information</h3>
                        <div class="payment-info-box">
                            <div class="payment-summary">
                                <div class="summary-row">
                                    <span>Service Fee:</span>
                                    <strong id="displayFee" style="color: #27ae60; font-size: 1.3em;">₱0.00</strong>
                                </div>
                            </div>

                            <div class="payment-option">
                                <input type="checkbox" id="payNow" name="pay_now" value="1">
                                <label for="payNow" style="margin-left: 10px;">
                                    <strong>Select payment method</strong>
                                    <br>
                                    <small style="color: #7f8c8d;">Choose how you will pay for this service</small>
                                </label>
                            </div>

                            <div id="paymentMethodSection" style="display: none; margin-top: 20px;">
                                <h4>Select Payment Method</h4>
                                <div class="payment-methods-grid">
                                    <div class="payment-method-option" data-method="gcash">
                                        <div class="method-icon">📱</div>
                                        <div class="method-name">GCash</div>
                                        <small style="color: #7f8c8d; font-size: 0.85em;">Upload proof required</small>
                                    </div>
                                    <div class="payment-method-option" data-method="cash">
                                        <div class="method-icon">💵</div>
                                        <div class="method-name">Cash at Office</div>
                                        <small style="color: #7f8c8d; font-size: 0.85em;">No upload needed</small>
                                    </div>
                                </div>
                                <input type="hidden" id="payment_method" name="payment_method" value="">

                                <div id="gcashDetails" style="display: none; margin-top: 15px; padding: 15px; background: #e3f2fd; border-radius: 8px;">
                                    <h4>📱 GCash Payment Details</h4>
                                    <p><strong>GCash Number:</strong> 0912-345-6789</p>
                                    <p><strong>Account Name:</strong> Barangay Sto. Tomas</p>
                                    <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 6px; border-left: 4px solid #ffc107;">
                                        <p style="margin: 0; color: #856404;"><strong>⚠️ Important:</strong> After sending payment via GCash, you must upload your payment proof/screenshot in the dashboard to complete your payment.</p>
                                    </div>
                                </div>

                                <div id="cashDetails" style="display: none; margin-top: 15px; padding: 15px; background: #d4edda; border-radius: 8px;">
                                    <h4>💵 Cash Payment at Office</h4>
                                    <p><strong>Office:</strong> Barangay Sto. Tomas Hall</p>
                                    <p><strong>Hours:</strong> Monday-Friday, 8:00 AM - 5:00 PM</p>
                                    <div style="margin-top: 15px; padding: 10px; background: #fff; border-radius: 6px; border-left: 4px solid #28a745;">
                                        <p style="margin: 0; color: #155724;"><strong>✓ No Payment Proof Required:</strong> Simply bring your appointment confirmation and pay at the office. No need to upload any payment proof.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">📅 Book Appointment</button>
                        <a href="dashboard.php" class="btn" style="background-color: #95a5a6; color: white; margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let selectedDate = null;
        let selectedTime = null;

        // Generate calendar
        function generateCalendar() {
            const calendar = document.getElementById('calendar');
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();

            // Calendar header
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            let html = `
                <div class="calendar-header">
                    <h3>${monthNames[currentMonth]} ${currentYear}</h3>
                </div>
                <div class="calendar-weekdays">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
                    <div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div class="calendar-days">
            `;

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const minDate = new Date(today);
            minDate.setDate(today.getDate() + 1);
            minDate.setHours(0, 0, 0, 0);
            const maxDate = new Date(today);
            maxDate.setDate(today.getDate() + 14);
            maxDate.setHours(23, 59, 59, 999);

            // Empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="calendar-day empty"></div>';
            }

            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentYear, currentMonth, day);
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const isPast = date < minDate;
                const isFuture = date > maxDate;
                const isDisabled = isPast || isFuture;

                const dateStr = date.toISOString().split('T')[0];
                const classes = ['calendar-day'];
                if (isWeekend) classes.push('weekend');
                if (isDisabled) classes.push('disabled');
                if (date.toDateString() === today.toDateString()) classes.push('today');

                html += `<div class="${classes.join(' ')}" data-date="${dateStr}" onclick="selectDate('${dateStr}')">${day}</div>`;
            }

            html += '</div>';
            calendar.innerHTML = html;
        }

        // Select date function
        window.selectDate = function(dateStr) {
            const dateElement = document.querySelector(`[data-date="${dateStr}"]`);
            if (dateElement.classList.contains('disabled')) {
                return;
            }

            // Remove previous selection
            document.querySelectorAll('.calendar-day').forEach(el => el.classList.remove('selected'));
            dateElement.classList.add('selected');

            selectedDate = dateStr;
            document.getElementById('appointment_date').value = dateStr;

            // Check if weekend
            const date = new Date(dateStr);
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;

            if (isWeekend) {
                alert('Note: The selected date is a weekend. The barangay office may be closed.');
            }

            // Generate time slots
            generateTimeSlots();
        };

        // Generate time slots
        function generateTimeSlots() {
            const container = document.getElementById('timeSlots');
            const times = [
                '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
                '16:00', '16:30'
            ];

            let html = '<div class="time-slots-grid">';

            times.forEach(time => {
                const [hours, minutes] = time.split(':');
                const period = parseInt(hours) >= 12 ? 'PM' : 'AM';
                const displayHours = parseInt(hours) > 12 ? parseInt(hours) - 12 : (parseInt(hours) === 0 ? 12 : parseInt(hours));
                const displayTime = `${displayHours}:${minutes} ${period}`;

                html += `<div class="time-slot" data-time="${time}" onclick="selectTime('${time}')">
                    <div class="time-label">${displayTime}</div>
                    <div class="time-status">Available</div>
                </div>`;
            });

            html += '</div>';
            container.innerHTML = html;
        }

        // Select time function
        window.selectTime = function(time) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
            document.querySelector(`[data-time="${time}"]`).classList.add('selected');

            selectedTime = time;
            document.getElementById('appointment_time').value = time;
        };

        // Service selection handler
        document.getElementById('service_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const serviceInfo = document.getElementById('serviceInfo');
            const paymentSection = document.getElementById('paymentSection');

            if (selectedOption.value) {
                const fee = parseFloat(selectedOption.getAttribute('data-fee'));
                document.getElementById('serviceDescription').textContent = selectedOption.getAttribute('data-description');
                document.getElementById('serviceRequirements').textContent = selectedOption.getAttribute('data-requirements');
                document.getElementById('serviceProcessing').textContent = selectedOption.getAttribute('data-processing');
                document.getElementById('serviceFee').textContent = '₱' + fee.toFixed(2);
                serviceInfo.style.display = 'block';

                // Show payment section if service has a fee
                if (fee > 0) {
                    document.getElementById('displayFee').textContent = '₱' + fee.toFixed(2);
                    paymentSection.style.display = 'block';
                } else {
                    paymentSection.style.display = 'none';
                    document.getElementById('payNow').checked = false;
                    document.getElementById('paymentMethodSection').style.display = 'none';
                }
            } else {
                serviceInfo.style.display = 'none';
                paymentSection.style.display = 'none';
            }
        });

        // Pay Now checkbox handler
        document.getElementById('payNow').addEventListener('change', function() {
            const paymentMethodSection = document.getElementById('paymentMethodSection');
            if (this.checked) {
                paymentMethodSection.style.display = 'block';
            } else {
                paymentMethodSection.style.display = 'none';
                document.getElementById('payment_method').value = '';
                document.querySelectorAll('.payment-method-option').forEach(el => el.classList.remove('selected'));
                document.getElementById('gcashDetails').style.display = 'none';
                document.getElementById('cashDetails').style.display = 'none';
            }
        });

        // Payment method selection
        document.querySelectorAll('.payment-method-option').forEach(option => {
            option.addEventListener('click', function() {
                const method = this.dataset.method;

                // Remove previous selection
                document.querySelectorAll('.payment-method-option').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');

                // Set payment method
                document.getElementById('payment_method').value = method;

                // Show method details
                document.getElementById('gcashDetails').style.display = method === 'gcash' ? 'block' : 'none';
                document.getElementById('cashDetails').style.display = method === 'cash' ? 'block' : 'none';
            });
        });

        // Form validation
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            if (!selectedDate) {
                e.preventDefault();
                alert('Please select an appointment date from the calendar');
                return false;
            }

            if (!selectedTime) {
                e.preventDefault();
                alert('Please select an appointment time slot');
                return false;
            }

            const appointmentDate = new Date(selectedDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (appointmentDate < today) {
                e.preventDefault();
                alert('Please select a future date');
                return false;
            }

            // Final confirmation for weekends
            const dayOfWeek = appointmentDate.getDay();
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                if (!confirm('The selected date is a weekend. Barangay office may be closed. Do you want to continue?')) {
                    e.preventDefault();
                    return false;
                }
            }

            return true;
        });

        // Initialize calendar on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateCalendar();
        });
    </script>

    <style>
        .service-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }

        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-item strong {
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        .info-item p {
            color: #666;
            margin: 0;
            padding-left: 10px;
        }

        .datetime-selection {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: #555;
            font-weight: 600;
            font-size: 16px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-group select {
            background-color: white;
            cursor: pointer;
        }

        .form-group small {
            display: block;
            margin-top: 10px;
            color: #7f8c8d;
            font-size: 13px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Calendar Styles */
        .calendar-container {
            background: white;
            border: 2px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }

        .calendar-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 20px;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-bottom: 10px;
        }

        .calendar-weekdays div {
            text-align: center;
            font-weight: 600;
            color: #7f8c8d;
            padding: 10px 0;
            font-size: 14px;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .calendar-day:not(.empty):not(.disabled):hover {
            background: #ecf0f1;
            transform: scale(1.05);
        }

        .calendar-day.empty {
            cursor: default;
        }

        .calendar-day.today {
            border-color: #3498db;
            color: #3498db;
            font-weight: 700;
        }

        .calendar-day.weekend {
            background: #fff3cd;
            color: #f39c12;
        }

        .calendar-day.disabled {
            color: #bdc3c7;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .calendar-day.selected {
            background: #3498db;
            color: white;
            font-weight: 700;
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        /* Time Slots Styles */
        .time-slots-container {
            background: white;
            border: 2px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-height: 500px;
            overflow-y: auto;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .time-slot {
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .time-slot:hover {
            border-color: #3498db;
            background: #ecf0f1;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .time-slot.selected {
            background: #3498db;
            border-color: #3498db;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .time-label {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .time-status {
            font-size: 12px;
            color: #27ae60;
            font-weight: 500;
        }

        .time-slot.selected .time-status {
            color: white;
        }

        @media (max-width: 1024px) {
            .datetime-selection {
                grid-template-columns: 1fr;
            }

            .time-slots-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .time-slots-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .calendar-day {
                font-size: 14px;
            }
        }

        /* Payment Section Styles */
        .payment-section {
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid #3498db;
        }

        .payment-section h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            font-size: 1.3em;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .payment-info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        .payment-summary {
            margin-bottom: 20px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1em;
        }

        .payment-option {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            background: #fff3cd;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
        }

        .payment-option input[type="checkbox"] {
            margin-top: 3px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .payment-option label {
            cursor: pointer;
            flex: 1;
        }

        #paymentMethodSection h4 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 1.1em;
        }

        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .payment-method-option {
            padding: 20px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method-option:hover {
            border-color: #3498db;
            background: #e3f2fd;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-method-option.selected {
            border-color: #27ae60;
            background: #e8f5e9;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2);
        }

        .method-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .method-name {
            font-weight: 600;
            color: #2c3e50;
        }

        #gcashDetails h4,
        #cashDetails h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 1em;
        }

        #gcashDetails p,
        #cashDetails p {
            margin: 5px 0;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .payment-methods-grid {
                grid-template-columns: 1fr;
            }

            .payment-section {
                padding: 15px;
            }
        }
    </style>
</body>

</html>