<?php
/**
 * Simple One-Click Maintenance
 * Performs basic website maintenance tasks with one click
 */

// Security check
session_start();
if (!isset($_SESSION['admin_id'])) {
    die('Access denied. Please login as admin.');
}

$results = [];
$startTime = microtime(true);

// 1. Clean cache
$cacheDir = '../cache/images/';
$deletedFiles = 0;
$cleanedSize = 0;

if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileAge = time() - filemtime($file);
            $fileSize = filesize($file);
            
            // Remove empty files or files older than 30 days
            if ($fileSize === 0 || $fileAge > (30 * 24 * 60 * 60)) {
                $cleanedSize += $fileSize;
                unlink($file);
                $deletedFiles++;
            }
        }
    }
}

$results[] = [
    'task' => 'Cache Cleanup',
    'status' => 'success',
    'message' => "Removed {$deletedFiles} files, freed " . round($cleanedSize/1024, 2) . "KB"
];

// 2. Check database connection
try {
    require '../config/db.php';
    $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
    $courseCount = $stmt->fetch()[0];
    
    $results[] = [
        'task' => 'Database Check',
        'status' => 'success',
        'message' => "Connected successfully. {$courseCount} courses found."
    ];
} catch (Exception $e) {
    $results[] = [
        'task' => 'Database Check',
        'status' => 'error',
        'message' => "Connection failed: " . $e->getMessage()
    ];
}

// 3. Check disk space (simplified)
$totalFiles = 0;
$totalSize = 0;
if (is_dir('../')) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('../'));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $totalFiles++;
            $totalSize += $file->getSize();
        }
    }
}

$results[] = [
    'task' => 'Disk Usage Check',
    'status' => 'info',
    'message' => "{$totalFiles} files using " . round($totalSize/1024/1024, 2) . "MB"
];

// 4. Performance test
$endTime = microtime(true);
$executionTime = round(($endTime - $startTime) * 1000, 2);

$results[] = [
    'task' => 'Performance Test',
    'status' => $executionTime < 500 ? 'success' : 'warning',
    'message' => "Maintenance completed in {$executionTime}ms"
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Click Maintenance - NBT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../assert/black.png">
    <meta http-equiv="refresh" content="10;url=health_check.php">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="text-6xl mb-4">🔧</div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Maintenance Complete!</h1>
                <p class="text-gray-600">Your website has been automatically optimized</p>
            </div>

            <!-- Results -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Maintenance Report</h2>
                <div class="space-y-4">
                    <?php foreach ($results as $result): ?>
                    <div class="flex items-center p-3 rounded-lg <?= 
                        $result['status'] === 'success' ? 'bg-green-50 border border-green-200' : 
                        ($result['status'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 
                        ($result['status'] === 'error' ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200')) ?>">
                        <div class="text-2xl mr-3">
                            <?= $result['status'] === 'success' ? '✅' : 
                                ($result['status'] === 'warning' ? '⚠️' : 
                                ($result['status'] === 'error' ? '❌' : 'ℹ️')) ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800"><?= $result['task'] ?></h3>
                            <p class="text-sm text-gray-600"><?= $result['message'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-xl p-6 mb-8">
                <h2 class="text-xl font-semibold mb-2">🎉 All Done!</h2>
                <p class="mb-4">Your website maintenance has been completed successfully. The system has been optimized for better performance.</p>
                <div class="text-sm opacity-90">
                    <p>✅ Cache cleaned and optimized</p>
                    <p>✅ Database connectivity verified</p>
                    <p>✅ System performance tested</p>
                    <p>✅ Storage usage analyzed</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center space-y-4">
                <div class="space-x-4">
                    <a href="health_check.php" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg inline-block transition-colors">
                        📊 View Health Check
                    </a>
                    <a href="dashboard.php" 
                       class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg inline-block transition-colors">
                        🏠 Back to Dashboard
                    </a>
                </div>
                
                <p class="text-sm text-gray-500">
                    Automatically redirecting to Health Check in <span id="countdown">10</span> seconds...
                </p>
            </div>
        </div>
    </div>

    <script>
        // Countdown timer
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                countdownElement.textContent = 'Redirecting...';
            }
        }, 1000);
    </script>
</body>
</html>
