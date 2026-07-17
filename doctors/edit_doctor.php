<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Doctor.php';

$doctor = new Doctor();

if (!isset($_GET['id'])) {
    die('Doctor ID not found.');
}

$id = $_GET['id'];
$message = '';

if (isset($_POST['update'])) {
    if ($doctor->updateDoctor(
        $id,
        $_POST['fullname'],
        $_POST['specialization'],
        $_POST['phone'],
        $_POST['email']
    )) {
        $message = 'Doctor updated successfully.';
        header('Location: view_doctors.php');
        exit();
    } else {
        $message = 'Unable to update doctor.';
    }
}

$row = $doctor->getDoctorById($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 py-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="mb-4">Edit Doctor</h2>
                        <?php if ($message) { ?>
                            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($row['fullname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($row['specialization']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">Update Doctor</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
