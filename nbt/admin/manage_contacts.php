<?php
require '../config/db.php'; // PDO connection

// Fetch records
try {
    $stmt = $pdo->query("SELECT * FROM contact_us ORDER BY id DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// CSV Download
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contact_us.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Full Name', 'Email', 'Subject', 'Message']);

    foreach ($contacts as $row) {
        fputcsv($output, [$row['id'], $row['full_name'], $row['email_address'], $row['subject'], $row['message']]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Submissions</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="./asset/black.png" type="image/x-icon" />
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
    <h2 class="text-3xl font-extrabold text-purple-900 mb-8">📨 Contact Form Submissions</h2>

    <div class="bg-white shadow-lg rounded-2xl p-6 mb-6">
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-purple-800">Submissions Table</h3>
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
              <th class="px-4 py-3">Full Name</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Subject</th>
              <th class="px-4 py-3">Message</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-100">
            <?php if (count($contacts) === 0): ?>
              <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">No submissions yet.</td></tr>
            <?php else: ?>
              <?php foreach ($contacts as $i => $c): ?>
                <tr class="hover:bg-purple-50">
                  <td class="px-4 py-3"><?= $i + 1 ?></td>
                  <td class="px-4 py-3 font-medium"><?= htmlspecialchars($c['full_name']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($c['email_address']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($c['subject']) ?></td>
                  <td class="px-4 py-3 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($c['message'])) ?></td>
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
