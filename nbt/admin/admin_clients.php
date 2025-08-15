<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM client WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle Add or Update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_id'])) {
        // Update
        if (!empty($_FILES['company_logo']['tmp_name'])) {
            $company_logo = file_get_contents($_FILES['company_logo']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE client SET company_name=?, company_logo=?, status=?, task=? WHERE id = ?");
            $stmt->execute([$_POST['company_name'], $company_logo, $_POST['status'], $_POST['task'], $_POST['update_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE client SET company_name=?, status=?, task=? WHERE id = ?");
            $stmt->execute([$_POST['company_name'], $_POST['status'], $_POST['task'], $_POST['update_id']]);
        }
    } else {
        // Insert
        $company_logo = null;
        if (!empty($_FILES['company_logo']['tmp_name'])) {
            $company_logo = file_get_contents($_FILES['company_logo']['tmp_name']);
        }
        $stmt = $pdo->prepare("INSERT INTO client (company_name, company_logo, status, task) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['company_name'], $company_logo, $_POST['status'], $_POST['task']]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch Records
$stmt = $pdo->query("SELECT * FROM client ORDER BY id DESC");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Clients - NBT</title>
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
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Clients</h1>

        <!-- Add Client Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-10">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Add New Client</h2>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="company_name" placeholder="Company Name" required class="border border-purple-300 p-2 rounded" />
                <select name="status" required class="border border-purple-300 p-2 rounded">
                    <option value="">Select Project Status</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <div class="col-span-1">
                    <input type="file" name="company_logo" accept="image/*" class="border border-purple-300 p-2 rounded w-full" />
                    <div class="text-xs text-gray-500 mt-1">Upload company logo (optional)</div>
                </div>
                <div></div>
                <div class="col-span-2">
                    <textarea name="task" placeholder="Project Description" rows="3" required class="w-full border border-purple-300 p-2 rounded"></textarea>
                </div>
                <div class="col-span-2 text-right">
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Client</button>
                </div>
            </form>
        </div>

        <!-- Clients List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Clients List (<?= count($clients) ?>)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800">
                            <th class="p-2 border">#</th>
                            <th class="p-2 border">Company Logo</th>
                            <th class="p-2 border">Company Name</th>
                            <th class="p-2 border">Project Status</th>
                            <th class="p-2 border">Project Description</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2 border text-center"><?= $client['id'] ?></td>
                                <td class="p-2 border text-center">
                                    <?php if (!empty($client['company_logo'])): ?>
                                        <img src="data:image/jpeg;base64,<?= base64_encode($client['company_logo']) ?>" alt="Logo" class="h-12 w-12 object-cover rounded mx-auto">
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">No Logo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border font-medium"><?= htmlspecialchars($client['company_name']) ?></td>
                                <td class="p-2 border text-center">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        <?php 
                                        switch($client['status']) {
                                            case 'active': echo 'bg-green-100 text-green-800'; break;
                                            case 'completed': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= ucfirst($client['status']) ?>
                                    </span>
                                </td>
                                <td class="p-2 border">
                                    <div class="max-w-xs truncate"><?= htmlspecialchars($client['task']) ?></div>
                                </td>
                                <td class="p-2 border space-x-2">
                                    <button onclick="toggleEdit(<?= $client['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                                    <a href="?delete=<?= $client['id'] ?>" onclick="return confirm('Delete this client?')" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Delete</a>
                                </td>
                            </tr>
                            <!-- Edit Form -->
                            <tr id="edit-<?= $client['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="6">
                                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                        <input type="hidden" name="update_id" value="<?= $client['id'] ?>">
                                        <input type="text" name="company_name" value="<?= htmlspecialchars($client['company_name']) ?>" class="border p-2 rounded" placeholder="Company Name" required />
                                        <select name="status" required class="border p-2 rounded">
                                            <option value="active" <?= $client['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="completed" <?= $client['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="pending" <?= $client['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="cancelled" <?= $client['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <div>
                                            <input type="file" name="company_logo" accept="image/*" class="border p-2 rounded w-full" />
                                            <div class="text-xs text-gray-500 mt-1">Upload new logo (leave blank to keep current)</div>
                                        </div>
                                        <div></div>
                                        <div class="col-span-2">
                                            <textarea name="task" rows="3" class="w-full border p-2 rounded" placeholder="Project Description" required><?= htmlspecialchars($client['task']) ?></textarea>
                                        </div>
                                        <div class="col-span-2 text-right space-x-2">
                                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
                                            <button type="button" onclick="toggleEdit(<?= $client['id'] ?>)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
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
