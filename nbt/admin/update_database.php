<?php
// Simple Database Update - Add end_date column to coupons table
require '../config/db.php';

try {
    echo "<h2>🔄 Adding end_date column to coupons table...</h2>\n";
    
    // Check if end_date column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM coupons LIKE 'end_date'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add end_date column
        $pdo->exec("ALTER TABLE coupons ADD COLUMN end_date DATE NULL AFTER discount");
        echo "✅ SUCCESS: 'end_date' column added to coupons table!<br>\n";
        
        // Convert existing time_limit data to end_date
        $stmt = $pdo->query("SELECT code, time_limit FROM coupons WHERE time_limit IS NOT NULL AND time_limit > 0");
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($coupons) > 0) {
            $updateStmt = $pdo->prepare("UPDATE coupons SET end_date = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE code = ?");
            foreach ($coupons as $coupon) {
                $updateStmt->execute([$coupon['time_limit'], $coupon['code']]);
            }
            echo "✅ SUCCESS: Converted " . count($coupons) . " existing coupons to use end_date!<br>\n";
        }
        
        echo "<br>🎉 Database update completed successfully!<br>";
        echo "You can now go back to the <a href='coupons.php'>Coupons Admin Panel</a><br>";
        
    } else {
        echo "ℹ️ INFO: 'end_date' column already exists in coupons table.<br>";
        echo "Go back to <a href='coupons.php'>Coupons Admin Panel</a><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
}
?>
