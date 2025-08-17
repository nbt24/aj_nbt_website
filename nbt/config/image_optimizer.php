<?php
/**
 * Automatic Image Compression System
 * Compresses images on upload without quality degradation
 */

class ImageOptimizer {
    private $maxWidth = 1920;        // Max width for large images
    private $maxHeight = 1080;       // Max height for large images
    private $jpegQuality = 90;       // High quality JPEG (90-95 is optimal)
    private $pngCompression = 6;     // PNG compression level (0-9, 6 is good balance)
    private $webpQuality = 90;       // WebP quality
    
    /**
     * Optimize uploaded image automatically
     */
    public function optimizeUploadedImage($uploadedFile, $targetPath) {
        if (!$this->isValidImage($uploadedFile)) {
            return false;
        }
        
        // Get image info
        $imageInfo = getimagesize($uploadedFile['tmp_name']);
        if (!$imageInfo) {
            return false;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Create image resource from uploaded file
        $sourceImage = $this->createImageFromFile($uploadedFile['tmp_name'], $mimeType);
        if (!$sourceImage) {
            return false;
        }
        
        // Calculate new dimensions (maintain aspect ratio)
        $dimensions = $this->calculateOptimalDimensions($originalWidth, $originalHeight);
        
        // Create optimized image
        $optimizedImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($optimizedImage, false);
            imagesavealpha($optimizedImage, true);
            $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
            imagefill($optimizedImage, 0, 0, $transparent);
        }
        
        // Resize image with high quality
        imagecopyresampled(
            $optimizedImage, $sourceImage,
            0, 0, 0, 0,
            $dimensions['width'], $dimensions['height'],
            $originalWidth, $originalHeight
        );
        
        // Save optimized image
        $result = $this->saveOptimizedImage($optimizedImage, $targetPath, $mimeType);
        
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($optimizedImage);
        
        return $result;
    }
    
    /**
     * Check if uploaded file is a valid image
     */
    private function isValidImage($uploadedFile) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($uploadedFile['tmp_name']);
        return in_array($fileType, $allowedTypes);
    }
    
    /**
     * Create image resource from file
     */
    private function createImageFromFile($filePath, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }
    
    /**
     * Calculate optimal dimensions maintaining aspect ratio
     */
    private function calculateOptimalDimensions($originalWidth, $originalHeight) {
        $width = $originalWidth;
        $height = $originalHeight;
        
        // Only resize if image is larger than max dimensions
        if ($width > $this->maxWidth || $height > $this->maxHeight) {
            $aspectRatio = $width / $height;
            
            if ($width > $height) {
                $width = $this->maxWidth;
                $height = $this->maxWidth / $aspectRatio;
            } else {
                $height = $this->maxHeight;
                $width = $this->maxHeight * $aspectRatio;
            }
        }
        
        return [
            'width' => round($width),
            'height' => round($height)
        ];
    }
    
    /**
     * Save optimized image with appropriate compression
     */
    private function saveOptimizedImage($imageResource, $targetPath, $mimeType) {
        $directory = dirname($targetPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagejpeg($imageResource, $targetPath, $this->jpegQuality);
            case 'image/png':
                return imagepng($imageResource, $targetPath, $this->pngCompression);
            case 'image/gif':
                return imagegif($imageResource, $targetPath);
            case 'image/webp':
                return imagewebp($imageResource, $targetPath, $this->webpQuality);
            default:
                return false;
        }
    }
    
    /**
     * Get file size reduction percentage
     */
    public function getCompressionStats($originalFile, $optimizedFile) {
        if (!file_exists($originalFile) || !file_exists($optimizedFile)) {
            return null;
        }
        
        $originalSize = filesize($originalFile);
        $optimizedSize = filesize($optimizedFile);
        $reduction = (($originalSize - $optimizedSize) / $originalSize) * 100;
        
        return [
            'original_size' => $this->formatFileSize($originalSize),
            'optimized_size' => $this->formatFileSize($optimizedSize),
            'reduction_percent' => round($reduction, 1),
            'saved_bytes' => $originalSize - $optimizedSize
        ];
    }
    
    /**
     * Format file size for display
     */
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

/**
 * Helper function to optimize any uploaded image
 */
function optimizeUploadedImage($uploadedFile, $targetPath) {
    $optimizer = new ImageOptimizer();
    return $optimizer->optimizeUploadedImage($uploadedFile, $targetPath);
}

/**
 * Helper function to get compression stats
 */
function getImageCompressionStats($originalFile, $optimizedFile) {
    $optimizer = new ImageOptimizer();
    return $optimizer->getCompressionStats($originalFile, $optimizedFile);
}
?>
