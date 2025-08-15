<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch gallery images
$stmt = $pdo->query("SELECT * FROM overview_images ORDER BY image_sequence");
$gallery = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $image_sequence = $_POST['image_sequence'];

        $image_name = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_data = file_get_contents($_FILES['image']['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO overview_images (title, image_sequence, image_name, image_type, image_size, image_data, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $image_sequence, $image_name, $image_type, $image_size, $image_data, isset($_POST['is_active']) ? 1 : 0]);

    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $image_sequence = $_POST['image_sequence'];

        if ($_FILES['image']['name']) {
            $image_name = $_FILES['image']['name'];
            $image_type = $_FILES['image']['type'];
            $image_size = $_FILES['image']['size'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE overview_images SET title = ?, image_sequence = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $image_sequence, $image_name, $image_type, $image_size, $image_data, isset($_POST['is_active']) ? 1 : 0, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE overview_images SET title = ?, image_sequence = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $image_sequence, isset($_POST['is_active']) ? 1 : 0, $id]);
        }

    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM overview_images WHERE id = ?");
        $stmt->execute([$id]);
    }
    // header('Location: manage_gallery.php');
    // exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - NBT</title>
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
        <h2 class="text-3xl font-bold text-purple-900 mb-6">Manage Gallery</h2>


        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded-lg mb-6">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>


        <!-- Add Image Form -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Add New Image</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="add" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Title</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Image Sequence</label>
                        <input type="number" name="image_sequence" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Upload Image</label>
                        <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2">
                    </div>
                </div>
                <button type="submit" class="bg-yellow-500 text-purple-900 py-2 px-6 rounded-lg font-semibold hover:bg-yellow-600">Add Image</button>
            </form>
        </div>

        <!-- Gallery List -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Image List</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-purple-900">
                        <th class="py-2 px-4 text-left">Thumbnail</th>
                        <th>Title</th>
                        <th>Sequence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gallery as $img): ?>
                        <tr class="border-t" id="row-<?php echo $img['id']; ?>">
                            <td class="py-2 px-4">
                                 <img src="data:<?php echo $img['image_type']; ?>;base64,<?php echo base64_encode($img['image_data']); ?>" 
                                    alt="<?php echo $img['image_name']; ?>" 
                                    class="w-24 h-24 object-cover rounded shadow" />

                          </td>
                            <td class="py-2">
                                <div id="view-title-<?php echo $img['id']; ?>"><?php echo htmlspecialchars($img['title']); ?></div>
                                <div id="edit-title-<?php echo $img['id']; ?>" class="hidden">
                                    <input type="text" id="input-title-<?php echo $img['id']; ?>" value="<?php echo htmlspecialchars($img['title']); ?>" class="border rounded px-2 py-1 w-full">
                                </div>
                            </td>
                            <td class="py-2">
                                <div id="view-sequence-<?php echo $img['id']; ?>"><?php echo htmlspecialchars($img['image_sequence']); ?></div>
                                <div id="edit-sequence-<?php echo $img['id']; ?>" class="hidden">
                                    <input type="number" id="input-sequence-<?php echo $img['id']; ?>" value="<?php echo htmlspecialchars($img['image_sequence']); ?>" class="border rounded px-2 py-1 w-full">
                                </div>
                            </td>
                            <td class="py-2">
                                <div id="view-buttons-<?php echo $img['id']; ?>">
                                    <button onclick="toggleEdit(<?php echo $img['id']; ?>)" class="text-yellow-500 hover:text-yellow-600 mr-2">Edit</button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                        <input type="hidden" name="delete" value="1">
                                        <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-600">Delete</button>
                                    </form>
                                </div>
                                <div id="edit-buttons-<?php echo $img['id']; ?>" class="hidden">
                                    <button onclick="saveEdit(<?php echo $img['id']; ?>)" class="text-green-500 hover:text-green-600 mr-2">Save</button>
                                    <button onclick="cancelEdit(<?php echo $img['id']; ?>)" class="text-gray-500 hover:text-gray-600">Cancel</button>
                                </div>
                                
                                <form id="edit-form-<?php echo $img['id']; ?>" method="POST" class="hidden">
                                    <input type="hidden" name="update" value="1">
                                    <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                                    <input type="hidden" id="form-title-<?php echo $img['id']; ?>" name="title">
                                    <input type="hidden" id="form-sequence-<?php echo $img['id']; ?>" name="image_sequence">
                                    <input type="hidden" name="is_active" value="1">
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
            document.getElementById('view-sequence-' + id).classList.add('hidden');
            document.getElementById('view-buttons-' + id).classList.add('hidden');
            
            // Show edit elements
            document.getElementById('edit-title-' + id).classList.remove('hidden');
            document.getElementById('edit-sequence-' + id).classList.remove('hidden');
            document.getElementById('edit-buttons-' + id).classList.remove('hidden');
        }

        function cancelEdit(id) {
            // Show view elements
            document.getElementById('view-title-' + id).classList.remove('hidden');
            document.getElementById('view-sequence-' + id).classList.remove('hidden');
            document.getElementById('view-buttons-' + id).classList.remove('hidden');
            
            // Hide edit elements
            document.getElementById('edit-title-' + id).classList.add('hidden');
            document.getElementById('edit-sequence-' + id).classList.add('hidden');
            document.getElementById('edit-buttons-' + id).classList.add('hidden');
        }

        function saveEdit(id) {
            // Get values from input fields
            const title = document.getElementById('input-title-' + id).value;
            const sequence = document.getElementById('input-sequence-' + id).value;
            
            // Set values in hidden form
            document.getElementById('form-title-' + id).value = title;
            document.getElementById('form-sequence-' + id).value = sequence;
            
            // Submit the form
            document.getElementById('edit-form-' + id).submit();
        }

        // Show success message if update was successful
        <?php if (isset($_POST['update'])): ?>
            alert('Image updated successfully!');
        <?php endif; ?>
    </script>
</body>
</html>
