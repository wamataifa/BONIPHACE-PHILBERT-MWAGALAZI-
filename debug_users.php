<?php
// Debug script to check users in database
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/Database.php';

try {
    $database = new Database();
    $conn = $database->connect();
    
    echo "<h2>Database Connection: SUCCESS</h2>";
    
    // Check if users table exists and has data
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Users in database: " . count($users) . "</h3>";
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Email</th><th>Role</th><th>Password Hash</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>" . substr(htmlspecialchars($user['password']), 0, 20) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>NO USERS FOUND IN DATABASE!</p>";
        echo "<p>This means registration is not working properly.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}
?>
