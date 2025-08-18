<?php
/**
 * Course Database Migration Script
 * This script updates the courses table to only include the simplified fields
 */

require '../config/db.php';

echo "Starting Course Database Migration...\n\n";

try {
    // Add new columns if they don't exist
    $new_columns = [
        'banner_image' => 'TEXT',
        'title' => 'VARCHAR(255)',
        'description' => 'TEXT',
        'duration' => 'VARCHAR(100)',
        'rating' => 'DECIMAL(3,2) DEFAULT 0',
        'enrolled_students' => 'INT DEFAULT 0',
        'price' => 'DECIMAL(10,2) DEFAULT 0',
        'course_link' => 'TEXT'
    ];
    
    echo "Adding new required columns...\n";
    foreach ($new_columns as $column => $type) {
        try {
            $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS $column $type");
            echo "✓ Added column: $column\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                echo "⚠ Warning for column $column: " . $e->getMessage() . "\n";
            } else {
                echo "✓ Column $column already exists\n";
            }
        }
    }
    
    echo "\nDatabase migration completed successfully!\n";
    echo "\nNEW COURSE STRUCTURE:\n";
    echo "- banner_image: Course banner image path\n";
    echo "- title: Course title\n";
    echo "- description: Course description\n";
    echo "- duration: Course duration\n";
    echo "- rating: Course rating (0-5)\n";
    echo "- enrolled_students: Number of enrolled students\n";
    echo "- price: Course price\n";
    echo "- course_link: External course link\n";
    
    echo "\nOLD COLUMNS (now optional/unused):\n";
    echo "- logo_url, banner_url, instructor, category, difficulty, status, etc.\n";
    echo "\nNote: Old columns are preserved for data safety but not used in the new interface.\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?>
