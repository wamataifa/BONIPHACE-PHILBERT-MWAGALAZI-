<?php
// 1. Force error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Import the Database class
require_once 'config/Database.php';

$message = "";
$message_type = "";

// 3. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = trim($_POST['role']);

    if (empty($fullname) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required!";
        $message_type = "error";
    } else {
        try {
            // Connect to database
            $database = new Database();
            $conn = $database->connect();

            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $sql = "INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':fullname' => $fullname,
                ':email' => $email,
                ':password' => $hashed_password,
                ':role' => $role
            ]);

            if ($result) {
                $message = "User registered successfully! You can now log in.";
                $message_type = "success";
            } else {
                $message = "Registration failed. Please try again.";
                $message_type = "error";
            }

        } catch (PDOException $e) {
            // Catch duplicate entry errors (like if email already exists)
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false || $e->getCode() == 23000) {
                $message = "Email is already registered!";
            } else {
                $message = "Database Error: " . $e->getMessage();
            }
            $message_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e8f1fb 0%, #dce9f7 50%, #cfdff5 100%);
            min-height: 100vh;
        }

        .card {
            border: 1px solid #cdd8e6;
            box-shadow: 0 8px 24px rgba(22, 59, 95, 0.12);
        }

        .logo-circle {
            width: 45px;
            height: 45px;
            min-width: 45px;
            min-height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #214a6f;
            box-shadow: 0 2px 8px rgba(22, 59, 95, 0.15);
            display: block;
        }

        .alert-fill-fields {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column align-items-center mb-3 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <img src="assets/images/logo.png" alt="Clinic Management System logo" class="logo-circle me-3">
                                <h2 class="mb-0 fw-bold">Clinic Management</h2>
                            </div>
                            <h2 class="mt-1 mb-0 fw-bold">System</h2>
                        </div>
                        <h5 class="text-center text-muted mb-4">Create Account</h5>
                        <div id="registerAlert">
                        <?php if (!empty($message)) { ?>
                            <div class="alert alert-<?= htmlspecialchars($message_type) ?>"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        </div>
                        <form method="POST" id="registerForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Doctor</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <p class="text-center mt-3 mb-0">
                            Already have an account? <a href="login.php">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const fullname = this.fullname.value.trim();
            const email = this.email.value.trim();
            const password = this.password.value.trim();
            const role = this.role.value;
            const alertContainer = document.getElementById('registerAlert');

            if (!fullname || !email || !password || !role) {
                e.preventDefault();
                alertContainer.innerHTML = '<div class="alert alert-fill-fields">Fill all fields</div>';
            } else {
                alertContainer.innerHTML = '';
            }
        });
    </script>
</body>
</html>