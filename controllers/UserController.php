<?php

/**
 * User Controller
 * Handles user CRUD operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AuthController.php';

class UserController
{
    private $db;
    private $user;

    public function __construct()
    {
        AuthController::requireAdmin();

        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    /**
     * Create new user
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->username = trim($_POST['username']);
            $this->user->email = trim($_POST['email']);
            $this->user->password = $_POST['password'];
            $this->user->first_name = trim($_POST['first_name']);
            $this->user->last_name = trim($_POST['last_name']);
            $this->user->middle_name = trim($_POST['middle_name']);
            $this->user->phone = trim($_POST['phone']);
            $this->user->address = trim($_POST['address']);
            $this->user->role = $_POST['role'];

            // Validation
            if (
                empty($this->user->username) ||
                empty($this->user->email) ||
                empty($this->user->password) ||
                empty($this->user->first_name) ||
                empty($this->user->last_name) ||
                empty($this->user->phone) ||
                empty($this->user->address)
            ) {
                $_SESSION['error'] = "Please fill in all required fields";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Email validation
            if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Check if username exists
            if ($this->user->usernameExists()) {
                $_SESSION['error'] = "Username already exists";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Check if email exists
            if ($this->user->emailExists()) {
                $_SESSION['error'] = "Email already exists";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Password strength validation
            if (strlen($this->user->password) < 6) {
                $_SESSION['error'] = "Password must be at least 6 characters";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            if ($this->user->create()) {
                $_SESSION['success'] = "User created successfully!";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to create user. Please try again.";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }
        }
    }

    /**
     * Update user
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->id = intval($_POST['user_id']);
            $this->user->username = trim($_POST['username']);
            $this->user->email = trim($_POST['email']);
            $this->user->first_name = trim($_POST['first_name']);
            $this->user->last_name = trim($_POST['last_name']);
            $this->user->middle_name = trim($_POST['middle_name']);
            $this->user->phone = trim($_POST['phone']);
            $this->user->address = trim($_POST['address']);
            $this->user->role = $_POST['role'];
            $this->user->is_active = intval($_POST['is_active']);

            // Validation
            if (
                empty($this->user->username) ||
                empty($this->user->email) ||
                empty($this->user->first_name) ||
                empty($this->user->last_name) ||
                empty($this->user->phone) ||
                empty($this->user->address)
            ) {
                $_SESSION['error'] = "Please fill in all required fields";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Email validation
            if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Check if username exists for other users
            $query = "SELECT id FROM users WHERE username = :username AND id != :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":username", $this->user->username);
            $stmt->bindParam(":id", $this->user->id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "Username already exists";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Check if email exists for other users
            $query = "SELECT id FROM users WHERE email = :email AND id != :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":email", $this->user->email);
            $stmt->bindParam(":id", $this->user->id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "Email already exists";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            // Update password if provided
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $_SESSION['error'] = "Password must be at least 6 characters";
                    header("Location: " . SITE_URL . "/views/admin/users.php");
                    exit();
                }

                $query = "UPDATE users SET password = :password WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bindParam(":password", $hashed_password);
                $stmt->bindParam(":id", $this->user->id);
                $stmt->execute();
            }

            if ($this->user->update()) {
                $_SESSION['success'] = "User updated successfully!";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to update user. Please try again.";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }
        }
    }

    /**
     * Delete user
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->id = intval($_POST['user_id']);

            // Prevent deleting own account
            if ($this->user->id == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot delete your own account";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }

            if ($this->user->delete()) {
                $_SESSION['success'] = "User deleted successfully!";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to delete user. They may have related records.";
                header("Location: " . SITE_URL . "/views/admin/users.php");
                exit();
            }
        }
    }
}

// Handle direct controller access
if (basename($_SERVER['PHP_SELF']) == 'UserController.php') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $controller = new UserController();

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
            header("Location: " . SITE_URL . "/views/admin/users.php");
            exit();
    }
}
