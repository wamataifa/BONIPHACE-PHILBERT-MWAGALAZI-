<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            color: #12324a;
        }

        .navbar {
            background: linear-gradient(90deg, #163b5f 0%, #264d73 100%) !important;
            box-shadow: 0 2px 10px rgba(11, 37, 62, 0.15);
        }

        .card {
            border: 1px solid #cdd8e6;
            box-shadow: 0 4px 14px rgba(22, 59, 95, 0.08);
        }

        .btn-primary {
            background-color: #214a6f;
            border-color: #214a6f;
        }

        .btn-primary:hover {
            background-color: #163b5f;
            border-color: #163b5f;
        }

        .table thead {
            background-color: #214a6f;
            color: white;
        }

        .brand-logo {
            width: 45px;
            height: 45px;
            min-width: 45px;
            min-height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            margin-right: 10px;
            display: block;
        }
    </style>
</head>
<body>
