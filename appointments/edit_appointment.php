<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Appointment.php';
require_once '../classes/Patient.php';
require_once '../classes/Doctor.php';

$appointment = new Appointment();
$patient = new Patient();
$doctor = new Doctor();

if (!isset($_GET['id'])) {
    die('Appointment ID not found.');
}

$id = (int) $_GET['id'];
$message = '';

if (isset($_POST['update'])) {
    if ($appointment->updateAppointment(
        $id,
        $_POST['patient_id'],
        $_POST['doctor_id'],
        $_POST['appointment_date'],
        $_POST['appointment_time'],
        $_POST['status'],
        $_POST['notes']
    )) {
        $message = 'Appointment updated successfully.';
        header('Location: view_appointments.php');
        exit();
    } else {
        $message = 'Unable to update appointment.';
    }
}

$row = $appointment->getAppointmentById($id);
$patients = $patient->getPatients();
$doctors = $doctor->getDoctors();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Edit Appointment</h2>
                    <?php if ($message) { ?>
                        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                    <?php } ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select patient</option>
                                <?php foreach ($patients as $patientRow) { ?>
                                    <option value="<?= $patientRow['patient_id'] ?>" <?= $row['patient_id'] == $patientRow['patient_id'] ? 'selected' : '' ?>><?= htmlspecialchars($patientRow['first_name'] . ' ' . $patientRow['last_name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select doctor</option>
                                <?php foreach ($doctors as $doctorRow) { ?>
                                    <option value="<?= $doctorRow['doctor_id'] ?>" <?= $row['doctor_id'] == $doctorRow['doctor_id'] ? 'selected' : '' ?>><?= htmlspecialchars($doctorRow['fullname']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="appointment_date" class="form-control" value="<?= htmlspecialchars($row['appointment_date']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" name="appointment_time" class="form-control" value="<?= htmlspecialchars($row['appointment_time']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Scheduled" <?= $row['status'] == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($row['notes']) ?></textarea>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Update Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
