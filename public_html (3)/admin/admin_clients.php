<?php
require '../config/db.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM client WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle Add or Update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fields = [
        'client_name', 'company_name', 'task', 'duration',
        'link', 'status', 'contact_email', 'notes'
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? '';
    }

    if (isset($_POST['update_id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE client SET 
            client_name=?, company_name=?, task=?, duration=?, link=?, status=?, contact_email=?, notes=?
            WHERE id = ?");
        $stmt->execute(array_values($data) + [$_POST['update_id']]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO client (client_name, company_name, task, duration, link, status, contact_email, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_values($data));
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
  <title>Client Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../assert/black.png">

</head>
<body class="bg-gray-100 text-gray-800 p-6">
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-purple-900">NBT Admin - Clients</h1>
      <div>
        <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
        <a href="logout.php" class="bg-yellow-500 text-purple-900 px-4 py-2 rounded-full hover:bg-yellow-600 font-semibold">Logout</a>
      </div>
    </div>
  </nav>

<h1 class="text-3xl font-bold text-purple-800 mb-6 mt-16">👥 Client Management Panel</h1>

<!-- Add New -->
<div class="bg-white p-6 rounded shadow mb-6">
  <h2 class="text-xl font-semibold mb-4 text-purple-700">➕ Add New Client</h2>
  <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <input name="client_name" placeholder="Client Name" class="border p-2 rounded" required>
    <input name="company_name" placeholder="Company Name" class="border p-2 rounded">
    <input name="task" placeholder="Task Description" class="border p-2 rounded">
    <input name="duration" placeholder="Duration" class="border p-2 rounded">
    <input name="link" placeholder="Link" class="border p-2 rounded">
    <input name="status" placeholder="Status (e.g. in progress)" class="border p-2 rounded" value="in progress">
    <input name="contact_email" placeholder="Contact Email" class="border p-2 rounded">
    <textarea name="notes" placeholder="Notes" class="border p-2 rounded col-span-full"></textarea>
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded col-span-full">Add Client</button>
  </form>
</div>

<!-- Client Table -->
<div class="bg-white p-6 rounded shadow overflow-auto">
  <h2 class="text-xl font-semibold mb-4 text-purple-700">📋 All Clients</h2>
  <table class="min-w-full border text-sm text-left">
    <thead class="bg-purple-100 text-purple-800">
      <tr>
        <th class="p-2">#</th>
        <th class="p-2">Client Name</th>
        <th class="p-2">Company</th>
        <th class="p-2">Task</th>
        <th class="p-2">Duration</th>
        <th class="p-2">Link</th>
        <th class="p-2">Status</th>
        <th class="p-2">Email</th>
        <th class="p-2">Notes</th>
        <th class="p-2">Created</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($clients as $i => $c): ?>
        <tr class="border-t hover:bg-purple-50">
          <form method="POST" class="text-xs">
            <input type="hidden" name="update_id" value="<?= $c['id'] ?>">
            <td class="p-2"><?= $i + 1 ?></td>
            <td class="p-2"><input name="client_name" value="<?= htmlspecialchars($c['client_name']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><input name="company_name" value="<?= htmlspecialchars($c['company_name']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><input name="task" value="<?= htmlspecialchars($c['task']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><input name="duration" value="<?= htmlspecialchars($c['duration']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><input name="link" value="<?= htmlspecialchars($c['link']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><input name="status" value="<?= htmlspecialchars($c['status']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><input name="contact_email" value="<?= htmlspecialchars($c['contact_email']) ?>" class="border px-1 py-0.5 w-full"></td>
            <td class="p-2"><textarea name="notes" class="border px-1 py-0.5 w-full"><?= htmlspecialchars($c['notes']) ?></textarea></td>
            <td class="p-2 text-xs"><?= $c['created_at'] ?></td>
            <td class="p-2 flex gap-1">
              <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Save</button>
              <a href="?delete=<?= $c['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Delete this client?')">Delete</a>
            </td>
          </form>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
