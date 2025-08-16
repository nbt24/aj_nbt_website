<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Create table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS youtube_videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_url VARCHAR(255) NOT NULL,
    sequence_number INT NOT NULL,
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_sequence (sequence_number)
)";
$pdo->exec($createTable);

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_video'])) {
        $stmt = $pdo->prepare("INSERT INTO youtube_videos (video_url, sequence_number, title) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['video_url'], $_POST['sequence_number'], $_POST['title']]);
        $success = "Video added successfully!";
    }
    
    if (isset($_POST['update_video'])) {
        $stmt = $pdo->prepare("UPDATE youtube_videos SET video_url = ?, sequence_number = ?, title = ? WHERE id = ?");
        $stmt->execute([$_POST['video_url'], $_POST['sequence_number'], $_POST['title'], $_POST['id']]);
        $success = "Video updated successfully!";
    }
    
    if (isset($_POST['delete_video'])) {
        $stmt = $pdo->prepare("DELETE FROM youtube_videos WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = "Video deleted successfully!";
    }
}

// Fetch all videos
$videos = $pdo->query("SELECT * FROM youtube_videos ORDER BY sequence_number")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage YouTube Videos - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-purple-50">
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-lg font-semibold text-purple-900">NBT Admin</div>
                <div>
                    <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
                    <a href="logout.php" class="bg-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        <h2 class="text-3xl font-bold text-purple-900 mb-6">Manage YouTube Videos</h2>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
        <?php endif; ?>
        
        <!-- Add Video Form -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Add New YouTube Video</h3>
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-purple-900 mb-2">YouTube URL:</label>
                        <input type="url" name="video_url" required 
                               placeholder="https://www.youtube.com/watch?v=VIDEO_ID"
                               class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900 mb-2">Sequence Number:</label>
                        <input type="number" name="sequence_number" required min="1"
                               class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-purple-900 mb-2">Video Title:</label>
                    <input type="text" name="title" required
                           class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <button type="submit" name="add_video" 
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Add Video
                </button>
            </form>
        </div>
        
        <!-- Videos List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-purple-200">
                <h3 class="text-xl font-semibold text-purple-900">Current Videos</h3>
            </div>
            
            <?php if (empty($videos)): ?>
                <div class="p-6 text-center text-gray-500">
                    No videos found. Add your first YouTube video above.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-purple-200">
                        <thead class="bg-purple-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-purple-900 uppercase tracking-wider">Sequence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-purple-900 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-purple-900 uppercase tracking-wider">URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-purple-900 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-purple-200">
                            <?php foreach ($videos as $video): ?>
                            <tr class="hover:bg-purple-50">
                                <form method="POST" class="contents">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="sequence_number" value="<?= $video['sequence_number'] ?>" min="1" 
                                               class="w-20 px-2 py-1 border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="title" value="<?= htmlspecialchars($video['title']) ?>" 
                                               class="w-full px-2 py-1 border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="url" name="video_url" value="<?= htmlspecialchars($video['video_url']) ?>" 
                                               class="w-full px-2 py-1 border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="hidden" name="id" value="<?= $video['id'] ?>">
                                        <button type="submit" name="update_video" 
                                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 mr-2">
                                            Update
                                        </button>
                                        <button type="submit" name="delete_video" 
                                                onclick="return confirm('Delete this video?')"
                                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                            Delete
                                        </button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
