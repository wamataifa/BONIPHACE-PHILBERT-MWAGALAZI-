<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Appointment.php';

$appointment = new Appointment();
$appointments = $appointment->getAppointments();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Appointments</h2>
                    <p class="text-muted mb-0">Manage all appointment records.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="addappointment.php" class="btn btn-outline-primary">Add Appointment</a>
                    <a href="../dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $row) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                        <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td><?= htmlspecialchars($row['notes']) ?></td>
                                        <td>
                                            <a href="edit_appointment.php?id=<?= $row['appointment_id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="delete_appointment.php?id=<?= $row['appointment_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this appointment?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
