<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test if XAMPP/Apache is properly configured
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

// Test database connection
try {
    require '../config/db.php';
    echo "✓ Database connected<br>";
    
    // Check if courses table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'courses'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Courses table exists<br>";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE courses");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Table structure:<br>";
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
        }
        
        // Try to select from courses
        $stmt = $pdo->query("SELECT * FROM courses LIMIT 1");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✓ Can query courses table. Found " . count($result) . " record(s)<br>";
        
    } else {
        echo "✗ Courses table does not exist<br>";
        echo "Available tables:<br>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "- " . $table . "<br>";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test session functionality
session_start();
echo "✓ Session started<br>";

// Test short tags
echo "Testing short tag: ";
?>
<?= "Short tags work!" ?><br>
<?php

// Test file permissions
$file = __FILE__;
echo "Current file: " . $file . "<br>";
echo "File permissions: " . substr(sprintf('%o', fileperms($file)), -4) . "<br>";

?>
<hr>
<a href="dashboard.php">Go to Dashboard</a> | 
<a href="manage_courses.php">Try Manage Courses</a>
