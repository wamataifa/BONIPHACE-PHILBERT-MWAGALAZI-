<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Treatment.php';

if (!isset($_GET['id'])) {
    die('Treatment ID not found.');
}

$treatment = new Treatment();
$treatment->deleteTreatment((int) $_GET['id']);

header('Location: view_treatments.php');
exit();
