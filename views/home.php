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
            <div class="logo">
                <h1>🏛️ Barangay Sto. Tomas</h1>
            </div>
            <nav class="home-nav">
                <a href="#about">About</a>
                <a href="#services">Services</a>
                <a href="#contact">Contact</a>
                <a href="<?php echo SITE_URL; ?>/views/login.php" class="btn btn-login">Login</a>
                <a href="<?php echo SITE_URL; ?>/views/register.php" class="btn btn-register">Register</a>
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
                <a href="<?php echo SITE_URL; ?>/views/register.php" class="btn btn-primary btn-lg">Get Started</a>
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

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>