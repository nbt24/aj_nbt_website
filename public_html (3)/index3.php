<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once './config/db.php';

try {
    $stmt = $pdo->query("SELECT title, excerpt, content, image_data, video_data, audio_data, created_at FROM posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $posts = [];
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Blog NBT</title>
  <meta name="description" content="Explore articles, videos, images, and audio content that inspire and inform.">
  
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="/assert/black.png">

  <style>
    body {
      background: linear-gradient(to right, #fdf4ff, #fefce8);
      color: #1f2937;
    }
  </style>
</head>
<body class="font-sans">

<!-- Header -->
<header class="bg-gradient-to-r from-purple-500 via-pink-400 to-yellow-300 py-16 text-center shadow-md">
  <h1 class="text-5xl font-extrabold text-white drop-shadow">Blog NBT</h1>
  <p class="text-lg text-white mt-2">Explore articles, videos, images, and audio content that inspire and inform</p>
</header>

<!-- Nav -->
<nav class="bg-white sticky top-0 shadow z-10">
  <div class="container mx-auto px-4 py-3 text-center font-semibold">
    <a href="/" class="text-purple-700 hover:text-yellow-500">Home</a>
  </div>
</nav>

<!-- Main -->
<main class="container mx-auto px-4 py-12">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
    <?php if (isset($error)): ?>
      <p class="col-span-full text-center text-red-600">Error fetching posts: <?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($posts)): ?>
      <p class="col-span-full text-center text-purple-700">No posts found.</p>
    <?php else: ?>
      <?php foreach ($posts as $index => $row): ?>
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-purple-100 p-5 flex flex-col justify-between">
          
          <!-- Header: Category + Date -->
          <div class="flex justify-between items-center mb-3">
            <span class="text-xs bg-yellow-400 text-white px-2 py-1 rounded-full uppercase font-semibold">Post</span>
            <span class="text-sm text-gray-500"><?= date('F j, Y', strtotime($row['created_at'])) ?></span>
          </div>

          <!-- Title -->
          <h2 class="text-xl font-bold text-purple-900 mb-2"><?= htmlspecialchars($row['title']) ?></h2>

          <!-- Image -->
          <?php if (!empty($row['image_data'])): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($row['image_data']) ?>"
                 alt="<?= htmlspecialchars($row['title']) ?>"
                 class="rounded-lg mb-4 w-full h-48 object-cover" />
          <?php endif; ?>

          <!-- Excerpt -->
          <p class="text-sm italic text-gray-600 mb-3"><?= htmlspecialchars($row['excerpt']) ?></p>

          <!-- Content -->
          <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($row['content'])) ?></p>

          <!-- Video -->
          <?php if (!empty($row['video_data'])): ?>
            <button onclick="openModal(<?= $index ?>)" class="mt-4 bg-purple-600 text-white px-4 py-2 rounded hover:bg-yellow-500 transition">
              ▶️ Watch Video
            </button>
            <div id="modal-<?= $index ?>" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
              <div class="bg-white rounded-xl overflow-hidden max-w-2xl w-full shadow-2xl">
                <div class="p-4 flex justify-between items-center bg-purple-600 text-white">
                  <h3 class="text-lg font-semibold">Now Playing: <?= htmlspecialchars($row['title']) ?></h3>
                  <button onclick="closeModal(<?= $index ?>)" class="text-white text-xl font-bold">✖</button>
                </div>
                <video controls autoplay class="w-full">
                  <source src="data:video/mp4;base64,<?= base64_encode($row['video_data']) ?>" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              </div>
            </div>
          <?php endif; ?>

          <!-- Audio -->
          <?php if (!empty($row['audio_data'])): ?>
            <audio controls class="w-full mt-4">
              <source src="data:audio/mp3;base64,<?= base64_encode($row['audio_data']) ?>" type="audio/mp3">
            </audio>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

<!-- Footer -->
<footer class="bg-gradient-to-r from-purple-100 to-yellow-100 py-6 text-center mt-10">
  <p class="text-purple-700 font-medium">© <?= date('Y') ?> Blog NBT. All rights reserved.</p>
</footer>

<!-- Modal Script -->
<script>
  function openModal(id) {
    document.getElementById('modal-' + id).classList.remove('hidden');
    document.getElementById('modal-' + id).classList.add('flex');
  }

  function closeModal(id) {
    document.getElementById('modal-' + id).classList.add('hidden');
    document.getElementById('modal-' + id).classList.remove('flex');
  }
</script>

</body>
</html>
