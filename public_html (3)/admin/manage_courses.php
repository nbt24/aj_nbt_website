<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
  header('Location: index.php');
  exit;
}

// Add new course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $image = null;
  if (!empty($_FILES['image']['tmp_name'])) {
    $image = file_get_contents($_FILES['image']['tmp_name']);
  }

  $stmt = $pdo->prepare("INSERT INTO courses (title, image, type, description_1, description_2, educator, timeline, people, rating, link)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $_POST['title'], $image, $_POST['type'], $_POST['description_1'],
    $_POST['description_2'], $_POST['educator'], $_POST['timeline'],
    $_POST['people'], $_POST['rating'], $_POST['link']
  ]);
  header("Location: manage_courses.php");
  exit;
}

// Delete course
if (isset($_GET['delete'])) {
  $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
  $stmt->execute([$_GET['delete']]);
  header("Location: manage_courses.php");
  exit;
}

// Edit course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
  $id = $_POST['id'];

  // If a new image is uploaded, use it; otherwise, keep the existing one
  if (!empty($_FILES['image']['tmp_name'])) {
    $image = file_get_contents($_FILES['image']['tmp_name']);
    $stmt = $pdo->prepare("UPDATE courses SET title=?, image=?, type=?, description_1=?, description_2=?, educator=?, timeline=?, people=?, rating=?, link=? WHERE id=?");
    $stmt->execute([
      $_POST['title'], $image, $_POST['type'], $_POST['description_1'],
      $_POST['description_2'], $_POST['educator'], $_POST['timeline'],
      $_POST['people'], $_POST['rating'], $_POST['link'], $id
    ]);
  } else {
    $stmt = $pdo->prepare("UPDATE courses SET title=?, type=?, description_1=?, description_2=?, educator=?, timeline=?, people=?, rating=?, link=? WHERE id=?");
    $stmt->execute([
      $_POST['title'], $_POST['type'], $_POST['description_1'],
      $_POST['description_2'], $_POST['educator'], $_POST['timeline'],
      $_POST['people'], $_POST['rating'], $_POST['link'], $id
    ]);
  }
  header("Location: manage_courses.php");
  exit;
}

// Fetch courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Courses</title>
  <script src="https://cdn.tailwindcss.com"></script>
   <link rel="icon" href="./asset/black.png" type="image/x-icon" />
</head>
<body class="bg-purple-50 font-sans">
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
