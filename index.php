<?php

/**
 * Index/Landing Page
 * Main entry point of the application
 */

require_once __DIR__ . '/config/config.php';

// If user is logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_STAFF) {
        header("Location: views/admin/dashboard.php");
    } else {
        header("Location: views/user/dashboard.php");
    }
    exit();
}

// Show landing page
require_once __DIR__ . '/views/home.php';
exit();
