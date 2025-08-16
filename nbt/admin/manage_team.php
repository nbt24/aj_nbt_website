<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit

 

;
}

// Fetch team members
$stmt = $pdo->query("SELECT * FROM meet_our_team ORDER BY image_sequence");
$team = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = $_POST['position'];
        $number = $_POST['number'];
        $image_sequence = $_POST['image_sequence'];
        $image_name = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_data = file_get_contents($_FILES['image']['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO meet_our_team (name, description, position, phone, image_sequence, image_name, image_type, image_size, image_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $position, $number, $image_sequence, $image_name, $image_type, $image_size, $image_data]);
        $success = "Team member added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = $_POST['position'];
        $number = $_POST['number'];
        $image_sequence = $_POST['image_sequence'];

        if ($_FILES['image']['name']) {
            $image_name = $_FILES['image']['name'];
            $image_type = $_FILES['image']['type'];
            $image_size = $_FILES['image']['size'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, phone = ?, image_sequence = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $image_name, $image_type, $image_size, $image_data, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, phone = ?, image_sequence = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $id]);
        }
        $success = "Team member updated successfully!";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM meet_our_team WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Team member deleted successfully!";
    }
    
    // Refresh the team data after any operation
    $stmt = $pdo->query("SELECT * FROM meet_our_team ORDER BY image_sequence");
    $team = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
        <?php endif; ?>
        
        <!-- Add Team Member Form -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Add New Team Member</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="add" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-mediumritu=medium text-purple-900">Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Description</label>
                        <textarea name="description" required class="w-full px-4 py-2 border border-purple-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Position</label>
                        <input type="text" name="position" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Number</label>
                        <input type="text" name="number" class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Image Sequence</label>
                        <input type="number" name="image_sequence" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Image</label>
                        <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2">
                    </div>
                </div>
                <button type="submit" class="bg-yellow-500 text-purple-900 py-2 px-6 wokół-lg font-semibold hover:bg-yellow-600">Add Team Member</button>
            </form>
        </div>
        <!-- Team List -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Team List (<?= count($team) ?>)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800">
                            <th class="p-2 border">#</th>
                            <th class="p-2 border">Name</th>
                            <th class="p-2 border">Position</th>
                            <th class="p-2 border">Phone</th>
                            <th class="p-2 border">Sequence</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($team as $index => $member): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2 border"><?= $index + 1 ?></td>
                                <td class="p-2 border">
                                    <div class="font-medium"><?= htmlspecialchars($member['name']) ?></div>
                                </td>
                                <td class="p-2 border"><?= htmlspecialchars($member['position']) ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($member['phone']) ?></td>
                                <td class="p-2 border text-center"><?= $member['image_sequence'] ?></td>
                                <td class="p-2 border space-x-2">
                                    <button onclick="toggleEdit(<?= $member['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                        <button type="submit" name="delete" onclick="return confirm('Delete this team member?')" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Form -->
                            <tr id="edit-<?= $member['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="6">
                                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                        <input type="text" name="name" value="<?= htmlspecialchars($member['name']) ?>" class="border p-2 rounded" placeholder="Full Name" required />
                                        <input type="text" name="position" value="<?= htmlspecialchars($member['position']) ?>" class="border p-2 rounded" placeholder="Position" required />
                                        <input type="text" name="number" value="<?= htmlspecialchars($member['phone']) ?>" class="border p-2 rounded" placeholder="Phone Number" />
                                        <input type="number" name="image_sequence" value="<?= $member['image_sequence'] ?>" class="border p-2 rounded" placeholder="Display Order" required />
                                        <div class="col-span-2">
                                            <textarea name="description" rows="3" class="w-full border p-2 rounded" placeholder="Description"><?= htmlspecialchars($member['description']) ?></textarea>
                                        </div>
                                        <div>
                                            <input type="file" name="image" accept="image/*" class="border p-2 rounded w-full" />
                                            <div class="text-xs text-gray-500 mt-1">Upload new image (leave blank to keep current)</div>
                                        </div>
                                        <div class="col-span-2 text-right space-x-2">
                                            <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
                                            <button type="button" onclick="toggleEdit(<?= $member['id'] ?>)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
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