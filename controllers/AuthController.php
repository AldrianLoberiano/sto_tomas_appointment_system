<?php

/**
 * Authentication Controller
 * Handles login, logout, and registration
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $db;
    private $user;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    /**
     * Handle user login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Please fill in all fields";
                header("Location: " . SITE_URL . "/views/login.php");
                exit();
            }

            if ($this->user->login($username, $password)) {
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['username'] = $this->user->username;
                $_SESSION['role'] = $this->user->role;
                $_SESSION['full_name'] = $this->user->first_name . ' ' . $this->user->last_name;
                $_SESSION['success'] = "Login successful!";

                // Redirect based on role
                if ($this->user->role == ROLE_ADMIN || $this->user->role == ROLE_STAFF) {
                    header("Location: " . SITE_URL . "/views/admin/dashboard.php");
                } else {
                    header("Location: " . SITE_URL . "/views/user/dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Invalid username or password";
                header("Location: " . SITE_URL . "/views/login.php");
                exit();
            }
        }
    }

    /**
     * Handle user registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->username = trim($_POST['username']);
            $this->user->email = trim($_POST['email']);
            $this->user->password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $this->user->first_name = trim($_POST['first_name']);
            $this->user->last_name = trim($_POST['last_name']);
            $this->user->middle_name = trim($_POST['middle_name']);
            $this->user->phone = trim($_POST['phone']);
            $this->user->address = trim($_POST['address']);
            $this->user->role = ROLE_RESIDENT;

            // Validation
            if (
                empty($this->user->username) || empty($this->user->email) ||
                empty($this->user->password) || empty($this->user->first_name) ||
                empty($this->user->last_name)
            ) {
                $_SESSION['error'] = "Please fill in all required fields";
                header("Location: " . SITE_URL . "/views/register.php");
                exit();
            }

            if ($this->user->password !== $confirm_password) {
                $_SESSION['error'] = "Passwords do not match";
                header("Location: " . SITE_URL . "/views/register.php");
                exit();
            }

            if (strlen($this->user->password) < 6) {
                $_SESSION['error'] = "Password must be at least 6 characters";
                header("Location: " . SITE_URL . "/views/register.php");
                exit();
            }

            if ($this->user->emailExists()) {
                $_SESSION['error'] = "Email already exists";
                header("Location: " . SITE_URL . "/views/register.php");
                exit();
            }

            if ($this->user->usernameExists()) {
                $_SESSION['error'] = "Username already exists";
                header("Location: " . SITE_URL . "/views/register.php");
                exit();
            }

            if ($this->user->create()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: " . SITE_URL . "/views/login.php");
                exit();
            } else {
                $_SESSION['error'] = "Registration failed. Please try again.";
                header("Location: " . SITE_URL . "/views/register.php");
                exit();
            }
        }
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        session_destroy();
        header("Location: " . SITE_URL . "/views/login.php");
        exit();
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role)
    {
        return isset($_SESSION['role']) && $_SESSION['role'] == $role;
    }

    /**
     * Require login
     */
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            $_SESSION['error'] = "Please login to access this page";
            header("Location: " . SITE_URL . "/views/login.php");
            exit();
        }
    }

    /**
     * Require admin role
     */
    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::hasRole(ROLE_ADMIN) && !self::hasRole(ROLE_STAFF)) {
            $_SESSION['error'] = "Access denied";
            header("Location: " . SITE_URL . "/views/user/dashboard.php");
            exit();
        }
    }
}

// Handle direct controller access
if (basename($_SERVER['PHP_SELF']) == 'AuthController.php') {
    $action = $_GET['action'] ?? '';
    $controller = new AuthController();

    switch ($action) {
        case 'login':
            $controller->login();
            break;
        case 'register':
            $controller->register();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            header("Location: " . SITE_URL . "/index.php");
            exit();
    }
}
