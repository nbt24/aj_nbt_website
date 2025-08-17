<?php
require './config/db.php';

try {
    // Create table with image and video as LONGBLOB
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255),
            course VARCHAR(255),
            rating INT,
            message TEXT,
            image LONGBLOB,
            video LONGBLOB,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_submit'])) {
        $name = htmlspecialchars($_POST['name'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        $course = htmlspecialchars($_POST['course'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);
        $message = htmlspecialchars($_POST['message'] ?? '');

        $image = null;
        $video = null;

        // Handle image upload with optimization
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Optimize uploaded image
            $tempOptimized = sys_get_temp_dir() . '/optimized_' . uniqid() . '.jpg';
            if (optimizeUploadedImage($_FILES['image'], $tempOptimized)) {
                $image = file_get_contents($tempOptimized);
                unlink($tempOptimized); // Clean up temp file
            } else {
                $image = file_get_contents($_FILES['image']['tmp_name']);
            }
        }

        // Handle video upload
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $video = file_get_contents($_FILES['video']['tmp_name']);
        }

        // Insert data into the database
        $stmt = $pdo->prepare("
            INSERT INTO course_testimonials (name, email, course, rating, message, image, video)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $email, $course, $rating, $message, $image, $video]);

        // Redirect after successful submission
        header("Location: https://nextbiggtech.com/phpp/index.php");
        exit();
    }

    // Fetch all testimonials
    $stmt = $pdo->query("SELECT * FROM course_testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Testimonials</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
     <link rel="icon" href="./asset/black.png" type="image/x-icon" />
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            font-size: 1.5rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }
        .star-rating input[type="radio"]:checked + label,
        .star-rating input[type="radio"]:checked + label ~ label {
            color: #facc15;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #facc15;
        }
        .card-expanded .content {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-purple-50 min-h-screen p-6">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-purple-800 mb-6">Course Form</h1>

        <!-- Form Card -->
        <div class="bg-white shadow-md rounded-xl p-8 mb-10">
            <h2 class="text-xl font-semibold text-purple-700 mb-4">Add New Testimonial</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-purple-800 block mb-1">Name</label>
                        <input type="text" name="name" required class="w-full p-2 border border-purple-300 rounded">
                    </div>
                    <div>
                        <label class="text-purple-800 block mb-1">Email</label>
                        <input type="email" name="email" required class="w-full p-2 border border-purple-300 rounded">
                    </div>
                    <div>
                        <label class="text-purple-800 block mb-1">Course</label>
                        <select name="course" required class="w-full p-2 border border-purple-300 rounded">
                            <option value="">Select Course</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Data Science">Data Science</option>
                            <option value="AI Basics">AI Basics</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-purple-800 block mb-1">Rating</label>
                        <div class="star-rating flex">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>" class="mx-1">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-purple-800 block mb-1">Message</label>
                        <textarea name="message" required class="w-full p-2 border border-purple-300 rounded h-28"></textarea>
                    </div>
                    <div>
                        <label class="text-purple-800 block mb-1">Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full">
                    </div>
                    <div>
                        <label class="text-purple-800 block mb-1">Video</label>
                        <input type="file" name="video" accept="video/*" class="w-full">
                    </div>
                </div>
                <button type="submit" name="course_submit" class="mt-6 px-6 py-2 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-600">
                    Add Testimonial
                </button>
            </form>
        </div>

        

    <script>
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', () => {
                card.classList.toggle('card-expanded');
            });
        });
    </script>
</body>
</html>