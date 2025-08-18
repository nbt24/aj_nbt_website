<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = $_POST['position'];
        $image_sequence = $_POST['image_sequence'];
        $image_name = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_data = file_get_contents($_FILES['image']['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO meet_our_team (name, description, position, image_sequence, image_name, image_type, image_size, image_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $position, $image_sequence, $image_name, $image_type, $image_size, $image_data]);
        $success = "Team member added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = $_POST['position'];
        $image_sequence = $_POST['image_sequence'];

        if ($_FILES['image']['name']) {
            $image_name = $_FILES['image']['name'];
            $image_type = $_FILES['image']['type'];
            $image_size = $_FILES['image']['size'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, image_sequence = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $image_sequence, $image_name, $image_type, $image_size, $image_data, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, image_sequence = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $image_sequence, $id]);
        }
        $success = "Team member updated successfully!";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM meet_our_team WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Team member deleted successfully!";
    }

    header('Location: manage_team.php');
    exit;
}

// Fetch team members
$stmt = $pdo->query("SELECT * FROM meet_our_team ORDER BY image_sequence");
$team = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assert/black.png" type="image/x-icon" />
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
        <h2 class="text-3xl font-bold text-purple-900 mb-6">Manage Team</h2>
        
        <?php if (isset($success)): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">✅ <?= $success ?></div>
        <?php endif; ?>

        <!-- Add Team Member Form -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Add Team Member</h3>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="add" value="1">
                <div>
                    <label class="block text-sm font-medium text-purple-900 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-purple-900 mb-1">Position</label>
                    <input type="text" name="position" required class="w-full px-4 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-purple-900 mb-1">Image Sequence</label>
                    <input type="number" name="image_sequence" required class="w-full px-4 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-purple-900 mb-1">Description</label>
                    <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Enter bullet points separated by • or new lines"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-purple-900 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition duration-300">Add Team Member</button>
                </div>
            </form>
        </div>

        <!-- Team Members List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-purple-600 text-white">
                <h3 class="text-xl font-semibold">Current Team Members</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-purple-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-purple-900">#</th>
                            <th class="px-4 py-3 text-left text-purple-900">Image</th>
                            <th class="px-4 py-3 text-left text-purple-900">Name</th>
                            <th class="px-4 py-3 text-left text-purple-900">Position</th>
                            <th class="px-4 py-3 text-left text-purple-900">Sequence</th>
                            <th class="px-4 py-3 text-left text-purple-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($team as $index => $member): ?>
                            <tr class="border-b hover:bg-purple-50">
                                <td class="px-4 py-3"><?= $index + 1 ?></td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($member['image_data'])): ?>
                                        <img src="data:<?= $member['image_type'] ?>;base64,<?= base64_encode($member['image_data']) ?>" 
                                             alt="<?= htmlspecialchars($member['name']) ?>" 
                                             class="w-12 h-12 object-cover rounded-full">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">No Image</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-purple-900"><?= htmlspecialchars($member['name']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-purple-700"><?= htmlspecialchars($member['position']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm"><?= $member['image_sequence'] ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button onclick="editTeamMember(<?= $member['id'] ?>)" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Edit</button>
                                        <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this team member?')">
                                            <input type="hidden" name="delete" value="1">
                                            <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Edit Form (Hidden by default) -->
                            <tr id="edit-form-<?= $member['id'] ?>" class="edit-form hidden">
                                <td colspan="6" class="px-4 py-4 bg-purple-50">
                                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input type="hidden" name="update" value="1">
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                        <input type="text" name="name" value="<?= htmlspecialchars($member['name']) ?>" class="border p-2 rounded" placeholder="Name" required />
                                        <input type="text" name="position" value="<?= htmlspecialchars($member['position']) ?>" class="border p-2 rounded" placeholder="Position" required />
                                        <input type="number" name="image_sequence" value="<?= $member['image_sequence'] ?>" class="border p-2 rounded" placeholder="Display Order" required />
                                        <input type="file" name="image" accept="image/*" class="border p-2 rounded" />
                                        <div class="flex space-x-2">
                                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Update</button>
                                            <button type="button" onclick="cancelEdit(<?= $member['id'] ?>)" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                                        </div>
                                        <div class="md:col-span-3">
                                            <textarea name="description" rows="3" class="w-full border p-2 rounded" placeholder="Description"><?= htmlspecialchars($member['description']) ?></textarea>
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
        function editTeamMember(id) {
            // Hide all edit forms
            document.querySelectorAll('.edit-form').forEach(form => form.classList.add('hidden'));
            // Show the specific edit form
            document.getElementById('edit-form-' + id).classList.remove('hidden');
        }

        function cancelEdit(id) {
            document.getElementById('edit-form-' + id).classList.add('hidden');
        }
    </script>
</body>
</html>
