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
    $pdo->exec("ALTER TABLE courses MODIFY COLUMN description TEXT");
    $pdo->exec("ALTER TABLE courses MODIFY COLUMN link TEXT");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    $pdo->exec("ALTER TABLE courses ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
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

            <!-- Course Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-purple-200">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-900 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-purple-900">
                        <?= $editCourse ? 'Edit Course' : 'Add New Course' ?>
                    </h2>
                </div>

                <form method="POST" class="space-y-6">
                    <?php if ($editCourse): ?>
                        <input type="hidden" name="id" value="<?= $editCourse['id'] ?>">
                    <?php endif; ?>

                    <!-- Row 1: Logo & Banner URLs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Course Logo URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="logo_url" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['logo_url']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="https://example.com/logo.jpg">
                            <p class="text-sm text-purple-600 mt-1">Square logo image (recommended: 200x200px)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Course Banner URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="banner_url" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['banner_url']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="https://example.com/banner.jpg">
                            <p class="text-sm text-purple-600 mt-1">Landscape banner (recommended: 800x400px)</p>
                        </div>
                    </div>

                    <!-- Row 2: Title & Instructor -->
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
                                Instructor Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="instructor" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['instructor']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="John Doe">
                        </div>
                    </div>

                    <!-- Row 3: Description -->
                    <div>
                        <label class="block text-sm font-semibold text-purple-900 mb-2">
                            Course Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" required rows="4"
                            class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                            placeholder="Comprehensive description of the course content, learning outcomes, and what students will achieve..."><?= $editCourse ? htmlspecialchars($editCourse['description']) : '' ?></textarea>
                    </div>

                    <!-- Row 4: Category & Difficulty -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category" required
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                <option value="">Select Category</option>
                                <option value="Web Development" <?= ($editCourse && $editCourse['category'] == 'Web Development') ? 'selected' : '' ?>>Web Development</option>
                                <option value="Data Science" <?= ($editCourse && $editCourse['category'] == 'Data Science') ? 'selected' : '' ?>>Data Science</option>
                                <option value="Mobile Development" <?= ($editCourse && $editCourse['category'] == 'Mobile Development') ? 'selected' : '' ?>>Mobile Development</option>
                                <option value="Cloud Computing" <?= ($editCourse && $editCourse['category'] == 'Cloud Computing') ? 'selected' : '' ?>>Cloud Computing</option>
                                <option value="DevOps" <?= ($editCourse && $editCourse['category'] == 'DevOps') ? 'selected' : '' ?>>DevOps</option>
                                <option value="AI/ML" <?= ($editCourse && $editCourse['category'] == 'AI/ML') ? 'selected' : '' ?>>AI/ML</option>
                                <option value="Cybersecurity" <?= ($editCourse && $editCourse['category'] == 'Cybersecurity') ? 'selected' : '' ?>>Cybersecurity</option>
                                <option value="Other" <?= ($editCourse && $editCourse['category'] == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Difficulty Level <span class="text-red-500">*</span>
                            </label>
                            <select name="difficulty" required
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                <option value="">Select Difficulty</option>
                                <option value="Beginner" <?= ($editCourse && $editCourse['difficulty'] == 'Beginner') ? 'selected' : '' ?>>Beginner</option>
                                <option value="Intermediate" <?= ($editCourse && $editCourse['difficulty'] == 'Intermediate') ? 'selected' : '' ?>>Intermediate</option>
                                <option value="Advanced" <?= ($editCourse && $editCourse['difficulty'] == 'Advanced') ? 'selected' : '' ?>>Advanced</option>
                                <option value="Expert" <?= ($editCourse && $editCourse['difficulty'] == 'Expert') ? 'selected' : '' ?>>Expert</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Duration & Students -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Duration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="duration" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['duration']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="8 weeks, 40 hours, 3 months">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Enrolled Students <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="people" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['people']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="1200+, 500 students, 2.5k">
                        </div>
                    </div>

                    <!-- Row 6: Rating & Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Rating <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="rating" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['rating']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="4.8, 4.5/5, 5 stars">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="price" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['price']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="$299, ₹19,999, Free">
                        </div>
                    </div>

                    <!-- Row 7: Course Link & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Course Link <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="link" required
                                value="<?= $editCourse ? htmlspecialchars($editCourse['link']) : '' ?>"
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                placeholder="https://academy.nbt.com/course/web-development">
                            <p class="text-sm text-purple-600 mt-1">Direct link to the course page</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-purple-900 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required
                                class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                <option value="active" <?= ($editCourse && $editCourse['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="draft" <?= ($editCourse && $editCourse['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                                <option value="coming_soon" <?= ($editCourse && $editCourse['status'] == 'coming_soon') ? 'selected' : '' ?>>Coming Soon</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t border-purple-200">
                        <?php if ($editCourse): ?>
                            <a href="manage_courses.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold mr-4 hover:bg-gray-400 transition-colors">Cancel</a>
                            <button type="submit" name="edit" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-purple-800 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                Update Course
                            </button>
                        <?php else: ?>
                            <button type="submit" name="add" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-purple-800 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                Add Course
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
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
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Category</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Instructor</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-purple-100">
                                <?php foreach ($courses as $course): ?>
                                    <tr class="hover:bg-purple-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <?php if (!empty($course['logo_url'])): ?>
                                                    <img src="<?= htmlspecialchars($course['logo_url']) ?>" alt="Logo" class="w-12 h-12 rounded-lg object-cover mr-4">
                                                <?php else: ?>
                                                    <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center mr-4">
                                                        <span class="text-purple-600 font-semibold"><?= htmlspecialchars(substr($course['title'], 0, 1)) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="font-semibold text-purple-900"><?= htmlspecialchars($course['title']) ?></div>
                                                    <div class="text-sm text-purple-600"><?= htmlspecialchars($course['duration']) ?> • <?= htmlspecialchars($course['people']) ?> students</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <?= htmlspecialchars($course['category']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-purple-900"><?= htmlspecialchars($course['instructor']) ?></td>
                                        <td class="px-6 py-4">
                                            <?php 
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'draft' => 'bg-yellow-100 text-yellow-800',
                                                'coming_soon' => 'bg-blue-100 text-blue-800'
                                            ];
                                            $statusClass = $statusColors[$course['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                                <?= ucfirst(str_replace('_', ' ', $course['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <a href="?edit=<?= $course['id'] ?>" class="text-purple-600 hover:text-purple-900 font-medium">Edit</a>
                                                <a href="?delete=<?= $course['id'] ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this course?')"
                                                   class="text-red-600 hover:text-red-900 font-medium">Delete</a>
                                                <?php if (!empty($course['link'])): ?>
                                                    <a href="<?= htmlspecialchars($course['link']) ?>" target="_blank" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                                                <?php endif; ?>
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
    </div>

    <!-- Course Preview Cards Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-purple-200">
            <h3 class="text-2xl font-bold text-purple-900 mb-6">Course Preview Cards</h3>
            <p class="text-purple-700 mb-8">Professional course cards that will be displayed on your website</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($courses as $course): ?>
                    <?php if ($course['status'] === 'active'): ?>
                        <div onclick="window.open('<?= htmlspecialchars($course['link']) ?>', '_blank')" 
                             class="group cursor-pointer transform hover:scale-105 transition-all duration-300">
                            <div class="bg-gradient-to-br from-white to-purple-50 rounded-2xl shadow-lg hover:shadow-2xl border border-purple-200 overflow-hidden">
                                <!-- Banner Image -->
                                <?php if (!empty($course['banner_url'])): ?>
                                    <div class="h-48 overflow-hidden">
                                        <img src="<?= htmlspecialchars($course['banner_url']) ?>" 
                                             alt="Course Banner" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <?php if (!empty($course['logo_url'])): ?>
                                            <img src="<?= htmlspecialchars($course['logo_url']) ?>" 
                                                 alt="Course Logo" 
                                                 class="w-12 h-12 rounded-lg object-cover">
                                        <?php endif; ?>
                                        <div class="flex items-center space-x-2">
                                            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                                <?= htmlspecialchars($course['difficulty']) ?>
                                            </span>
                                            <div class="flex items-center text-yellow-500">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($course['rating']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Title & Category -->
                                    <h4 class="text-lg font-bold text-purple-900 mb-2 group-hover:text-purple-700 transition-colors">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </h4>
                                    <p class="text-sm text-purple-600 mb-3"><?= htmlspecialchars($course['category']) ?></p>
                                    
                                    <!-- Description -->
                                    <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                                        <?= htmlspecialchars(substr($course['description'], 0, 120)) ?>...
                                    </p>
                                    
                                    <!-- Instructor -->
                                    <div class="flex items-center mb-4">
                                        <div class="w-8 h-8 bg-purple-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-purple-600 font-semibold text-sm">
                                                <?= htmlspecialchars(substr($course['instructor'], 0, 1)) ?>
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700">by <?= htmlspecialchars($course['instructor']) ?></span>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="flex items-center justify-between pt-4 border-t border-purple-100">
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span><?= htmlspecialchars($course['duration']) ?></span>
                                            <span>•</span>
                                            <span><?= htmlspecialchars($course['people']) ?></span>
                                        </div>
                                        <span class="text-lg font-bold text-purple-900"><?= htmlspecialchars($course['price']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
