<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch services
$stmt = $pdo->query("SELECT * FROM our_services");
$services = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $points = $_POST['points'];
        $price = $_POST['price'];
        $image_name = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_data = file_get_contents($_FILES['image']['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO our_services (title, description, points, price, image_name, image_type, image_size, image_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $points, $price, $image_name, $image_type, $image_size, $image_data]);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $points = $_POST['points'];
        $price = $_POST['price'];

        if ($_FILES['image']['name']) {
            $image_name = $_FILES['image']['name'];
            $image_type = $_FILES['image']['type'];
            $image_size = $_FILES['image']['size'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE our_services SET title = ?, description = ?, points = ?, price = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ? WHERE id = ?");
            $stmt->execute([$title, $description, $points, $price, $image_name, $image_type, $image_size, $image_data, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE our_services SET title = ?, description = ?, points = ?, price = ? WHERE id = ?");
            $stmt->execute([$title, $description, $points, $price, $id]);
        }
        } elseif (isset($_POST['delete'])) {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM our_services WHERE id = ?");
            $stmt->execute([$id]);
        }
        header('Location: manage_services.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <link rel="icon" href="./asset/black.png" type="image/x-icon" />
</head>
<body class="min-h-screen bg-purple-50">
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-lg font-semibold text-purple-900">NBT Admin</div>
                <div>
                    <a href="dashboard.php" class="text-purple-900 hover采访:text-yellow-500 px-4">Dashboard</a>
                    <a href="logout.php" class="bg-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        <h2 class="text-3xl font-bold text-purple-900 mb-6">Manage Services</h2>
        <!-- Add Service Form -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Add New Service</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="add" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-purple
    
    -900">Title</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Description</label>
                        <textarea name="description" required class="w-full px-4 py-2 border border-purple-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Points (comma-separated)</label>
                        <textarea name="points" required class="w-full px-4 py-2 border border-purple-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Price</label>
                        <input type="number" step="0.01" name="price" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Image</label>
                        <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2">
                    </div>
                </div>
                <button type="submit" class="bg-yellow-500 text-purple-900 py-2 px-6 rounded-lg font-semibold hover:bg-yellow-600">Add Service</button>
            </form>
        </div>
        <!-- Service List -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Service List</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-purple-900">
                        <th>Title</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr class="border-t" id="row-<?php echo $service['id']; ?>">
                            <td class="py-2">
                                <div id="view-title-<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['title']); ?></div>
                                <div id="edit-title-<?php echo $service['id']; ?>" class="hidden">
                                    <input type="text" id="input-title-<?php echo $service['id']; ?>" value="<?php echo htmlspecialchars($service['title']); ?>" class="border rounded px-2 py-1 w-full">
                                </div>
                                
                                <div id="view-desc-<?php echo $service['id']; ?>" class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($service['description'], 0, 50)); ?>...</div>
                                <div id="edit-desc-<?php echo $service['id']; ?>" class="hidden mt-1">
                                    <textarea id="input-desc-<?php echo $service['id']; ?>" class="border rounded px-2 py-1 w-full text-sm" rows="2"><?php echo htmlspecialchars($service['description']); ?></textarea>
                                </div>
                                
                                <div id="view-points-<?php echo $service['id']; ?>" class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($service['points'], 0, 30)); ?>...</div>
                                <div id="edit-points-<?php echo $service['id']; ?>" class="hidden mt-1">
                                    <textarea id="input-points-<?php echo $service['id']; ?>" class="border rounded px-2 py-1 w-full text-sm" rows="2"><?php echo htmlspecialchars($service['points']); ?></textarea>
                                </div>
                            </td>
                            <td class="py-2">
                                <div id="view-price-<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['price']); ?></div>
                                <div id="edit-price-<?php echo $service['id']; ?>" class="hidden">
                                    <input type="text" id="input-price-<?php echo $service['id']; ?>" value="<?php echo htmlspecialchars($service['price']); ?>" class="border rounded px-2 py-1 w-full">
                                </div>
                            </td>
                            <td class="py-2">
                                <div id="view-buttons-<?php echo $service['id']; ?>">
                                    <button onclick="toggleEdit(<?php echo $service['id']; ?>)" class="text-yellow-500 hover:text-yellow-600 mr-2">Edit</button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?')">
                                        <input type="hidden" name="delete" value="1">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-600">Delete</button>
                                    </form>
                                </div>
                                <div id="edit-buttons-<?php echo $service['id']; ?>" class="hidden">
                                    <button onclick="saveEdit(<?php echo $service['id']; ?>)" class="text-green-500 hover:text-green-600 mr-2">Save</button>
                                    <button onclick="cancelEdit(<?php echo $service['id']; ?>)" class="text-gray-500 hover:text-gray-600">Cancel</button>
                                </div>
                                
                                <form id="edit-form-<?php echo $service['id']; ?>" method="POST" class="hidden">
                                    <input type="hidden" name="update" value="1">
                                    <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                    <input type="hidden" id="form-title-<?php echo $service['id']; ?>" name="title">
                                    <input type="hidden" id="form-desc-<?php echo $service['id']; ?>" name="description">
                                    <input type="hidden" id="form-points-<?php echo $service['id']; ?>" name="points">
                                    <input type="hidden" id="form-price-<?php echo $service['id']; ?>" name="price">
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
            // Hide view elements
            document.getElementById('view-title-' + id).classList.add('hidden');
            document.getElementById('view-desc-' + id).classList.add('hidden');
            document.getElementById('view-points-' + id).classList.add('hidden');
            document.getElementById('view-price-' + id).classList.add('hidden');
            document.getElementById('view-buttons-' + id).classList.add('hidden');
            
            // Show edit elements
            document.getElementById('edit-title-' + id).classList.remove('hidden');
            document.getElementById('edit-desc-' + id).classList.remove('hidden');
            document.getElementById('edit-points-' + id).classList.remove('hidden');
            document.getElementById('edit-price-' + id).classList.remove('hidden');
            document.getElementById('edit-buttons-' + id).classList.remove('hidden');
        }

        function cancelEdit(id) {
            // Show view elements
            document.getElementById('view-title-' + id).classList.remove('hidden');
            document.getElementById('view-desc-' + id).classList.remove('hidden');
            document.getElementById('view-points-' + id).classList.remove('hidden');
            document.getElementById('view-price-' + id).classList.remove('hidden');
            document.getElementById('view-buttons-' + id).classList.remove('hidden');
            
            // Hide edit elements
            document.getElementById('edit-title-' + id).classList.add('hidden');
            document.getElementById('edit-desc-' + id).classList.add('hidden');
            document.getElementById('edit-points-' + id).classList.add('hidden');
            document.getElementById('edit-price-' + id).classList.add('hidden');
            document.getElementById('edit-buttons-' + id).classList.add('hidden');
        }

        function saveEdit(id) {
            // Get values from input fields
            const title = document.getElementById('input-title-' + id).value;
            const description = document.getElementById('input-desc-' + id).value;
            const points = document.getElementById('input-points-' + id).value;
            const price = document.getElementById('input-price-' + id).value;
            
            // Set values in hidden form
            document.getElementById('form-title-' + id).value = title;
            document.getElementById('form-desc-' + id).value = description;
            document.getElementById('form-points-' + id).value = points;
            document.getElementById('form-price-' + id).value = price;
            
            // Submit the form
            document.getElementById('edit-form-' + id).submit();
        }

        // Show success message if update was successful
        <?php if (isset($_POST['update'])): ?>
            alert('Service updated successfully!');
        <?php endif; ?>
    </script>
</body>
</html>