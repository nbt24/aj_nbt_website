<?php
require_once '../config/db.php';
session_start();

// Only allow access for logged-in users
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = "Image Compression Monitor";

// Get compression statistics
function getCompressionStats() {
    global $pdo;
    
    $stats = [
        'total_images' => 0,
        'compressed_images' => 0,
        'estimated_savings' => 0,
        'recent_uploads' => []
    ];
    
    // Count images from all tables
    $tables = [
        'courses' => 'image',
        'meet_our_team' => 'image_data',
        'founder_card' => 'image_data',
        'client_testimonials' => 'company_logo'
    ];
    
    foreach ($tables as $table => $column) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table WHERE $column IS NOT NULL");
        $stmt->execute();
        $result = $stmt->fetch();
        $stats['total_images'] += $result['count'];
    }
    
    return $stats;
}

$compressionStats = getCompressionStats();

// Test image compression if form submitted
$testResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $file = $_FILES['test_image'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $originalSize = $file['size'];
        
        // Create temporary optimized file
        $tempOptimized = sys_get_temp_dir() . '/test_optimized_' . uniqid() . '.jpg';
        
        if (optimizeUploadedImage($file, $tempOptimized)) {
            $optimizedSize = filesize($tempOptimized);
            $reduction = (($originalSize - $optimizedSize) / $originalSize) * 100;
            
            $testResult = [
                'success' => true,
                'original_size' => formatFileSize($originalSize),
                'optimized_size' => formatFileSize($optimizedSize),
                'reduction_percent' => round($reduction, 1),
                'saved_bytes' => $originalSize - $optimizedSize
            ];
            
            unlink($tempOptimized); // Clean up
        } else {
            $testResult = ['success' => false, 'error' => 'Optimization failed'];
        }
    }
}

function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Compression Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🗜️ Image Compression Monitor</h1>
                    <p class="text-gray-600 mt-2">Monitor automatic image optimization across your website</p>
                </div>
                <a href="dashboard.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <span class="text-2xl">📸</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Total Images</h3>
                        <p class="text-3xl font-bold text-blue-600"><?= $compressionStats['total_images'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <span class="text-2xl">✅</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Auto-Optimized</h3>
                        <p class="text-3xl font-bold text-green-600">Active</p>
                        <p class="text-sm text-gray-500">All new uploads</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <span class="text-2xl">⚡</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Compression</h3>
                        <p class="text-3xl font-bold text-purple-600">30-60%</p>
                        <p class="text-sm text-gray-500">Average reduction</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Image Compression -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 fade-in">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🧪 Test Image Compression</h2>
            <p class="text-gray-600 mb-6">Upload an image to see how much it would be compressed by the automatic system.</p>
            
            <form method="post" enctype="multipart/form-data" class="mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="file" name="test_image" accept="image/*" required 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                        Test Compression
                    </button>
                </div>
            </form>

            <?php if ($testResult): ?>
                <div class="<?= $testResult['success'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-lg p-4">
                    <?php if ($testResult['success']): ?>
                        <h3 class="text-lg font-semibold text-green-800 mb-2">✅ Compression Results</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Original Size:</span>
                                <span class="text-gray-600"><?= $testResult['original_size'] ?></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Compressed Size:</span>
                                <span class="text-green-600"><?= $testResult['optimized_size'] ?></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Size Reduction:</span>
                                <span class="text-green-600 font-bold"><?= $testResult['reduction_percent'] ?>%</span>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <strong>Saved:</strong> <?= formatFileSize($testResult['saved_bytes']) ?>
                        </div>
                    <?php else: ?>
                        <h3 class="text-lg font-semibold text-red-800">❌ Compression Failed</h3>
                        <p class="text-red-600"><?= $testResult['error'] ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- How It Works -->
        <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🔧 How Automatic Compression Works</h2>
            <div class="space-y-4">
                <div class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full p-2 mr-4 mt-1">1</span>
                    <div>
                        <h3 class="font-semibold text-gray-800">Upload Detection</h3>
                        <p class="text-gray-600">When any image is uploaded through admin forms or frontend, the system automatically detects it.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full p-2 mr-4 mt-1">2</span>
                    <div>
                        <h3 class="font-semibold text-gray-800">Smart Optimization</h3>
                        <p class="text-gray-600">Images are resized to optimal dimensions (max 1920x1080) and compressed with high quality settings.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full p-2 mr-4 mt-1">3</span>
                    <div>
                        <h3 class="font-semibold text-gray-800">Quality Preservation</h3>
                        <p class="text-gray-600">JPEG quality set to 90%, PNG compression level 6 - maintaining visual quality while reducing file size.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full p-2 mr-4 mt-1">4</span>
                    <div>
                        <h3 class="font-semibold text-gray-800">Automatic Storage</h3>
                        <p class="text-gray-600">Optimized images are automatically stored in the database, replacing the original without any manual intervention.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active on These Sections -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl shadow-lg p-6 mt-8 fade-in">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📍 Active Compression Areas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow">
                    <h3 class="font-semibold text-gray-800">Course Images</h3>
                    <p class="text-sm text-gray-600">All course thumbnail uploads</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <h3 class="font-semibold text-gray-800">Team Photos</h3>
                    <p class="text-sm text-gray-600">Team member profile pictures</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <h3 class="font-semibold text-gray-800">Founder Images</h3>
                    <p class="text-sm text-gray-600">Founder profile pictures</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <h3 class="font-semibold text-gray-800">Company Logos</h3>
                    <p class="text-sm text-gray-600">Client testimonial logos</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
