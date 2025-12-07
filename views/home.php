<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Sto. Tomas - Home</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/home.css">
</head>

<body>
    <!-- Navigation Header -->
    <header class="home-header">
        <div class="header-content">
            <div class="logo" style="display: flex !important; align-items: center !important; gap: 15px !important; justify-content: flex-start !important; flex-direction: row !important;">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=<?php echo time(); ?>" alt="Barangay Sto. Tomas" class="logo-image" style="height: 60px !important; width: 60px !important; flex-shrink: 0 !important;">
                <h1 style="margin: 0 !important; font-size: 1.5rem !important; text-align: left !important;">Barangay Sto. Tomas</h1>
            </div>
            <nav class="home-nav">
                <a href="#services">Services</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <a href="#" class="btn btn-login" onclick="openLoginModal(); return false;">Login</a>
                <a href="#" class="btn btn-register" onclick="openRegisterModal(); return false;">Register</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Barangay Sto. Tomas</h1>
            <p class="hero-subtitle">Your Partner in Community Development and Service</p>
            <p class="hero-description">Book appointments online for barangay services quickly and conveniently</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary btn-lg" onclick="openAppointmentModal(); return false;">Book Appointment</a>
                <a href="#services" class="btn btn-secondary btn-lg">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">We offer various services to serve our community better</p>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">📄</div>
                    <h3>Barangay Clearance</h3>
                    <p>Request and obtain your barangay clearance for employment, business, and other purposes.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">🏠</div>
                    <h3>Certificate of Residency</h3>
                    <p>Get certified proof of your residency in Barangay Sto. Tomas for various transactions.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">💼</div>
                    <h3>Business Permit</h3>
                    <p>Apply for business permits and clearances for small businesses in the barangay.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">👨‍👩‍👧‍👦</div>
                    <h3>Indigency Certificate</h3>
                    <p>Request certificate of indigency for medical, educational, and other assistance programs.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">🎉</div>
                    <h3>Community Events</h3>
                    <p>Schedule and organize community events, meetings, and activities at the barangay hall.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">📋</div>
                    <h3>Other Documents</h3>
                    <p>Request various barangay documents and certifications for your specific needs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">About Us</h2>
                    <p>Barangay Sto. Tomas is committed to providing efficient and accessible services to all residents. Our online appointment system is designed to make it easier for you to access barangay services without the hassle of long queues and waiting times.</p>
                    <p>We strive to serve our community with integrity, transparency, and dedication. Our goal is to make barangay services more convenient and accessible to everyone.</p>
                    <div class="about-stats">
                        <div class="stat">
                            <h3>1000+</h3>
                            <p>Residents Served</p>
                        </div>
                        <div class="stat">
                            <h3>50+</h3>
                            <p>Daily Appointments</p>
                        </div>
                        <div class="stat">
                            <h3>24/7</h3>
                            <p>Online Booking</p>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <div class="image-placeholder">
                        <span>🏛️</span>
                        <p>Barangay Hall</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Simple steps to book your appointment</p>

            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create Account</h3>
                    <p>Register with your valid information and contact details</p>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Choose Service</h3>
                    <p>Select the service you need from our available options</p>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Book Appointment</h3>
                    <p>Pick your preferred date and time for your appointment</p>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Visit Barangay</h3>
                    <p>Come to the barangay hall on your scheduled appointment</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            <p class="section-subtitle">Get in touch with us for any inquiries</p>

            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">📍</div>
                    <h3>Address</h3>
                    <p>Barangay Sto. Tomas Hall<br>Sto. Tomas, Philippines</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">📞</div>
                    <h3>Phone</h3>
                    <p>(123) 456-7890</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">✉️</div>
                    <h3>Email</h3>
                    <p>barangay.stotomas@email.com</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">🕒</div>
                    <h3>Office Hours</h3>
                    <p>Monday - Friday<br>8:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="home-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Barangay Sto. Tomas</h3>
                    <p>Serving the community with dedication and integrity.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/views/login.php">Login</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Twitter</a>
                        <a href="#" class="social-link">Instagram</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Barangay Sto. Tomas. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <div class="modal-header">
                <h2>Barangay Appointment System</h2>
                <h3>Login</h3>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo SITE_URL; ?>/controllers/AuthController.php?action=login" method="POST">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="modal-footer">
                <p>Don't have an account? <a href="#" onclick="closeLoginModal(); openRegisterModal(); return false;">Register here</a></p>
            </div>
        </div>
    </div>

    <!-- Appointment Modal -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content modal-content-large">
            <span class="close" onclick="closeAppointmentModal()">&times;</span>
            <div class="modal-header">
                <h2>Book an Appointment</h2>
                <h3>Schedule your visit to the barangay</h3>
            </div>

            <div class="alert alert-error" id="appointmentError" style="display: none; margin: 20px 40px;"></div>

            <form action="<?php echo SITE_URL; ?>/controllers/AppointmentController.php?action=create" method="POST" id="appointmentForm">
                <div class="form-group">
                    <label for="service_id">Service *</label>
                    <select id="service_id" name="service_id" required>
                        <option value="">Select a service</option>
                        <?php
                        require_once __DIR__ . '/../config/database.php';
                        require_once __DIR__ . '/../models/Service.php';

                        $database = new Database();
                        $db = $database->getConnection();
                        $service = new Service($db);
                        $services = $service->readActive();

                        while ($row = $services->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}' data-fee='{$row['fee']}'>{$row['service_name']} - ₱" . number_format($row['fee'], 2) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="appointment_date">Appointment Date *</label>
                        <input type="date" id="appointment_date" name="appointment_date" required
                            min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                            max="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
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
                    <textarea id="purpose" name="purpose" rows="3" required placeholder="Please describe the purpose of your appointment"></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Any additional information"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </form>

            <div class="modal-footer">
                <p>Please ensure you are logged in. <a href="#" onclick="closeAppointmentModal(); openLoginModal(); return false;">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content modal-content-large">
            <span class="close" onclick="closeRegisterModal()">&times;</span>
            <div class="modal-header">
                <h2>Barangay Appointment System</h2>
                <h3>Register</h3>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo SITE_URL; ?>/controllers/AuthController.php?action=register" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reg_username">Username *</label>
                        <input type="text" id="reg_username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_email">Email *</label>
                        <input type="email" id="reg_email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reg_password">Password *</label>
                        <input type="password" id="reg_password" name="password" required>
                        <small>Minimum 6 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <div class="modal-footer">
                <p>Already have an account? <a href="#" onclick="closeRegisterModal(); openLoginModal(); return false;">Login here</a></p>
            </div>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script>
        // Modal functions
        function openLoginModal() {
            closeRegisterModal();
            closeAppointmentModal();
            document.getElementById('loginModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openRegisterModal() {
            closeLoginModal();
            closeAppointmentModal();
            document.getElementById('registerModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openAppointmentModal() {
            closeLoginModal();
            closeRegisterModal();
            document.getElementById('appointmentModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeAppointmentModal() {
            document.getElementById('appointmentModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const loginModal = document.getElementById('loginModal');
            const registerModal = document.getElementById('registerModal');
            const appointmentModal = document.getElementById('appointmentModal');

            if (event.target == loginModal) {
                closeLoginModal();
            } else if (event.target == registerModal) {
                closeRegisterModal();
            } else if (event.target == appointmentModal) {
                closeAppointmentModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLoginModal();
                closeRegisterModal();
                closeAppointmentModal();
            }
        });

        // Form validation for appointment
        document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
            const serviceId = document.getElementById('service_id').value;
            const appointmentDate = document.getElementById('appointment_date').value;
            const appointmentTime = document.getElementById('appointment_time').value;
            const purpose = document.getElementById('purpose').value;

            if (!serviceId || !appointmentDate || !appointmentTime || !purpose.trim()) {
                e.preventDefault();
                document.getElementById('appointmentError').textContent = 'Please fill in all required fields';
                document.getElementById('appointmentError').style.display = 'block';
                return false;
            }

            // Check if user is logged in (basic check)
            <?php if (!isset($_SESSION['user_id'])): ?>
                e.preventDefault();
                closeAppointmentModal();
                openLoginModal();
                return false;
            <?php endif; ?>
        });

        <?php if (isset($_SESSION['error']) || isset($_SESSION['success'])): ?>
            // Open modal if there are messages
            window.addEventListener('DOMContentLoaded', function() {
                openLoginModal();
            });
        <?php endif; ?>
    </script>
</body>

</html>