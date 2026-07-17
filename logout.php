<?php
session_start();
session_unset();
session_destroy();

$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
if ($scriptDir !== '/' && basename($scriptDir) !== 'clinic_management') {
    $scriptDir = dirname($scriptDir);
}
$appBase = rtrim($scriptDir, '/');
if ($appBase === '') {
    $appBase = '/';
}

header('Location: ' . $appBase . '/login.php');
exit();