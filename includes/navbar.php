<?php
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
if ($scriptDir !== '/' && basename($scriptDir) !== 'clinic_management') {
    $scriptDir = dirname($scriptDir);
}
$appBase = rtrim($scriptDir, '/');
if ($appBase === '') {
    $appBase = '/';
}
?>
<nav class="navbar navbar-dark bg-primary px-3">
    <a class="navbar-brand d-flex align-items-center" href="<?= $appBase ?>/dashboard.php">
        <img src="<?= $appBase ?>/assets/images/logo.png" alt="Clinic Management System logo" class="brand-logo" style="width: 45px; height: 45px; min-width: 45px; min-height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; display: block; margin-right: 10px;">
        <span>Clinic Management System</span>
    </a>
    <div class="text-white">
        <?= isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'User' ?> |
        <?= isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : 'Guest' ?> |
        <a href="<?= $appBase ?>/logout.php" class="text-white ms-2">Logout</a>
    </div>
</nav>
