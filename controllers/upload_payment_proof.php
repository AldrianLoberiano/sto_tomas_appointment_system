<?php

/**
 * Payment Proof Upload Handler
 * Handles the upload of payment proof images for appointments
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/AuthController.php';

// Require authentication
AuthController::requireLogin();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

// Validate appointment ID
if (!isset($_POST['appointment_id']) || empty($_POST['appointment_id'])) {
    $_SESSION['error'] = 'Appointment ID is required';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

$appointmentId = intval($_POST['appointment_id']);
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

// Verify that the appointment belongs to the logged-in user
$database = new Database();
$db = $database->getConnection();

$query = "SELECT user_id, status FROM appointments WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $appointmentId);
$stmt->execute();
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    $_SESSION['error'] = 'Appointment not found';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

if ($appointment['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Unauthorized access to this appointment';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

if ($appointment['status'] !== 'approved') {
    $_SESSION['error'] = 'Payment proof can only be uploaded for approved appointments';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = 'Please select a payment proof file';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

$file = $_FILES['payment_proof'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'File upload error. Please try again.';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

// Validate file size (5MB max)
$maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $maxFileSize) {
    $_SESSION['error'] = 'File size must be less than 5MB';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, and PDF files are allowed';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

// Get file extension
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, ['jpg', 'jpeg', 'png', 'pdf'])) {
    $extension = $mimeType === 'application/pdf' ? 'pdf' : 'jpg';
}

// Create uploads directory if it doesn't exist
$uploadsDir = __DIR__ . '/../uploads/payment_proofs';
if (!is_dir($uploadsDir)) {
    if (!mkdir($uploadsDir, 0755, true)) {
        $_SESSION['error'] = 'Failed to create upload directory';
        header('Location: ' . SITE_URL . '/views/user/dashboard.php');
        exit();
    }
}

// Generate unique filename
$filename = 'payment_' . $appointmentId . '_' . time() . '_' . uniqid() . '.' . $extension;
$filepath = $uploadsDir . '/' . $filename;
$relativePath = 'uploads/payment_proofs/' . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    $_SESSION['error'] = 'Failed to save uploaded file';
    header('Location: ' . SITE_URL . '/views/user/dashboard.php');
    exit();
}

// Delete old payment proof if exists
$queryOld = "SELECT payment_proof FROM appointments WHERE id = :id";
$stmtOld = $db->prepare($queryOld);
$stmtOld->bindParam(':id', $appointmentId);
$stmtOld->execute();
$oldProof = $stmtOld->fetch(PDO::FETCH_ASSOC);

if ($oldProof && !empty($oldProof['payment_proof'])) {
    $oldFilePath = __DIR__ . '/../' . $oldProof['payment_proof'];
    if (file_exists($oldFilePath)) {
        @unlink($oldFilePath);
    }
}

// Update database with payment proof path
try {
    $updateQuery = "UPDATE appointments 
                    SET payment_proof = :payment_proof,
                        payment_proof_uploaded_at = NOW()";

    // Add notes to admin_notes if provided
    if (!empty($notes)) {
        $updateQuery .= ", admin_notes = CONCAT(COALESCE(admin_notes, ''), '\n[Payment Notes]: ', :notes)";
    }

    $updateQuery .= " WHERE id = :id";

    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':payment_proof', $relativePath);
    $updateStmt->bindParam(':id', $appointmentId);

    if (!empty($notes)) {
        $updateStmt->bindParam(':notes', $notes);
    }

    if ($updateStmt->execute()) {
        $_SESSION['success'] = 'Payment proof uploaded successfully! The admin will verify your payment.';
    } else {
        // Delete uploaded file if database update fails
        @unlink($filepath);
        $_SESSION['error'] = 'Failed to save payment proof information';
    }
} catch (PDOException $e) {
    // Delete uploaded file on error
    @unlink($filepath);
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
}

// Redirect back to dashboard
header('Location: ' . SITE_URL . '/views/user/dashboard.php');
exit();
