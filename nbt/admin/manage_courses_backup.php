<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Update database schema for new course structure
try {
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS logo_url TEXT");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS banner_url TEXT");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS instructor VARCHAR(255)");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS category VARCHAR(100)");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS difficulty VARCHAR(50)");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'active'");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS duration VARCHAR(100)");
    $pdo->exec("ALTER TABLE courses MODIFY COLUMN link TEXT");
} catch (PDOException $e) {
    // Columns might already exist
}

// Add new course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO courses (logo_url, banner_url, title, description, instructor, category, duration, difficulty, rating, people, price, link, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_POST['logo_url'],
        $_POST['banner_url'], 
        $_POST['title'],
        $_POST['description'],
        $_POST['instructor'],
        $_POST['category'],
        $_POST['duration'],
        $_POST['difficulty'],
        $_POST['rating'],
        $_POST['people'],
        $_POST['price'],
        $_POST['link'],
        $_POST['status']
    ]);
    $success = "Course added successfully!";
}

// Delete course
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "Course deleted successfully!";
}

// Edit course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE courses SET logo_url=?, banner_url=?, title=?, description=?, instructor=?, category=?, duration=?, difficulty=?, rating=?, people=?, price=?, link=?, status=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([
        $_POST['logo_url'],
        $_POST['banner_url'],
        $_POST['title'],
        $_POST['description'],
        $_POST['instructor'],
        $_POST['category'],
        $_POST['duration'],
        $_POST['difficulty'],
        $_POST['rating'],
        $_POST['people'],
        $_POST['price'],
        $_POST['link'],
        $_POST['status'],
        $_POST['id']
    ]);
    $success = "Course updated successfully!";
}

// Fetch courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get course for editing
$editCourse = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCourse = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses - NBT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assert/black.png" type="image/x-icon" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'purple': {
                            950: '#1a0b2e'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-purple-100 font-sans min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50 border-b-4 border-yellow-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-900 rounded-full flex items-center justify-center">
                        <span class="text-yellow-400 font-bold text-sm">N</span>
                    </div>
                    <span class="text-xl font-bold text-purple-900">NBT Admin</span>
                    <span class="text-purple-600 font-medium">/ Courses</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-purple-900 hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">Dashboard</a>
                    <a href="logout.php" class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:from-yellow-500 hover:to-yellow-600 transform hover:scale-105 transition-all duration-300 shadow-lg">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-purple-900 mb-2">Course Management</h1>
                <p class="text-purple-700">Create and manage professional course offerings with text-based inputs</p>
            </div>

            <!-- Success Message -->
            <?php if (isset($success)): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-green-700 font-medium"><?= htmlspecialchars($success) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
    </nav>
  <div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Courses</h1>

    <!-- Add Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-10">
      <h2 class="text-xl font-semibold mb-4 text-purple-800">Add New Course</h2>
      <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="title" placeholder="Title" required class="border border-purple-300 p-2 rounded" />
        <textarea name="description_1" placeholder="Description 1" class="border border-purple-300 p-2 rounded"></textarea>
        <input type="text" name="type" placeholder="Type" class="border border-purple-300 p-2 rounded" />
        <textarea name="description_2" placeholder="Description 2" class="border border-purple-300 p-2 rounded"></textarea>
        <input type="text" name="educator" placeholder="Educator" class="border border-purple-300 p-2 rounded" />
        <input type="text" name="timeline" placeholder="Timeline" class="border border-purple-300 p-2 rounded" />
        <input type="text" name="people" placeholder="People" class="border border-purple-300 p-2 rounded" />
        <input type="text" name="rating" placeholder="Rating" class="border border-purple-300 p-2 rounded" />
        <input type="text" name="price" placeholder="Price (e.g., $499.00)" class="border border-purple-300 p-2 rounded" />
        <input type="text" name="link" placeholder="Course Link" class="border border-purple-300 p-2 rounded" />
        <input type="file" name="image" class="border border-purple-300 p-2 rounded" />
        <div class="col-span-2 text-right">
          <button type="submit" name="add" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Course</button>
        </div>
      </form>
    </div>

    <!-- Courses List -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold mb-4 text-purple-800">Courses List</h2>
      <table class="min-w-full table-auto text-sm border">
        <thead>
          <tr class="bg-purple-100 text-purple-800">
            <th class="p-2 border">#</th>
            <th class="p-2 border">Title</th>
            <th class="p-2 border">Image</th>
            <th class="p-2 border">Educator</th>
            <th class="p-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($courses as $course): ?>
            <tr class="border-t">
              <td class="p-2 border"><?= $course['id'] ?></td>
              <td class="p-2 border"><?= htmlspecialchars($course['title']) ?></td>
              <td class="p-2 border">
                <?php if (!empty($course['image'])): ?>
                  <img src="data:image/jpeg;base64,<?= base64_encode($course['image']) ?>" alt="Course Image" class="h-10">
                <?php endif; ?>
              </td>
              <td class="p-2 border"><?= htmlspecialchars($course['educator']) ?></td>
              <td class="p-2 border space-x-2">
                <button onclick="toggleEdit(<?= $course['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                <a href="?delete=<?= $course['id'] ?>" onclick="return confirm('Delete this course?')" class="bg-red-600 text-white px-2 py-1 rounded">Delete</a>
              </td>
            </tr>
            <!-- Edit Form -->
            <tr id="edit-<?= $course['id'] ?>" class="hidden bg-gray-50">
              <td colspan="5">
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                  <input type="hidden" name="id" value="<?= $course['id'] ?>">
                  <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" class="border p-2 rounded" />
                  <textarea name="description_1" class="border p-2 rounded"><?= htmlspecialchars($course['description_1']) ?></textarea>
                  <input type="text" name="type" value="<?= htmlspecialchars($course['type']) ?>" class="border p-2 rounded" />
                  <textarea name="description_2" class="border p-2 rounded"><?= htmlspecialchars($course['description_2']) ?></textarea>
                  <input type="text" name="educator" value="<?= htmlspecialchars($course['educator']) ?>" class="border p-2 rounded" />
                  <input type="text" name="timeline" value="<?= htmlspecialchars($course['timeline']) ?>" class="border p-2 rounded" />
                  <input type="text" name="people" value="<?= htmlspecialchars($course['people']) ?>" class="border p-2 rounded" />
                  <input type="text" name="rating" value="<?= htmlspecialchars($course['rating']) ?>" class="border p-2 rounded" />
                  <input type="text" name="price" value="<?= htmlspecialchars($course['price']) ?>" class="border p-2 rounded" placeholder="Price (e.g., $499.00)" />
                  <input type="text" name="link" value="<?= htmlspecialchars($course['link']) ?>" class="border p-2 rounded" />
                  <input type="file" name="image" class="border p-2 rounded" />
                  <div class="col-span-2 text-right">
                    <button type="submit" name="edit" class="bg-green-600 text-white px-4 py-2 rounded">Update Course</button>
                  </div>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function toggleEdit(id) {
      document.getElementById('edit-' + id).classList.toggle('hidden');
    }
  </script>
</body>
</html>
