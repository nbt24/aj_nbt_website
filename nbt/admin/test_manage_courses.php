<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing manage_courses.php step by step</h1>";

// Step 1: Test basic PHP
echo "<p>✓ PHP is working</p>";

// Step 2: Test session
try {
    session_start();
    echo "<p>✓ Session started</p>";
} catch (Exception $e) {
    echo "<p>✗ Session error: " . $e->getMessage() . "</p>";
}

// Step 3: Test database connection
try {
    require '../config/db.php';
    echo "<p>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p>✗ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Step 4: Test courses table query
try {
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC LIMIT 1");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>✓ Courses table query successful</p>";
    echo "<p>Number of courses found: " . count($courses) . "</p>";
} catch (Exception $e) {
    echo "<p>✗ Courses query error: " . $e->getMessage() . "</p>";
}

// Step 5: Check table structure
try {
    $stmt = $pdo->query("DESCRIBE courses");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>✓ Courses table structure:</p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p>✗ Table structure error: " . $e->getMessage() . "</p>";
}

// Step 6: Check for admin session
if (!isset($_SESSION['admin_id'])) {
    echo "<p>⚠ No admin session found (would redirect to index.php)</p>";
} else {
    echo "<p>✓ Admin session found: " . $_SESSION['admin_id'] . "</p>";
}

echo "<hr>";
echo "<p><a href='manage_courses.php'>Try Original manage_courses.php</a></p>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
?>
