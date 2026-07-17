<?php
require_once __DIR__ . '/env.php';

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $sqlitePath;
    private $driver = "mysql";
    public $conn;
    private $lastError = "";

    public function __construct()
    {
        // XAMPP MySQL defaults
        $this->host     = getenv('DB_HOST')     ?: 'localhost';
        $this->dbname   = getenv('DB_NAME')     ?: 'clinic_management';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->sqlitePath = __DIR__ . '/../clinic_management.sqlite';
    }

    public function connect()
    {
        $this->lastError = '';

        // Use MySQL for XAMPP
        if (class_exists('PDO') && extension_loaded('pdo_mysql')) {
            $hosts = [$this->host];
            // Also try 127.0.0.1 as fallback if host is localhost
            if ($this->host === 'localhost') {
                $hosts[] = '127.0.0.1';
            }

            foreach ($hosts as $host) {
                try {
                    $this->driver = 'mysql';
                    $this->conn = new PDO("mysql:host=" . $host . ";charset=utf8mb4", $this->username, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->dbname . "`");

                    $this->conn = new PDO(
                        "mysql:host=" . $host . ";dbname=" . $this->dbname . ";charset=utf8mb4",
                        $this->username,
                        $this->password
                    );
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $this->createTables();
                    return $this->conn;
                } catch (PDOException $e) {
                    $this->lastError = $e->getMessage();
                }
            }
        }

        throw new PDOException("Unable to connect to MySQL database. Please ensure XAMPP MySQL is running and PDO MySQL is enabled. " . $this->lastError);
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    private function createTables()
    {
        $queries = [];

        if ($this->driver === 'sqlite') {
            $queries = [
                "CREATE TABLE IF NOT EXISTS users (
                    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    fullname VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(50) NOT NULL
                )",
                "CREATE TABLE IF NOT EXISTS patients (
                    patient_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    first_name VARCHAR(100) NOT NULL,
                    last_name VARCHAR(100) NOT NULL,
                    gender VARCHAR(20) NOT NULL,
                    date_of_birth DATE NULL,
                    phone VARCHAR(20) NULL,
                    email VARCHAR(100) NULL,
                    address TEXT NULL
                )",
                "CREATE TABLE IF NOT EXISTS doctors (
                    doctor_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    fullname VARCHAR(100) NOT NULL,
                    specialization VARCHAR(100) NOT NULL,
                    phone VARCHAR(20) NULL,
                    email VARCHAR(100) NULL
                )",
                "CREATE TABLE IF NOT EXISTS appointments (
                    appointment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    patient_id INT NOT NULL,
                    doctor_id INT NOT NULL,
                    appointment_date DATE NOT NULL,
                    appointment_time TIME NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    notes TEXT NULL
                )",
                "CREATE TABLE IF NOT EXISTS treatments (
                    treatment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    patient_id INT NOT NULL,
                    doctor_id INT NOT NULL,
                    treatment_name VARCHAR(100) NOT NULL,
                    treatment_date DATE NOT NULL,
                    description TEXT NULL,
                    status VARCHAR(50) NOT NULL
                )",
                "CREATE TABLE IF NOT EXISTS payments (
                    payment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    patient_id INT NOT NULL,
                    amount DECIMAL(10,2) NOT NULL,
                    payment_date DATE NOT NULL,
                    payment_method VARCHAR(50) NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    notes TEXT NULL
                )"
            ];
        } else {
            $queries = [
                "CREATE TABLE IF NOT EXISTS users (
                    user_id INT AUTO_INCREMENT PRIMARY KEY,
                    fullname VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(50) NOT NULL
                )",
                "CREATE TABLE IF NOT EXISTS patients (
                    patient_id INT AUTO_INCREMENT PRIMARY KEY,
                    first_name VARCHAR(100) NOT NULL,
                    last_name VARCHAR(100) NOT NULL,
                    gender VARCHAR(20) NOT NULL,
                    date_of_birth DATE NULL,
                    phone VARCHAR(20) NULL,
                    email VARCHAR(100) NULL,
                    address TEXT NULL
                )",
                "CREATE TABLE IF NOT EXISTS doctors (
                    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
                    fullname VARCHAR(100) NOT NULL,
                    specialization VARCHAR(100) NOT NULL,
                    phone VARCHAR(20) NULL,
                    email VARCHAR(100) NULL
                )",
                "CREATE TABLE IF NOT EXISTS appointments (
                    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
                    patient_id INT NOT NULL,
                    doctor_id INT NOT NULL,
                    appointment_date DATE NOT NULL,
                    appointment_time TIME NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    notes TEXT NULL
                )",
                "CREATE TABLE IF NOT EXISTS treatments (
                    treatment_id INT AUTO_INCREMENT PRIMARY KEY,
                    patient_id INT NOT NULL,
                    doctor_id INT NOT NULL,
                    treatment_name VARCHAR(100) NOT NULL,
                    treatment_date DATE NOT NULL,
                    description TEXT NULL,
                    status VARCHAR(50) NOT NULL
                )",
                "CREATE TABLE IF NOT EXISTS payments (
                    payment_id INT AUTO_INCREMENT PRIMARY KEY,
                    patient_id INT NOT NULL,
                    amount DECIMAL(10,2) NOT NULL,
                    payment_date DATE NOT NULL,
                    payment_method VARCHAR(50) NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    notes TEXT NULL
                )"
            ];
        }

        foreach ($queries as $sql) {
            $this->conn->exec($sql);
        }

        $this->ensureRequiredColumns();
        $this->repairLegacyTreatmentSchema();
        $this->seedDefaultRecords();
    }

    private function repairLegacyTreatmentSchema()
    {
        if ($this->driver !== 'mysql') {
            return;
        }

        try {
            $this->ensureColumnExists('treatments', 'appointment_id', 'INT NULL');
            $this->conn->exec('ALTER TABLE treatments MODIFY appointment_id INT NULL');

            $stmt = $this->conn->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'treatments' AND REFERENCED_TABLE_NAME = 'appointments'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->conn->exec('ALTER TABLE treatments DROP FOREIGN KEY ' . $row['CONSTRAINT_NAME']);
            }
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
        }
    }

    private function seedDefaultRecords()
    {
        // No default sample records — deleted data should stay deleted.
    }

    private function ensureRequiredColumns()
    {
        if ($this->driver === 'sqlite') {
            $this->ensureColumnExists('treatments', 'patient_id', 'INTEGER NOT NULL');
            $this->ensureColumnExists('treatments', 'doctor_id', 'INTEGER NOT NULL');
            $this->ensureColumnExists('treatments', 'treatment_name', 'VARCHAR(100) NOT NULL');
            $this->ensureColumnExists('treatments', 'treatment_date', 'DATE NOT NULL');
            $this->ensureColumnExists('treatments', 'description', 'TEXT NULL');
            $this->ensureColumnExists('treatments', 'status', 'VARCHAR(50) NOT NULL');
            $this->ensureColumnExists('payments', 'patient_id', 'INTEGER NOT NULL');
            $this->ensureColumnExists('payments', 'amount', 'DECIMAL(10,2) NOT NULL');
            $this->ensureColumnExists('payments', 'payment_date', 'DATE NOT NULL');
            $this->ensureColumnExists('payments', 'payment_method', 'VARCHAR(50) NOT NULL');
            $this->ensureColumnExists('payments', 'status', 'VARCHAR(50) NOT NULL');
            $this->ensureColumnExists('payments', 'notes', 'TEXT NULL');
        } else {
            $this->ensureColumnExists('treatments', 'patient_id', 'INT NOT NULL');
            $this->ensureColumnExists('treatments', 'doctor_id', 'INT NOT NULL');
            $this->ensureColumnExists('treatments', 'treatment_name', 'VARCHAR(100) NOT NULL');
            $this->ensureColumnExists('treatments', 'treatment_date', 'DATE NOT NULL');
            $this->ensureColumnExists('treatments', 'description', 'TEXT NULL');
            $this->ensureColumnExists('treatments', 'status', 'VARCHAR(50) NOT NULL');
            $this->ensureColumnExists('payments', 'patient_id', 'INT NOT NULL');
            $this->ensureColumnExists('payments', 'amount', 'DECIMAL(10,2) NOT NULL');
            $this->ensureColumnExists('payments', 'payment_date', 'DATE NOT NULL');
            $this->ensureColumnExists('payments', 'payment_method', 'VARCHAR(50) NOT NULL');
            $this->ensureColumnExists('payments', 'status', 'VARCHAR(50) NOT NULL');
            $this->ensureColumnExists('payments', 'notes', 'TEXT NULL');
        }
    }

    private function ensureColumnExists($table, $column, $definition)
    {
        try {
            if ($this->driver === 'sqlite') {
                $stmt = $this->conn->query("PRAGMA table_info('" . $table . "')");
                $columns = [];
                if ($stmt) {
                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $columns[] = $row['name'];
                    }
                }
                if (!in_array($column, $columns, true)) {
                    $this->conn->exec("ALTER TABLE " . $table . " ADD COLUMN " . $column . " " . $definition);
                }
            } else {
                $stmt = $this->conn->query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
                if ($stmt->fetch() === false) {
                    $this->conn->exec("ALTER TABLE `" . $table . "` ADD COLUMN `" . $column . "` " . $definition);
                }
            }
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
        }
    }
}
?>