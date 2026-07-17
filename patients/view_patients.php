<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Patient.php';

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$canManagePatients = $role === 'admin' || $role === 'doctor';

$patient = new Patient();
$patients = $patient->getPatients();

$message = '';
$messageType = 'info';
if (isset($_GET['deleted'])) {
    $message = 'Patient deleted successfully.';
    $messageType = 'success';
} elseif (isset($_GET['error'])) {
    $messageType = 'danger';
    if ($_GET['error'] === 'unauthorized') {
        $message = 'You are not allowed to delete patients.';
    } elseif ($_GET['error'] === 'missing_id') {
        $message = 'Patient ID not found.';
    } else {
        $message = 'Unable to delete patient. Please try again.';
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <h2 class="mb-4">Registered Patients</h2>

            <?php if ($message) { ?>
                <div class="alert alert-<?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <?php if ($canManagePatients) { ?>
                                <th>Action</th>
                                <?php } ?>
                            </tr>

                            <?php foreach ($patients as $row) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['patient_id']) ?></td>
                                <td><?= htmlspecialchars($row['first_name']) ?></td>
                                <td><?= htmlspecialchars($row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['gender']) ?></td>
                                <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <?php if ($canManagePatients) { ?>
                                <td class="text-nowrap">
                                    <a href="edit_patient.php?id=<?= (int) $row['patient_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete_patient.php?id=<?= (int) $row['patient_id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this patient?');">Delete</a>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
