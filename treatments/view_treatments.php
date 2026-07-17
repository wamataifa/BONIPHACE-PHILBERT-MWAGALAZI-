<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Treatment.php';

$treatment = new Treatment();
$treatments = $treatment->getTreatments();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Treatments</h2>
                    <p class="text-muted mb-0">Manage all treatment records.</p>
                </div>
                <a href="index.php" class="btn btn-outline-primary">Add Treatment</a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Treatment</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($treatments as $row) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['treatment_id']) ?></td>
                                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($row['treatment_name']) ?></td>
                                        <td><?= htmlspecialchars($row['treatment_date']) ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td><?= htmlspecialchars($row['description']) ?></td>
                                        <td>
                                            <a href="edit_treatment.php?id=<?= $row['treatment_id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="delete_treatment.php?id=<?= $row['treatment_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this treatment?')">Delete</a>
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
