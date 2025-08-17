<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Minimal Manage Courses Test</h1>";

session_start();
require '../config/db.php';

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    echo "<p>No admin session found. <a href='index.php'>Login here</a></p>";
    exit;
}

echo "<p>Admin session found: " . $_SESSION['admin_id'] . "</p>";

// Try to fetch courses
try {
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Courses Found: " . count($courses) . "</h2>";
    
    if (count($courses) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Title</th><th>Educator</th><th>Type</th></tr>";
        foreach ($courses as $course) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($course['id']) . "</td>";
            echo "<td>" . htmlspecialchars($course['title']) . "</td>";
            echo "<td>" . htmlspecialchars($course['educator']) . "</td>";
            echo "<td>" . htmlspecialchars($course['type']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No courses found in database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching courses: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>Back to Dashboard</a></p>";
echo "<p><a href='manage_courses.php'>Try Full Manage Courses</a></p>";
?>
