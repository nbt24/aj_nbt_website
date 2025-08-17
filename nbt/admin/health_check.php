<?php
/**
 * Simple Performance Widget for Admin Dashboard
 * Shows basic performance metrics in an easy-to-understand format
 */

// Get basic performance metrics
$startTime = microtime(true);
require '../config/db.php';

// Simple database check
$dbStatus = "Connected";
$imageCount = 0;
$cacheSize = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
    $courseCount = $stmt->fetch()['count'];
} catch (Exception $e) {
    $dbStatus = "Error";
    $courseCount = 0;
}

// Check cache directory
$cacheDir = '../cache/images/';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '*');
    $imageCount = count($files);
    foreach ($files as $file) {
        if (is_file($file)) {
            $cacheSize += filesize($file);
        }
    }
}

$cacheSize = round($cacheSize / 1024 / 1024, 2); // Convert to MB
$loadTime = round((microtime(true) - $startTime) * 1000, 2); // Convert to ms

// Determine status colors
$loadTimeClass = $loadTime < 100 ? 'text-green-600' : ($loadTime < 300 ? 'text-yellow-600' : 'text-red-600');
$cacheClass = $imageCount > 0 ? 'text-green-600' : 'text-yellow-600';
$dbClass = $dbStatus === "Connected" ? 'text-green-600' : 'text-red-600';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Health Check - NBT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-good { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
        .status-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
        .status-error { background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Website Health Check</h1>
            <p class="text-gray-600">Simple performance monitoring for your team</p>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Speed Status -->
            <div class="<?= $loadTime < 100 ? 'status-good' : ($loadTime < 300 ? 'status-warning' : 'status-error') ?> rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Website Speed</h3>
                        <p class="<?= $loadTimeClass ?> text-2xl font-bold"><?= $loadTime ?>ms</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <?= $loadTime < 100 ? '🚀 Excellent!' : ($loadTime < 300 ? '⚡ Good' : '🐌 Needs attention') ?>
                        </p>
                    </div>
                    <div class="text-4xl">
                        <?= $loadTime < 100 ? '🚀' : ($loadTime < 300 ? '⚡' : '🐌') ?>
                    </div>
                </div>
            </div>

            <!-- Cache Status -->
            <div class="<?= $imageCount > 0 ? 'status-good' : 'status-warning' ?> rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Image Cache</h3>
                        <p class="<?= $cacheClass ?> text-2xl font-bold"><?= $imageCount ?> files</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <?= $cacheSize ?>MB cached
                        </p>
                    </div>
                    <div class="text-4xl">
                        <?= $imageCount > 0 ? '💾' : '📁' ?>
                    </div>
                </div>
            </div>

            <!-- Database Status -->
            <div class="<?= $dbStatus === 'Connected' ? 'status-good' : 'status-error' ?> rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Database</h3>
                        <p class="<?= $dbClass ?> text-2xl font-bold"><?= $dbStatus ?></p>
                        <p class="text-sm text-gray-600 mt-1">
                            <?= $courseCount ?> courses loaded
                        </p>
                    </div>
                    <div class="text-4xl">
                        <?= $dbStatus === 'Connected' ? '🟢' : '🔴' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <a href="../cache/cleanup.php" 
                   class="bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🧹 Clean Cache
                </a>
                
                <a href="../performance_monitor.php" 
                   class="bg-green-500 hover:bg-green-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    📊 Full Report
                </a>
                
                <a href="image_checker.php" 
                   class="bg-orange-500 hover:bg-orange-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    📸 Check Images
                </a>
                
                <a href="compression_monitor.php" 
                   class="bg-purple-500 hover:bg-purple-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🗜️ Compression
                </a>
                
                <a href="maintenance.php" 
                   class="bg-red-500 hover:bg-red-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🔧 Auto Maintenance
                </a>
                
                <a href="documentation.php" 
                   class="bg-purple-500 hover:bg-purple-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    📚 Help Guide
                </a>
                
                <a href="../index.php" 
                   class="bg-gray-500 hover:bg-gray-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🌐 View Website
                </a>
                
                <a href="dashboard.php" 
                   class="bg-gray-500 hover:bg-gray-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🏠 Admin Home
                </a>
            </div>
        </div>

        <!-- Simple Tips -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">💡 Today's Tips</h3>
            <div class="space-y-3">
                <?php if ($loadTime > 300): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-3">
                    <p class="text-red-700">
                        <strong>Action Needed:</strong> Website is loading slowly. 
                        <a href="../cache/cleanup.php" class="underline">Clean cache</a> or compress recent images.
                    </p>
                </div>
                <?php endif; ?>

                <?php if ($imageCount === 0): ?>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3">
                    <p class="text-yellow-700">
                        <strong>Notice:</strong> No cached images found. Cache will build automatically as people visit your site.
                    </p>
                </div>
                <?php endif; ?>

                <?php if ($loadTime < 100 && $imageCount > 0): ?>
                <div class="bg-green-50 border-l-4 border-green-400 p-3">
                    <p class="text-green-700">
                        <strong>Great job!</strong> Your website is running smoothly. Keep up the good maintenance habits!
                    </p>
                </div>
                <?php endif; ?>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-3">
                    <p class="text-blue-700">
                        <strong>Remember:</strong> Compress images before uploading. Use 
                        <a href="https://tinypng.com" target="_blank" class="underline">TinyPNG.com</a> - it's free!
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-500">
            <p>Last checked: <?= date('Y-m-d H:i:s') ?></p>
            <button onclick="location.reload()" 
                    class="mt-2 text-blue-500 hover:text-blue-700 underline">
                🔄 Refresh Check
            </button>
        </div>
    </div>
</body>
</html>
