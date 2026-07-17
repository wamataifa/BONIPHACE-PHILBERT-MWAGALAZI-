<?php
// Direct SQLite database check
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Direct SQLite Database Check</h2>";

try {
    $sqlitePath = __DIR__ . '/clinic_management.sqlite';
    echo "<p>SQLite Path: " . $sqlitePath . "</p>";
    echo "<p>File exists: " . (file_exists($sqlitePath) ? "YES" : "NO") . "</p>";
    echo "<p>File size: " . (file_exists($sqlitePath) ? filesize($sqlitePath) . " bytes" : "N/A") . "</p>";
    
    if (!file_exists($sqlitePath)) {
        echo "<p style='color: red; font-weight: bold;'>SQLite database file does not exist!</p>";
        echo "<p>This is why registration is failing - the database file doesn't exist.</p>";
        exit;
    }
    
    $conn = new PDO("sqlite:" . $sqlitePath);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✓ SQLite connected successfully</p>";
    
    // Check if users table exists
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='users'";
    $stmt = $conn->query($sql);
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p style='color: red; font-weight: bold;'>Users table does NOT exist!</p>";
        echo "<p>Creating users table now...</p>";
        
        $createTableSql = "CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL
        )";
        $conn->exec($createTableSql);
        echo "<p>✓ Users table created</p>";
    } else {
        echo "<p>✓ Users table exists</p>";
    }
    
    // Check users in database
    $userSql = "SELECT * FROM users";
    $userStmt = $conn->query($userSql);
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Users in Database: " . count($users) . "</h3>";
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password Hash</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>" . substr(htmlspecialchars($user['password']), 0, 30) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>NO USERS IN DATABASE!</p>";
        echo "<p>This explains why login says 'User not found'.</p>";
        
        // Try to add a test user
        echo "<h3>Adding test user...</h3>";
        $testEmail = 'bonieinfinix@gmail.com';
        $testName = 'Test User';
        $testPassword = password_hash('test123', PASSWORD_DEFAULT);
        $testRole = 'admin';
        
        try {
            $insertSql = "INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)";
            $insertStmt = $conn->prepare($insertSql);
            $result = $insertStmt->execute([
                ':fullname' => $testName,
                ':email' => $testEmail,
                ':password' => $testPassword,
                ':role' => $testRole
            ]);
            
            if ($result) {
                echo "<p style='color: green; font-weight: bold;'>✓ Test user added successfully!</p>";
                echo "<p>Email: bonieinfinix@gmail.com</p>";
                echo "<p>Password: test123</p>";
                echo "<p>You can now try logging in with these credentials.</p>";
            } else {
                echo "<p style='color: red;'>Failed to add test user</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error adding test user: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</p>";
}
?>
