<?php
/**
 * Database Connection Test
 * 
 * Visit this page on your AWS server to verify the database is working.
 * URL: http://your-ec2-ip/clinic_management/test.php
 * 
 * DELETE THIS FILE after confirming everything works (security risk).
 */
require_once "config/Database.php";

echo "<h2>Clinic Management - Server Diagnostic</h2>";
echo "<hr>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Check required extensions
echo "<p><strong>PDO MySQL extension:</strong> " . (extension_loaded('pdo_mysql') ? '✅ Loaded' : '❌ NOT loaded — run: sudo apt install php-mysql') . "</p>";
echo "<p><strong>PDO SQLite extension:</strong> " . (extension_loaded('pdo_sqlite') ? '✅ Loaded' : '⚠️ Not loaded (optional)') . "</p>";

// Check environment variables
echo "<h3>Database Configuration</h3>";
echo "<p><strong>DB_HOST:</strong> " . (getenv('DB_HOST') ?: '<em>not set (using default: localhost)</em>') . "</p>";
echo "<p><strong>DB_NAME:</strong> " . (getenv('DB_NAME') ?: '<em>not set (using default: clinic_management)</em>') . "</p>";
echo "<p><strong>DB_USERNAME:</strong> " . (getenv('DB_USERNAME') ?: '<em>not set (using default: root)</em>') . "</p>";
echo "<p><strong>DB_PASSWORD:</strong> " . (getenv('DB_PASSWORD') !== false ? '✅ Set (hidden)' : '<em>not set (using default: empty)</em>') . "</p>";

// Try connecting
echo "<h3>Connection Test</h3>";
try {
    $database = new Database();
    $conn = $database->connect();
    echo "<p style='color:green; font-weight:bold;'>✅ Database Connected Successfully!</p>";
    
    // Check tables
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Tables found:</strong> " . implode(', ', $tables) . "</p>";
    
    // Check user count
    $userCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "<p><strong>Registered users:</strong> " . $userCount . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red; font-weight:bold;'>❌ Connection Failed!</p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>How to fix:</h3>";
    echo "<ol>";
    echo "<li>Make sure MySQL is installed: <code>sudo apt install mysql-server -y</code></li>";
    echo "<li>Start MySQL: <code>sudo systemctl start mysql</code></li>";
    echo "<li>Set a MySQL password: <code>sudo mysql -e \"ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourPassword123!';\"</code></li>";
    echo "<li>Install PHP MySQL extension: <code>sudo apt install php-mysql -y</code></li>";
    echo "<li>Set environment variables in Apache config or <code>/etc/environment</code></li>";
    echo "<li>Restart Apache: <code>sudo systemctl restart apache2</code></li>";
    echo "</ol>";
}
?>