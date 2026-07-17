<?php
// Comprehensive database debug script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Connection Debug</h2>";

// Test SQLite
echo "<h3>Testing SQLite Connection</h3>";
try {
    $sqlitePath = __DIR__ . '/clinic_management.sqlite';
    echo "<p>SQLite Path: " . $sqlitePath . "</p>";
    echo "<p>File exists: " . (file_exists($sqlitePath) ? "YES" : "NO") . "</p>";
    
    $sqliteConn = new PDO("sqlite:" . $sqlitePath);
    $sqliteConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✓ SQLite connected successfully</p>";
    
    // Check users table
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='users'";
    $stmt = $sqliteConn->query($sql);
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p>✓ Users table exists in SQLite</p>";
        
        $userSql = "SELECT * FROM users";
        $userStmt = $sqliteConn->query($userSql);
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Users in SQLite: " . count($users) . "</p>";
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
        echo "<p style='color: red;'>✗ Users table does NOT exist in SQLite</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>SQLite Error: " . $e->getMessage() . "</p>";
}

// Test MySQL
echo "<h3>Testing MySQL Connection</h3>";
echo "<p>MySQL Extension: " . (extension_loaded('pdo_mysql') ? "LOADED" : "NOT LOADED") . "</p>";

if (extension_loaded('pdo_mysql')) {
    try {
        $mysqlConn = new PDO("mysql:host=localhost;charset=utf8mb4", "bonietech_user", "Boni@2026/");
        $mysqlConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✓ MySQL connected successfully</p>";
        
        // Try to use the database
        $mysqlConn->exec("USE `bonie_db`");
        echo "<p>✓ Database 'bonie_db' accessible</p>";
        
        // Check users table
        $userSql = "SELECT * FROM users";
        $userStmt = $mysqlConn->query($userSql);
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Users in MySQL: " . count($users) . "</p>";
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
        } else {
            echo "<p style='color: orange;'>No users found in MySQL database</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>MySQL Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>MySQL PDO extension not available</p>";
}

echo "<h3>Recommendation</h3>";
echo "<p>Based on the results above, we should force the system to use one database type consistently.</p>";
?>
