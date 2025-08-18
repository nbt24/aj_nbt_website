<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Update database schema for simplified course structure
try {
    // Add the new required columns if they don't exist
    $columns_to_add = [
        'banner_image' => 'TEXT',
        'title' => 'VARCHAR(255)',
        'description' => 'TEXT',
        'duration' => 'VARCHAR(100)',
        'rating' => 'DECIMAL(3,2)',
        'enrolled_students' => 'INT',
        'price' => 'DECIMAL(10,2)',
        'course_link' => 'TEXT'
    ];
    
    foreach ($columns_to_add as $column => $type) {
        try {
            $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS $column $type");
        } catch (PDOException $e) {
            // Column might already exist
        }
    }
} catch (PDOException $e) {
    // Handle database errors
}

// Add new course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $banner_image = '';
    
    // Handle banner image upload
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_extension = pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path)) {
            $banner_image = 'uploads/' . $new_filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO courses (banner_image, title, description, duration, rating, enrolled_students, price, course_link) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $banner_image,
        $_POST['title'],
        $_POST['description'],
        $_POST['duration'],
        $_POST['rating'],
        $_POST['enrolled_students'],
        $_POST['price'],
        $_POST['course_link']
    ]);
    $success = "Course added successfully!";
}

// Edit course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $banner_image = $_POST['existing_banner'];
    
    // Handle new banner image upload
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_extension = pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path)) {
            $banner_image = 'uploads/' . $new_filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE courses SET banner_image = ?, title = ?, description = ?, duration = ?, rating = ?, enrolled_students = ?, price = ?, course_link = ? WHERE id = ?");
    $stmt->execute([
        $banner_image,
        $_POST['title'],
        $_POST['description'],
        $_POST['duration'],
        $_POST['rating'],
        $_POST['enrolled_students'],
        $_POST['price'],
        $_POST['course_link'],
        $id
    ]);
    $success = "Course updated successfully!";
}

// Delete course
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "Course deleted successfully!";
}

// Get course for editing
$editCourse = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCourse = $stmt->fetch();
}

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - NBT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-purple-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-purple-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-purple-900 hover:text-purple-700 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-bold text-purple-900">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        Manage Courses
                    </h1>
                </div>
                <a href="logout.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Success Message -->
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i><?= $success ?>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Course Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-purple-200 mb-8">
            <div class="p-8 border-b border-purple-200">
                <h2 class="text-2xl font-bold text-purple-900">
                    <?= $editCourse ? 'Edit Course' : 'Add New Course' ?>
                </h2>
                <p class="text-purple-700 mt-1">
                    <?= $editCourse ? 'Update course information' : 'Create a new course for your platform' ?>
                </p>
            </div>

            <div class="p-8">
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <?php if ($editCourse): ?>
                        <input type="hidden" name="id" value="<?= $editCourse['id'] ?>">
                        <input type="hidden" name="existing_banner" value="<?= htmlspecialchars($editCourse['banner_image']) ?>">
                    <?php endif; ?>

                    <!-- Banner Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-purple-900 mb-2">
                            Course Banner Image <span class="text-red-500">*</span>
                        </label>
                        <?php if ($editCourse && !empty($editCourse['banner_image'])): ?>
                            <div class="mb-4">
                                <img src="../<?= htmlspecialchars($editCourse['banner_image']) ?>" 
                                     alt="Current Banner" 
                                     class="w-48 h-32 object-cover rounded-lg border border-purple-300">
                                <p class="text-sm text-purple-600 mt-1">Current banner image</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="banner_image" accept="image/*" 
                               <?= !$editCourse ? 'required' : '' ?>
                               class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        <p class="text-sm text-purple-600 mt-1">Upload a high-quality banner image (recommended: 1200x600px)</p>
                    </div>

                    <!-- Title and Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Course Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['title']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="Complete Web Development Bootcamp">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Duration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="duration" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['duration']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="8 weeks">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-purple-900 mb-2">
                            Course Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" required rows="4"
                            class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                            placeholder="Comprehensive description of the course content, learning outcomes, and what students will achieve..."><?= $editCourse ? htmlspecialchars($editCourse['description']) : '' ?></textarea>
                    </div>

                    <!-- Rating, Students, and Price -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Rating <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="rating" required step="0.1" min="0" max="5"
                                value="<?= $editCourse ? htmlspecialchars($editCourse['rating']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="4.8">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Enrolled Students <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="enrolled_students" required min="0"
                                value="<?= $editCourse ? htmlspecialchars($editCourse['enrolled_students']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="1200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price" required step="0.01" min="0"
                                value="<?= $editCourse ? htmlspecialchars($editCourse['price']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="99.99">
                        </div>
                    </div>

                    <!-- Course Link -->
                    <div>
                        <label class="block text-sm font-semibold text-purple-900 mb-2">
                            Course Link <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="course_link" required
                            value="<?= $editCourse ? htmlspecialchars($editCourse['course_link']) : '' ?>"
                            class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                            placeholder="https://courses.nextbiggtech.com/web-development">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-purple-200">
                        <a href="manage_courses.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold mr-4 hover:bg-gray-400 transition-colors">Cancel</a>
                        <button type="submit" name="<?= $editCourse ? 'edit' : 'add' ?>" 
                                class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>
                            <?= $editCourse ? 'Update Course' : 'Add Course' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Courses List -->
        <div class="bg-white rounded-2xl shadow-xl border border-purple-200">
            <div class="p-8 border-b border-purple-200">
                <h3 class="text-2xl font-bold text-purple-900">Existing Courses</h3>
                <p class="text-purple-700 mt-1">Manage your course catalog</p>
            </div>

            <?php if (empty($courses)): ?>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-purple-900 mb-2">No Courses Yet</h4>
                    <p class="text-purple-600">Add your first course using the form above</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-purple-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Course</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Duration</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Students</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Rating</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Price</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100">
                            <?php foreach ($courses as $course): ?>
                                <tr class="hover:bg-purple-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($course['banner_image'])): ?>
                                                <img src="../<?= htmlspecialchars($course['banner_image']) ?>" alt="Banner" class="w-16 h-10 rounded-lg object-cover mr-4">
                                            <?php else: ?>
                                                <div class="w-16 h-10 bg-purple-200 rounded-lg flex items-center justify-center mr-4">
                                                    <span class="text-purple-600 font-semibold text-xs">IMG</span>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="font-semibold text-purple-900"><?= htmlspecialchars($course['title']) ?></div>
                                                <div class="text-sm text-purple-600"><?= htmlspecialchars(substr($course['description'], 0, 50)) ?>...</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-purple-700"><?= htmlspecialchars($course['duration']) ?></td>
                                    <td class="px-6 py-4 text-sm text-purple-700"><?= number_format($course['enrolled_students']) ?></td>
                                    <td class="px-6 py-4 text-sm text-purple-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                                            <?= number_format($course['rating'], 1) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-purple-700">$<?= number_format($course['price'], 2) ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center space-x-2">
                                            <a href="?edit=<?= $course['id'] ?>" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <a href="?delete=<?= $course['id'] ?>" 
                                               onclick="return confirm('Are you sure you want to delete this course?')"
                                               class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </a>
                                        </div>
                                    </td>
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
