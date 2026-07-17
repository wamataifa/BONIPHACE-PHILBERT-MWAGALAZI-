<?php
// Keep PHP error reporting active so we can see what goes wrong
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect to the main dashboard
header("Location: dashboard.php");
exit();
?>