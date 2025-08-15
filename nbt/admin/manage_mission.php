<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS our_mission (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        description TEXT,
        students VARCHAR(100),
        courses VARCHAR(100),
        success_rate VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$check = $pdo->query("SELECT COUNT(*) FROM our_mission")->fetchColumn();
if ($check == 0) {
    $pdo->exec("INSERT INTO our_mission (title, description, students, courses, success_rate) 
                VALUES ('Default Title', 'Default Description', '1000+', '10+', '95%')");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE our_mission SET title = ?, description = ?, students = ?, courses = ?, success_rate = ? WHERE id = ?");
    $stmt->execute([
        $_POST['title'], $_POST['description'], $_POST['students'],
        $_POST['courses'], $_POST['success_rate'], $_POST['id']
    ]);
    $success = "Mission updated successfully!";
}

$mission = $pdo->query("SELECT * FROM our_mission LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Our Mission</title>
    <link rel="icon" href="/asset/black.png" type="image/png" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-purple-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="text-xl font-bold text-purple-800">NBT Admin</div>
            <div class="flex gap-4">
                <a href="dashboard.php" class="text-purple-800 hover:text-yellow-500">Dashboard</a>
                <a href="logout.php" class="bg-yellow-500 text-purple-900 px-4 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="max-w-5xl mx-auto mt-28 px-6">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Manage Mission</h1>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold text-purple-800 mb-4">Update Mission Info</h2>

            <?php if (isset($success)): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">✅ <?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $mission['id'] ?>">
                <input type="hidden" name="update" value="1">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm text-purple-800 font-medium mb-1">Title</label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($mission['title']) ?>" class="w-full border border-purple-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm text-purple-800 font-medium mb-1">Success Rate</label>
                        <input type="text" name="success_rate" required value="<?= htmlspecialchars($mission['success_rate']) ?>" class="w-full border border-purple-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-purple-800 font-medium mb-1">Description</label>
                    <textarea name="description" required rows="4" class="w-full border border-purple-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"><?= htmlspecialchars($mission['description']) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm text-purple-800 font-medium mb-1">Students</label>
                        <input type="text" name="students" required value="<?= htmlspecialchars($mission['students']) ?>" class="w-full border border-purple-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm text-purple-800 font-medium mb-1">Courses</label>
                        <input type="text" name="courses" required value="<?= htmlspecialchars($mission['courses']) ?>" class="w-full border border-purple-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </div>
                </div>

                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-purple-900 px-6 py-2 rounded-lg font-semibold">
                    Update Mission
                </button>
            </form>
        </div>
    </div>
</body>
</html>
