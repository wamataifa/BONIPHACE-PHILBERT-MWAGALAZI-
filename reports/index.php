<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Patient.php';
require_once '../classes/Doctor.php';
require_once '../classes/Appointment.php';
require_once '../classes/Treatment.php';
require_once '../classes/Payment.php';

$patient = new Patient();
$doctor = new Doctor();
$appointment = new Appointment();
$treatment = new Treatment();
$payment = new Payment();

$patients = $patient->getPatients();
$doctors = $doctor->getDoctors();
$appointments = $appointment->getAppointments();
$treatments = $treatment->getTreatments();
$payments = $payment->getPayments();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Clinic Reports</h2>
                    <p class="text-muted mb-0">Overview of patients, doctors, appointments, treatments, and payments.</p>
                </div>
                <a href="../dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card text-bg-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Patients</h5>
                            <h2><?= count($patients) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-success shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Doctors</h5>
                            <h2><?= count($doctors) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-warning shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Appointments</h5>
                            <h2><?= count($appointments) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-danger shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Payments</h5>
                            <h2><?= count($payments) ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title">Recent Treatments</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Treatment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($treatments, 0, 8) as $row) { ?>
                                    <tr>
                                        <td><?= $row['treatment_id'] ?></td>
                                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($row['treatment_name']) ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
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