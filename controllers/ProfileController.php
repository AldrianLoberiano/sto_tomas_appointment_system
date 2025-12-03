<?php

/**
 * Profile Controller
 * Handles user profile update operations including profile picture
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AuthController.php';

class ProfileController
{
    private $db;
    private $user;

    public function __construct()
    {
        AuthController::requireLogin();

        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    /**
     * Update user profile
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->id = intval($_POST['user_id']);

            // Verify user is updating their own profile
            if ($this->user->id != $_SESSION['user_id']) {
                $_SESSION['error'] = "You can only update your own profile";
                $this->redirectToProfile();
            }

            $this->user->username = trim($_POST['username']);
            $this->user->email = trim($_POST['email']);
            $this->user->first_name = trim($_POST['first_name']);
            $this->user->last_name = trim($_POST['last_name']);
            $this->user->middle_name = trim($_POST['middle_name']);
            $this->user->phone = trim($_POST['phone']);
            $this->user->address = trim($_POST['address']);

            // Get current user data to preserve role and is_active
            $this->user->readOne();

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
                $this->redirectToProfile();
            }

            // Email validation
            if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format";
                $this->redirectToProfile();
            }

            // Check if username exists for other users
            $query = "SELECT id FROM users WHERE username = :username AND id != :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":username", $this->user->username);
            $stmt->bindParam(":id", $this->user->id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "Username already exists";
                $this->redirectToProfile();
            }

            // Check if email exists for other users
            $query = "SELECT id FROM users WHERE email = :email AND id != :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":email", $this->user->email);
            $stmt->bindParam(":id", $this->user->id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "Email already exists";
                $this->redirectToProfile();
            }

            if ($this->user->update()) {
                // Update session variables
                $_SESSION['username'] = $this->user->username;
                $_SESSION['full_name'] = $this->user->first_name . ' ' . $this->user->last_name;

                $_SESSION['success'] = "Profile updated successfully!";
                $this->redirectToProfile();
            } else {
                $_SESSION['error'] = "Failed to update profile. Please try again.";
                $this->redirectToProfile();
            }
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = intval($_POST['user_id']);

            // Verify user is changing their own password
            if ($user_id != $_SESSION['user_id']) {
                $_SESSION['error'] = "You can only change your own password";
                $this->redirectToProfile();
            }

            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Validation
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['error'] = "All password fields are required";
                $this->redirectToProfile();
            }

            if ($new_password !== $confirm_password) {
                $_SESSION['error'] = "New passwords do not match";
                $this->redirectToProfile();
            }

            if (strlen($new_password) < 6) {
                $_SESSION['error'] = "Password must be at least 6 characters";
                $this->redirectToProfile();
            }

            // Verify current password
            $query = "SELECT password FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($current_password, $row['password'])) {
                $_SESSION['error'] = "Current password is incorrect";
                $this->redirectToProfile();
            }

            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":id", $user_id);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Password changed successfully!";
                $this->redirectToProfile();
            } else {
                $_SESSION['error'] = "Failed to change password. Please try again.";
                $this->redirectToProfile();
            }
        }
    }

    /**
     * Upload profile picture
     */
    public function uploadPicture()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = intval($_POST['user_id']);

            // Verify user is uploading their own picture
            if ($user_id != $_SESSION['user_id']) {
                $_SESSION['error'] = "You can only upload pictures for your own profile";
                $this->redirectToProfile();
            }

            // File upload handling
            if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = "Please select an image to upload";
                $this->redirectToProfile();
            }

            $file = $_FILES['profile_picture'];
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime_type, $allowed_types)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, PNG, and GIF are allowed";
                $this->redirectToProfile();
            }

            // Validate file size
            if ($file['size'] > $max_size) {
                $_SESSION['error'] = "File size exceeds 2MB limit";
                $this->redirectToProfile();
            }

            // Create uploads directory if it doesn't exist
            $upload_dir = __DIR__ . '/../uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Get old profile picture to delete
            $query = "SELECT profile_picture FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $old_picture = $row['profile_picture'];

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            $db_filepath = 'uploads/profiles/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $_SESSION['error'] = "Failed to upload image";
                $this->redirectToProfile();
            }

            // Update database
            $query = "UPDATE users SET profile_picture = :profile_picture WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':profile_picture', $db_filepath);
            $stmt->bindParam(':id', $user_id);

            if ($stmt->execute()) {
                // Delete old profile picture if exists
                if ($old_picture && file_exists(__DIR__ . '/../' . $old_picture)) {
                    unlink(__DIR__ . '/../' . $old_picture);
                }

                $_SESSION['success'] = "Profile picture updated successfully!";
                $this->redirectToProfile();
            } else {
                // Delete uploaded file if database update fails
                unlink($filepath);
                $_SESSION['error'] = "Failed to update profile picture. Please try again.";
                $this->redirectToProfile();
            }
        }
    }

    /**
     * Redirect to profile page
     */
    private function redirectToProfile()
    {
        // Check if user is admin
        if ($_SESSION['role'] === 'admin') {
            header("Location: " . SITE_URL . "/views/admin/profile.php");
        } else {
            header("Location: " . SITE_URL . "/views/user/profile.php");
        }
        exit();
    }
}

// Handle direct controller access
if (basename($_SERVER['PHP_SELF']) == 'ProfileController.php') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $controller = new ProfileController();

    switch ($action) {
        case 'update':
            $controller->update();
            break;
        case 'change_password':
            $controller->changePassword();
            break;
        case 'upload_picture':
            $controller->uploadPicture();
            break;
        default:
            header("Location: " . SITE_URL . "/views/user/profile.php");
            exit();
    }
}
