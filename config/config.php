<?php

/**
 * General Configuration
 * Barangay Appointment System
 */

// Timezone
date_default_timezone_set('Asia/Manila');

// Site Settings
define('SITE_NAME', 'Barangay Appointment System');
define('SITE_URL', 'http://localhost/Sto%20Tomas');

// Session Settings
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting (Change to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Pagination
define('RECORDS_PER_PAGE', 10);

// Appointment Settings
define('APPOINTMENT_SLOTS_PER_DAY', 20);
define('OFFICE_HOURS_START', '08:00');
define('OFFICE_HOURS_END', '17:00');
define('SLOT_DURATION', 30); // minutes

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_RESIDENT', 'resident');

// Appointment Status
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_COMPLETED', 'completed');
define('STATUS_CANCELLED', 'cancelled');
define('STATUS_REJECTED', 'rejected');
