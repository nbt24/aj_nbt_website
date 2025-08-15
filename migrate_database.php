<?php
/**
 * NBT Database Migration Script
 * Run this script in your browser: http://localhost/nbt/aj_nbt_website/migrate_database.php
 * 
 * WARNING: This will make structural changes to your database!
 * Always backup your database before running migrations.
 */

require 'public_html (3)/config/db.php';

echo "<h1>NBT Database Migration Tool</h1>";
echo "<p>Starting database migration...</p>";

try {
    // 1. Add indexes for performance
    echo "<h3>Adding Performance Indexes...</h3>";
    
    $indexes = [
        "ALTER TABLE courses ADD INDEX IF NOT EXISTS idx_rating (rating)",
        "ALTER TABLE courses ADD INDEX IF NOT EXISTS idx_educator (educator)",
        "ALTER TABLE client_testimonials ADD INDEX IF NOT EXISTS idx_active (is_active)",
        "ALTER TABLE client_testimonials ADD INDEX IF NOT EXISTS idx_rating (rating)",
        "ALTER TABLE course_testimonials ADD INDEX IF NOT EXISTS idx_active (is_active)",
        "ALTER TABLE course_testimonials ADD INDEX IF NOT EXISTS idx_rating (rating)",
        "ALTER TABLE overview_images ADD INDEX IF NOT EXISTS idx_sequence (image_sequence)",
        "ALTER TABLE meet_our_team ADD INDEX IF NOT EXISTS idx_sequence (image_sequence)",
        "ALTER TABLE founder_card ADD INDEX IF NOT EXISTS idx_sequence (image_sequence)"
    ];
    
    foreach ($indexes as $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Added index successfully<br>";
        } catch (PDOException $e) {
            echo "⚠️ Index might already exist: " . $e->getMessage() . "<br>";
        }
    }
    
    // 2. Add missing columns
    echo "<h3>Adding Missing Columns...</h3>";
    
    $columns = [
        "ALTER TABLE course_testimonials ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT 1",
        "ALTER TABLE client_testimonials ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT 1",
        "ALTER TABLE client ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];
    
    foreach ($columns as $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Added column successfully<br>";
        } catch (PDOException $e) {
            echo "⚠️ Column might already exist: " . $e->getMessage() . "<br>";
        }
    }
    
    // 3. Add data constraints
    echo "<h3>Adding Data Constraints...</h3>";
    
    $constraints = [
        "ALTER TABLE course_testimonials ADD CONSTRAINT IF NOT EXISTS chk_course_rating CHECK (rating BETWEEN 1 AND 5)",
        "ALTER TABLE client_testimonials ADD CONSTRAINT IF NOT EXISTS chk_client_rating CHECK (rating BETWEEN 1 AND 5)",
        "ALTER TABLE testimonials ADD CONSTRAINT IF NOT EXISTS chk_testimonial_rating CHECK (rating BETWEEN 1 AND 5)"
    ];
    
    foreach ($constraints as $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Added constraint successfully<br>";
        } catch (PDOException $e) {
            echo "⚠️ Constraint might already exist: " . $e->getMessage() . "<br>";
        }
    }
    
    // 4. Check table sizes and LONGBLOB usage
    echo "<h3>Database Analysis...</h3>";
    
    $analysis_queries = [
        "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema = DATABASE()",
        "SELECT COUNT(*) as total_courses FROM courses",
        "SELECT COUNT(*) as total_team_members FROM meet_our_team",
        "SELECT COUNT(*) as total_services FROM our_services",
        "SELECT COUNT(*) as total_client_testimonials FROM client_testimonials",
        "SELECT COUNT(*) as total_course_testimonials FROM course_testimonials",
        "SELECT COUNT(*) as total_contacts FROM contact_us"
    ];
    
    foreach ($analysis_queries as $query) {
        try {
            $result = $pdo->query($query);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            echo "<pre>" . print_r($data, true) . "</pre>";
        } catch (PDOException $e) {
            echo "Query error: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<h3>✅ Migration Completed!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>Review the analysis above</li>";
    echo "<li>Consider moving LONGBLOB data to files before Hostinger migration</li>";
    echo "<li>Test the website functionality</li>";
    echo "<li>Create a database backup</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h3>❌ Migration Failed!</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #7c3aed; }
h3 { color: #9333ea; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
pre { background: #f3f4f6; padding: 10px; border-radius: 5px; }
</style>
