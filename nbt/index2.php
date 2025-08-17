<?php
require './config/db.php';

try {
    // Create table with company_logo as LONGBLOB
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS client_testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(255),
            company_email VARCHAR(255),
            linkedin VARCHAR(255),
            project_description TEXT,
            rating INT,
            company_logo LONGBLOB,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_submit'])) {
        $company_name = htmlspecialchars($_POST['company_name'] ?? '');
        $company_email = htmlspecialchars($_POST['company_email'] ?? '');
        $linkedin = htmlspecialchars($_POST['linkedin'] ?? '');
        $project_description = htmlspecialchars($_POST['project_description'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);

        $company_logo = null;

        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            // Optimize uploaded image
            $tempOptimized = sys_get_temp_dir() . '/optimized_' . uniqid() . '.jpg';
            if (optimizeUploadedImage($_FILES['company_logo'], $tempOptimized)) {
                $company_logo = file_get_contents($tempOptimized);
                unlink($tempOptimized); // Clean up temp file
            } else {
                $company_logo = file_get_contents($_FILES['company_logo']['tmp_name']);
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO client_testimonials (company_name, company_email, linkedin, project_description, rating, company_logo)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$company_name, $company_email, $linkedin, $project_description, $rating, $company_logo]);

        header("Location: https://nextbiggtech.com/phpp/index.php");
        exit();
    }

    $stmt = $pdo->query("SELECT * FROM client_testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NBT - Client Testimonials</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="./asset/black.png" type="image/x-icon" />
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            font-size: 1.75rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #facc15;
        }
    </style>
</head>

<body class="bg-[#faf4ff] text-purple-900">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-8">NBT - Add Client Testimonial</h1>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="bg-white shadow-lg rounded-lg p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Company Name</label>
                    <input type="text" name="company_name" required class="w-full border border-purple-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Company Email</label>
                    <input type="email" name="company_email" required class="w-full border border-purple-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">LinkedIn</label>
                    <input type="text" name="linkedin" placeholder="LinkedIn link (optional)" class="w-full border border-purple-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="text-purple-800 block mb-1">Rating</label>
                    <div>
                        <div class="star-rating mt-1">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Project Description</label>
                <textarea name="project_description" rows="4" required class="w-full border border-purple-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Company Logo</label>
                <input type="file" name="company_logo" accept="image/*" class="w-full border border-purple-300 rounded px-3 py-2">
            </div>

            <button type="submit" name="client_submit" class="bg-yellow-400 text-purple-900 px-6 py-2 font-semibold rounded hover:bg-yellow-500 transition duration-200">
                Add Client
            </button>
        </form>
    </div>
</body>
</html>
