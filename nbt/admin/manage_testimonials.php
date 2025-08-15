<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch testimonials
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO testimonials (name, email, message, rating, course_name, company, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'], $_POST['email'], $_POST['message'], $_POST['rating'],
            $_POST['course_name'], $_POST['company'], 
            isset($_POST['is_featured']) ? 1 : 0,
            isset($_POST['is_active']) ? 1 : 0
        ]);
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, email = ?, message = ?, rating = ?, course_name = ?, company = ?, is_featured = ?, is_active = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'], $_POST['email'], $_POST['message'], $_POST['rating'],
            $_POST['course_name'], $_POST['company'], 
            isset($_POST['is_featured']) ? 1 : 0,
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['id']
        ]);
    } elseif (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
    header('Location: manage_testimonials.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assert/black.png" type="image/x-icon" />
</head>
<body class="bg-purple-50 font-sans min-h-screen">
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

    <div class="max-w-7xl mx-auto p-6 pt-24">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Testimonials</h1>

        <!-- Add Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-10">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Add New Testimonial</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="name" placeholder="Full Name" required class="border border-purple-300 p-2 rounded" />
                <input type="email" name="email" placeholder="Email Address" class="border border-purple-300 p-2 rounded" />
                <input type="text" name="course_name" placeholder="Course Name (optional)" class="border border-purple-300 p-2 rounded" />
                <input type="text" name="company" placeholder="Company (optional)" class="border border-purple-300 p-2 rounded" />
                <select name="rating" required class="border border-purple-300 p-2 rounded">
                    <option value="">Select Rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" class="mr-2">
                        <span class="text-sm">Featured</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" checked class="mr-2">
                        <span class="text-sm">Active</span>
                    </label>
                </div>
                <div class="col-span-2">
                    <textarea name="message" placeholder="Testimonial Message" required rows="4" class="w-full border border-purple-300 p-2 rounded"></textarea>
                </div>
                <div class="col-span-2 text-right">
                    <button type="submit" name="add" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Testimonial</button>
                </div>
            </form>
        </div>

        <!-- Testimonials List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Testimonials List (<?= count($testimonials) ?>)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800">
                            <th class="p-2 border">#</th>
                            <th class="p-2 border">Name</th>
                            <th class="p-2 border">Rating</th>
                            <th class="p-2 border">Course/Company</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2 border"><?= $testimonial['id'] ?></td>
                                <td class="p-2 border">
                                    <div class="font-medium"><?= htmlspecialchars($testimonial['name']) ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($testimonial['email']) ?></div>
                                </td>
                                <td class="p-2 border text-center">
                                    <div class="text-yellow-500">
                                        <?= str_repeat('★', $testimonial['rating']) . str_repeat('☆', 5 - $testimonial['rating']) ?>
                                    </div>
                                </td>
                                <td class="p-2 border">
                                    <?php if ($testimonial['course_name']): ?>
                                        <div class="text-sm font-medium"><?= htmlspecialchars($testimonial['course_name']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($testimonial['company']): ?>
                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($testimonial['company']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border text-center">
                                    <?php if ($testimonial['is_featured']): ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Featured</span>
                                    <?php endif; ?>
                                    <span class="bg-<?= $testimonial['is_active'] ? 'green' : 'red' ?>-100 text-<?= $testimonial['is_active'] ? 'green' : 'red' ?>-800 px-2 py-1 rounded text-xs">
                                        <?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="p-2 border space-x-2">
                                    <button onclick="toggleEdit(<?= $testimonial['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $testimonial['id'] ?>">
                                        <button type="submit" name="delete" onclick="return confirm('Delete this testimonial?')" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Form -->
                            <tr id="edit-<?= $testimonial['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="6">
                                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                        <input type="hidden" name="id" value="<?= $testimonial['id'] ?>">
                                        <input type="text" name="name" value="<?= htmlspecialchars($testimonial['name']) ?>" class="border p-2 rounded" placeholder="Full Name" required />
                                        <input type="email" name="email" value="<?= htmlspecialchars($testimonial['email']) ?>" class="border p-2 rounded" placeholder="Email" />
                                        <input type="text" name="course_name" value="<?= htmlspecialchars($testimonial['course_name']) ?>" class="border p-2 rounded" placeholder="Course Name" />
                                        <input type="text" name="company" value="<?= htmlspecialchars($testimonial['company']) ?>" class="border p-2 rounded" placeholder="Company" />
                                        <select name="rating" required class="border p-2 rounded">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?= $i ?>" <?= $testimonial['rating'] == $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <div class="flex items-center space-x-4">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="is_featured" <?= $testimonial['is_featured'] ? 'checked' : '' ?> class="mr-2">
                                                <span class="text-sm">Featured</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="is_active" <?= $testimonial['is_active'] ? 'checked' : '' ?> class="mr-2">
                                                <span class="text-sm">Active</span>
                                            </label>
                                        </div>
                                        <div class="col-span-2">
                                            <textarea name="message" required rows="3" class="w-full border p-2 rounded" placeholder="Testimonial Message"><?= htmlspecialchars($testimonial['message']) ?></textarea>
                                        </div>
                                        <div class="col-span-2 text-right space-x-2">
                                            <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
                                            <button type="button" onclick="toggleEdit(<?= $testimonial['id'] ?>)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit(id) {
            const editRow = document.getElementById('edit-' + id);
            editRow.classList.toggle('hidden');
        }
    </script>
</body>
</html>
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
        <h2 class="text-3xl font-bold text-purple-900 mb-6">Manage Testimonials</h2>
        <!-- Add Testimonial Form -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Add New Testimonial</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="add" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Message</label>
                        <textarea name="message" required class="w-full px-4 py-2 border border-purple-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Rating</label>
                        <input type="number" name="rating" min="1" max="5" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                </div>
                <button type="submit" class="bg-yellow-500 text-purple-900 py-2 px-6 rounded-lg font-semibold hover:bg-yellow-600">Add Testimonial</button>
            </form>
        </div>
        <!-- Testimonial List -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Testimonial List</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-purple-900">
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <tr class="border-t">
                            <td class="py-2"><?php echo htmlspecialchars($testimonial['name']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($testimonial['rating']); ?></td>
                            <td class="py-2">
                                <button onclick="editTestimonial(<?php echo $testimonial['id']; ?>)" class="text-yellow-500 hover:text-yellow-600">Edit</button>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="delete" value="1">
                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function editTestimonial(id) {
            alert('Edit functionality to be implemented with a modal or separate form');
        }
    </script>
</body>
</html>