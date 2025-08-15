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
        $linkedin = $_POST['linkedin'];
        $email = $_POST['email'];
        $image_name = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_data = file_get_contents($_FILES['image']['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO meet_our_team (name, description, position, number, image_sequence, linkedin, email, image_name, image_type, image_size, image_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $position, $number, $image_sequence, $linkedin, $email, $image_name, $image_type, $image_size, $image_data]);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $position = $_POST['position'];
        $number = $_POST['number'];
        $image_sequence = $_POST['image_sequence'];
        $linkedin = $_POST['linkedin'];
        $email = $_POST['email'];

        if ($_FILES['image']['name']) {
            $image_name = $_FILES['image']['name'];
            $image_type = $_FILES['image']['type'];
            $image_size = $_FILES['image']['size'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, number = ?, image_sequence = ?, linkedin = ?, email = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $linkedin, $email, $image_name, $image_type, $image_size, $image_data, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, number = ?, image_sequence = ?, linkedin = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $linkedin, $email, $id]);
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM meet_our_team WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: manage_team.php');
    exit;
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
                        <label class="block text-sm font-medium text-purple-900">LinkedIn</label>
                        <input type="text" name="linkedin" class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border border-purple-300 rounded-lg">
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
            <h3 class="text-xl font-semibold text-purple-900 mb-4">Team List</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-purple-900">
                        <th>Name</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team as $member): ?>
                        <tr class="border-t">
                            <td class="py-2"><?php echo htmlspecialchars($member['name']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($member['position']); ?></td>
                            <td class="py-2">
                                <button onclick="editTeam(<?php echo $member['id']; ?>)" class="text-yellow-500 hover:text-yellow-600">Edit</button>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="delete" value="1">
                                    <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
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
        function editTeam(id) {
            alert('Edit functionality to be implemented with a modal or separate form');
        }
    </script>
</body>
</html>