<?php

/**
 * Helper Functions
 * Utility functions used throughout the application
 */

/**
 * Sanitize input data
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y')
{
    return date($format, strtotime($date));
}

/**
 * Format time
 */
function formatTime($time, $format = 'h:i A')
{
    return date($format, strtotime($time));
}

/**
 * Generate random string
 */
function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

/**
 * Send email notification
 */
function sendEmail($to, $subject, $message)
{
    $headers = "From: " . SITE_NAME . " <noreply@barangay.local>\r\n";
    $headers .= "Reply-To: noreply@barangay.local\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
}

/**
 * Upload file
 */
function uploadFile($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'], $max_size = MAX_FILE_SIZE)
{
    $target_dir = UPLOAD_PATH;
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed size'];
    }

    // Check file type
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    // Create upload directory if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
}

/**
 * Delete file
 */
function deleteFile($filepath)
{
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get time slots
 */
function getTimeSlots($start_time = OFFICE_HOURS_START, $end_time = OFFICE_HOURS_END, $duration = SLOT_DURATION)
{
    $slots = [];
    $start = strtotime($start_time);
    $end = strtotime($end_time);

    while ($start < $end) {
        $slots[] = date('H:i', $start);
        $start = strtotime("+{$duration} minutes", $start);
    }

    return $slots;
}

/**
 * Check if date is weekend
 */
function isWeekend($date)
{
    $day = date('N', strtotime($date));
    return ($day >= 6);
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status)
{
    $classes = [
        'pending' => 'badge-pending',
        'approved' => 'badge-approved',
        'completed' => 'badge-completed',
        'cancelled' => 'badge-cancelled',
        'rejected' => 'badge-rejected'
    ];

    return $classes[$status] ?? 'badge-secondary';
}

/**
 * Redirect
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

/**
 * Check if string is valid email
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Log activity
 */
function logActivity($user_id, $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null)
{
    require_once __DIR__ . '/../config/database.php';

    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO audit_log 
              SET user_id=:user_id, action=:action, table_name=:table_name, 
                  record_id=:record_id, old_values=:old_values, new_values=:new_values,
                  ip_address=:ip_address, user_agent=:user_agent";

    $stmt = $db->prepare($query);

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $old_values_json = $old_values ? json_encode($old_values) : null;
    $new_values_json = $new_values ? json_encode($new_values) : null;

    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":action", $action);
    $stmt->bindParam(":table_name", $table_name);
    $stmt->bindParam(":record_id", $record_id);
    $stmt->bindParam(":old_values", $old_values_json);
    $stmt->bindParam(":new_values", $new_values_json);
    $stmt->bindParam(":ip_address", $ip_address);
    $stmt->bindParam(":user_agent", $user_agent);

    return $stmt->execute();
}
