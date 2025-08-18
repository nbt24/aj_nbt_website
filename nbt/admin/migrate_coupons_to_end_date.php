<?php
/**
 * Database Migration Script: Convert coupons time_limit to end_date
 * Run this script ONCE to update your database structure
 */

require '../config/db.php';

try {
    echo "<h2>🔄 Migrating Coupons Database Structure...</h2>\n";
    echo "<pre>\n";
    
    // Step 1: Check if end_date column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM coupons LIKE 'end_date'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "1. Adding 'end_date' column to coupons table...\n";
        $pdo->exec("ALTER TABLE coupons ADD COLUMN end_date DATE NULL AFTER discount");
        echo "   ✅ 'end_date' column added successfully!\n\n";
    } else {
        echo "1. ✅ 'end_date' column already exists.\n\n";
    }
    
    // Step 2: Migrate existing time_limit data to end_date
    echo "2. Converting existing time_limit data to end_date...\n";
    
    $stmt = $pdo->query("SELECT code, time_limit FROM coupons WHERE time_limit IS NOT NULL AND (end_date IS NULL OR end_date = '')");
    $couponsToMigrate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($couponsToMigrate) > 0) {
        $updateStmt = $pdo->prepare("UPDATE coupons SET end_date = ? WHERE code = ?");
        
        foreach ($couponsToMigrate as $coupon) {
            $daysToAdd = (int)$coupon['time_limit'];
            if ($daysToAdd > 0) {
                // Calculate end date by adding days to current date
                $endDate = date('Y-m-d', strtotime("+{$daysToAdd} days"));
                $updateStmt->execute([$endDate, $coupon['code']]);
                echo "   - Coupon '{$coupon['code']}': {$daysToAdd} days → {$endDate}\n";
            }
        }
        echo "   ✅ Migrated " . count($couponsToMigrate) . " coupon(s) successfully!\n\n";
    } else {
        echo "   ℹ️ No coupons to migrate.\n\n";
    }
    
    // Step 3: Optional - Remove time_limit column (commented out for safety)
    echo "3. 🔸 time_limit column preservation:\n";
    echo "   ℹ️ The 'time_limit' column has been kept for backup purposes.\n";
    echo "   ℹ️ You can manually remove it later if desired:\n";
    echo "   ⚠️ ALTER TABLE coupons DROP COLUMN time_limit;\n\n";
    
    // Step 4: Show final structure
    echo "4. Final coupons table structure:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM coupons")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "   - {$column['Field']} ({$column['Type']}) " . 
             ($column['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column['Default'] ? " DEFAULT {$column['Default']}" : '') . "\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "🎯 You can now use the updated admin panel with End Date functionality.\n";
    echo "\n</pre>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Migration Error:</h3>";
    echo "<pre style='color: red;'>Error: " . $e->getMessage() . "</pre>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Coupons Database Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <h1>🗃️ Coupons Database Migration Complete</h1>
    <p><strong>Next Steps:</strong></p>
    <ol>
        <li>✅ Your admin panel now uses "End Date" instead of "Time Limit (days)"</li>
        <li>✅ Existing coupons have been converted automatically</li>
        <li>🔗 <a href="coupons.php">Go to Coupons Admin Panel</a></li>
        <li>🔗 <a href="../index.php">View Frontend with Updated Countdown</a></li>
    </ol>
    
    <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px;">
        <h3>📝 What Changed:</h3>
        <ul>
            <li><strong>Admin Panel:</strong> Now shows a date picker for "End Date"</li>
            <li><strong>Database:</strong> Added 'end_date' column (DATE format)</li>
            <li><strong>Frontend:</strong> Countdown now works with actual end dates</li>
            <li><strong>Features:</strong> Shows "EXPIRED" and "ENDING SOON" status in admin</li>
        </ul>
    </div>
</body>
</html>
