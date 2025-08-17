<?php
/**
 * Performance Monitor
 * Tracks page load times and cache effectiveness
 */

$startTime = microtime(true);
$startMemory = memory_get_usage();

// Include the main index file functionality (without output)
ob_start();
$pdo; // Will be set by the included file

// Simple database queries to test performance
try {
    require 'config/db.php';
    
    // Test query performance
    $queryStart = microtime(true);
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses");
    $coursesCount = $stmt->fetch()['total'];
    $queryTime = (microtime(true) - $queryStart) * 1000;
    
    $queryStart = microtime(true);
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM meet_our_team");
    $teamCount = $stmt->fetch()['total'];
    $teamQueryTime = (microtime(true) - $queryStart) * 1000;
    
    $queryStart = microtime(true);
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM our_services");
    $servicesCount = $stmt->fetch()['total'];
    $servicesQueryTime = (microtime(true) - $queryStart) * 1000;
    
} catch (Exception $e) {
    $coursesCount = "Error";
    $queryTime = "Error";
}

ob_end_clean();

$endTime = microtime(true);
$endMemory = memory_get_usage();

$executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
$memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
$peakMemory = memory_get_peak_usage() / 1024 / 1024; // Convert to MB

// Check cache directory
$cacheDir = 'cache/images/';
$cacheFiles = 0;
$cacheSize = 0;

if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '*');
    $cacheFiles = count($files);
    foreach ($files as $file) {
        if (is_file($file)) {
            $cacheSize += filesize($file);
        }
    }
}
$cacheSize = $cacheSize / 1024 / 1024; // Convert to MB

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBT Performance Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .metric-good { color: #10b981; }
        .metric-warning { color: #f59e0b; }
        .metric-bad { color: #ef4444; }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">NBT Website Performance Monitor</h1>
        
        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Execution Time</h3>
                <p class="text-2xl font-bold <?= $executionTime < 100 ? 'metric-good' : ($executionTime < 500 ? 'metric-warning' : 'metric-bad') ?>">
                    <?= number_format($executionTime, 2) ?>ms
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    <?= $executionTime < 100 ? 'Excellent' : ($executionTime < 500 ? 'Good' : 'Needs Optimization') ?>
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Memory Usage</h3>
                <p class="text-2xl font-bold <?= $memoryUsed < 5 ? 'metric-good' : ($memoryUsed < 10 ? 'metric-warning' : 'metric-bad') ?>">
                    <?= number_format($memoryUsed, 2) ?>MB
                </p>
                <p class="text-sm text-gray-500 mt-1">Peak: <?= number_format($peakMemory, 2) ?>MB</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Cache Status</h3>
                <p class="text-2xl font-bold metric-good"><?= $cacheFiles ?> files</p>
                <p class="text-sm text-gray-500 mt-1"><?= number_format($cacheSize, 2) ?>MB cached</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Database</h3>
                <p class="text-2xl font-bold <?= $queryTime < 10 ? 'metric-good' : ($queryTime < 50 ? 'metric-warning' : 'metric-bad') ?>">
                    <?= number_format($queryTime, 2) ?>ms
                </p>
                <p class="text-sm text-gray-500 mt-1">Query response time</p>
            </div>
        </div>
        
        <!-- Database Metrics -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Database Content</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600"><?= $coursesCount ?></p>
                    <p class="text-sm text-gray-600">Courses</p>
                    <p class="text-xs text-gray-500"><?= number_format($queryTime, 2) ?>ms</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600"><?= $teamCount ?></p>
                    <p class="text-sm text-gray-600">Team Members</p>
                    <p class="text-xs text-gray-500"><?= number_format($teamQueryTime, 2) ?>ms</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600"><?= $servicesCount ?></p>
                    <p class="text-sm text-gray-600">Services</p>
                    <p class="text-xs text-gray-500"><?= number_format($servicesQueryTime, 2) ?>ms</p>
                </div>
            </div>
        </div>
        
        <!-- Recommendations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Performance Recommendations</h3>
            <ul class="space-y-2">
                <?php if ($executionTime > 500): ?>
                <li class="flex items-center text-red-600">
                    <span class="mr-2">⚠️</span>
                    Page execution time is high. Consider optimizing database queries or enabling PHP caching.
                </li>
                <?php endif; ?>
                
                <?php if ($memoryUsed > 10): ?>
                <li class="flex items-center text-red-600">
                    <span class="mr-2">⚠️</span>
                    Memory usage is high. Check for memory leaks or large data processing.
                </li>
                <?php endif; ?>
                
                <?php if ($cacheFiles === 0): ?>
                <li class="flex items-center text-yellow-600">
                    <span class="mr-2">💡</span>
                    No cache files found. Cache will be generated on first page load.
                </li>
                <?php endif; ?>
                
                <?php if ($executionTime < 100 && $memoryUsed < 5): ?>
                <li class="flex items-center text-green-600">
                    <span class="mr-2">✅</span>
                    Performance is excellent! Your optimizations are working well.
                </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- Actions -->
        <div class="mt-8 text-center">
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded mr-4">
                View Main Site
            </a>
            <a href="cache/cleanup.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded mr-4">
                Clean Cache
            </a>
            <button onclick="location.reload()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                Refresh Monitor
            </button>
        </div>
    </div>
</body>
</html>
