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
        $company_logo = null;
        if (!empty($_FILES['company_logo']['tmp_name'])) {
            $company_logo = file_get_contents($_FILES['company_logo']['tmp_name']);
        }
        
        $stmt = $pdo->prepare("INSERT INTO client_testimonials (company_name, company_email, linkedin, project_description, rating, company_logo, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['company_name'], $_POST['company_email'], $_POST['linkedin'], 
            $_POST['project_description'], $_POST['rating'], $company_logo,
            isset($_POST['is_active']) ? 1 : 0
        ]);
        $success = "Client testimonial added successfully!";
    } elseif (isset($_POST['update'])) {
        if (!empty($_FILES['company_logo']['tmp_name'])) {
            $company_logo = file_get_contents($_FILES['company_logo']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE client_testimonials SET company_name = ?, company_email = ?, linkedin = ?, project_description = ?, rating = ?, company_logo = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $_POST['company_name'], $_POST['company_email'], $_POST['linkedin'], 
                $_POST['project_description'], $_POST['rating'], $company_logo,
                isset($_POST['is_active']) ? 1 : 0, $_POST['id']
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE client_testimonials SET company_name = ?, company_email = ?, linkedin = ?, project_description = ?, rating = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $_POST['company_name'], $_POST['company_email'], $_POST['linkedin'], 
                $_POST['project_description'], $_POST['rating'],
                isset($_POST['is_active']) ? 1 : 0, $_POST['id']
            ]);
        }
        $success = "Client testimonial updated successfully!";
    } elseif (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM client_testimonials WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = "Client testimonial deleted successfully!";
    }
}

// Fetch client testimonials
$stmt = $pdo->query("SELECT * FROM client_testimonials ORDER BY created_at DESC");
$client_testimonials = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Client Testimonials - NBT</title>
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
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Client Testimonials</h1>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
        <?php endif; ?>

        <!-- Add Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-10">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Add New Client Testimonial</h2>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="company_name" placeholder="Company Name" required class="border border-purple-300 p-2 rounded" />
                <input type="email" name="company_email" placeholder="Company Email" class="border border-purple-300 p-2 rounded" />
                <input type="url" name="linkedin" placeholder="LinkedIn URL" class="border border-purple-300 p-2 rounded" />
                <select name="rating" required class="border border-purple-300 p-2 rounded">
                    <option value="">Select Rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
                <div class="col-span-2">
                    <textarea name="project_description" placeholder="Project Description" rows="3" class="w-full border border-purple-300 p-2 rounded"></textarea>
                </div>
                <div class="col-span-1">
                    <input type="file" name="company_logo" accept="image/*" class="border border-purple-300 p-2 rounded w-full" />
                    <div class="text-xs text-gray-500 mt-1">Upload company logo (optional)</div>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" checked class="mr-2">
                        <span class="text-sm">Active</span>
                    </label>
                </div>
                <div class="col-span-2 text-right">
                    <button type="submit" name="add" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Client Testimonial</button>
                </div>
            </form>
        </div>

        <!-- Client Testimonials List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Client Testimonials List (<?= count($client_testimonials) ?>)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm border">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800">
                            <th class="p-2 border">#</th>
                            <th class="p-2 border">Company</th>
                            <th class="p-2 border">Logo</th>
                            <th class="p-2 border">Rating</th>
                            <th class="p-2 border">Project</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client_testimonials as $testimonial): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2 border"><?= $testimonial['id'] ?></td>
                                <td class="p-2 border">
                                    <div class="font-medium"><?= htmlspecialchars($testimonial['company_name']) ?></div>
                                    <?php if ($testimonial['company_email']): ?>
                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($testimonial['company_email']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($testimonial['linkedin']): ?>
                                        <a href="<?= htmlspecialchars($testimonial['linkedin']) ?>" target="_blank" class="text-xs text-blue-500 hover:underline">LinkedIn</a>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border text-center">
                                    <?php if (!empty($testimonial['company_logo'])): ?>
                                        <img src="data:image/jpeg;base64,<?= base64_encode($testimonial['company_logo']) ?>" alt="Logo" class="h-10 w-10 object-cover rounded mx-auto">
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">No Logo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border text-center">
                                    <div class="text-yellow-500">
                                        <?= str_repeat('★', $testimonial['rating']) . str_repeat('☆', 5 - $testimonial['rating']) ?>
                                    </div>
                                </td>
                                <td class="p-2 border">
                                    <div class="text-xs max-w-xs truncate"><?= htmlspecialchars($testimonial['project_description']) ?></div>
                                </td>
                                <td class="p-2 border text-center">
                                    <span class="bg-<?= $testimonial['is_active'] ? 'green' : 'red' ?>-100 text-<?= $testimonial['is_active'] ? 'green' : 'red' ?>-800 px-2 py-1 rounded text-xs">
                                        <?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="p-2 border space-x-2">
                                    <button onclick="toggleEdit(<?= $testimonial['id'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $testimonial['id'] ?>">
                                        <button type="submit" name="delete" onclick="return confirm('Delete this testimonial?')" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Edit Form -->
                            <tr id="edit-<?= $testimonial['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="7">
                                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                        <input type="hidden" name="id" value="<?= $testimonial['id'] ?>">
                                        <input type="text" name="company_name" value="<?= htmlspecialchars($testimonial['company_name']) ?>" class="border p-2 rounded" placeholder="Company Name" required />
                                        <input type="email" name="company_email" value="<?= htmlspecialchars($testimonial['company_email']) ?>" class="border p-2 rounded" placeholder="Company Email" />
                                        <input type="url" name="linkedin" value="<?= htmlspecialchars($testimonial['linkedin']) ?>" class="border p-2 rounded" placeholder="LinkedIn URL" />
                                        <select name="rating" required class="border p-2 rounded">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?= $i ?>" <?= $testimonial['rating'] == $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <div class="col-span-2">
                                            <textarea name="project_description" rows="3" class="w-full border p-2 rounded" placeholder="Project Description"><?= htmlspecialchars($testimonial['project_description']) ?></textarea>
                                        </div>
                                        <div>
                                            <input type="file" name="company_logo" accept="image/*" class="border p-2 rounded w-full" />
                                            <div class="text-xs text-gray-500 mt-1">Upload new logo (leave blank to keep current)</div>
                                        </div>
                                        <div class="flex items-center">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="is_active" <?= $testimonial['is_active'] ? 'checked' : '' ?> class="mr-2">
                                                <span class="text-sm">Active</span>
                                            </label>
                                        </div>
                                        <div class="col-span-2 text-right space-x-2">
                                            <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
                                            <button type="button" onclick="toggleEdit(<?= $testimonial['id'] ?>)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
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
