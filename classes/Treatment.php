<?php

require_once __DIR__ . "/../config/Database.php";

class Treatment
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

    public function addTreatment($patient_id, $doctor_id, $treatment_name, $treatment_date, $description, $status)
    {
        $this->lastError = '';

        try {
            $sql = "INSERT INTO treatments (patient_id, doctor_id, treatment_name, treatment_date, description, status)
                    VALUES (:patient_id, :doctor_id, :treatment_name, :treatment_date, :description, :status)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':patient_id', $patient_id);
            $stmt->bindValue(':doctor_id', $doctor_id);
            $stmt->bindValue(':treatment_name', $treatment_name);
            $stmt->bindValue(':treatment_date', $treatment_date);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':status', $status);

            $success = $stmt->execute();
            if (!$success) {
                $this->lastError = 'The treatment record could not be inserted.';
            }

            return $success;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('Treatment save failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getTreatments()
    {
        try {
            $sql = "SELECT t.*, p.first_name, p.last_name, d.fullname AS doctor_name
                    FROM treatments t
                    LEFT JOIN patients p ON p.patient_id = t.patient_id
                    LEFT JOIN doctors d ON d.doctor_id = t.doctor_id
                    ORDER BY t.treatment_date DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getTreatmentById($id)
    {
        try {
            $sql = "SELECT * FROM treatments WHERE treatment_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function updateTreatment($id, $patient_id, $doctor_id, $treatment_name, $treatment_date, $description, $status)
    {
        $this->lastError = '';

        try {
            $sql = "UPDATE treatments SET patient_id = :patient_id, doctor_id = :doctor_id, treatment_name = :treatment_name, treatment_date = :treatment_date, description = :description, status = :status WHERE treatment_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':patient_id', $patient_id);
            $stmt->bindValue(':doctor_id', $doctor_id);
            $stmt->bindValue(':treatment_name', $treatment_name);
            $stmt->bindValue(':treatment_date', $treatment_date);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deleteTreatment($id)
    {
        $sql = "DELETE FROM treatments WHERE treatment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}
?>