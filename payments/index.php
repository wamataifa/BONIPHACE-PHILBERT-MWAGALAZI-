<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../classes/Payment.php';
require_once '../classes/Patient.php';

$payment = new Payment();
$patient = new Patient();

$message = '';

if (isset($_POST['save'])) {
    if ($payment->addPayment(
        $_POST['patient_id'],
        $_POST['amount'],
        $_POST['payment_date'],
        $_POST['payment_method'],
        $_POST['status'],
        $_POST['notes']
    )) {
        $message = 'Payment recorded successfully.';
    } else {
        $message = 'Unable to record payment.';
        $detail = $payment->getLastError();
        if ($detail) {
            $message .= ' ' . htmlspecialchars($detail);
        }
    }
}

$payments = $payment->getPayments();
$patients = $patient->getPatients();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Payments</h2>
                    <p class="text-muted mb-0">Track invoices and payments for patients.</p>
                </div>
                <a href="../dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a>
            </div>

            <?php if ($message) { ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php } ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">New Payment</h5>
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
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="payment_date" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Method</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Mobile Money">Mobile Money</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Paid">Paid</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" name="save" class="btn btn-primary">Save Payment</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Payment History</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Patient</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $row) { ?>
                                            <tr>
                                                <td><?= $row['payment_id'] ?></td>
                                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                                <td><?= htmlspecialchars($row['amount']) ?></td>
                                                <td><?= htmlspecialchars($row['payment_date']) ?></td>
                                                <td><?= htmlspecialchars($row['payment_method']) ?></td>
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