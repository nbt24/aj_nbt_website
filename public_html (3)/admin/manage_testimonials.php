<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch testimonials
$stmt = $pdo->query("SELECT * FROM testimonials");
$testimonials = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        $rating = $_POST['rating'];

        $stmt = $pdo->prepare("INSERT INTO testimonials (name, email, message, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $message, $rating]);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        $rating = $_POST['rating'];

        $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, email = ?, message = ?, rating = ? WHERE id = ?");
        $stmt->execute([$name, $email, $message, $rating, $id]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
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
     <link rel="icon" href="./asset/black.png" type="image/x-icon" />
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