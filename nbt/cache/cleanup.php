<?php
/**
 * Image Cache Cleanup Utility
 * This script removes cached image files older than 7 days
 * Run this periodically to prevent cache directory from growing too large
 */

$cacheDir = __DIR__ . '/images/';
$maxAge = 7 * 24 * 60 * 60; // 7 days in seconds

if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '*');
    $cleanedCount = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if ((time() - filemtime($file)) > $maxAge) {
                unlink($file);
                $cleanedCount++;
            }
        }
    }
    
    echo "Cache cleanup completed. Removed $cleanedCount old files.\n";
} else {
    echo "Cache directory not found.\n";
}
?>
