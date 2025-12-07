<?php

/**
 * Appointment Controller
 * Handles appointment CRUD operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Appointment.php';

class AppointmentController
{
    private $db;
    private $appointment;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->appointment = new Appointment($this->db);
    }

    /**
     * Create new appointment
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->appointment->user_id = $_SESSION['user_id'];
            $this->appointment->service_id = $_POST['service_id'];
            $this->appointment->appointment_date = $_POST['appointment_date'];
            $this->appointment->appointment_time = $_POST['appointment_time'];
            $this->appointment->purpose = trim($_POST['purpose']);
            $this->appointment->notes = trim($_POST['notes']);
            $this->appointment->status = STATUS_PENDING;
            $this->appointment->payment_method = !empty($_POST['payment_method']) ? $_POST['payment_method'] : null;

            // Validation
            if (
                empty($this->appointment->service_id) ||
                empty($this->appointment->appointment_date) ||
                empty($this->appointment->appointment_time)
            ) {
                $_SESSION['error'] = "Please fill in all required fields";
                return false;
            }

            // Check if date is in the future
            $selected_date = strtotime($this->appointment->appointment_date);
            $today = strtotime(date('Y-m-d'));

            if ($selected_date < $today) {
                $_SESSION['error'] = "Appointment date must be in the future";
                return false;
            }

            // Check if time slot is available
            if (!$this->appointment->isTimeSlotAvailable(
                $this->appointment->appointment_date,
                $this->appointment->appointment_time
            )) {
                $_SESSION['error'] = "This time slot is not available";
                return false;
            }

            if ($this->appointment->create()) {
                $_SESSION['success'] = "Appointment created successfully!";
                return true;
            } else {
                $_SESSION['error'] = "Failed to create appointment";
                return false;
            }
        }
    }

    /**
     * Update appointment
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->appointment->id = $_POST['appointment_id'];
            $this->appointment->appointment_date = $_POST['appointment_date'];
            $this->appointment->appointment_time = $_POST['appointment_time'];
            $this->appointment->status = $_POST['status'];
            $this->appointment->admin_notes = trim($_POST['admin_notes']);
            $this->appointment->queue_number = trim($_POST['queue_number']);

            if ($this->appointment->update()) {
                $_SESSION['success'] = "Appointment updated successfully!";
                return true;
            } else {
                $_SESSION['error'] = "Failed to update appointment";
                return false;
            }
        }
    }

    /**
     * Update appointment status
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->appointment->id = $_POST['appointment_id'];
            $this->appointment->status = $_POST['status'];
            $this->appointment->admin_notes = trim($_POST['admin_notes'] ?? '');

            if ($this->appointment->updateStatus()) {
                $_SESSION['success'] = "Status updated successfully!";
                return true;
            } else {
                $_SESSION['error'] = "Failed to update status";
                return false;
            }
        }
    }

    /**
     * Cancel appointment
     */
    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->appointment->id = $_POST['appointment_id'];
            $this->appointment->status = STATUS_CANCELLED;
            $this->appointment->admin_notes = "Cancelled by user";

            if ($this->appointment->updateStatus()) {
                $_SESSION['success'] = "Appointment cancelled successfully!";
                return true;
            } else {
                $_SESSION['error'] = "Failed to cancel appointment";
                return false;
            }
        }
    }

    /**
     * Delete appointment
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->appointment->id = $_POST['appointment_id'];

            if ($this->appointment->delete()) {
                $_SESSION['success'] = "Appointment deleted successfully!";
                return true;
            } else {
                $_SESSION['error'] = "Failed to delete appointment";
                return false;
            }
        }
    }

    /**
     * Get all appointments
     */
    public function getAll()
    {
        return $this->appointment->read();
    }

    /**
     * Get user appointments
     */
    public function getUserAppointments($user_id)
    {
        $this->appointment->user_id = $user_id;
        return $this->appointment->readByUser();
    }

    /**
     * Get appointment by ID
     */
    public function getById($id)
    {
        $this->appointment->id = $id;
        return $this->appointment->readOne();
    }

    /**
     * Get appointments by date
     */
    public function getByDate($date)
    {
        return $this->appointment->readByDate($date);
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        return $this->appointment->getStatistics();
    }
}

// Handle direct controller access
if (basename($_SERVER['PHP_SELF']) == 'AppointmentController.php') {
    require_once __DIR__ . '/AuthController.php';
    AuthController::requireLogin();

    $action = $_GET['action'] ?? '';
    $controller = new AppointmentController();

    switch ($action) {
        case 'create':
            if ($controller->create()) {
                header("Location: " . SITE_URL . "/views/user/dashboard.php");
            } else {
                header("Location: " . SITE_URL . "/views/user/new_appointment.php");
            }
            break;

        case 'update':
            AuthController::requireAdmin();
            if ($controller->update()) {
                header("Location: " . SITE_URL . "/views/admin/appointments.php");
            } else {
                header("Location: " . SITE_URL . "/views/admin/appointments.php");
            }
            break;

        case 'update_status':
            AuthController::requireAdmin();
            if ($controller->updateStatus()) {
                header("Location: " . SITE_URL . "/views/admin/appointments.php");
            } else {
                header("Location: " . SITE_URL . "/views/admin/appointments.php");
            }
            break;

        case 'get':
            // AJAX endpoint to get appointment details
            header('Content-Type: application/json');
            if (isset($_GET['id'])) {
                $appointment = $controller->getById($_GET['id']);
                // Allow admin to view any appointment, or user to view their own
                if ($appointment && ($_SESSION['role'] == 'admin' || $appointment['user_id'] == $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'appointment' => $appointment]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Appointment not found or access denied']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID required']);
            }
            exit();

        case 'cancel':
            if ($controller->cancel()) {
                header("Location: " . SITE_URL . "/views/user/dashboard.php");
            } else {
                header("Location: " . SITE_URL . "/views/user/dashboard.php");
            }
            break;

        case 'delete':
            AuthController::requireAdmin();
            if ($controller->delete()) {
                header("Location: " . SITE_URL . "/views/admin/appointments.php");
            } else {
                header("Location: " . SITE_URL . "/views/admin/appointments.php");
            }
            break;

        default:
            header("Location: " . SITE_URL . "/index.php");
            exit();
    }
}
