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

if (!isset($_GET['id'])) {
    die('Treatment ID not found.');
}

$id = (int) $_GET['id'];
$message = '';

if (isset($_POST['update'])) {
    if ($treatment->updateTreatment(
        $id,
        $_POST['patient_id'],
        $_POST['doctor_id'],
        $_POST['treatment_name'],
        $_POST['treatment_date'],
        $_POST['description'],
        $_POST['status']
    )) {
        $message = 'Treatment updated successfully.';
        header('Location: view_treatments.php');
        exit();
    } else {
        $message = 'Unable to update treatment.';
    }
}

$row = $treatment->getTreatmentById($id);
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
                    <h2 class="mb-4">Edit Treatment</h2>
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
                            <label class="form-label">Treatment Name</label>
                            <input type="text" name="treatment_name" class="form-control" value="<?= htmlspecialchars($row['treatment_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="treatment_date" class="form-control" value="<?= htmlspecialchars($row['treatment_date']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Planned" <?= $row['status'] == 'Planned' ? 'selected' : '' ?>>Planned</option>
                                <option value="In Progress" <?= $row['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Update Treatment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
