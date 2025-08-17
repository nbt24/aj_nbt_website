<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../assert/black.png">

</head>
<body class="min-h-screen bg-purple-50">
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-lg font-semibold text-purple-900">NBT Admin</div>
                <a href="logout.php" class="bg-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
            </div>
        </div>
    </nav>
    
    <!-- Quick Optimization Widget -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-xl shadow-lg mt-20 mx-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold mb-2">🚀 Keep Your Website Fast!</h2>
                <p class="text-blue-100 mb-4">Essential tools for website performance</p>
                <div class="flex flex-wrap gap-2">
                    <a href="health_check.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition-colors">
                        📊 Health Check
                    </a>
                    <a href="documentation.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition-colors">
                        📚 How-To Guide
                    </a>
                    <a href="https://tinypng.com" target="_blank" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition-colors">
                        🗜️ Compress Images
                    </a>
                    <button onclick="showQuickStart()" class="bg-yellow-400 hover:bg-yellow-500 text-purple-800 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        🚀 Quick Start
                    </button>
                </div>
            </div>
            <div class="text-6xl opacity-50">
                ⚡
            </div>
        </div>
    </div>

   <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mx-6">
    <a href="manage_courses.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Courses</h3>
        <p class="text-purple-700">Add, edit, or delete courses</p>
    </a>
    <a href="course_testimonials_admin.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Course Testimonials</h3>
        <p class="text-purple-700">Edit, delete & activate/deactivate testimonials</p>
    </a>
    <a href="manage_youtube_videos.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage YouTube Videos</h3>
        <p class="text-purple-700">Add, edit, and organize video links with sequence</p>
    </a>
    <a href="manage_client_testimonials.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Business Testimonials</h3>
        <p class="text-purple-700">Company reviews and project feedback</p>
    </a>
    <a href="coupons.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Coupons</h3>
        <p class="text-purple-700">Create, edit, and manage discount codes</p>
    </a>
    <a href="manage_services.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Services</h3>
        <p class="text-purple-700">Manage service offerings</p>
    </a>
    <a href="admin_clients.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Clients</h3>
        <p class="text-purple-700">Create, update, and delete client records</p>
    </a>
    <a href="company_list.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Companies Testimonials</h3>
        <p class="text-purple-700">View, activate/inactivate & export company data</p>
    </a>
    <a href="manage_team.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Team</h3>
        <p class="text-purple-700">Manage team members</p>
    </a>
    <a href="manage_social_media.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Social Media Section</h3>
        <p class="text-purple-700">Configure your social media platforms</p>
    </a>
    <a href="manage_contacts.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Contacts</h3>
        <p class="text-purple-700">View and manage contact submissions</p>
    </a>
    <a href="manage_mission.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Mission</h3>
        <p class="text-purple-700">Update mission stats</p>
    </a>
    <a href="manage_overview_images.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Overview Images</h3>
        <p class="text-purple-700">Upload the images you want in overview section</p>
    </a>
    <a href="admin_founders.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Founder Cards</h3>
        <p class="text-purple-700">Upload, edit, or delete founder profiles</p>
    </a>
    <a href="manage_admin_users.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all">
        <h3 class="text-xl font-semibold text-purple-900">Manage Admin Users</h3>
        <p class="text-purple-700">Add, edit, or delete admin accounts</p>
    </a>
    
    <!-- Performance Monitoring -->
    <a href="health_check.php" class="bg-gradient-to-r from-green-500 to-blue-500 p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all text-white">
        <h3 class="text-xl font-semibold">🚀 Website Health Check</h3>
        <p class="text-green-100">Monitor performance and speed</p>
    </a>
    
    <!-- Documentation -->
    <a href="documentation.php" class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all text-white">
        <h3 class="text-xl font-semibold">📚 Optimization Guide</h3>
        <p class="text-purple-100">Step-by-step website optimization</p>
    </a>
</div>

<!-- Quick Start Modal Script -->
<script>
function showQuickStart() {
    // Check if modal exists, if not create it
    let modal = document.getElementById('quickStartModal');
    if (!modal) {
        fetch('quick_start_modal.php')
            .then(response => response.text())
            .then(html => {
                document.body.insertAdjacentHTML('beforeend', html);
                document.getElementById('quickStartModal').style.display = 'flex';
            });
    } else {
        modal.style.display = 'flex';
    }
}
</script>

</body>
</html>