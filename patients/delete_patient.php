<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
if ($role !== 'admin' && $role !== 'doctor') {
    header('Location: view_patients.php?error=unauthorized');
    exit();
}

require_once '../classes/Patient.php';

if (!isset($_GET['id'])) {
    header('Location: view_patients.php?error=missing_id');
    exit();
}

$patient = new Patient();
$id = (int) $_GET['id'];

if ($patient->deletePatient($id)) {
    header('Location: view_patients.php?deleted=1');
} else {
    header('Location: view_patients.php?error=delete_failed');
}
exit();
