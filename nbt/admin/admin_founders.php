<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// INSERT
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO founder_card (image_name, image_type, image_size, image_data, name, description, position, number, image_sequence, linkedin, email)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Optimize uploaded image
    $tempOptimized = sys_get_temp_dir() . '/optimized_' . uniqid() . '.jpg';
    if (optimizeUploadedImage($_FILES['image'], $tempOptimized)) {
        $imageData = file_get_contents($tempOptimized);
        unlink($tempOptimized); // Clean up temp file
    } else {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }
    
    $imageName = $_FILES['image']['name'];
    $imageType = $_FILES['image']['type'];
    $imageSize = $_FILES['image']['size'];

    $stmt->execute([
        $imageName, $imageType, $imageSize, $imageData,
        $_POST['name'], $_POST['description'], $_POST['position'],
        $_POST['number'], $_POST['image_sequence'], $_POST['linkedin'],
        $_POST['email']
    ]);
    header("Location: admin_founders.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM founder_card WHERE id = ?")->execute([$id]);
    header("Location: admin_founders.php");
    exit;
}

// UPDATE
if (isset($_POST['edit'])) {
    $id = $_POST['edit_id'];
    if (!empty($_FILES['image']['tmp_name'])) {
        // Optimize uploaded image
        $tempOptimized = sys_get_temp_dir() . '/optimized_' . uniqid() . '.jpg';
        if (optimizeUploadedImage($_FILES['image'], $tempOptimized)) {
            $imageData = file_get_contents($tempOptimized);
            unlink($tempOptimized); // Clean up temp file
        } else {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
        }
        
        $imageName = $_FILES['image']['name'];
        $imageType = $_FILES['image']['type'];
        $imageSize = $_FILES['image']['size'];

        $stmt = $pdo->prepare("UPDATE founder_card SET image_name=?, image_type=?, image_size=?, image_data=?, name=?, description=?, position=?, number=?, image_sequence=?, linkedin=?, email=? WHERE id=?");
        $stmt->execute([
            $imageName, $imageType, $imageSize, $imageData,
            $_POST['name'], $_POST['description'], $_POST['position'],
            $_POST['number'], $_POST['image_sequence'], $_POST['linkedin'],
            $_POST['email'], $id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE founder_card SET name=?, description=?, position=?, number=?, image_sequence=?, linkedin=?, email=? WHERE id=?");
        $stmt->execute([
            $_POST['name'], $_POST['description'], $_POST['position'],
            $_POST['number'], $_POST['image_sequence'], $_POST['linkedin'],
            $_POST['email'], $id
        ]);
    }
    header("Location: admin_founders.php");
    exit;
}

// FETCH ALL
$founders = $pdo->query("SELECT * FROM founder_card ORDER BY image_sequence ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Founder Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../assert/black.png">
</head>
<body class="bg-gray-100 text-gray-800 p-6">
    <!-- Header -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-purple-900">NBT Admin - Founders</h1>
            <div>
                <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
                <a href="logout.php" class="bg-yellow-500 text-purple-900 px-4 py-2 rounded-full hover:bg-yellow-600 font-semibold">Logout</a>
            </div>
        </div>
    </nav>

    <h1 class="text-3xl font-bold text-purple-800 mb-6 mt-16">👨‍💼 Founder Management Panel</h1>

    <!-- Add Founder -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4 text-purple-700">➕ Add New Founder</h2>
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="file" name="image" required class="border p-2 rounded col-span-full">
            <input name="name" placeholder="Name" class="border p-2 rounded" required>
            <input name="position" placeholder="Position" class="border p-2 rounded">
            <input name="number" placeholder="Phone Number" class="border p-2 rounded">
            <input name="linkedin" placeholder="LinkedIn URL" class="border p-2 rounded">
            <input name="email" placeholder="Email" type="email" class="border p-2 rounded">
            <input name="image_sequence" placeholder="Sequence" type="number" class="border p-2 rounded">
            <textarea name="description" placeholder="Description" class="border p-2 rounded col-span-full"></textarea>
            <button type="submit" name="add" class="bg-green-600 text-white px-4 py-2 rounded col-span-full">Add Founder</button>
        </form>
    </div>

    <!-- Founder Table -->
    <div class="bg-white p-6 rounded shadow overflow-auto">
        <h2 class="text-xl font-semibold mb-4 text-purple-700">📋 All Founders</h2>
        <table class="min-w-full border text-sm text-left">
            <thead class="bg-purple-100 text-purple-800">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">Photo</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Position</th>
                    <th class="p-2">Phone</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">LinkedIn</th>
                    <th class="p-2">Sequence</th>
                    <th class="p-2">Description</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($founders as $i => $f): ?>
                    <tr class="border-t hover:bg-purple-50 text-xs">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="edit_id" value="<?= $f['id'] ?>">
                            <td class="p-2"><?= $i + 1 ?></td>
                            <td class="p-2">
                                <?php if ($f['image_data']): ?>
                                    <img src="data:<?= $f['image_type'] ?>;base64,<?= base64_encode($f['image_data']) ?>" width="40" class="rounded-full" />
                                <?php endif; ?>
                                <input type="file" name="image" class="mt-1 text-xs">
                            </td>
                            <td class="p-2"><input name="name" value="<?= htmlspecialchars($f['name']) ?>" class="border p-1 rounded w-full"></td>
                            <td class="p-2"><input name="position" value="<?= htmlspecialchars($f['position']) ?>" class="border p-1 rounded w-full"></td>
                            <td class="p-2"><input name="number" value="<?= htmlspecialchars($f['number']) ?>" class="border p-1 rounded w-full"></td>
                            <td class="p-2"><input name="email" value="<?= htmlspecialchars($f['email']) ?>" class="border p-1 rounded w-full"></td>
                            <td class="p-2"><input name="linkedin" value="<?= htmlspecialchars($f['linkedin']) ?>" class="border p-1 rounded w-full"></td>
                            <td class="p-2"><input name="image_sequence" value="<?= $f['image_sequence'] ?>" type="number" class="border p-1 rounded w-full"></td>
                            <td class="p-2"><textarea name="description" class="border p-1 rounded w-full"><?= htmlspecialchars($f['description']) ?></textarea></td>
                            <td class="p-2 flex flex-col gap-2">
                                <button name="edit" class="bg-blue-500 text-white px-2 py-1 rounded">Save</button>
                                <a href="?delete=<?= $f['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded text-center" onclick="return confirm('Delete this founder?')">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
