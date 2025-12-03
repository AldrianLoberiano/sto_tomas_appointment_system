<?php
require_once __DIR__ . '/../config/config.php';

// Redirect to home page - login is now modal-based
header("Location: " . SITE_URL . "/index.php");
exit();
