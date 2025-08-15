<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $rating = $_POST['rating'];
    $message = $_POST['message'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = !empty($_FILES['image']['tmp_name']) ? file_get_contents($_FILES['image']['tmp_name']) : null;
    $video = !empty($_FILES['video']['tmp_name']) ? file_get_contents($_FILES['video']['tmp_name']) : null;

    $stmt = $pdo->prepare("INSERT INTO course_testimonials (name, email, course, rating, message, image, video, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $course, $rating, $message, $image, $video, $is_active]);
    header('Location:course_testimonials_admin.php');
    exit;
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $rating = $_POST['rating'];
    $message = $_POST['message'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Get current data
    $stmt = $pdo->prepare("SELECT image, video FROM course_testimonials WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    $image = $current['image'];
    $video = $current['video'];

    // Update image if new one uploaded
    if (!empty($_FILES['image']['tmp_name'])) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Update video if new one uploaded
    if (!empty($_FILES['video']['tmp_name'])) {
        $video = file_get_contents($_FILES['video']['tmp_name']);
    }

    $stmt = $pdo->prepare("UPDATE course_testimonials SET name = ?, email = ?, course = ?, rating = ?, message = ?, image = ?, video = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$name, $email, $course, $rating, $message, $image, $video, $is_active, $id]);

    header('Location: course_testimonials_admin.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM course_testimonials WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: course_testimonials_admin.php');
    exit;
}

// Handle Toggle Status
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $stmt = $pdo->prepare("UPDATE course_testimonials SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: course_testimonials_admin.php');
    exit;
}

// Fetch all testimonials
$testimonials = $pdo->query("SELECT * FROM course_testimonials ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Course Testimonials - Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../assert/black.png">
</head>
<body class="bg-gray-100 text-gray-900">

  <div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-purple-800">Admin Dashboard - Course Testimonials</h1>
      <a href="dashboard.php" class="bg-purple-600 text-white px-4 py-2 rounded shadow">Dashboard</a>
    </div>

    <!-- Add Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-10">
      <h2 class="text-xl font-bold mb-4">Add New Testimonial</h2>
      <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input name="name" required placeholder="Name" class="border p-2 rounded w-full" />
          <input name="email" required placeholder="Email" class="border p-2 rounded w-full" />
          <input name="course" required placeholder="Course" class="border p-2 rounded w-full" />
          <input name="rating" type="number" min="1" max="5" required placeholder="Rating (1-5)" class="border p-2 rounded w-full" />
        </div>
        <textarea name="message" placeholder="Message" class="border p-2 rounded w-full h-24"></textarea>
        <div class="flex items-center gap-4">
          <input type="file" name="image" accept="image/*" />
          <input type="file" name="video" accept="video/*" />
        </div>
        <div class="flex items-center">
          <input type="checkbox" name="is_active" id="is_active" checked class="mr-2">
          <label for="is_active">Active</label>
        </div>
        <button name="add" class="bg-green-600 text-white px-6 py-2 rounded">Add Testimonial</button>
      </form>
    </div>

    <!-- Testimonial List -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold mb-4">All Testimonials</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border">
          <thead>
            <tr class="bg-gray-200">
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-left">Name</th>
              <th class="p-3 text-left">Email</th>
              <th class="p-3 text-left">Course</th>
              <th class="p-3 text-left">Rating</th>
              <th class="p-3 text-left">Message</th>
              <th class="p-3 text-left">Image</th>
              <th class="p-3 text-left">Video</th>
              <th class="p-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($testimonials as $row): ?>
              <tr class="border-t <?= $row['is_active'] ? '' : 'bg-gray-100' ?>">
                <td class="p-3">
                  <a href="?toggle_status=<?= $row['id'] ?>" class="px-2 py-1 rounded <?= $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                  </a>
                </td>
                <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['email']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['course']) ?></td>
                <td class="p-3"><?= $row['rating'] ?></td>
                <td class="p-3"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td class="p-3">
                  <?php if ($row['image']): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" class="w-16 h-16 object-cover rounded" />
                  <?php endif; ?>
                </td>
                <td class="p-3">
                  <?php if ($row['video']): ?>
                    <video src="data:video/mp4;base64,<?= base64_encode($row['video']) ?>" class="w-32 h-20" controls></video>
                  <?php endif; ?>
                </td>
                <td class="p-3 space-x-2">
                  <button onclick='openEditModal({
                    id: <?= $row['id'] ?>,
                    name: "<?= addslashes($row['name']) ?>",
                    email: "<?= addslashes($row['email']) ?>",
                    course: "<?= addslashes($row['course']) ?>",
                    rating: <?= $row['rating'] ?>,
                    message: "<?= addslashes($row['message']) ?>",
                    image: <?= $row['image'] ? '"' . base64_encode($row['image']) . '"' : 'null' ?>,
                    video: <?= $row['video'] ? '"' . base64_encode($row['video']) . '"' : 'null' ?>,
                    is_active: <?= $row['is_active'] ?>
                  })' class="bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                  <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this testimonial?')" class="bg-red-500 text-white px-3 py-1 rounded">Delete</a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl relative">
      <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-500">&times;</button>
      <h2 class="text-xl font-bold mb-4">Edit Testimonial</h2>
      <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="id" id="edit-id" />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input name="name" id="edit-name" required class="border p-2 rounded w-full" />
          <input name="email" id="edit-email" required class="border p-2 rounded w-full" />
          <input name="course" id="edit-course" required class="border p-2 rounded w-full" />
          <input name="rating" id="edit-rating" type="number" min="1" max="5" required class="border p-2 rounded w-full" />
        </div>
        <textarea name="message" id="edit-message" class="border p-2 rounded w-full h-24"></textarea>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div id="current-image-container" class="mb-2">
              <p class="text-sm text-gray-500">Current Image:</p>
              <img id="current-image" class="w-32 h-32 object-cover rounded" />
            </div>
            <input type="file" name="image" id="edit-image" accept="image/*" class="w-full" />
            <p class="text-xs text-gray-500">Leave blank to keep current image</p>
          </div>
          <div>
            <div id="current-video-container" class="mb-2">
              <p class="text-sm text-gray-500">Current Video:</p>
              <video id="current-video" class="w-32 h-32" controls></video>
            </div>
            <input type="file" name="video" id="edit-video" accept="video/*" class="w-full" />
            <p class="text-xs text-gray-500">Leave blank to keep current video</p>
          </div>
        </div>
        
        <div class="flex items-center">
          <input type="checkbox" name="is_active" id="edit-is-active" class="mr-2">
          <label for="edit-is-active">Active</label>
        </div>
        
        <button name="edit" class="bg-blue-600 text-white px-6 py-2 rounded">Update</button>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(data) {
      document.getElementById('edit-id').value = data.id;
      document.getElementById('edit-name').value = data.name;
      document.getElementById('edit-email').value = data.email;
      document.getElementById('edit-course').value = data.course;
      document.getElementById('edit-rating').value = data.rating;
      document.getElementById('edit-message').value = data.message;
      document.getElementById('edit-is-active').checked = data.is_active == 1;
      
      // Handle image preview
      const currentImage = document.getElementById('current-image');
      const currentImageContainer = document.getElementById('current-image-container');
      if (data.image) {
        currentImage.src = `data:image/jpeg;base64,${data.image}`;
        currentImageContainer.style.display = 'block';
      } else {
        currentImageContainer.style.display = 'none';
      }
      
      // Handle video preview
      const currentVideo = document.getElementById('current-video');
      const currentVideoContainer = document.getElementById('current-video-container');
      if (data.video) {
        currentVideo.src = `data:video/mp4;base64,${data.video}`;
        currentVideoContainer.style.display = 'block';
      } else {
        currentVideoContainer.style.display = 'none';
      }
      
      document.getElementById('editModal').classList.remove('hidden');
      document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
      document.getElementById('editModal').classList.remove('flex');
    }
  </script>

</body>
</html>