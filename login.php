<?php
session_start();
require_once "config/Database.php";
$message = "";
$messageType = "danger";
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = "Fill the fields";
        $messageType = "fill-fields";
    } else {
    try {
        $database = new Database();
        $conn = $database->connect();

        // Debug: Check if connection works
        if (!$conn) {
            $message = "Database connection failed!";
        } else {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            // Debug: Check how many users found
            $count = $stmt->rowCount();
            if ($count > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Incorrect password!";
                }
            } else {
                $message = "User not found! (Email: " . htmlspecialchars($email) . ")";
            }
        }
    } catch (Exception $e) {
        $message = "Database connection error: " . $e->getMessage();
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
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
                        <h5 class="text-center text-muted mb-4">User Login</h5>
                        <div id="loginAlert">
                        <?php if ($message) { ?>
                            <div class="alert alert-<?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        </div>
                        <form method="POST" id="loginForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>
                        <p class="text-center mt-3 mb-0">
                            <a href="register.php">Create an account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const email = this.email.value.trim();
            const password = this.password.value.trim();
            const alertContainer = document.getElementById('loginAlert');

            if (!email || !password) {
                e.preventDefault();
                alertContainer.innerHTML = '<div class="alert alert-fill-fields">Fill the fields</div>';
            } else {
                alertContainer.innerHTML = '';
            }
        });
    </script>
</body>
</html>