<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";

// Test database connection
try {
    require '../config/db.php';
    echo "Database connection successful!<br>";
    
    // Test if courses table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'courses'");
    $result = $stmt->fetch();
    if ($result) {
        echo "Courses table exists!<br>";
        
        // Test if we can query the courses table
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
        $count = $stmt->fetch();
        echo "Number of courses: " . $count['count'] . "<br>";
    } else {
        echo "Courses table does not exist!<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Test session
session_start();
echo "Session started successfully!<br>";

if (!isset($_SESSION['admin_id'])) {
    echo "No admin session found - this would redirect to index.php<br>";
} else {
    echo "Admin session found: " . $_SESSION['admin_id'] . "<br>";
}
?>
