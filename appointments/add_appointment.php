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

$message = '';

if (isset($_POST['save'])) {
    if ($appointment->addAppointment(
        $_POST['patient_id'],
        $_POST['doctor_id'],
        $_POST['appointment_date'],
        $_POST['appointment_time'],
        $_POST['status'],
        $_POST['notes']
    )) {
        $message = 'Appointment scheduled successfully.';
    } else {
        $message = 'Unable to schedule appointment.';
        $detail = $appointment->getLastError();
        if ($detail) {
            $message .= ' ' . htmlspecialchars($detail);
        }
    }
}

$patients = $patient->getPatients();
$doctors = $doctor->getDoctors();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Add Appointment</h2>
                    <p class="text-muted mb-0">Create a new appointment record.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="view_appointments.php" class="btn btn-outline-secondary">View All</a>
                    <a href="../dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
                </div>
            </div>

            <?php if ($message) { ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select patient</option>
                                <?php foreach ($patients as $patientRow) { ?>
                                    <option value="<?= $patientRow['patient_id'] ?>"><?= htmlspecialchars($patientRow['first_name'] . ' ' . $patientRow['last_name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select doctor</option>
                                <?php foreach ($doctors as $doctorRow) { ?>
                                    <option value="<?= $doctorRow['doctor_id'] ?>"><?= htmlspecialchars($doctorRow['fullname']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="appointment_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Scheduled">Scheduled</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" name="save" class="btn btn-primary">Save Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
