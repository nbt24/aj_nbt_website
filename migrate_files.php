<?php
/**
 * File Migration Script - Convert LONGBLOB to File System
 * WARNING: This will modify your database structure!
 * Always backup before running!
 * 
 * Access via: http://localhost/nbt/aj_nbt_website/migrate_files.php
 */

require 'public_html (3)/config/db.php';

// Create uploads directory structure
$uploadDirs = [
    'public_html (3)/uploads',
    'public_html (3)/uploads/courses',
    'public_html (3)/uploads/team',
    'public_html (3)/uploads/founders',
    'public_html (3)/uploads/services',
    'public_html (3)/uploads/overview',
    'public_html (3)/uploads/testimonials',
    'public_html (3)/uploads/testimonials/images',
    'public_html (3)/uploads/testimonials/videos',
    'public_html (3)/uploads/clients'
];

echo "<h1>📁 File Migration Tool</h1>";
echo "<p>Converting LONGBLOB data to file system...</p>";

// Create directories
foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created directory: {$dir}<br>";
    } else {
        echo "📁 Directory exists: {$dir}<br>";
    }
}

// Migration functions
function migrateTable($pdo, $table, $idField, $blobField, $uploadDir, $nameField = null) {
    echo "<h3>Migrating {$table}.{$blobField}...</h3>";
    
    try {
        $query = "SELECT {$idField}, {$blobField}" . ($nameField ? ", {$nameField}" : "") . " FROM {$table} WHERE {$blobField} IS NOT NULL";
        $stmt = $pdo->query($query);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $migrated = 0;
        foreach ($records as $record) {
            $id = $record[$idField];
            $blobData = $record[$blobField];
            
            if (empty($blobData)) continue;
            
            // Generate filename
            $extension = 'jpg'; // Default extension
            if ($nameField && isset($record[$nameField])) {
                $originalName = $record[$nameField];
                $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'jpg';
            }
            
            $filename = $table . '_' . $id . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;
            $relativePath = str_replace('public_html (3)/', '', $filepath);
            
            // Save file
            if (file_put_contents($filepath, $blobData)) {
                // Update database with file path
                $updateQuery = "UPDATE {$table} SET {$blobField}_path = ? WHERE {$idField} = ?";
                $updateStmt = $pdo->prepare($updateQuery);
                
                try {
                    // First add the column if it doesn't exist
                    $pdo->exec("ALTER TABLE {$table} ADD COLUMN IF NOT EXISTS {$blobField}_path VARCHAR(255)");
                } catch (PDOException $e) {
                    // Column might already exist
                }
                
                $updateStmt->execute([$relativePath, $id]);
                $migrated++;
                echo "✅ Migrated {$table} ID {$id} -> {$relativePath}<br>";
            } else {
                echo "❌ Failed to save file for {$table} ID {$id}<br>";
            }
        }
        
        echo "<p><strong>Migrated {$migrated} files from {$table}.{$blobField}</strong></p>";
        return $migrated;
        
    } catch (PDOException $e) {
        echo "❌ Error migrating {$table}: " . $e->getMessage() . "<br>";
        return 0;
    }
}

try {
    // Migrate each table
    $totalMigrated = 0;
    
    // Courses
    $totalMigrated += migrateTable($pdo, 'courses', 'id', 'image', 'public_html (3)/uploads/courses');
    
    // Team members
    $totalMigrated += migrateTable($pdo, 'meet_our_team', 'id', 'image_data', 'public_html (3)/uploads/team', 'image_name');
    
    // Founders
    $totalMigrated += migrateTable($pdo, 'founder_card', 'id', 'image_data', 'public_html (3)/uploads/founders', 'image_name');
    
    // Services
    $totalMigrated += migrateTable($pdo, 'our_services', 'id', 'image_data', 'public_html (3)/uploads/services', 'image_name');
    
    // Overview images
    $totalMigrated += migrateTable($pdo, 'overview_images', 'id', 'image_data', 'public_html (3)/uploads/overview', 'image_name');
    
    // Course testimonials - images
    $totalMigrated += migrateTable($pdo, 'course_testimonials', 'id', 'image', 'public_html (3)/uploads/testimonials/images');
    
    // Course testimonials - videos
    $totalMigrated += migrateTable($pdo, 'course_testimonials', 'id', 'video', 'public_html (3)/uploads/testimonials/videos');
    
    // Client testimonials
    $totalMigrated += migrateTable($pdo, 'client_testimonials', 'id', 'company_logo', 'public_html (3)/uploads/clients');
    
    echo "<h2>🎉 Migration Complete!</h2>";
    echo "<p>Total files migrated: <strong>{$totalMigrated}</strong></p>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
    echo "<h3>⚠️ Next Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Test the website</strong> - Check if images display correctly</li>";
    echo "<li><strong>Update PHP code</strong> - Modify display logic to use file paths instead of base64</li>";
    echo "<li><strong>Backup database</strong> - Create a backup before removing LONGBLOB columns</li>";
    echo "<li><strong>Remove LONGBLOB columns</strong> - After confirming files work correctly</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h3>🗃️ Database Cleanup (Run after testing):</h3>";
    echo "<pre>";
    echo "-- Remove LONGBLOB columns after confirming migration success\n";
    echo "-- ALTER TABLE courses DROP COLUMN image;\n";
    echo "-- ALTER TABLE meet_our_team DROP COLUMN image_data;\n";
    echo "-- ALTER TABLE founder_card DROP COLUMN image_data;\n";
    echo "-- ALTER TABLE our_services DROP COLUMN image_data;\n";
    echo "-- ALTER TABLE overview_images DROP COLUMN image_data;\n";
    echo "-- ALTER TABLE course_testimonials DROP COLUMN image;\n";
    echo "-- ALTER TABLE course_testimonials DROP COLUMN video;\n";
    echo "-- ALTER TABLE client_testimonials DROP COLUMN company_logo;\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>❌ Migration Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #7c3aed; }
h2 { color: #9333ea; }
h3 { color: #059669; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
pre { background: #f3f4f6; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>
