<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$isAdmin = $role === 'admin';
$isDoctor = $role === 'doctor';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h3 class="mb-2">Welcome back, <?= htmlspecialchars($_SESSION['fullname']) ?></h3>
                                <p class="text-muted mb-0">You are logged in as <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4 g-3">
                    <?php if ($isAdmin || $isDoctor) { ?>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Add Patient</h5>
                                <p class="card-text">Register a new patient quickly.</p>
                                <a href="patients/add_patient.php" class="btn btn-primary">Add Patient</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if ($isAdmin) { ?>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Add Doctor</h5>
                                <p class="card-text">Register a new doctor in the clinic.</p>
                                <a href="doctors/add_doctor.php" class="btn btn-success">Add Doctor</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Patients</h5>
                                <p class="card-text">Manage patient records and personal information.</p>
                                <a href="patients/view_patients.php" class="btn btn-primary">Open</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Doctors</h5>
                                <p class="card-text">Maintain doctor profiles and specializations.</p>
                                <a href="doctors/view_doctors.php" class="btn btn-success">Open</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Appointments</h5>
                                <p class="card-text">Schedule consultations and track appointments.</p>
                                <a href="appointments/index.php" class="btn btn-warning">Open</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Treatments</h5>
                                <p class="card-text">Record prescribed treatments and care plans.</p>
                                <a href="treatments/index.php" class="btn btn-info">Open</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Payments</h5>
                                <p class="card-text">Capture payments and billing history.</p>
                                <a href="payments/index.php" class="btn btn-danger">Open</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Reports</h5>
                                <p class="card-text">Visualize system data and track activity.</p>
                                <a href="reports/index.php" class="btn btn-secondary">Open</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>