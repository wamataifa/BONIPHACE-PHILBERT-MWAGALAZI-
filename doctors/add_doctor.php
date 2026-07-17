<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Doctor.php';

$message = '';

if (isset($_POST['save'])) {
    $doctor = new Doctor();

    if ($doctor->addDoctor(
        $_POST['fullname'],
        $_POST['specialization'],
        $_POST['phone'],
        $_POST['email']
    )) {
        $message = 'Doctor registered successfully.';
    } else {
        $message = 'Registration failed.' . ($doctor->getLastError() ? ' Error: ' . $doctor->getLastError() : '');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e8f1fb 0%, #dce9f7 50%, #cfdff5 100%);
            color: #12324a;
        }

        .compact-logo {
            width: 45px;
            height: 45px;
            min-width: 45px;
            min-height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #214a6f;
            display: block;
        }

        .page-shell {
            background: transparent;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid page-shell">
        <div class="row">
            <div class="col-12 py-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <img src="../assets/images/logo.png" alt="Clinic Management System logo" class="compact-logo me-3" style="width: 45px; height: 45px; min-width: 45px; min-height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #214a6f; display: block;">
                            <h2 class="mb-0">Add Doctor</h2>
                        </div>
                        <?php if ($message) { ?>
                            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Save Doctor</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>