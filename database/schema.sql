-- Barangay Sto. Tomas Appointment System Database Schema
-- Create Database
CREATE DATABASE IF NOT EXISTS barangay_appointment CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE barangay_appointment;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    profile_picture VARCHAR(255) DEFAULT NULL,
    role ENUM('admin', 'staff', 'resident') DEFAULT 'resident',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Services Table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    requirements TEXT,
    processing_time VARCHAR(50),
    fee DECIMAL(10, 2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_service_name (service_name)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Appointments Table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM(
        'pending',
        'approved',
        'completed',
        'cancelled',
        'rejected'
    ) DEFAULT 'pending',
    purpose TEXT,
    notes TEXT,
    admin_notes TEXT,
    queue_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_service_id (service_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Documents Table
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE CASCADE,
    INDEX idx_appointment_id (appointment_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_id INT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Audit Log Table
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Insert Default Admin User (password: admin123)
INSERT INTO
    users (
        username,
        email,
        password,
        first_name,
        last_name,
        role
    )
VALUES (
        'admin',
        'admin@barangay.local',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Admin',
        'User',
        'admin'
    );

-- Insert Default Services
INSERT INTO
    services (
        service_name,
        description,
        requirements,
        processing_time,
        fee
    )
VALUES (
        'Barangay Clearance',
        'Certificate of residency and good moral character',
        'Valid ID, Proof of Residency',
        '1-2 days',
        50.00
    ),
    (
        'Certificate of Indigency',
        'Certificate for indigent residents',
        'Valid ID, Barangay Clearance',
        '1 day',
        0.00
    ),
    (
        'Business Permit',
        'Permit for small business operations',
        'Business Documents, Valid ID',
        '3-5 days',
        100.00
    ),
    (
        'Complaint Filing',
        'Filing of complaints and disputes',
        'Valid ID, Supporting Documents',
        'Varies',
        0.00
    ),
    (
        'Community Tax Certificate (Cedula)',
        'Annual community tax certificate',
        'Valid ID',
        '1 day',
        30.00
    );

-- Insert Default Settings
INSERT INTO
    settings (
        setting_key,
        setting_value,
        description
    )
VALUES (
        'barangay_name',
        'Barangay Sto. Tomas',
        'Name of the barangay'
    ),
    (
        'barangay_address',
        'Sto. Tomas, Philippines',
        'Complete address of the barangay'
    ),
    (
        'contact_number',
        '(123) 456-7890',
        'Contact number'
    ),
    (
        'email_address',
        'barangay.stotomas@email.com',
        'Official email address'
    ),
    (
        'office_hours',
        'Monday-Friday, 8:00 AM - 5:00 PM',
        'Office operating hours'
    ),
    (
        'max_appointments_per_day',
        '50',
        'Maximum appointments per day'
    ),
    (
        'appointment_advance_days',
        '14',
        'How many days in advance can appointments be made'
    );