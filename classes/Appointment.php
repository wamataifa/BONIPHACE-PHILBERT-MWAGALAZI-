<?php

require_once __DIR__ . "/../config/Database.php";

class Appointment
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

    public function addAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $status, $notes)
    {
        $this->lastError = '';

        $patient_id = trim((string) $patient_id);
        $doctor_id = trim((string) $doctor_id);
        $appointment_date = trim((string) $appointment_date);
        $appointment_time = trim((string) $appointment_time);
        $status = trim((string) $status);
        $notes = trim((string) $notes);

        if ($patient_id === '' || $doctor_id === '' || $appointment_date === '' || $appointment_time === '') {
            $this->lastError = 'Please select a patient, doctor, appointment date, and time.';
            return false;
        }

        try {
            $patientExists = $this->conn->prepare('SELECT 1 FROM patients WHERE patient_id = :id LIMIT 1');
            $patientExists->bindValue(':id', $patient_id);
            $patientExists->execute();
            if ($patientExists->fetchColumn() === false) {
                $this->lastError = 'The selected patient does not exist.';
                return false;
            }

            $doctorExists = $this->conn->prepare('SELECT 1 FROM doctors WHERE doctor_id = :id LIMIT 1');
            $doctorExists->bindValue(':id', $doctor_id);
            $doctorExists->execute();
            if ($doctorExists->fetchColumn() === false) {
                $this->lastError = 'The selected doctor does not exist.';
                return false;
            }

            $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, notes)
                    VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :status, :notes)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':patient_id', $patient_id);
            $stmt->bindValue(':doctor_id', $doctor_id);
            $stmt->bindValue(':appointment_date', $appointment_date);
            $stmt->bindValue(':appointment_time', $appointment_time);
            $stmt->bindValue(':status', $status !== '' ? $status : 'Scheduled');
            $stmt->bindValue(':notes', $notes);

            $success = $stmt->execute();
            if (!$success) {
                $this->lastError = 'The appointment could not be inserted.';
            }

            return $success;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('Appointment save failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getAppointments()
    {
        try {
            $sql = "SELECT a.*, p.first_name, p.last_name, d.fullname AS doctor_name
                    FROM appointments a
                    LEFT JOIN patients p ON p.patient_id = a.patient_id
                    LEFT JOIN doctors d ON d.doctor_id = a.doctor_id
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getAppointmentById($id)
    {
        $sql = "SELECT * FROM appointments WHERE appointment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAppointment($id, $patient_id, $doctor_id, $appointment_date, $appointment_time, $status, $notes)
    {
        $sql = "UPDATE appointments SET patient_id = :patient_id, doctor_id = :doctor_id, appointment_date = :appointment_date, appointment_time = :appointment_time, status = :status, notes = :notes WHERE appointment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patient_id);
        $stmt->bindValue(':doctor_id', $doctor_id);
        $stmt->bindValue(':appointment_date', $appointment_date);
        $stmt->bindValue(':appointment_time', $appointment_time);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':notes', $notes);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function deleteAppointment($id)
    {
        $sql = "DELETE FROM appointments WHERE appointment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}
?>