<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch admin users
$stmt = $pdo->query("SELECT id, email, name, created_at FROM admin ORDER BY created_at DESC");
$admins = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin (email, password, name) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['email'], $password_hash, $_POST['name']]);
        $success = "Admin user created successfully!";
    } elseif (isset($_POST['update'])) {
        if (!empty($_POST['password'])) {
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin SET email = ?, password = ?, name = ? WHERE id = ?");
            $stmt->execute([$_POST['email'], $password_hash, $_POST['name'], $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin SET email = ?, name = ? WHERE id = ?");
            $stmt->execute([$_POST['email'], $_POST['name'], $_POST['id']]);
        }
        $success = "Admin user updated successfully!";
    } elseif (isset($_POST['delete'])) {
        if ($_POST['id'] != $_SESSION['admin_id']) {
            $stmt = $pdo->prepare("DELETE FROM admin WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Admin user deleted successfully!";
        } else {
            $error = "You cannot delete your own account!";
        }
    }
    
    // Refresh the list
    $stmt = $pdo->query("SELECT id, email, name, created_at FROM admin ORDER BY created_at DESC");
    $admins = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Users - NBT</title>
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
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Admin Users</h1>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <!-- Add Admin Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-10">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Add New Admin User</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="name" placeholder="Full Name" required class="border border-purple-300 p-2 rounded" />
                <input type="email" name="email" placeholder="Email Address" required class="border border-purple-300 p-2 rounded" />
                <input type="password" name="password" placeholder="Password" required class="border border-purple-300 p-2 rounded" />
                <div class="col-span-1 md:col-span-2 text-right">
                    <button type="submit" name="add" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Admin User</button>
                </div>
            </form>
        </div>

        <!-- Admin Users List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Admin Users List (<?= count($admins) ?>)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800">
                            <th class="p-2 border">#</th>
                            <th class="p-2 border">Name</th>
                            <th class="p-2 border">Email</th>
                            <th class="p-2 border">Created At</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2 border"><?= $admin['id'] ?></td>
                                <td class="p-2 border font-medium">
                                    <?= htmlspecialchars($admin['name']) ?>
                                    <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">You</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border"><?= htmlspecialchars($admin['email']) ?></td>
                                <td class="p-2 border text-xs text-gray-500"><?= date('M j, Y', strtotime($admin['created_at'])) ?></td>
                                <td class="p-2 border space-x-2">
                                    <button onclick="toggleEdit(<?= $admin['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                            <button type="submit" name="delete" onclick="return confirm('Delete this admin user?')" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <!-- Edit Form -->
                            <tr id="edit-<?= $admin['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="5">
                                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                        <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" class="border p-2 rounded" placeholder="Full Name" required />
                                        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" class="border p-2 rounded" placeholder="Email" required />
                                        <input type="password" name="password" class="border p-2 rounded" placeholder="New Password (leave blank to keep current)" />
                                        <div class="col-span-1 md:col-span-2 text-right space-x-2">
                                            <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
                                            <button type="button" onclick="toggleEdit(<?= $admin['id'] ?>)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
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
