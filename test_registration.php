<?php
// Test script to verify registration works
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/Database.php';

echo "<h2>Testing Registration</h2>";

try {
    $database = new Database();
    $conn = $database->connect();
    
    echo "<p>✓ Database connected successfully</p>";
    
    // Test inserting a user
    $test_email = 'test@example.com';
    $test_fullname = 'Test User';
    $test_password = password_hash('test123', PASSWORD_DEFAULT);
    $test_role = 'admin';
    
    // First, delete if exists
    $delete_sql = "DELETE FROM users WHERE email = :email";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute([':email' => $test_email]);
    
    // Insert test user
    $insert_sql = "INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)";
    $insert_stmt = $conn->prepare($insert_sql);
    $result = $insert_stmt->execute([
        ':fullname' => $test_fullname,
        ':email' => $test_email,
        ':password' => $test_password,
        ':role' => $test_role
    ]);
    
    if ($result) {
        echo "<p>✓ Test user inserted successfully</p>";
        
        // Try to retrieve the user
        $select_sql = "SELECT * FROM users WHERE email = :email";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->execute([':email' => $test_email]);
        $user = $select_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>✓ User retrieved successfully</p>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
            
            // Test password verification
            if (password_verify('test123', $user['password'])) {
                echo "<p>✓ Password verification successful</p>";
            } else {
                echo "<p>✗ Password verification failed</p>";
            }
        } else {
            echo "<p>✗ Failed to retrieve user after insertion</p>";
        }
    } else {
        echo "<p>✗ Failed to insert test user</p>";
        echo "<p>Error info: </p>";
        print_r($insert_stmt->errorInfo());
    }
    
    // Show all users
    echo "<h3>All Users in Database:</h3>";
    $all_users_sql = "SELECT * FROM users";
    $all_users_stmt = $conn->prepare($all_users_sql);
    $all_users_stmt->execute();
    $all_users = $all_users_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total users: " . count($all_users) . "</p>";
    if (count($all_users) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        foreach ($all_users as $u) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($u['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($u['fullname']) . "</td>";
            echo "<td>" . htmlspecialchars($u['email']) . "</td>";
            echo "<td>" . htmlspecialchars($u['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
