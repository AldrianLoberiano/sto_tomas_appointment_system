<?php

/**
 * Service Controller
 * Handles service CRUD operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/AuthController.php';

class ServiceController
{
    private $db;
    private $service;

    public function __construct()
    {
        AuthController::requireAdmin();

        $database = new Database();
        $this->db = $database->getConnection();
        $this->service = new Service($this->db);
    }

    /**
     * Create new service
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->service->service_name = trim($_POST['service_name']);
            $this->service->description = trim($_POST['description']);
            $this->service->requirements = trim($_POST['requirements']);
            $this->service->processing_time = trim($_POST['processing_time']);
            $this->service->fee = floatval($_POST['fee']);
            $this->service->is_active = intval($_POST['is_active']);

            // Validation
            if (
                empty($this->service->service_name) ||
                empty($this->service->description) ||
                empty($this->service->requirements) ||
                empty($this->service->processing_time)
            ) {
                $_SESSION['error'] = "Please fill in all required fields";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }

            if ($this->service->fee < 0) {
                $_SESSION['error'] = "Service fee cannot be negative";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }

            if ($this->service->create()) {
                $_SESSION['success'] = "Service created successfully!";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to create service. Please try again.";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }
        }
    }

    /**
     * Update service
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->service->id = intval($_POST['service_id']);
            $this->service->service_name = trim($_POST['service_name']);
            $this->service->description = trim($_POST['description']);
            $this->service->requirements = trim($_POST['requirements']);
            $this->service->processing_time = trim($_POST['processing_time']);
            $this->service->fee = floatval($_POST['fee']);
            $this->service->is_active = intval($_POST['is_active']);

            // Validation
            if (
                empty($this->service->service_name) ||
                empty($this->service->description) ||
                empty($this->service->requirements) ||
                empty($this->service->processing_time)
            ) {
                $_SESSION['error'] = "Please fill in all required fields";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }

            if ($this->service->fee < 0) {
                $_SESSION['error'] = "Service fee cannot be negative";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }

            if ($this->service->update()) {
                $_SESSION['success'] = "Service updated successfully!";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to update service. Please try again.";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }
        }
    }

    /**
     * Delete service
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->service->id = intval($_POST['service_id']);

            if ($this->service->delete()) {
                $_SESSION['success'] = "Service deleted successfully!";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to delete service. It may be in use.";
                header("Location: " . SITE_URL . "/views/admin/services.php");
                exit();
            }
        }
    }
}

// Handle direct controller access
if (basename($_SERVER['PHP_SELF']) == 'ServiceController.php') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $controller = new ServiceController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            header("Location: " . SITE_URL . "/views/admin/services.php");
            exit();
    }
}
