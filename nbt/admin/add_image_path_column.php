<?php
require '../config/db.php';

// Check if image_path column exists, if not add it
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM meet_our_team LIKE 'image_path'");
    $result = $stmt->fetch();
    
    if (!$result) {
        echo "Adding image_path column to meet_our_team table...\n";
        $pdo->exec("ALTER TABLE meet_our_team ADD COLUMN image_path VARCHAR(255) DEFAULT NULL");
        echo "✓ image_path column added successfully!\n";
    } else {
        echo "✓ image_path column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
