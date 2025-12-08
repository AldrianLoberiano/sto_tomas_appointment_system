<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Sto. Tomas - Home</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/home.css?v=<?php echo time(); ?>">
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
                <a href="#home">Home</a>
                <a href="#services">Services</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <a href="#" class="btn btn-login" onclick="openLoginModal(); return false;">Login</a>
                <a href="#" class="btn btn-register" onclick="openRegisterModal(); return false;">Register</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
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
        <div class="footer-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo" class="footer-logo-img">
                    </div>
                    <h3>🏘️ Barangay Sto. Tomas</h3>
                    <p>Serving the community with dedication and integrity since 1950.</p>
                    <div class="footer-contact">
                        <p><span class="icon">📍</span> Sto. Tomas, Philippines</p>
                        <p><span class="icon">📞</span> (123) 456-7890</p>
                        <p><span class="icon">✉️</span> info@barangay.ph</p>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>🔗 Quick Links</h3>
                    <ul>
                        <li><a href="#home"><span class="link-arrow">→</span> Home</a></li>
                        <li><a href="#about"><span class="link-arrow">→</span> About Us</a></li>
                        <li><a href="#services"><span class="link-arrow">→</span> Services</a></li>
                        <li><a href="#contact"><span class="link-arrow">→</span> Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>🕐 Office Hours</h3>
                    <div class="office-hours">
                        <div class="hours-item">
                            <span class="day">Mon - Fri</span>
                            <span class="time">8:00 AM - 5:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="day">Saturday</span>
                            <span class="time">8:00 AM - 12:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="day">Sunday</span>
                            <span class="time">Closed</span>
                        </div>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>🌐 Connect With Us</h3>
                    <div class="social-links">
                        <a href="#" class="social-link social-facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                            <span>Facebook</span>
                        </a>
                        <a href="#" class="social-link social-twitter">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                            <span>Twitter</span>
                        </a>
                        <a href="#" class="social-link social-instagram">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                            </svg>
                            <span>Instagram</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Barangay Sto. Tomas. All rights reserved. | Made with <span class="heart">❤️</span> for the community</p>
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