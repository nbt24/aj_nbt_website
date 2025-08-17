<?php
/**
 * Simple Image Size Checker
 * Helps admin users check if their images are too large before uploading
 */
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$uploadMessage = '';
$imageInfo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $file = $_FILES['test_image'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileSize = $file['size'];
        $fileSizeKB = round($fileSize / 1024, 2);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        $imageInfo = getimagesize($file['tmp_name']);
        
        $recommendations = [];
        $status = 'good';
        
        // Check file size
        if ($fileSize > 500 * 1024) { // 500KB
            $recommendations[] = "🔴 File is too large ({$fileSizeKB}KB). Compress to under 500KB.";
            $status = 'bad';
        } elseif ($fileSize > 200 * 1024) { // 200KB
            $recommendations[] = "🟡 File is large ({$fileSizeKB}KB). Consider compressing for better performance.";
            if ($status !== 'bad') $status = 'warning';
        } else {
            $recommendations[] = "🟢 File size is good ({$fileSizeKB}KB).";
        }
        
        // Check dimensions
        if ($imageInfo) {
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            if ($width > 1200 || $height > 1200) {
                $recommendations[] = "🔴 Image dimensions too large ({$width}x{$height}). Resize to under 1200x1200.";
                $status = 'bad';
            } elseif ($width > 800 || $height > 800) {
                $recommendations[] = "🟡 Image dimensions are large ({$width}x{$height}). Consider resizing.";
                if ($status !== 'bad') $status = 'warning';
            } else {
                $recommendations[] = "🟢 Image dimensions are good ({$width}x{$height}).";
            }
        }
        
        $uploadMessage = [
            'status' => $status,
            'recommendations' => $recommendations,
            'fileSize' => $fileSizeKB,
            'fileSizeMB' => $fileSizeMB,
            'dimensions' => $imageInfo ? $imageInfo[0] . 'x' . $imageInfo[1] : 'Unknown'
        ];
    } else {
        $uploadMessage = [
            'status' => 'error',
            'recommendations' => ['Error uploading file. Please try again.']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Size Checker - NBT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../assert/black.png">
</head>
<body class="bg-purple-50 font-sans min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-lg font-semibold text-purple-900">NBT Admin - Image Checker</div>
                <div>
                    <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
                    <a href="health_check.php" class="text-purple-900 hover:text-yellow-500 px-4">Health Check</a>
                    <a href="logout.php" class="bg-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto p-6 mt-20">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-purple-800 mb-4">📸 Image Size Checker</h1>
            <p class="text-gray-600">Check if your images are optimized before uploading to the website</p>
        </div>

        <!-- Quick Guidelines -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-purple-800 mb-4">📋 Quick Guidelines</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-800">✅ Good</h3>
                    <ul class="text-sm text-green-700 mt-2">
                        <li>• File size: Under 200KB</li>
                        <li>• Dimensions: Under 800x800px</li>
                        <li>• Format: JPG or PNG</li>
                    </ul>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-yellow-800">⚠️ Acceptable</h3>
                    <ul class="text-sm text-yellow-700 mt-2">
                        <li>• File size: 200KB - 500KB</li>
                        <li>• Dimensions: 800x800 - 1200x1200px</li>
                        <li>• Consider compressing</li>
                    </ul>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-red-800">❌ Too Large</h3>
                    <ul class="text-sm text-red-700 mt-2">
                        <li>• File size: Over 500KB</li>
                        <li>• Dimensions: Over 1200x1200px</li>
                        <li>• Must compress first!</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Image Upload Test -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-purple-800 mb-4">🔍 Test Your Image</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select an image to test (this won't be uploaded to your website):
                    </label>
                    <input type="file" 
                           name="test_image" 
                           accept="image/*" 
                           required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                </div>
                <button type="submit" 
                        class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                    🔍 Check Image
                </button>
            </form>
        </div>

        <!-- Results -->
        <?php if ($uploadMessage): ?>
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-purple-800 mb-4">📊 Analysis Results</h2>
            
            <?php if ($uploadMessage['status'] === 'good'): ?>
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <h3 class="text-lg font-semibold text-green-800">🎉 Perfect! Your image is ready to upload!</h3>
            </div>
            <?php elseif ($uploadMessage['status'] === 'warning'): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <h3 class="text-lg font-semibold text-yellow-800">⚠️ Image is acceptable but could be optimized</h3>
            </div>
            <?php else: ?>
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <h3 class="text-lg font-semibold text-red-800">❌ Image needs optimization before upload</h3>
            </div>
            <?php endif; ?>

            <?php if ($uploadMessage['status'] !== 'error'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 p-4 rounded">
                    <strong>File Size:</strong> <?= $uploadMessage['fileSize'] ?>KB (<?= $uploadMessage['fileSizeMB'] ?>MB)
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <strong>Dimensions:</strong> <?= $uploadMessage['dimensions'] ?> pixels
                </div>
            </div>
            <?php endif; ?>

            <div class="space-y-2">
                <?php foreach ($uploadMessage['recommendations'] as $recommendation): ?>
                <p class="text-sm"><?= $recommendation ?></p>
                <?php endforeach; ?>
            </div>

            <?php if ($uploadMessage['status'] === 'bad' || $uploadMessage['status'] === 'warning'): ?>
            <div class="mt-4 p-4 bg-blue-50 rounded">
                <h4 class="font-semibold text-blue-800 mb-2">💡 How to optimize your image:</h4>
                <ol class="text-sm text-blue-700 space-y-1">
                    <li>1. Go to <a href="https://tinypng.com" target="_blank" class="underline font-semibold">TinyPNG.com</a> (free tool)</li>
                    <li>2. Upload your image there</li>
                    <li>3. Download the compressed version</li>
                    <li>4. Test the compressed image here again</li>
                    <li>5. Once it shows green, you can upload to your website!</li>
                </ol>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Helpful Tools -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-purple-800 mb-4">🛠️ Helpful Tools</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Image Compression</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• <a href="https://tinypng.com" target="_blank" class="text-blue-500 hover:underline">TinyPNG.com</a> (Recommended)</li>
                        <li>• <a href="https://compressor.io" target="_blank" class="text-blue-500 hover:underline">Compressor.io</a></li>
                        <li>• <a href="https://optimizilla.com" target="_blank" class="text-blue-500 hover:underline">Optimizilla.com</a></li>
                    </ul>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Quick Actions</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• <a href="health_check.php" class="text-purple-500 hover:underline">Check Website Speed</a></li>
                        <li>• <a href="../cache/cleanup.php" class="text-green-500 hover:underline">Clean Image Cache</a></li>
                        <li>• <a href="dashboard.php" class="text-gray-500 hover:underline">Back to Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
