<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Doctor.php';

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$isAdmin = $role === 'admin';

$doctor = new Doctor();
$doctors = $doctor->getDoctors();

$message = '';
$messageType = 'info';
if (isset($_GET['deleted'])) {
    $message = 'Doctor deleted successfully.';
    $messageType = 'success';
} elseif (isset($_GET['error'])) {
    $messageType = 'danger';
    if ($_GET['error'] === 'unauthorized') {
        $message = 'Only admins can delete doctors.';
    } elseif ($_GET['error'] === 'missing_id') {
        $message = 'Doctor ID not found.';
    } else {
        $message = 'Unable to delete doctor. Please try again.';
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <h2 class="mb-4">Registered Doctors</h2>

            <?php if ($message) { ?>
                <div class="alert alert-<?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
            <?php } ?>

            <?php if (!empty($doctors)) { ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Specialization</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <?php if ($isAdmin) { ?>
                                <th>Action</th>
                                <?php } ?>
                            </tr>

                            <?php foreach ($doctors as $row) { ?>
                            <tr>
                                <td><?= (int) $row['doctor_id'] ?></td>
                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                <td><?= htmlspecialchars($row['specialization']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <?php if ($isAdmin) { ?>
                                <td class="text-nowrap">
                                    <a href="edit_doctor.php?id=<?= (int) $row['doctor_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete_doctor.php?id=<?= (int) $row['doctor_id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this doctor?');">Delete</a>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
            <?php } else { ?>
            <div class="alert alert-info">No doctors found.</div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
