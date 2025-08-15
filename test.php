<?php
/**
 * Simple Test Script
 * Access via: http://localhost/nbt/aj_nbt_website/test.php
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>NBT Test Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f0f0; }
        .card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .success { border-left: 4px solid #28a745; background: #d4edda; }
        .error { border-left: 4px solid #dc3545; background: #f8d7da; }
        .info { border-left: 4px solid #17a2b8; background: #d1ecf1; }
    </style>
</head>
<body>
    <h1>🧪 NBT System Test</h1>
    
    <div class="card success">
        <h3>✅ PHP is working!</h3>
        <p>PHP Version: <?php echo PHP_VERSION; ?></p>
        <p>Current Time: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    
    <div class="card info">
        <h3>📁 Directory Check</h3>
        <ul>
            <?php
            $checkDirs = [
                'public_html (3)' => 'Main project folder',
                'public_html (3)/config' => 'Configuration folder',
                'public_html (3)/admin' => 'Admin panel folder',
                'public_html (3)/index.php' => 'Main website file'
            ];
            
            foreach ($checkDirs as $dir => $description) {
                $exists = file_exists($dir);
                $icon = $exists ? "✅" : "❌";
                $class = $exists ? "success" : "error";
                echo "<li><span style='color: " . ($exists ? "green" : "red") . "'>{$icon}</span> <strong>{$dir}</strong> - {$description}</li>";
            }
            ?>
        </ul>
    </div>
    
    <div class="card info">
        <h3>🔗 Test Links</h3>
        <ul>
            <li><a href="http://localhost/nbt/aj_nbt_website/public_html (3)/index.php" target="_blank">Main Website</a></li>
            <li><a href="http://localhost/nbt/aj_nbt_website/public_html (3)/admin/index.php" target="_blank">Admin Login</a></li>
            <li><a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
            <li><a href="analyze_database.php">Database Analysis</a></li>
        </ul>
    </div>
    
    <div class="card">
        <h3>🔍 Database Test</h3>
        <?php
        $configPaths = [
            'public_html (3)/config/db.php',
            'public_html/config/db.php',
            'config/db.php'
        ];
        
        $configFound = false;
        $configPath = '';
        
        foreach ($configPaths as $path) {
            if (file_exists($path)) {
                $configPath = $path;
                $configFound = true;
                break;
            }
        }
        
        if ($configFound) {
            echo "<p class='success'>✅ Config file found at: <strong>{$configPath}</strong></p>";
            
            try {
                require $configPath;
                echo "<p class='success'>✅ Database configuration loaded successfully!</p>";
                
                $testQuery = $pdo->query("SELECT DATABASE() as db_name, COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = DATABASE()");
                $result = $testQuery->fetch(PDO::FETCH_ASSOC);
                
                echo "<p class='success'>✅ Database connection successful!</p>";
                echo "<p>Database Name: <strong>" . $result['db_name'] . "</strong></p>";
                echo "<p>Number of Tables: <strong>" . $result['table_count'] . "</strong></p>";
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Database configuration file not found!</p>";
            echo "<p>Checked paths:</p>";
            echo "<ul>";
            foreach ($configPaths as $path) {
                echo "<li>{$path}</li>";
            }
            echo "</ul>";
        }
        ?>
    </div>
    
    <div class="card info">
        <h3>🛠️ Troubleshooting</h3>
        <p>If you're getting errors, try these steps:</p>
        <ol>
            <li><strong>Check XAMPP Services:</strong> Make sure Apache and MySQL are running in XAMPP Control Panel</li>
            <li><strong>Check Database:</strong> Go to <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a> and verify the database exists</li>
            <li><strong>Check File Paths:</strong> Ensure the folder structure matches what's shown above</li>
            <li><strong>Clear Browser Cache:</strong> Try refreshing or using incognito mode</li>
        </ol>
    </div>
</body>
</html>
