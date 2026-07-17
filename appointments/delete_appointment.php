<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Appointment.php';

if (!isset($_GET['id'])) {
    die('Appointment ID not found.');
}

$appointment = new Appointment();
$appointment->deleteAppointment((int) $_GET['id']);

header('Location: view_appointments.php');
exit();
