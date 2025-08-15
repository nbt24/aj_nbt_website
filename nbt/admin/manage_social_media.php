
<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Add new social media entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO social_media (platform, followers, platform_url, is_active) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['platform'],
        $_POST['followers'],
        $_POST['platform_url'],
        isset($_POST['is_active']) ? 1 : 0
    ]);
    header("Location: manage_social_media.php");
    exit;
}

// Delete social media entry
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM social_media WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_social_media.php");
    exit;
}

// Edit social media entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("UPDATE social_media SET platform = ?, followers = ?, platform_url = ?, is_active = ? WHERE id = ?");
    $stmt->execute([
        $_POST['platform'],
        $_POST['followers'],
        $_POST['platform_url'],
        isset($_POST['is_active']) ? 1 : 0,
        $id
    ]);
    header("Location: manage_social_media.php");
    exit;
}

// Fetch social media entries
$stmt = $pdo->query("SELECT * FROM social_media ORDER BY id DESC");
$social_media = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Social Media</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../assert/black.png">

</head>
<body class="bg-purple-50 font-sans">
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
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Social Media</h1>

        <!-- Add Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-10">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Add New Social Media</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="platform" placeholder="Platform (e.g., Facebook)" required class="border border-purple-300 p-2 rounded" />
                <input type="number" name="followers" placeholder="Followers (e.g., 10000)" required class="border border-purple-300 p-2 rounded" />
                <input type="url" name="platform_url" placeholder="Platform URL" class="border border-purple-300 p-2 rounded" />
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" checked class="mr-2">
                        <span class="text-sm">Active</span>
                    </label>
                </div>
                <div class="col-span-2 text-right">
                    <button type="submit" name="add" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Social Media</button>
                </div>
            </form>
        </div>

        <!-- Social Media List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Social Media List</h2>
            <table class="min-w-full table-auto text-sm border">
                <thead>
                    <tr class="bg-purple-100 text-purple-800">
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Platform</th>
                        <th class="p-2 border">Followers</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($social_media as $sm): ?>
                        <tr class="border-t">
                            <td class="p-2 border"><?= htmlspecialchars($sm['id']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($sm['platform']) ?></td>
                            <td class="p-2 border"><?= number_format($sm['followers']) ?></td>
                            <td class="p-2 border space-x-2">
                                <button onclick="toggleEdit(<?= $sm['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                                <a href="?delete=<?= $sm['id'] ?>" onclick="return confirm('Delete this social media entry?')" class="bg-red-600 text-white px-2 py-1 rounded">Delete</a>
                            </td>
                        </tr>
                        <!-- Edit Form -->
                        <tr id="edit-<?= $sm['id'] ?>" class="hidden bg-gray-50">
                            <td colspan="4">
                                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                    <input type="hidden" name="id" value="<?= $sm['id'] ?>">
                                    <input type="text" name="platform" value="<?= htmlspecialchars($sm['platform']) ?>" required class="border p-2 rounded" />
                                    <input type="number" name="followers" value="<?= htmlspecialchars($sm['followers']) ?>" required class="border p-2 rounded" />
                                    <div class="col-span-2 text-right">
                                        <button type="submit" name="edit" class="bg-green-600 text-white px-4 py-2 rounded">Update Social Media</button>
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
