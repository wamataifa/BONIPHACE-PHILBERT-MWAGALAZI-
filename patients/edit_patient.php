<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Patient.php';

$patient = new Patient();

if (!isset($_GET['id'])) {
    die('Patient ID not found.');
}

$id = $_GET['id'];
$message = '';

if (isset($_POST['update'])) {
    if ($patient->updatePatient(
        $id,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['gender'],
        $_POST['date_of_birth'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['address']
    )) {
        $message = 'Patient updated successfully.';
        header('Location: view_patients.php');
        exit();
    } else {
        $message = 'Unable to update patient.';
    }
}

$row = $patient->getPatientById($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 py-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="mb-4">Edit Patient</h2>
                        <?php if ($message) { ?>
                            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($row['first_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($row['last_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" <?= $row['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $row['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($row['date_of_birth']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control"><?= htmlspecialchars($row['address']) ?></textarea>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">Update Patient</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>