<?php

require_once __DIR__ . "/../config/Database.php";

class Payment
{
    private $conn;
    private $lastError = '';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function addPayment($patient_id, $amount, $payment_date, $payment_method, $status, $notes)
    {
        $this->lastError = '';

        try {
            $sql = "INSERT INTO payments (patient_id, amount, payment_date, payment_method, status, notes)
                    VALUES (:patient_id, :amount, :payment_date, :payment_method, :status, :notes)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':patient_id', $patient_id);
            $stmt->bindValue(':amount', $amount);
            $stmt->bindValue(':payment_date', $payment_date);
            $stmt->bindValue(':payment_method', $payment_method);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':notes', $notes);

            $success = $stmt->execute();
            if (!$success) {
                $this->lastError = 'The payment record could not be inserted.';
            }

            return $success;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('Payment save failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getPayments()
    {
        try {
            $sql = "SELECT p.*, pa.first_name, pa.last_name
                    FROM payments p
                    LEFT JOIN patients pa ON pa.patient_id = p.patient_id
                    ORDER BY p.payment_date DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function deletePayment($id)
    {
        $sql = "DELETE FROM payments WHERE payment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}
?>