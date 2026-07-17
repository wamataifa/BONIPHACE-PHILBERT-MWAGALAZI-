<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Patient.php';

$message = '';

if (isset($_POST['save'])) {
    $patient = new Patient();

    if ($patient->addPatient(
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['gender'],
        $_POST['date_of_birth'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['address']
    )) {
        $message = 'Patient registered successfully.';
    } else {
        $message = 'Registration failed.' . ($patient->getLastError() ? ' Error: ' . $patient->getLastError() : '');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient</title>
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
                            <h2 class="mb-0">Add Patient</h2>
                        </div>
                        <?php if ($message) { ?>
                            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control"></textarea>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Save Patient</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>