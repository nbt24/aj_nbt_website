<?php
session_start();
require '../config/db.php'; // adjust path if needed

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $discount = $_POST['discount'];
    
    // Check if database has end_date column, otherwise use time_limit
    try {
        $columnCheck = $pdo->query("SHOW COLUMNS FROM coupons LIKE 'end_date'")->fetch();
        if ($columnCheck) {
            // Use end_date column
            $end_date = $_POST['end_date'] ?: null;
            $original_code = $_POST['original_code'];

            if ($original_code) {
                $stmt = $pdo->prepare("UPDATE coupons SET code=?, discount=?, end_date=? WHERE code=?");
                $stmt->execute([$code, $discount, $end_date, $original_code]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO coupons (code, discount, end_date) VALUES (?, ?, ?)");
                $stmt->execute([$code, $discount, $end_date]);
            }
        } else {
            // Fall back to time_limit column
            $time_limit = $_POST['time_limit'] ?: null;
            $original_code = $_POST['original_code'];

            if ($original_code) {
                $stmt = $pdo->prepare("UPDATE coupons SET code=?, discount=?, time_limit=? WHERE code=?");
                $stmt->execute([$code, $discount, $time_limit, $original_code]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO coupons (code, discount, time_limit) VALUES (?, ?, ?)");
                $stmt->execute([$code, $discount, $time_limit]);
            }
        }
    } catch (Exception $e) {
        // If there's any error, fall back to time_limit
        $time_limit = $_POST['time_limit'] ?: null;
        $original_code = $_POST['original_code'];

        if ($original_code) {
            $stmt = $pdo->prepare("UPDATE coupons SET code=?, discount=?, time_limit=? WHERE code=?");
            $stmt->execute([$code, $discount, $time_limit, $original_code]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO coupons (code, discount, time_limit) VALUES (?, ?, ?)");
            $stmt->execute([$code, $discount, $time_limit]);
        }
    }

    header("Location: coupons.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM coupons WHERE code=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: coupons.php");
    exit;
}

// Handle CSV Download
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="coupons.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Code', 'Discount', 'End Date', 'Created At']);
    $rows = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        fputcsv($out, [$row['code'], $row['discount'], $row['end_date'], $row['created_at']]);
    }
    fclose($out);
    exit;
}

// Get All Coupons
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get Coupon for Editing
$edit_coupon = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code=?");
    $stmt->execute([$_GET['edit']]);
    $edit_coupon = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Coupons</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/x-icon" href="../assert/black.png">

</head>
<body class="bg-purple-50 text-gray-800 min-h-screen">
    

  <!-- Navbar -->
  <nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-purple-900">NBT Admin - Coupons</h1>
      <div>
        <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
        <a href="logout.php" class="bg-yellow-500 text-purple-900 px-4 py-2 rounded-full hover:bg-yellow-600 font-semibold">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="max-w-5xl mx-auto pt-24 pb-12 px-4">
    <!-- Form -->
    <div class="bg-white p-6 rounded-xl shadow mb-8">
      <h2 class="text-2xl font-bold text-purple-800 mb-4"><?= $edit_coupon ? '✏️ Edit Coupon' : '➕ Add New Coupon' ?></h2>
      <form method="post" class="space-y-4">
        <input type="hidden" name="original_code" value="<?= $edit_coupon['code'] ?? '' ?>" />
        <div>
          <label class="block font-medium text-gray-700">Coupon Code</label>
          <input name="code" required type="text"
                 class="mt-1 w-full border border-gray-300 rounded px-3 py-2"
                 value="<?= htmlspecialchars($edit_coupon['code'] ?? '') ?>" />
        </div>
        <div>
          <label class="block font-medium text-gray-700">Discount (%)</label>
          <input name="discount" required type="number" step="0.1"
                 class="mt-1 w-full border border-gray-300 rounded px-3 py-2"
                 value="<?= htmlspecialchars($edit_coupon['discount'] ?? '') ?>" />
        </div>
        <div>
          <label class="block font-medium text-gray-700">End Date</label>
          <input name="end_date" type="date"
                 class="mt-1 w-full border border-gray-300 rounded px-3 py-2"
                 value="<?= htmlspecialchars($edit_coupon['end_date'] ?? '') ?>" />
          <small class="text-gray-500">Leave empty for no expiration</small>
        </div>
        <div class="text-right">
          <?php if ($edit_coupon): ?>
            <a href="coupons.php" class="mr-4 text-sm text-gray-500 hover:underline">Cancel Edit</a>
          <?php endif; ?>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
            <?= $edit_coupon ? 'Update' : 'Create' ?>
          </button>
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="bg-white p-6 rounded-xl shadow">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-purple-800">🎟️ All Coupons</h2>
        <a href="?download=true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">⬇️ Download CSV</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700 border rounded-lg">
          <thead class="bg-purple-100 text-xs uppercase text-purple-800 font-semibold">
            <tr>
              <th class="px-4 py-3">#</th>
              <th class="px-4 py-3">Code</th>
              <th class="px-4 py-3">Discount (%)</th>
              <th class="px-4 py-3">End Date</th>
              <th class="px-4 py-3">Created At</th>
              <th class="px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-100">
            <?php if (empty($coupons)): ?>
              <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">No coupons yet.</td></tr>
            <?php else: ?>
              <?php foreach ($coupons as $i => $c): ?>
                <tr class="hover:bg-purple-50">
                  <td class="px-4 py-3"><?= $i + 1 ?></td>
                  <td class="px-4 py-3 font-medium"><?= htmlspecialchars($c['code']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($c['discount']) ?></td>
                  <td class="px-4 py-3">
                    <?php if ($c['end_date']): ?>
                      <?= date('M d, Y', strtotime($c['end_date'])) ?>
                      <?php 
                        $today = new DateTime();
                        $endDate = new DateTime($c['end_date']);
                        if ($endDate < $today): ?>
                          <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-700 rounded">EXPIRED</span>
                        <?php else: 
                          $diff = $today->diff($endDate);
                          if ($diff->days <= 3): ?>
                            <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">ENDING SOON</span>
                          <?php endif;
                        endif; ?>
                    <?php else: ?>
                      <span class="text-gray-500">No expiration</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3"><?= htmlspecialchars($c['created_at']) ?></td>
                  <td class="px-4 py-3 space-x-2">
                    <a href="?edit=<?= urlencode($c['code']) ?>" class="text-blue-600 hover:underline">Edit</a>
                    <a href="?delete=<?= urlencode($c['code']) ?>" class="text-red-600 hover:underline"
                       onclick="return confirm('Delete this coupon?')">Delete</a>
                  </td>
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
