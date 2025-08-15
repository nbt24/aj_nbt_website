<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <link rel="icon" href="./asset/black.png" type="image/x-icon" />
</head>
<body class="min-h-screen bg-purple-50 flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-purple-900 mb-6 text-center">Admin Login</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-purple-900">Email</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-purple-900">Password</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-purple-900 py-3 rounded-lg font-semibold hover:bg-yellow-600">Login</button>
        </form>
    </div>
</body>
</html>