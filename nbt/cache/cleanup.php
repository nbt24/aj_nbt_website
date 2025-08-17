<?php
/**
 * Enhanced Cache Cleanup Script
 * Removes old and invalid cache files to improve performance
 */

// Security check - only allow execution from localhost or CLI
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) && php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Access denied');
}

$cacheDir = __DIR__ . '/images/';
$maxAge = 30 * 24 * 60 * 60; // 30 days in seconds
$deleted = 0;
$cleaned = 0;

echo "<h1>Cache Cleanup Report</h1>\n";
echo "<p>Starting cleanup at " . date('Y-m-d H:i:s') . "</p>\n";

if (!is_dir($cacheDir)) {
    echo "<p>Cache directory doesn't exist.</p>\n";
    exit;
}

$files = glob($cacheDir . '*');
echo "<p>Found " . count($files) . " files in cache directory</p>\n";

foreach ($files as $file) {
    if (!is_file($file)) continue;
    
    $filename = basename($file);
    $fileAge = time() - filemtime($file);
    $fileSize = filesize($file);
    
    // Remove empty files
    if ($fileSize === 0) {
        unlink($file);
        echo "<p style='color: orange;'>Removed empty file: $filename</p>\n";
        $cleaned++;
        continue;
    }
    
    // Remove files older than maxAge
    if ($fileAge > $maxAge) {
        unlink($file);
        echo "<p style='color: red;'>Removed old file: $filename (age: " . round($fileAge/86400) . " days)</p>\n";
        $deleted++;
        continue;
    }
    
    // Check if file is a valid image
    $imageInfo = @getimagesize($file);
    if ($imageInfo === false) {
        unlink($file);
        echo "<p style='color: red;'>Removed invalid image: $filename</p>\n";
        $cleaned++;
        continue;
    }
    
    echo "<p style='color: green;'>Kept valid file: $filename (" . round($fileSize/1024) . " KB)</p>\n";
}

echo "<hr>\n";
echo "<p><strong>Cleanup Summary:</strong></p>\n";
echo "<ul>\n";
echo "<li>Files deleted (old): $deleted</li>\n";
echo "<li>Files cleaned (empty/invalid): $cleaned</li>\n";
echo "<li>Total removed: " . ($deleted + $cleaned) . "</li>\n";
echo "</ul>\n";
echo "<p>Cleanup completed at " . date('Y-m-d H:i:s') . "</p>\n";
?>
