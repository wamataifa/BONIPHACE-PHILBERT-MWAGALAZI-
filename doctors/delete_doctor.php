<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
if ($role !== 'admin') {
    header('Location: view_doctors.php?error=unauthorized');
    exit();
}

require_once '../classes/Doctor.php';

if (!isset($_GET['id'])) {
    header('Location: view_doctors.php?error=missing_id');
    exit();
}

$doctor = new Doctor();
$id = (int) $_GET['id'];

if ($doctor->deleteDoctor($id)) {
    header('Location: view_doctors.php?deleted=1');
} else {
    header('Location: view_doctors.php?error=delete_failed');
}
exit();
