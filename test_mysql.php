<?php
// Test MySQL connection for XAMPP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>MySQL Connection Test (XAMPP)</h2>";

echo "<h3>Step 1: Check MySQL Extension</h3>";
echo "<p>MySQL PDO Extension: " . (extension_loaded('pdo_mysql') ? "LOADED ✓" : "NOT LOADED ✗") . "</p>";

if (!extension_loaded('pdo_mysql')) {
    echo "<p style='color: red; font-weight: bold;'>MySQL PDO extension is not enabled in PHP.</p>";
    echo "<p>Please enable it in php.ini and restart Apache.</p>";
    exit;
}

echo "<h3>Step 2: Test MySQL Connection</h3>";
try {
    // Try connecting with XAMPP defaults
    $conn = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✓ Connected to MySQL server successfully</p>";
    
    // Create database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS `clinic_management`");
    echo "<p>✓ Database 'clinic_management' created/verified</p>";
    
    // Select the database
    $conn->exec("USE `clinic_management`");
    echo "<p>✓ Using 'clinic_management' database</p>";
    
    // Check if users table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->rowCount() > 0) {
        echo "<p>✓ Users table exists</p>";
        
        // Show existing users
        $userQuery = $conn->query("SELECT * FROM users");
        $users = $userQuery->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Current users in database: " . count($users) . "</p>";
        
        if (count($users) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>Users table does not exist</p>";
        echo "<p>Creating users table...</p>";
        
        $createTable = "CREATE TABLE users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            fullname VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL
        )";
        $conn->exec($createTable);
        echo "<p>✓ Users table created</p>";
        
        // Create other tables
        $otherTables = [
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
        
        foreach ($otherTables as $sql) {
            $conn->exec($sql);
        }
        echo "<p>✓ All tables created successfully</p>";
    }
    
    echo "<h3>Step 3: Test User Insert</h3>";
    $testEmail = 'bonieinfinix@gmail.com';
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);
    
    // Delete if exists
    $conn->prepare("DELETE FROM users WHERE email = ?")->execute([$testEmail]);
    
    // Insert test user
    $insert = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $result = $insert->execute(['Test User', $testEmail, $testPassword, 'admin']);
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✓ Test user inserted successfully!</p>";
        echo "<p>Email: bonieinfinix@gmail.com</p>";
        echo "<p>Password: test123</p>";
        echo "<p>You can now try logging in at login.php</p>";
    } else {
        echo "<p style='color: red;'>Failed to insert test user</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>MySQL Error: " . $e->getMessage() . "</p>";
    echo "<p>Please ensure:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL server is running</li>";
    echo "<li>MySQL credentials are correct (root with empty password)</li>";
    echo "<li>PHP MySQL PDO extension is enabled</li>";
    echo "</ul>";
}
?>
