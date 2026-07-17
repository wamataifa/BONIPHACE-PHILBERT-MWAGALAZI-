<?php
require_once __DIR__ . "/../config/Database.php";

class User
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function register($fullname, $email, $password, $role)
    {
        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(fullname, email, password, role) VALUES(:fullname, :email, :password, :role)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":fullname", $fullname);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":role", $role);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>