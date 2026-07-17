<?php

require_once __DIR__ . "/../config/Database.php";

class Patient
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

    public function addPatient($first_name, $last_name, $gender, $dob, $phone, $email, $address)
    {
        try {
            $sql = "INSERT INTO patients (first_name, last_name, gender, date_of_birth, phone, email, address) VALUES (:first_name, :last_name, :gender, :dob, :phone, :email, :address)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":gender", $gender);
            $stmt->bindParam(":dob", $dob);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":address", $address);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getPatients()
    {
        try {
            $sql = "SELECT * FROM patients ORDER BY patient_id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getPatientById($id)
    {
        try {
            $sql = "SELECT * FROM patients WHERE patient_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePatient($id, $first_name, $last_name, $gender, $dob, $phone, $email, $address)
    {
        try {
            $sql = "UPDATE patients SET first_name = :first_name, last_name = :last_name, gender = :gender, date_of_birth = :dob, phone = :phone, email = :email, address = :address WHERE patient_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":gender", $gender);
            $stmt->bindParam(":dob", $dob);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":address", $address);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deletePatient($id)
    {
        try {
            $this->conn->beginTransaction();

            $relatedTables = ['appointments', 'treatments', 'payments'];
            foreach ($relatedTables as $table) {
                $sql = "DELETE FROM {$table} WHERE patient_id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $sql = "DELETE FROM patients WHERE patient_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $deleted = $stmt->rowCount() > 0;
            $this->conn->commit();

            return $deleted;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
?>