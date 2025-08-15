<?php
/**
 * NBT Database Analysis Tool - Fixed Version
 * Access via: http://localhost/nbt/aj_nbt_website/analyze_database.php
 */

// Try to find the correct config path
$configPaths = [
    'public_html (3)/config/db.php',
    'public_html/config/db.php',
    'config/db.php'
];

$configFound = false;
foreach ($configPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $configFound = true;
        break;
    }
}

if (!$configFound) {
    die("❌ Database configuration file not found. Please check the path.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBT Database Analysis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8fafc; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: bold; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .alert-warning { background: #fef3cd; border-left: 4px solid #ffc107; }
        .alert-info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; }
        .stat { text-align: center; padding: 20px; }
        .stat-number { font-size: 2em; font-weight: bold; color: #7c3aed; }
        .stat-label { color: #6b7280; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 NBT Database Analysis</h1>
        
        <?php
        try {
            // Database Size Analysis
            echo "<div class='card'>";
            echo "<h2>📊 Database Size Analysis</h2>";
            
            $sizeQuery = "SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb',
                table_rows
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC";
            
            $result = $pdo->query($sizeQuery);
            $tables = $result->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table>";
            echo "<tr><th>Table Name</th><th>Size (MB)</th><th>Rows</th></tr>";
            $totalSize = 0;
            foreach ($tables as $table) {
                echo "<tr>";
                echo "<td>" . $table['table_name'] . "</td>";
                echo "<td>" . $table['size_mb'] . "</td>";
                echo "<td>" . number_format($table['table_rows']) . "</td>";
                echo "</tr>";
                $totalSize += $table['size_mb'];
            }
            echo "</table>";
            
            if ($totalSize > 100) {
                echo "<div class='alert alert-warning'>⚠️ Large database detected ({$totalSize} MB). Consider optimizing for Hostinger.</div>";
            } else {
                echo "<div class='alert alert-success'>✅ Database size ({$totalSize} MB) is acceptable for shared hosting.</div>";
            }
            echo "</div>";
            
            // Content Statistics
            echo "<div class='card'>";
            echo "<h2>📈 Content Statistics</h2>";
            echo "<div class='grid'>";
            
            $stats = [
                'Courses' => 'SELECT COUNT(*) as count FROM courses',
                'Team Members' => 'SELECT COUNT(*) as count FROM meet_our_team',
                'Services' => 'SELECT COUNT(*) as count FROM our_services',
                'Client Testimonials' => 'SELECT COUNT(*) as count FROM client_testimonials',
                'Course Testimonials' => 'SELECT COUNT(*) as count FROM course_testimonials',
                'Clients' => 'SELECT COUNT(*) as count FROM client',
                'Coupons' => 'SELECT COUNT(*) as count FROM coupons',
                'Contact Submissions' => 'SELECT COUNT(*) as count FROM contact_us'
            ];
            
            foreach ($stats as $label => $query) {
                try {
                    $result = $pdo->query($query);
                    $count = $result->fetch(PDO::FETCH_ASSOC)['count'];
                    echo "<div class='stat'>";
                    echo "<div class='stat-number'>" . number_format($count) . "</div>";
                    echo "<div class='stat-label'>{$label}</div>";
                    echo "</div>";
                } catch (PDOException $e) {
                    echo "<div class='stat'>";
                    echo "<div class='stat-number'>N/A</div>";
                    echo "<div class='stat-label'>{$label}</div>";
                    echo "</div>";
                }
            }
            echo "</div>";
            echo "</div>";
            
            // LONGBLOB Analysis
            echo "<div class='card'>";
            echo "<h2>🗃️ File Storage Analysis (LONGBLOB)</h2>";
            
            $lobTables = [
                'courses' => 'image',
                'meet_our_team' => 'image_data',
                'founder_card' => 'image_data',
                'our_services' => 'image_data',
                'overview_images' => 'image_data',
                'course_testimonials' => ['image', 'video'],
                'client_testimonials' => 'company_logo'
            ];
            
            foreach ($lobTables as $table => $columns) {
                if (is_array($columns)) {
                    foreach ($columns as $column) {
                        try {
                            $query = "SELECT COUNT(*) as count, AVG(LENGTH({$column})) as avg_size FROM {$table} WHERE {$column} IS NOT NULL";
                            $result = $pdo->query($query);
                            $data = $result->fetch(PDO::FETCH_ASSOC);
                            $avgSizeMB = round($data['avg_size'] / 1024 / 1024, 2);
                            echo "<p><strong>{$table}.{$column}:</strong> {$data['count']} files, avg size: {$avgSizeMB} MB</p>";
                        } catch (PDOException $e) {
                            echo "<p><strong>{$table}.{$column}:</strong> Error analyzing</p>";
                        }
                    }
                } else {
                    try {
                        $query = "SELECT COUNT(*) as count, AVG(LENGTH({$columns})) as avg_size FROM {$table} WHERE {$columns} IS NOT NULL";
                        $result = $pdo->query($query);
                        $data = $result->fetch(PDO::FETCH_ASSOC);
                        $avgSizeMB = round($data['avg_size'] / 1024 / 1024, 2);
                        echo "<p><strong>{$table}.{$columns}:</strong> {$data['count']} files, avg size: {$avgSizeMB} MB</p>";
                    } catch (PDOException $e) {
                        echo "<p><strong>{$table}.{$columns}:</strong> Error analyzing</p>";
                    }
                }
            }
            
            echo "<div class='alert alert-warning'>⚠️ LONGBLOB storage may cause issues on shared hosting. Consider migrating to file-based storage.</div>";
            echo "</div>";
            
            // Index Analysis
            echo "<div class='card'>";
            echo "<h2>🔍 Index Analysis</h2>";
            
            $indexQuery = "SELECT 
                table_name,
                index_name,
                column_name,
                cardinality
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE()
                ORDER BY table_name, index_name";
            
            $result = $pdo->query($indexQuery);
            $indexes = $result->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table>";
            echo "<tr><th>Table</th><th>Index Name</th><th>Column</th><th>Cardinality</th></tr>";
            foreach ($indexes as $index) {
                echo "<tr>";
                echo "<td>" . $index['table_name'] . "</td>";
                echo "<td>" . $index['index_name'] . "</td>";
                echo "<td>" . $index['column_name'] . "</td>";
                echo "<td>" . number_format($index['cardinality']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
            
            // Recommendations
            echo "<div class='card'>";
            echo "<h2>💡 Recommendations for Hostinger Migration</h2>";
            echo "<div class='alert alert-info'>";
            echo "<h4>High Priority:</h4>";
            echo "<ul>";
            echo "<li>🔄 Migrate LONGBLOB data to file system</li>";
            echo "<li>📝 Update database credentials in config/db.php</li>";
            echo "<li>📧 Configure email settings for contact forms</li>";
            echo "<li>🔑 Test YouTube API functionality</li>";
            echo "</ul>";
            echo "<h4>Medium Priority:</h4>";
            echo "<ul>";
            echo "<li>📊 Add missing indexes for performance</li>";
            echo "<li>🛡️ Add data validation constraints</li>";
            echo "<li>💾 Implement regular backup strategy</li>";
            echo "<li>🗜️ Optimize image sizes and compression</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='alert alert-warning'>";
            echo "<h3>❌ Database Connection Error</h3>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
            echo "<p>Please check your database configuration in config/db.php</p>";
            echo "</div>";
        }
        ?>
        
        <div class="card">
            <h2>🔧 Quick Actions</h2>
            <p><a href="migrate_database.php">🚀 Run Database Migration</a></p>
            <p><a href="public_html (3)/admin/dashboard.php">🎛️ Admin Dashboard</a></p>
            <p><a href="public_html (3)/index.php">🌐 View Website</a></p>
        </div>
    </div>
</body>
</html>
