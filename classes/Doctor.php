<?php

require_once __DIR__ . "/../config/Database.php";

class Doctor
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

    public function addDoctor($fullname, $specialization, $phone, $email)
    {
        try {
            $sql = "INSERT INTO doctors(fullname, specialization, phone, email) VALUES(:fullname, :specialization, :phone, :email)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":fullname", $fullname);
            $stmt->bindParam(":specialization", $specialization);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":email", $email);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getDoctors()
    {
        try {
            $sql = "SELECT * FROM doctors ORDER BY doctor_id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getDoctorById($id)
    {
        try {
            $sql = "SELECT * FROM doctors WHERE doctor_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateDoctor($id, $fullname, $specialization, $phone, $email)
    {
        try {
            $sql = "UPDATE doctors SET fullname = :fullname, specialization = :specialization, phone = :phone, email = :email WHERE doctor_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":fullname", $fullname);
            $stmt->bindParam(":specialization", $specialization);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deleteDoctor($id)
    {
        try {
            $this->conn->beginTransaction();

            $relatedTables = ['appointments', 'treatments'];
            foreach ($relatedTables as $table) {
                $sql = "DELETE FROM {$table} WHERE doctor_id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $sql = "DELETE FROM doctors WHERE doctor_id = :id";
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