<?php

/**
 * Router Handler
 * Routes requests to appropriate controllers
 */

require_once __DIR__ . '/../config/config.php';

// Get action from query string
$action = $_GET['action'] ?? '';

// Route to appropriate controller
switch ($action) {
    case 'login':
        require_once __DIR__ . '/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        require_once __DIR__ . '/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        require_once __DIR__ . '/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'create_appointment':
        require_once __DIR__ . '/AppointmentController.php';
        $controller = new AppointmentController();
        if ($controller->create()) {
            header("Location: " . SITE_URL . "/views/user/dashboard.php");
        } else {
            header("Location: " . SITE_URL . "/views/user/new_appointment.php");
        }
        break;

    case 'update_appointment':
        require_once __DIR__ . '/AppointmentController.php';
        $controller = new AppointmentController();
        if ($controller->update()) {
            header("Location: " . SITE_URL . "/views/admin/appointments.php");
        } else {
            header("Location: " . SITE_URL . "/views/admin/appointments.php");
        }
        break;

    case 'update_status':
        require_once __DIR__ . '/AppointmentController.php';
        $controller = new AppointmentController();
        if ($controller->updateStatus()) {
            header("Location: " . SITE_URL . "/views/admin/appointments.php");
        } else {
            header("Location: " . SITE_URL . "/views/admin/appointments.php");
        }
        break;

    case 'cancel':
        require_once __DIR__ . '/AppointmentController.php';
        $controller = new AppointmentController();
        if ($controller->cancel()) {
            header("Location: " . SITE_URL . "/views/user/dashboard.php");
        } else {
            header("Location: " . SITE_URL . "/views/user/dashboard.php");
        }
        break;

    default:
        header("Location: " . SITE_URL . "/index.php");
        break;
}
