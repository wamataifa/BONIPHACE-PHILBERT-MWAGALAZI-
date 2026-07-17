<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Treatment.php';
require_once '../classes/Patient.php';
require_once '../classes/Doctor.php';

$treatment = new Treatment();
$patient = new Patient();
$doctor = new Doctor();

$message = '';

if (isset($_POST['save'])) {
    if ($treatment->addTreatment(
        $_POST['patient_id'],
        $_POST['doctor_id'],
        $_POST['treatment_name'],
        $_POST['treatment_date'],
        $_POST['description'],
        $_POST['status']
    )) {
        $message = 'Treatment record was saved.';
    } else {
        $message = 'Unable to save treatment.';
        $detail = $treatment->getLastError();
        if ($detail) {
            $message .= ' ' . htmlspecialchars($detail);
        }
    }
}

$treatments = $treatment->getTreatments();
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
                    <h2 class="mb-1">Treatments</h2>
                    <p class="text-muted mb-0">Manage prescribed treatments and care plans.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="view_treatments.php" class="btn btn-outline-secondary">View All</a>
                    <a href="../dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
                </div>
            </div>

            <?php if ($message) { ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php } ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">New Treatment</h5>
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
                                    <label class="form-label">Treatment Name</label>
                                    <input type="text" name="treatment_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="treatment_date" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Planned">Planned</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" name="save" class="btn btn-primary">Save Treatment</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Treatment Records</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Treatment</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($treatments as $row) { ?>
                                            <tr>
                                                <td><?= $row['treatment_id'] ?></td>
                                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                                <td><?= htmlspecialchars($row['treatment_name']) ?></td>
                                                <td><?= htmlspecialchars($row['treatment_date']) ?></td>
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
    </div>
</div>

<?php include '../includes/footer.php'; ?>