<?php
session_start();
require '.././config/db.php'; // PDO connection

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Handle Add
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {
    $logoData = null;
    if (!empty($_FILES['company_logo']['tmp_name'])) {
        $logoData = file_get_contents($_FILES['company_logo']['tmp_name']);
    }
    
    $stmt = $pdo->prepare("INSERT INTO client_testimonials (company_name, company_email, linkedin, project_description, rating, company_logo, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['company_name'],
        $_POST['company_email'],
        $_POST['linkedin'],
        $_POST['project_description'],
        $_POST['rating'],
        $logoData,
        isset($_POST['is_active']) ? 1 : 0
    ]);
    $success = "Company testimonial added successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM client_testimonials WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle Edit (Update)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_id'])) {
    $logoData = null;

    if (!empty($_FILES['company_logo']['tmp_name'])) {
        $logoData = file_get_contents($_FILES['company_logo']['tmp_name']);
        $stmt = $pdo->prepare("UPDATE client_testimonials SET 
            company_name = ?, 
            company_email = ?, 
            linkedin = ?, 
            project_description = ?,
            rating = ?, 
            is_active = ?, 
            company_logo = ?
            WHERE id = ?");
        $stmt->execute([
            $_POST['company_name'],
            $_POST['company_email'],
            $_POST['linkedin'],
            $_POST['project_description'],
            $_POST['rating'],
            $_POST['is_active'],
            $logoData,
            $_POST['update_id']
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE client_testimonials SET 
            company_name = ?, 
            company_email = ?, 
            linkedin = ?, 
            project_description = ?,
            rating = ?, 
            is_active = ?
            WHERE id = ?");
        $stmt->execute([
            $_POST['company_name'],
            $_POST['company_email'],
            $_POST['linkedin'],
            $_POST['project_description'],
            $_POST['rating'],
            $_POST['is_active'],
            $_POST['update_id']
        ]);
    }

    $success = "Company testimonial updated successfully!";
}

// Fetch records
try {
    $stmt = $pdo->query("SELECT * FROM client_testimonials ORDER BY id DESC");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Companies Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../assert/black.png">
</head>
<body class="bg-[#faf5ff] text-gray-800 min-h-screen">

<!-- Navbar -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <div class="text-xl font-bold text-purple-900">NBT Admin</div>
      <div>
        <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
        <a href="logout.php" class="bg-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
      </div>
    </div>
  </div>
</nav>

<!-- Content -->
<div class="max-w-7xl mx-auto px-4 py-12 mt-20">
  <h2 class="text-3xl font-extrabold text-purple-900 mb-8">🏢 Companies List</h2>

  <?php if (isset($success)): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
  <?php endif; ?>

  <!-- Add New Company Form -->
  <div class="bg-white shadow-lg rounded-2xl p-6 mb-6">
    <h3 class="text-xl font-semibold text-purple-800 mb-4">Add New Company Testimonial</h3>
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
        <button type="submit" name="add" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded">Add Company</button>
      </div>
    </form>
  </div>

  <!-- Companies List -->
  <div class="bg-white shadow-lg rounded-2xl p-6 mb-6">
    <div class="flex justify-between items-center">
      <h3 class="text-xl font-semibold text-purple-800">Company Records</h3>
      <a href="?download=true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12v9m0 0l-3-3m3 3l3-3M12 3v9" />
        </svg>
        <span>Download CSV</span>
      </a>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700 border rounded-lg overflow-hidden">
        <thead class="bg-purple-100 text-xs uppercase text-purple-800 font-semibold">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Company</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">LinkedIn</th>
            <th class="px-4 py-3">Rating</th>
            <th class="px-4 py-3">Active</th>
            <th class="px-4 py-3">Logo</th>
            <th class="px-4 py-3">Created At</th>
            <th class="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-purple-100">
          <?php if (count($companies) === 0): ?>
            <tr><td colspan="9" class="px-4 py-4 text-center text-gray-500">No companies found.</td></tr>
          <?php else: ?>
            <?php foreach ($companies as $i => $c): ?>
              <tr class="hover:bg-purple-50">
                <form method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="update_id" value="<?= $c['id'] ?>">
                  <td class="px-4 py-3"><?= $i + 1 ?></td>
                  <td class="px-4 py-3"><input name="company_name" class="border rounded px-2 py-1 w-full" value="<?= htmlspecialchars($c['company_name']) ?>"></td>
                  <td class="px-4 py-3"><input name="company_email" class="border rounded px-2 py-1 w-full" value="<?= htmlspecialchars($c['company_email']) ?>"></td>
                  <td class="px-4 py-3"><input name="linkedin" class="border rounded px-2 py-1 w-full" value="<?= htmlspecialchars($c['linkedin']) ?>"></td>
                  <td class="px-4 py-3"><input type="number" name="rating" class="border rounded px-2 py-1 w-16" value="<?= $c['rating'] ?>"></td>
                  <td class="px-4 py-3">
                    <select name="is_active" class="border rounded px-2 py-1">
                      <option value="1" <?= $c['is_active'] ? 'selected' : '' ?>>Yes</option>
                      <option value="0" <?= !$c['is_active'] ? 'selected' : '' ?>>No</option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <?php if ($c['company_logo']): ?>
                      <img src="data:image/png;base64,<?= base64_encode($c['company_logo']) ?>" alt="Logo" class="w-10 h-10 object-contain mb-1" />
                    <?php else: ?>
                      <span class="text-gray-400 text-xs">No Logo</span>
                    <?php endif; ?>
                    <input type="file" name="company_logo" class="mt-1 block w-full text-sm" />
                  </td>
                  <td class="px-4 py-3"><?= $c['created_at'] ?></td>
                  <td class="px-4 py-3 flex gap-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Save</button>
                    <a href="?delete=<?= $c['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded" onclick="return confirm('Are you sure to delete?')">Delete</a>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
