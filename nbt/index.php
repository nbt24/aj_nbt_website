<?php
require 'config/db.php';

/**
 * Optimized image caching function with performance improvements
 * @param string $imageData - The binary image data
 * @param string $imageType - The MIME type of the image
 * @param string $prefix - A prefix for the filename
 * @param int|string $id - Unique identifier for the image
 * @return string - The path to the cached image file or base64 fallback
 */
function getOptimizedImagePath($imageData, $imageType, $prefix, $id) {
    // Skip caching for empty images
    if (empty($imageData)) {
        return null;
    }
    
    // Create cache directory if it doesn't exist (only once)
    static $cacheDir = 'cache/images/';
    static $dirCreated = false;
    
    if (!$dirCreated && !is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
        $dirCreated = true;
    }
    
    // Generate filename based on content hash
    $imageHash = md5($imageData);
    $extension = '';
    
    // Determine file extension from MIME type
    switch($imageType) {
        case 'image/jpeg':
        case 'image/jpg':
            $extension = '.jpg';
            break;
        case 'image/png':
            $extension = '.png';
            break;
        case 'image/gif':
            $extension = '.gif';
            break;
        case 'image/webp':
            $extension = '.webp';
            break;
        default:
            $extension = '.jpg'; // fallback
    }
    
    $filename = $prefix . '_' . $id . '_' . substr($imageHash, 0, 8) . $extension;
    $filepath = $cacheDir . $filename;
    
    // Only create file if it doesn't exist and has content
    if (!file_exists($filepath) && !empty($imageData)) {
        if (file_put_contents($filepath, $imageData) === false) {
            // Fallback to base64 if file writing fails
            return 'data:' . $imageType . ';base64,' . base64_encode($imageData);
        }
    }
    
    // Verify file exists and has content
    if (file_exists($filepath) && filesize($filepath) > 0) {
        return $filepath;
    }
    
    // Fallback to base64 if cache fails
    return 'data:' . $imageType . ';base64,' . base64_encode($imageData);
}

// Fetch data
$mission_stmt = $pdo->query("SELECT * FROM our_mission LIMIT 1");
$mission = $mission_stmt->fetch();

// Function to convert video URL to embed URL
function getVideoEmbedData($url) {
    if (empty($url)) return null;
    
    $data = ['type' => 'unknown', 'embed_url' => '', 'thumbnail' => '', 'video_id' => ''];
    
    // YouTube URL patterns
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        $data['type'] = 'youtube';
        $data['video_id'] = $matches[1];
        $data['embed_url'] = "https://www.youtube.com/embed/" . $matches[1] . "?rel=0&modestbranding=1&showinfo=0&end_screen=0";
        $data['thumbnail'] = "https://img.youtube.com/vi/" . $matches[1] . "/maxresdefault.jpg";
        return $data;
    }
    
    // Vimeo URL patterns
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        $data['type'] = 'vimeo';
        $data['video_id'] = $matches[1];
        $data['embed_url'] = "https://player.vimeo.com/video/" . $matches[1] . "?color=ffffff&title=0&byline=0&portrait=0";
        $data['thumbnail'] = "https://vumbnail.com/" . $matches[1] . ".jpg";
        return $data;
    }
    
    // Direct video URL
    if (preg_match('/\.(mp4|webm|ogg)$/i', $url)) {
        $data['type'] = 'direct';
        $data['embed_url'] = $url;
        return $data;
    }
    
    return null;
}

$mission_video = null;
if (!empty($mission['video_url'])) {
    $mission_video = getVideoEmbedData($mission['video_url']);
}

$courses_stmt = $pdo->query("SELECT * FROM courses");
$courses = $courses_stmt->fetchAll();

$team_stmt = $pdo->query("SELECT * FROM meet_our_team ORDER BY image_sequence");
$team = $team_stmt->fetchAll();

$services_stmt = $pdo->query("SELECT * FROM our_services");
$services = $services_stmt->fetchAll();

$overview_stmt = $pdo->query("SELECT * FROM overview_images ORDER BY image_sequence");
$overview_images = $overview_stmt->fetchAll();

$clients_stmt = $pdo->query("SELECT * FROM client ORDER BY id DESC");
$clients = $clients_stmt->fetchAll();

$coupons_stmt = $pdo->query("SELECT * FROM coupons");
$coupons = $coupons_stmt->fetchAll();

// Fetch data for four top members card 
$founder_stmt = $pdo->query("SELECT * FROM founder_card ORDER BY image_sequence");
$founder = $founder_stmt->fetchAll();

// Fetch data for client testimonials
$clients_testimonials_stmt = $pdo->query("SELECT company_name, company_email, linkedin, project_description, rating, company_logo FROM client_testimonials ORDER BY rating DESC");
$clients_testimonials = $clients_testimonials_stmt->fetchAll();

// Fetch data for course testimonials 
$course_testimonials_stmt = $pdo->query("SELECT name, email, course, rating, message, image, video FROM course_testimonials ORDER BY rating DESC");
$course_testimonials = $course_testimonials_stmt->fetchAll();

// // Social Media Fetching Section
// $stmt = $pdo->query("SELECT * FROM social_media");
// // First row (e.g., Instagram)
// $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
// // Second row (e.g., Facebook)
// $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
// // Third row (e.g., Twitter)
// $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
// // Fourth row (e.g., Youtube)
// $row4 = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch YouTube videos
$youtube_stmt = $pdo->prepare("SELECT * FROM youtube_videos ORDER BY sequence_number");
$youtube_stmt->execute();
$youtube_videos = $youtube_stmt->fetchAll();

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    $full_name = $_POST['full_name'];
    $email_address = $_POST['email_address'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO contact_us (full_name, email_address, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $email_address, $subject, $message]);
    // Redirect to avoid resubmission
    header('Location: index.php?success=contact_submitted#contact');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Performance and SEO Meta Tags -->
    <meta name="description" content="NBT - Next Bigg Tech - Leading technology solutions and training">
    <meta name="robots" content="index, follow">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    
    <title>NBT - Next Bigg Tech</title>
    
    <!-- Critical CSS loaded first -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Non-critical resources with async/defer -->
    <script defer src="https://cdn.jsdelivr.net/npm/@formspree/formspree-js@1.0.0/dist/formspree.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'" />
    
    <link rel="icon" type="image/x-icon" href="./assert/black.png">

    <style>
        /* Performance Optimizations */
        * {
            backface-visibility: hidden;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Global Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Disable smooth scrolling for users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }

        /* Line Clamp Utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .animate-marquee {
            animation: marquee 40s linear infinite;
            will-change: transform;
            transform: translate3d(0, 0, 0);
        }
         /* Marquee Animation - Hardware Accelerated */
        @keyframes marquee {
            0% {
                transform: translate3d(0, 0, 0);
            }

            100% {
                transform: translate3d(-50%, 0, 0);
            }
        }

        /*.hover\:pause:hover {*/
        /*    animation-play-state: paused;*/
        /*}*/

        /* Pulse Animation */
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Bounce Animation */
        .animate-bounce-slow {
            animation: bounce 2s infinite;
        }

        /* Section Fade-In Animation */
        .animate-section {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Base Colors */
        :root {
            --primary-purple: rgb(66, 23, 107);
            /* Primary purple */
            --light-purple-bg: rgb(230, 219, 241);
            /* Light theme background */
            --dark-purple-bg: rgb(250, 245, 255);
            /* Dark theme background */
            --medium-purple: rgb(108, 70, 145);
            /* Medium purple for borders and fallback */
            --very-light-purple: rgb(245, 240, 255);
            /* Very light purple for subtle backgrounds */
            --dark-purple: rgb(45, 15, 73);
            /* Darker purple for secondary elements */
            --yellow-accent: #facc15;
            /* Yellow accent for both themes */
            --light-yellow: #fffbeb;
            /* Light yellow for backgrounds */
        }

        /* Light Theme */
        .bg-purple-50 {
            background-color: var(--light-purple-bg);
        }

        .text-purple-700 {
            color: #ffffff;
            /* White text for light theme */
        }

        .text-purple-900 {
            color: #ffffff;
            /* White text for light theme */
        }

        .border-purple-300 {
            border-color: rgb(185, 165, 215);
            /* Slightly darker light purple for borders */
        }

        .bg-purple-100 {
            background-color: var(--very-light-purple);
        }

        .bg-purple-20 {
            background-color: var(--very-light-purple-bg);
        }

        .text-yellow-500 {
            color: var(--yellow-accent);
        }

        .bg-yellow-500 {
            background-color: var(--yellow-accent);
        }

        .border-yellow-400 {
            border-color: #fed7aa;
            /* Softer yellow for borders */
        }

        .bg-yellow-400 {
            background-color: #fed7aa;
        }

        .bg-purple-300 {
            background-color: rgb(185, 165, 215);
        }

        /* Dark Theme */
        .dark .bg-purple-50 {
            background-color: var(--dark-purple-bg);
        }

        .dark .bg-white {
            background-color: var(--dark-purple-bg);
        }

        .dark .text-purple-900 {
            color: #000000;
            /* Black text for dark theme */
        }

        .dark .text-purple-700 {
            color: #000000;
            /* Black text for dark theme */
        }

        .dark .border-purple-300 {
            border-color: rgb(140, 110, 170);
            /* Medium purple for borders */
        }

        .dark .bg-purple-100 {
            background-color: rgb(240, 235, 245);
            /* Slightly darker than dark theme bg */
        }

        .dark .bg-purple-20 {
            background-color: var(--dark-purple-bg);
        }

        .dark .text-yellow-500 {
            color: var(--yellow-accent);
        }

        .dark .bg-yellow-500 {
            background-color: var(--yellow-accent);
        }

        .dark .border-yellow-400 {
            border-color: #fed7aa;
        }

        .dark .bg-yellow-400 {
            background-color: #fed7aa;
        }

        .dark .bg-purple-300 {
            background-color: rgb(200, 180, 230);
            /* Lighter purple for nav in dark mode */
        }

        .star-rating .filled {
            color: var(--yellow-accent);
        }

        .star-rating .empty {
            color: #d1d5db;
        }

        /* Hero Carousel */
        #carousel {
            display: flex;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            flex: 0 0 100%;
            transition: opacity 1.5s ease-in-out, transform 1.5s ease-in-out;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Word Animation */
        #animated-word {
            display: inline-block;
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        }

        /* Scrolling Container - Hardware Accelerated */
        .scrolling-container {
            display: flex;
            gap: 2rem;
            flex-wrap: nowrap;
            animation: scroll-x 40s linear infinite;
            will-change: transform;
            transform: translate3d(0, 0, 0);
        }

        /*.scrolling-wrapper:hover .scrolling-container {*/
        /*    animation-play-state: paused;*/
        /*}*/

        @keyframes scroll-x {
            0% {
                transform: translate3d(0%, 0, 0);
            }

            100% {
                transform: translate3d(-50%, 0, 0);
            }
        }

        .scrolling-wrapper {
            overflow: hidden;
            position: relative;
        }

        /* Glow Pulse */
        @keyframes glowPulse {

            0%,
            100% {
                background-color: var(--primary-purple);
            }

            50% {
                background-color: var(--medium-purple);
            }
        }

        .glow-box {
            animation: glowPulse 3s ease-in-out infinite;
        }

        /* Consistent Card Shadow */
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .card-shadow:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        /* Smooth Moving Flash Border Animation */
        .glow-border {
            position: relative;
            border: 2px solid #fbbf24;
            background: #fbbf24;
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
            overflow: hidden;
        }

        /* Main moving flash effect */
        .glow-border::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.1) 40%,
                rgba(255, 255, 255, 0.8) 50%,
                rgba(255, 255, 255, 1) 52%,
                rgba(255, 255, 255, 0.8) 54%,
                rgba(255, 255, 255, 0.1) 60%,
                transparent 70%
            );
            animation: sweep-flash 5s ease-in-out infinite;
            z-index: 1;
        }

        /* Secondary glow pulse */
        .glow-border::after {
            content: '';
            position: absolute;
            inset: -3px;
            background: linear-gradient(45deg, #fbbf24, #f59e0b, #fde047, #fbbf24);
            background-size: 300% 300%;
            border-radius: inherit;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: xor;
            -webkit-mask-composite: xor;
            animation: glow-rotate 0.5s linear infinite;
            z-index: 0;
        }

        /* Button content overlay */
        .glow-border span {
            position: relative;
            z-index: 2;
        }

        @keyframes sweep-flash {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
                opacity: 0;
            }
            5% {
                opacity: 1;
            }
            92.5% {
                opacity: 1;
            }
            95% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
                opacity: 0;
            }
            95.1%, 100% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
                opacity: 0;
            }
        }

        @keyframes glow-rotate {
            0% {
                background-position: 0% 50%;
                filter: hue-rotate(0deg);
            }
            33% {
                background-position: 100% 50%;
                filter: hue-rotate(10deg);
            }
            66% {
                background-position: 50% 100%;
                filter: hue-rotate(-10deg);
            }
            100% {
                background-position: 0% 50%;
                filter: hue-rotate(0deg);
            }
        }

        /* Enhanced hover effects */
        .glow-border:hover {
            box-shadow: 0 0 30px rgba(251, 191, 36, 0.6), 0 0 60px rgba(251, 191, 36, 0.3);
            transform: scale(1.05);
        }

        .glow-border:hover::before {
            animation: sweep-flash 5s ease-in-out infinite;
        }

        .glow-border:hover::after {
            animation: glow-rotate 1.5s linear infinite;
        }
    </style>



</head>

<body class="min-h-screen bg-purple-30 transition-colors duration-300 font-sans text-black-900">
    <!-- Fixed Navigation -->
    <nav class="bg-purple-100 dark:bg-purple-900/90 backdrop-blur-md shadow-lg fixed w-full top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2 group">
                    <img src="./assert/black.png" alt="NBT Logo" class="h-14 w-14 object-contain transition-transform duration-300 group-hover:scale-110">
                    <div class="ml-1 text-lg font-semibold text-purple-900 dark:text-purple-200 hidden sm:block transition-colors duration-300 group-hover:text-yellow-500 dark:group-hover:text-yellow-400"></div>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <button onclick="scrollToSection('home')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Home
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('about')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        About
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('courses')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Courses
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('student-testimonials')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Student Reviews
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('videos-section')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Videos
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('testimonials')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Client Reviews
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('coupons')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Offers
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('services')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Services
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('client')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Clients
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="scrollToSection('contact')" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 transition-all duration-300 relative group capitalize font-medium">
                        Contact
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-500 dark:bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
                    </button>
                    <button onclick="toggleTheme()" class="p-2 rounded-full bg-purple-100 dark:bg-purple-800 text-purple-900 dark:text-purple-200 hover:bg-yellow-100 dark:hover:bg-yellow-600 transition-all duration-300" aria-label="Toggle theme">
                        <i data-lucide="moon" class="h-5 w-5"></i>
                    </button>
                </div>
                <div class="hidden md:block">
                    <button onclick="window.location.href='https://courses.nextbiggtech.com'" class="bg-yellow-500 text-purple-900 dark:bg-yellow-400 dark:text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600 dark:hover:bg-yellow-500 hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl glow-border">
                        <span class="relative z-10">Login</span>
                    </button>
                </div>
                <div class="md:hidden flex items-center space-x-2">
                    <button onclick="toggleTheme()" class="p-2 rounded-full bg-purple-100 dark:bg-purple-800 text-purple-900 dark:text-purple-200 hover:bg-yellow-100 dark:hover:bg-yellow-600 transition-all duration-300" aria-label="Toggle theme">
                        <i data-lucide="moon" class="h-5 w-5"></i>
                    </button>
                    <button onclick="toggleMenu()" class="text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 focus:outline-none transition-all duration-300">
                        <i data-lucide="menu" class="h-6 w-6" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="md:hidden bg-white/95 dark:bg-purple-900/95 backdrop-blur-md border-t dark:border-purple-700 transition-all duration-300 max-h-0 opacity-0 overflow-hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <button onclick="scrollToSection('home')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Home</button>
                <button onclick="scrollToSection('about')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">About</button>
                <button onclick="scrollToSection('courses')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Courses</button>
                <button onclick="scrollToSection('student-testimonials')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Student Reviews</button>
                <button onclick="scrollToSection('videos-section')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Videos</button>
                <button onclick="scrollToSection('testimonials')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Client Reviews</button>
                <button onclick="scrollToSection('coupons')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Offers</button>
                <button onclick="scrollToSection('services')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Services</button>
                <button onclick="scrollToSection('client')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Clients</button>
                <button onclick="scrollToSection('contact')" class="block w-full text-left px-3 py-2 text-purple-900 dark:text-purple-200 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-purple-100 dark:hover:bg-purple-800 transition-all duration-300 capitalize transform hover:translate-x-2 font-medium">Contact</button>
            </div>
        </div>
    </nav>

    

    <!-- Main Content -->
    <!-- <div id="main-content" class="hidden"> -->
        <div id="main-content">
        <!-- Hero Section -->
        <section id="home" class="bg-gradient-to-br from-purple-600 via-purple-700 to-yellow-500 dark:from-purple-800 dark:via-purple-900 dark:to-yellow-600 text-white py-20 pt-36 relative overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-400/30 dark:bg-purple-500/30 rounded-full animate-pulse-slow"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-yellow-400/30 dark:bg-yellow-500/30 rounded-full animate-pulse-slow" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 w-60 h-60 bg-purple-300/20 dark:bg-purple-400/20 rounded-full animate-pulse-slow" style="animation-delay: 4s;"></div>
            </div>
            <div class="w-full px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-6 animate-section flex-1">
                        <h1 class="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
                            Your <span id="animated-word" class="text-yellow-300 dark:text-yellow-400"></span><br /> Journey Starts Here
                        </h1>
                        <p class="text-xl text-purple-100 dark:text-purple-200">
                            Transform your career with expert consultancy, cutting-edge tech services, and industry-leading courses in web development, data science, and digital marketing.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button onclick="scrollToSection('contact')" class="bg-yellow-500 text-purple-900 dark:bg-yellow-400 dark:text-purple-800 px-8 py-3 rounded-full font-semibold hover:bg-yellow-600 dark:hover:bg-yellow-500 hover:scale-105 transition-all duration-300 text-center shadow-lg hover:shadow-xl transform glow-border">
                                <span class="relative z-10">Get Free Consultation</span>
                            </button>
                            <button onclick="scrollToSection('courses')" class="border-2 border-yellow-400 text-yellow-400 dark:border-yellow-400 dark:text-yellow-400 px-8 py-3 rounded-full font-semibold hover:bg-yellow-400 hover:text-purple-900 dark:hover:bg-yellow-400 dark:hover:text-purple-800 hover:scale-105 transition-all duration-300 text-center transform glow-border">
                                <span class="relative z-10">View Courses</span>
                            </button>
                        </div>
                    </div>
                    <!-- Right Carousel Image -->
                    <div class="animate-section flex-1" style="animation-delay: 300ms;">
                        <div class="relative w-full aspect-video overflow-hidden rounded-xl shadow-2xl bg-white dark:bg-gray-800 flex items-center justify-center border-4 border-yellow-400/80 dark:border-yellow-400/90 hover:border-yellow-400 dark:hover:border-yellow-400 transition-all duration-300">
                            <div id="carousel" class="flex w-full h-full items-center justify-center transition-transform duration-500 ease-in-out">
                                <?php
                                if (!empty($overview_images)) {
                                    foreach ($overview_images as $image) {
                                        $imageData = base64_encode($image['image_data']);
                                        $imageType = $image['image_type'];
                                        $title = htmlspecialchars($image['title']);
                                        echo "<div class='carousel-item flex-shrink-0 w-full h-full flex items-center justify-center'>";
                                        echo "<img src='data:$imageType;base64,$imageData' alt='$title' class='object-contain w-full h-full'>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<div class='carousel-item flex-shrink-0 w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-xl'>";
                                    echo "<p class='text-gray-500 dark:text-gray-400'>No images available</p>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce-slow">
                    <i data-lucide="arrow-down" class="h-6 w-6 text-yellow-300 dark:text-yellow-400"></i>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-purple-20 dark:bg-purple-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 animate-section">
                    <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">About NBT</h2>
                    <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                        Empowering careers and businesses through cutting-edge courses, expert consultancy, and B2B tech solutions tailored for real-world impact.
                    </p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
                    <div class="animate-section" style="animation-delay: 200ms;">
                        <h3 class="text-3xl font-bold text-purple-900 dark:text-purple-200 mb-6 tracking-tight">Our Mission</h3>
                        <p class="text-lg text-purple-700 dark:text-purple-300 mb-4">
                            <?php echo htmlspecialchars($mission['description']); ?>
                        </p>
                        <p class="text-lg text-purple-700 dark:text-purple-300 mb-6">
                            Since 2020, we’ve empowered over <?php echo htmlspecialchars($mission['students']); ?> individuals and 100+ startups to scale with confidence through our services and educational programs.
                        </p>
                        <div class="grid grid-cols-3 gap-6 text-center">
                            <div class="group">
                                <div class="text-3xl font-bold text-yellow-500 dark:text-yellow-400 group-hover:scale-110 transition-transform duration-300" data-counter="<?php echo htmlspecialchars($mission['students'] . '+'); ?>"></div>
                                <div class="text-purple-700 dark:text-purple-300">Students</div>
                            </div>
                            <div class="group">
                                <div class="text-3xl font-bold text-yellow-500 dark:text-yellow-400 group-hover:scale-110 transition-transform duration-300" data-counter="<?php echo htmlspecialchars($mission['courses'] . '+'); ?>"></div>
                                <div class="text-purple-700 dark:text-purple-300">Courses</div>
                            </div>
                            <div class="group">
                                <div class="text-3xl font-bold text-yellow-500 dark:text-yellow-400 group-hover:scale-110 transition-transform duration-300" data-counter="<?php echo htmlspecialchars($mission['success_rate'] . '%'); ?>"></div>
                                <div class="text-purple-700 dark:text-purple-300">Success Rate</div>
                            </div>
                        </div>
                    </div>
                    <div class="animate-section" style="animation-delay: 400ms;">
                        <?php if ($mission_video): ?>
                            <!-- Elegant Inline Video Player -->
                            <div class="relative overflow-hidden rounded-3xl shadow-2xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group">
                                <!-- Video Container -->
                                <div class="relative bg-black rounded-3xl overflow-hidden min-h-[300px] aspect-video transition-transform duration-500 group-hover:scale-105" id="missionVideoContainer">
                                    <!-- Video Thumbnail with Play Button -->
                                    <div class="video-thumbnail absolute inset-0 cursor-pointer group" onclick="loadMissionVideo()">
                                        <?php if ($mission_video['type'] === 'youtube'): ?>
                                            <img 
                                                src="<?= $mission_video['thumbnail'] ?>" 
                                                alt="Mission Video Thumbnail"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                onerror="this.src='https://img.youtube.com/vi/<?= $mission_video['video_id'] ?>/hqdefault.jpg'"
                                            />
                                        <?php elseif ($mission_video['type'] === 'vimeo'): ?>
                                            <img 
                                                src="<?= $mission_video['thumbnail'] ?>" 
                                                alt="Mission Video Thumbnail"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                onerror="this.style.display='none'"
                                            />
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <svg class="w-16 h-16 mx-auto mb-4 opacity-80" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                    <p class="text-lg font-medium">Click to play video</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Overlay with Play Button -->
                                        <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                            <div class="w-20 h-20 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition-all duration-300">
                                                <svg class="w-8 h-8 text-purple-900 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Video Type Badge -->
                                        <div class="absolute top-4 right-4 px-3 py-1 bg-black/70 backdrop-blur-sm rounded-full">
                                            <div class="flex items-center gap-2">
                                                <?php if ($mission_video['type'] === 'youtube'): ?>
                                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                    </svg>
                                                    <span class="text-white text-xs font-medium">YouTube</span>
                                                <?php elseif ($mission_video['type'] === 'vimeo'): ?>
                                                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197c1.185-1.044 2.351-2.084 3.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.1 3.447 3.838.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.473.06 3.628 1.664 3.493 4.797l-.013.01z"/>
                                                    </svg>
                                                    <span class="text-white text-xs font-medium">Vimeo</span>
                                                <?php else: ?>
                                                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                    <span class="text-white text-xs font-medium">Video</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Video Player (Hidden initially) -->
                                    <div class="video-player hidden absolute inset-0">
                                        <?php if ($mission_video['type'] === 'direct'): ?>
                                            <video 
                                                controls 
                                                class="w-full h-full object-cover"
                                                preload="metadata"
                                            >
                                                <source src="<?= htmlspecialchars($mission_video['embed_url']) ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php else: ?>
                                            <iframe 
                                                class="w-full h-full border-0" 
                                                src="" 
                                                data-src="<?= htmlspecialchars($mission_video['embed_url']) ?><?= strpos($mission_video['embed_url'], '?') !== false ? '&' : '?' ?>autoplay=1"
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Loading State -->
                                    <div class="video-loading hidden absolute inset-0 bg-black/90 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <div class="w-12 h-12 border-4 border-white/30 border-t-white rounded-full animate-spin mx-auto mb-4"></div>
                                            <p class="text-sm">Loading video...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Fallback when no video is set -->
                            <div class="relative overflow-hidden rounded-3xl shadow-2xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 p-8 group">
                                <div class="text-center transition-transform duration-500 group-hover:scale-105">
                                    <svg class="w-24 h-24 mx-auto mb-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <h4 class="text-2xl font-bold text-purple-900 dark:text-purple-200 mb-3">Coming Soon</h4>
                                    <p class="text-purple-700 dark:text-purple-300 mb-4">
                                        We're preparing an inspiring video about our mission and journey.
                                    </p>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-200 dark:bg-purple-700 rounded-full text-purple-800 dark:text-purple-200 text-sm">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                        Video coming soon
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Our Core Values -->
                <section class="mb-20 relative overflow-hidden bg-white dark:bg-purple-950 py-20">
                    <!-- Background Accents -->
                    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
                    <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

                    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Header -->
                        <div class="text-center mb-16 animate-section">
                            <h3 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Our Core Values</h3>
                            <p class="text-xl text-purple-700 dark:text-purple-300">The principles that guide everything we do</p>
                        </div>

                        <!-- Value Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                            <!-- Value Card -->
                            <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group animate-section">
                                <div class="absolute inset-0 opacity-10 pointer-events-none">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>

                                <div class="relative z-10 p-6 text-center transition-transform duration-500 group-hover:scale-105">
                                    <div class="bg-purple-100 dark:bg-purple-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-600 transition-colors duration-300">
                                        <i data-lucide="target" class="h-8 w-8 text-purple-600 dark:text-purple-300 group-hover:text-purple-900 dark:group-hover:text-purple-800 transition-colors duration-300"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold text-purple-900 dark:text-purple-200 mb-2 group-hover:text-yellow-500 dark:group-hover:text-yellow-400 transition-colors duration-300">Excellence</h4>
                                    <p class="text-purple-700 dark:text-purple-300">We strive for excellence in everything we do, from our courses to our consultancy services.</p>
                                </div>
                            </div>

                            <!-- Repeat for other values -->
                            <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group animate-section" style="animation-delay: 100ms;">
                                <div class="absolute inset-0 opacity-10 pointer-events-none">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>
                                <div class="relative z-10 p-6 text-center transition-transform duration-500 group-hover:scale-105">
                                    <div class="bg-purple-100 dark:bg-purple-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-600 transition-colors duration-300">
                                        <i data-lucide="heart" class="h-8 w-8 text-purple-600 dark:text-purple-300 group-hover:text-purple-900 dark:group-hover:text-purple-800 transition-colors duration-300"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold text-purple-900 dark:text-purple-200 mb-2 group-hover:text-yellow-500 dark:group-hover:text-yellow-400 transition-colors duration-300">Passion</h4>
                                    <p class="text-purple-700 dark:text-purple-300">We're passionate about helping people achieve their career goals and reach their potential.</p>
                                </div>
                            </div>

                            <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group animate-section" style="animation-delay: 200ms;">
                                <div class="absolute inset-0 opacity-10 pointer-events-none">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>
                                <div class="relative z-10 p-6 text-center transition-transform duration-500 group-hover:scale-105">
                                    <div class="bg-purple-100 dark:bg-purple-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-600 transition-colors duration-300">
                                        <i data-lucide="users" class="h-8 w-8 text-purple-600 dark:text-purple-300 group-hover:text-purple-900 dark:group-hover:text-purple-800 transition-colors duration-300"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold text-purple-900 dark:text-purple-200 mb-2 group-hover:text-yellow-500 dark:group-hover:text-yellow-400 transition-colors duration-300">Community</h4>
                                    <p class="text-purple-700 dark:text-purple-300">We believe in building a supportive community where everyone can learn and grow together.</p>
                                </div>
                            </div>

                            <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group animate-section" style="animation-delay: 300ms;">
                                <div class="absolute inset-0 opacity-10 pointer-events-none">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>
                                <div class="relative z-10 p-6 text-center transition-transform duration-500 group-hover:scale-105">
                                    <div class="bg-purple-100 dark:bg-purple-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-600 transition-colors duration-300">
                                        <i data-lucide="award" class="h-8 w-8 text-purple-600 dark:text-purple-300 group-hover:text-purple-900 dark:group-hover:text-purple-800 transition-colors duration-300"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold text-purple-900 dark:text-purple-200 mb-2 group-hover:text-yellow-500 dark:group-hover:text-yellow-400 transition-colors duration-300">Innovation</h4>
                                    <p class="text-purple-700 dark:text-purple-300">We stay at the forefront of technology and teaching methods to provide the best experience.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Meet Our Team -->
                <div class="mb-20">
                    <div class="text-center mb-16 animate-section">
                        <h3 class="text-3xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Meet Our Team</h3>
                        <p class="text-xl text-purple-700 dark:text-purple-300">The experts behind your success</p>
                    </div>

                    <!--four Founders-->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 px-4">
                        <?php foreach ($founder as $mem): ?>
                            <div class="w-64 text-center bg-purple-50 dark:bg-purple-900/70 rounded-xl shadow-lg mx-auto overflow-hidden border-4 border-yellow-400/80 dark:border-yellow-400/90 hover:border-yellow-400 dark:hover:border-yellow-400 transition-all duration-300">
                                <div class="relative w-full h-64 border-b-2 border-purple-300 dark:border-purple-600">
                                    <?php 
                                        // Use cached image for better performance
                                        $imagePath = getOptimizedImagePath($mem['image_data'], $mem['image_type'], 'founder', $mem['id']);
                                    ?>
                                    <img src="<?= $imagePath ?>"
                                        alt="<?php echo htmlspecialchars($mem['name']); ?>"
                                        class="absolute inset-0 w-full h-full object-cover shadow-lg"
                                        loading="lazy" />
                                    <div class="absolute inset-0 bg-yellow-500/20 dark:bg-yellow-400/20 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                                <div class="p-6">
                                    <h4 class="text-xl font-semibold text-purple-900 dark:text-purple-200 mb-1 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">
                                        <?php echo htmlspecialchars($mem['name']); ?>
                                    </h4>
                                    <div class="text-yellow-500 dark:text-yellow-400 font-medium mb-2">
                                        <?php echo htmlspecialchars($mem['position']); ?>
                                    </div>
                                    <p class="text-purple-700 dark:text-purple-300 text-sm">
                                        <?php echo htmlspecialchars($mem['description']); ?>
                                    </p>
                                    <!-- Optional Socials -->
                                    <div class="flex justify-center gap-4 mt-2">
                                        <?php if (!empty($mem['linkedin'])): ?>
                                            <a href="<?php echo htmlspecialchars($mem['linkedin']); ?>" target="_blank"
                                                class="text-purple-400 hover:text-yellow-500 transition">
                                                <i data-lucide="linkedin" class="w-5 h-5"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($mem['email'])): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($mem['email']); ?>"
                                                class="text-purple-400 hover:text-yellow-500 transition">
                                                <i data-lucide="mail" class="w-5 h-5"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!--Company Team Members - Performance Optimized but Same Look-->
                    <div class="mt-16 overflow-hidden relative team-marquee-container manual-scroll">
                        <div id="team-marquee" class="flex gap-12 animate-marquee px-4" style="animation: marquee 60s linear infinite;">
                            <?php
                            // Single team array - no duplication for better performance
                            foreach ($team as $index => $member): ?>
                                <div class="flex-shrink-0 w-72 text-center group team-card">
                                    <!-- Card Container with Optimized Height for Content -->
                                    <div class="relative bg-white dark:bg-purple-900/90 backdrop-blur-sm rounded-3xl p-6 shadow-2xl border-4 border-yellow-400/80 dark:border-yellow-400/90 transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group-hover:border-yellow-400 dark:group-hover:border-yellow-400 overflow-hidden h-80">
                                        
                                        <!-- Decorative Background Pattern -->
                                        <div class="absolute inset-0 opacity-5 pointer-events-none">
                                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                        </div>
                                        
                                        <!-- Gradient Border Effect -->
                                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-yellow-400/20 via-purple-500/20 to-yellow-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                                        
                                        <!-- Content with Flex Layout - No flex-1 for tighter content -->
                                        <div class="relative z-10 h-full flex flex-col justify-start transition-transform duration-500 group-hover:scale-105">
                                            <!-- Profile Image -->
                                            <div class="relative overflow-hidden rounded-full w-16 h-16 mx-auto mb-3 border-3 border-gradient-to-r from-yellow-400 to-purple-500 shadow-xl group-hover:shadow-2xl transition-shadow duration-300">
                                                <div class="absolute inset-0 rounded-full bg-gradient-to-r from-yellow-400 to-purple-500 p-0.5">
                                                    <div class="w-full h-full rounded-full overflow-hidden bg-white dark:bg-purple-900">
                                                        <?php if (!empty($member['image_path']) && file_exists($member['image_path'])): ?>
                                                            <img src="<?php echo htmlspecialchars($member['image_path']); ?>"
                                                                alt="<?php echo htmlspecialchars($member['name']); ?>"
                                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                                loading="lazy" />
                                                        <?php elseif (!empty($member['image_data'])): ?>
                                                            <?php 
                                                                // Use optimized image caching for better performance
                                                                $imagePath = getOptimizedImagePath($member['image_data'], $member['image_type'], 'team', $member['id']);
                                                            ?>
                                                            <img src="<?= $imagePath ?>"
                                                                alt="<?php echo htmlspecialchars($member['name']); ?>"
                                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                                loading="lazy" 
                                                                decoding="async" />
                                                        <?php else: ?>
                                                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                                <i data-lucide="user" class="w-6 h-6 text-gray-400"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Name (smaller font, sentence case) -->
                                            <h4 class="text-sm font-bold text-purple-900 dark:text-purple-200 mb-1 group-hover:text-yellow-500 dark:group-hover:text-yellow-400 transition-colors duration-300">
                                                <?php echo ucwords(strtolower(htmlspecialchars($member['name']))); ?>
                                            </h4>

                                            <!-- Position (larger font) -->
                                            <div class="text-yellow-500 dark:text-yellow-400 font-semibold mb-2 text-lg">
                                                <?php echo htmlspecialchars($member['position']); ?>
                                            </div>

                                            <!-- Skills/Description - Fixed Height Section -->
                                            <div class="text-purple-700 dark:text-purple-300 text-xs text-left space-y-1 overflow-hidden">
                                                <?php 
                                                $description = htmlspecialchars($member['description']);
                                                // Split by bullet points (•) or newlines
                                                $bullet_points = preg_split('/[•\n]/', $description);
                                                $bullet_points = array_filter(array_map('trim', $bullet_points)); // Remove empty items
                                                $bullet_points = array_slice($bullet_points, 0, 4); // Limit to 4 points for consistent height
                                                
                                                foreach ($bullet_points as $point): 
                                                    if (!empty($point)):
                                                ?>
                                                    <div class="flex items-start gap-2">
                                                        <span class="text-yellow-500 dark:text-yellow-400 text-xs mt-0.5 flex-shrink-0">•</span>
                                                        <span class="flex-1 leading-relaxed line-clamp-2"><?php echo $point; ?></span>
                                                    </div>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Performance-Optimized Team Animations (Same Visual Look) -->
                    <style>
                        /* Original marquee animation with hardware acceleration */
                        @keyframes marquee {
                            0% {
                                transform: translate3d(0%, 0, 0);
                            }
                            100% {
                                transform: translate3d(-100%, 0, 0);
                            }
                        }
                        
                        /* Team marquee - smooth continuous animation */
                        /* Removed hover pause for better performance */
                        
                        /* Manual scroll styles - same as original */
                        .manual-scroll {
                            overflow-x: auto;
                            cursor: grab;
                        }
                        
                        .manual-scroll:active {
                            cursor: grabbing;
                        }
                        
                        .manual-scroll::-webkit-scrollbar {
                            height: 8px;
                        }
                        
                        .manual-scroll::-webkit-scrollbar-track {
                            background: rgba(147, 51, 234, 0.1);
                            border-radius: 4px;
                        }
                        
                        .manual-scroll::-webkit-scrollbar-thumb {
                            background: rgba(147, 51, 234, 0.3);
                            border-radius: 4px;
                        }
                        
                        .manual-scroll::-webkit-scrollbar-thumb:hover {
                            background: rgba(147, 51, 234, 0.5);
                        }
                        
                        /* Hardware acceleration for better performance */
                        .team-card {
                            transform: translateZ(0);
                            backface-visibility: hidden;
                        }
                        
                        /* Optimize animations for 60fps */
                        .animate-marquee {
                            will-change: transform;
                        }
                    </style>
                </div>

        </section>

        <!-- Courses Section -->
        <section id="courses" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
    <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-section">
            <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Our Courses</h2>
            <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                Learn from industry experts and gain in-demand skills
            </p>
        </div>

        <div class="relative mx-2 md:mx-4 lg:mx-6 courses-container">
            <!-- Curved Container Background -->
            <div class="bg-purple-100 dark:bg-purple-900 rounded-[3rem] border-2 border-purple-300 dark:border-purple-600 shadow-lg overflow-hidden">
                <!-- Inner padding container -->
                <div class="px-2 py-2 sm:px-3 sm:py-3 md:px-4 md:py-4">
                    <div class="scrolling-wrapper overflow-hidden rounded-[2rem] manual-scroll" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="scrolling-container flex gap-x-6 min-w-max animate-marquee">
                            <?php 
                            // Duplicate the courses array for seamless looping
                            $courses_loop = array_merge($courses, $courses);
                            foreach ($courses_loop as $index => $course): ?>
                                <div class="group animate-section w-[300px] sm:w-[320px] flex-shrink-0" style="animation-delay: <?= 100 * $index ?>ms;">
                                    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 h-[420px] flex flex-col">

                                        <!-- Banner Image -->
                                        <?php if (!empty($course['banner_image'])): ?>
                                            <div class="overflow-hidden rounded-t-3xl flex-shrink-0">
                                                <img src="<?= htmlspecialchars($course['banner_image']) ?>"
                                                    alt="<?= htmlspecialchars($course['title']) ?>"
                                                    class="w-full h-48 object-cover rounded-t-3xl transition-transform duration-300 group-hover:scale-110"
                                                    loading="lazy"
                                                    onerror="this.parentElement.innerHTML='<div class=&quot;w-full h-48 bg-purple-200 flex items-center justify-center rounded-t-3xl text-purple-600 font-semibold&quot;>Course Banner</div>'">
                                            </div>
                                        <?php else: ?>
                                            <div class="w-full h-48 bg-purple-200 flex items-center justify-center rounded-t-3xl text-purple-600 font-semibold flex-shrink-0 transition-transform duration-300 group-hover:scale-105">
                                                Course Banner
                                            </div>
                                        <?php endif; ?>

                                        <!-- Content -->
                                        <div class="p-4 flex flex-col flex-grow relative z-10 transition-transform duration-300 group-hover:scale-105">
                                            <!-- Course Title -->
                                            <div class="mb-3">
                                                <h3 class="text-lg font-bold text-purple-900 dark:text-purple-100 leading-tight"><?= htmlspecialchars($course['title']) ?></h3>
                                            </div>

                                            <!-- Description -->
                                            <p class="text-sm text-purple-700 dark:text-purple-300 line-clamp-2 mb-4 flex-grow"><?= htmlspecialchars($course['description']) ?></p>

                                            <!-- Course Details Section - Clean Layout -->
                                            <div class="bg-purple-50 dark:bg-purple-800/30 rounded-lg p-3 mb-3">
                                                <div class="flex items-center justify-between text-sm">
                                                    <!-- Duration -->
                                                    <div class="flex items-center space-x-2 text-purple-700 dark:text-purple-300">
                                                        <i class="fas fa-clock text-yellow-500 w-4 h-4"></i>
                                                        <span><?= htmlspecialchars($course['duration']) ?></span>
                                                    </div>
                                                    
                                                    <!-- Students -->
                                                    <div class="flex items-center space-x-2 text-purple-700 dark:text-purple-300">
                                                        <i class="fas fa-users text-yellow-500 w-4 h-4"></i>
                                                        <span><?= number_format($course['enrolled_students']) ?></span>
                                                    </div>
                                                    
                                                    <!-- Rating -->
                                                    <div class="flex items-center space-x-2 text-purple-700 dark:text-purple-300">
                                                        <i class="fas fa-star text-yellow-500 w-4 h-4"></i>
                                                        <span><?= number_format($course['rating'], 1) ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Check Out Button with Glowing Animation -->
                                            <div class="text-center mt-auto">
                                                <button onclick="window.open('<?= htmlspecialchars($course['course_link'] ?? '#') ?>', '_blank')"
                                                    class="w-full py-2 px-4 rounded-xl bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold text-sm shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 relative overflow-hidden glow-border">
                                                    <span class="relative z-10 flex items-center justify-center space-x-2">
                                                        <span>Check Out</span>
                                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .scrolling-wrapper::-webkit-scrollbar {
                display: none;
            }
            .animate-marquee {
                animation: marquee 60s linear infinite;
                will-change: transform; /* Improves performance */
            }
            /* Removed hover-pause for smoother performance */
            @keyframes marquee {
                0% {
                    transform: translateX(0);
                }
                100% {
                    transform: translateX(-50%); /* Move half the width due to duplicated content */
                }
            }
        </style>
    </div>
</section>

        <!-- Course Testimonials Section -->
        <section id="student-testimonials" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
            <!-- Decorative Backgrounds -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16 animate-section">
                    <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Testimonials from Courses</h2>
                    <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                        Hear from students who achieved success with our courses
                    </p>
                </div>

                <!-- Marquee Slider -->
                <div class="relative overflow-hidden course-testimonials-container manual-scroll">
                    <div class="flex animate-marquee" style="width: calc(300% + 48px); animation: marquee 40s linear infinite;">
                        <?php foreach (array_merge($course_testimonials, $course_testimonials) as $testimonial): ?>
                            <div class="flex-none w-80 mx-4 relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group p-6">
                                <!-- Tech background pattern -->
                                <div class="absolute inset-0 opacity-10 pointer-events-none">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>

                                <!-- Star Rating -->
                                <div class="relative z-10 flex items-center mb-4 star-rating transition-transform duration-500 group-hover:scale-105">
                                    <?php
                                    $rating = floor($testimonial['rating']);
                                    for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $rating): ?>
                                            <i class="fas fa-star text-yellow-500 h-5 w-5 mr-1"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-yellow-500 h-5 w-5 mr-1"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="ml-2 text-purple-700 dark:text-purple-300 font-medium"><?= number_format($testimonial['rating'], 1) ?></span>
                                </div>

                                <!-- Testimonial Message -->
                                <p class="relative z-10 text-purple-700 dark:text-purple-300 mb-4 transition-transform duration-500 group-hover:scale-105">
                                    <?= htmlspecialchars($testimonial['message']) ?>
                                </p>

                                <!-- Author -->
                                <div class="relative z-10 flex items-center transition-transform duration-500 group-hover:scale-105">
                                    <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center">
                                        <?php if (!empty($testimonial['image'])): ?>
                                            <?php 
                                                // Use optimized image caching for better performance
                                                $imagePath = getOptimizedImagePath($testimonial['image'], 'image/jpeg', 'testimonial', $testimonial['name']);
                                            ?>
                                            <img src="<?= $imagePath ?>"
                                                alt="<?= htmlspecialchars($testimonial['name']) ?>"
                                                class="w-full h-full object-cover"
                                                loading="lazy" />
                                        <?php else: ?>
                                            <div class="w-full h-full bg-purple-200 dark:bg-purple-700 flex items-center justify-center text-purple-900 dark:text-purple-200 font-semibold">
                                                <?= htmlspecialchars(substr($testimonial['name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-semibold text-purple-900 dark:text-purple-200"><?= htmlspecialchars($testimonial['name']) ?></p>
                                        <p class="text-sm text-purple-700 dark:text-purple-300"><?= htmlspecialchars($testimonial['course']) ?></p>
                                        <p class="text-sm text-purple-700 dark:text-purple-300"><?= htmlspecialchars($testimonial['email']) ?></p>
                                    </div>
                                </div>

                                <!-- Optional Video -->
                                <?php if (!empty($testimonial['video'])): ?>
                                    <div class="flex justify-center mt-3">
                                        <a href="<?= htmlspecialchars($testimonial['video']) ?>" target="_blank"
                                            class="text-purple-400 hover:text-yellow-500 transition">
                                            <i data-lucide="video" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Course Testimonials Animation -->
        <style>
            @keyframes marquee {
                0% {
                    transform: translateX(0);
                }

                100% {
                    transform: translateX(-50%);
                }
            }
            
            /* Removed course testimonials hover-pause for smoother performance */
        </style>

        <!-- Educational Videos Section -->
        <section id="videos-section" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-section">
                    <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">
                        Our Latest YouTube Videos
                    </h2>
                    <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                        Discover our latest tutorials and insights on our YouTube channel
                    </p>
                </div>

                <!-- Videos Marquee -->
                <div class="relative mx-2 md:mx-4 lg:mx-6">
                    <!-- Curved Container Background -->
                    <div class="bg-gradient-to-r from-purple-100/50 via-purple-50/30 to-purple-100/50 dark:from-purple-900/20 dark:via-purple-800/10 dark:to-purple-900/20 rounded-[3rem] border-2 border-purple-300/70 dark:border-purple-600/60 shadow-lg shadow-purple-200/50 dark:shadow-purple-900/30 backdrop-blur-sm overflow-hidden ring-1 ring-purple-400/20 dark:ring-purple-500/30">
                        <!-- Inner padding container -->
                        <div class="px-2 py-2 sm:px-3 sm:py-3 md:px-4 md:py-4">
                            <div id="videos-wrapper" class="overflow-hidden animate-section rounded-[2rem] manual-scroll" style="scrollbar-width: none; -ms-overflow-style: none;">
                                <div id="videos" class="flex gap-x-6 min-h-[200px] animate-marquee">
                                    <?php if (empty($youtube_videos)): ?>
                                        <div class="text-purple-700 dark:text-purple-300 text-center w-full">
                                            No videos available at the moment. Please check back later!
                                        </div>
                                    <?php else: ?>
                                        <!-- First set of videos -->
                                        <?php foreach ($youtube_videos as $video): 
                                            $video_id = '';
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video['video_url'], $matches)) {
                                                $video_id = $matches[1];
                                            }
                                        ?>
                                            <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-300 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 min-w-[300px] max-w-[350px] cursor-pointer flex-shrink-0 group" onclick="openVideoModal('<?= $video_id ?>', '<?= htmlspecialchars($video['title']) ?>')">
                                                <div class="relative w-full overflow-hidden border-b border-purple-300 dark:border-purple-600 transition-transform duration-300 group-hover:scale-105" style="height: 200px;">
                                                    <img src="https://img.youtube.com/vi/<?= $video_id ?>/maxresdefault.jpg" alt="<?= htmlspecialchars($video['title']) ?>"
                                                        class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105" 
                                                        onerror="this.src='https://img.youtube.com/vi/<?= $video_id ?>/hqdefault.jpg'" />
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center opacity-80 hover:opacity-100 transition-opacity">
                                                            <svg class="w-4 h-4 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-4">
                                                    <h4 class="text-lg font-semibold text-purple-900 dark:text-purple-200 mb-2 line-clamp-2">
                                                        <?= htmlspecialchars($video['title']) ?>
                                                    </h4>
                                                    <button class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-yellow-400 text-purple-900 font-bold text-sm shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 w-full glow-border">
                                                        <span class="relative z-10">Watch Now</span>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <!-- Duplicate set for seamless loop -->
                                        <?php foreach ($youtube_videos as $video): 
                                            $video_id = '';
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video['video_url'], $matches)) {
                                                $video_id = $matches[1];
                                            }
                                        ?>
                                            <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-300 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 min-w-[300px] max-w-[350px] cursor-pointer flex-shrink-0 group" onclick="openVideoModal('<?= $video_id ?>', '<?= htmlspecialchars($video['title']) ?>')">
                                                <div class="relative w-full overflow-hidden border-b border-purple-300 dark:border-purple-600 transition-transform duration-300 group-hover:scale-105" style="height: 200px;">
                                                    <img src="https://img.youtube.com/vi/<?= $video_id ?>/maxresdefault.jpg" alt="<?= htmlspecialchars($video['title']) ?>"
                                                        class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105" 
                                                        onerror="this.src='https://img.youtube.com/vi/<?= $video_id ?>/hqdefault.jpg'" />
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center opacity-80 hover:opacity-100 transition-opacity">
                                                            <svg class="w-4 h-4 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-4">
                                                    <h4 class="text-lg font-semibold text-purple-900 dark:text-purple-200 mb-2 line-clamp-2">
                                                        <?= htmlspecialchars($video['title']) ?>
                                                    </h4>
                                                    <button class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-yellow-400 text-purple-900 font-bold text-sm shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 w-full glow-border">
                                                        <span class="relative z-10">Watch Now</span>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Curved fade edges for seamless effect -->
                        <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-purple-100/80 to-transparent dark:from-purple-900/40 dark:to-transparent pointer-events-none rounded-l-[3rem]"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-purple-100/80 to-transparent dark:from-purple-900/40 dark:to-transparent pointer-events-none rounded-r-[3rem]"></div>
                    </div>
                </div>

                <!-- Hide scrollbar (WebKit) -->
                <style>
                    #videos-wrapper::-webkit-scrollbar {
                        display: none;
                    }

                    .animate-marquee {
                        animation: marquee 40s linear infinite;
                        will-change: transform;
                        width: fit-content;
                    }
                    /* Removed videos hover-pause for smoother performance */

                    @keyframes marquee {
                        0% {
                            transform: translateX(0%);
                        }
                        100% {
                            transform: translateX(-50%);
                        }
                    }

                    /* Ensure smooth transitions */
                    #videos {
                        transition: transform 0.1s ease-out;
                    }
                    
                    /* Mission Video Player Styles */
                    .video-thumbnail {
                        transition: all 0.3s ease;
                    }
                    
                    .video-thumbnail:hover {
                        transform: scale(1.02);
                    }
                    
                    .video-player {
                        z-index: 10;
                    }
                    
                    .video-player iframe,
                    .video-player video {
                        transition: opacity 0.3s ease;
                        display: block;
                        width: 100% !important;
                        height: 100% !important;
                        border: none;
                        outline: none;
                        border-radius: 1.5rem; /* Match rounded-3xl */
                    }
                </style>
            </div>
        </section>

        <!-- Client Testimonials Section -->
       <section id="testimonials" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-1/3 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16 animate-section">
            <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">What Our Clients Say</h2>
            <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                Real stories from our valued clients who partnered with NBT
            </p>
        </div>

        <!-- Marquee Testimonial Cards -->
        <div class="relative mx-2 md:mx-4 lg:mx-6 client-testimonials-container">
            <!-- Curved Container Background -->
            <div class="bg-gradient-to-r from-purple-100/50 via-purple-50/30 to-purple-100/50 dark:from-purple-900/20 dark:via-purple-800/10 dark:to-purple-900/20 rounded-[3rem] border-2 border-purple-300/70 dark:border-purple-600/60 shadow-lg shadow-purple-200/50 dark:shadow-purple-900/30 backdrop-blur-sm overflow-hidden ring-1 ring-purple-400/20 dark:ring-purple-500/30">
                <!-- Inner padding container -->
                <div class="px-2 py-2 sm:px-3 sm:py-3 md:px-4 md:py-4">
                    <div class="relative overflow-hidden rounded-[2rem] manual-scroll" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="flex animate-marquee gap-x-8" style="width: max-content;">
                            <?php foreach (array_merge($clients_testimonials, $clients_testimonials) as $testimonial): ?>
                                <div class="flex-none w-80 mx-4 rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group">
                                    <!-- Rating Stars -->
                                    <div class="flex items-center mt-6 mb-4 px-6 star-rating transition-transform duration-500 group-hover:scale-105">
                                        <?php $rating = floor($testimonial['rating']); ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $rating): ?>
                                                <i class="fas fa-star text-yellow-500 h-5 w-5 mr-1"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-yellow-500 h-5 w-5 mr-1"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-purple-700 dark:text-purple-300 font-medium"><?= number_format($testimonial['rating'], 1) ?></span>
                                    </div>

                                    <!-- Testimonial Text -->
                                    <p class="text-purple-700 dark:text-purple-300 mb-4 px-6 transition-transform duration-500 group-hover:scale-105"><?php echo htmlspecialchars($testimonial['project_description']); ?></p>

                        <!-- User Info -->
                        <div class="flex items-center px-6 pb-4 transition-transform duration-500 group-hover:scale-105">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-purple-200 dark:bg-purple-700">
                                <?php if (!empty($testimonial['company_logo'])): ?>
                                    <?php 
                                        // Use optimized image caching for better performance
                                        $imagePath = getOptimizedImagePath($testimonial['company_logo'], 'image/jpeg', 'company', $testimonial['company_name']);
                                    ?>
                                    <img src="<?= $imagePath ?>"
                                        alt="<?php echo htmlspecialchars($testimonial['company_name']); ?>"
                                        class="w-full h-full object-cover"
                                        loading="lazy" />
                                <?php else: ?>
                                    <div class="text-purple-900 dark:text-purple-200 font-semibold">
                                        <?php echo htmlspecialchars(substr($testimonial['company_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-purple-900 dark:text-purple-200"><?php echo htmlspecialchars($testimonial['company_name']); ?></p>
                                <p class="text-sm text-purple-700 dark:text-purple-300"><?php echo htmlspecialchars($testimonial['company_email']); ?></p>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="flex justify-center gap-4 mb-6">
                            <?php if (!empty($testimonial['linkedin'])): ?>
                                <a href="<?= htmlspecialchars($testimonial['linkedin']) ?>" target="_blank"
                                    class="text-purple-400 hover:text-yellow-500 transition">
                                    <i data-lucide="linkedin" class="w-5 h-5"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Curved fade edges for seamless effect -->
                <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-purple-100/80 to-transparent dark:from-purple-900/40 dark:to-transparent pointer-events-none rounded-l-[3rem]"></div>
                <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-purple-100/80 to-transparent dark:from-purple-900/40 dark:to-transparent pointer-events-none rounded-r-[3rem]"></div>
            </div>
        </div>
    </div>
</section>

<!-- Client Testimonials Keyframes -->
<style>
    @keyframes marquee {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    .animate-marquee {
        animation: marquee 40s linear infinite;
        will-change: transform; /* Improves performance */
    }
    /* Removed client testimonials hover-pause for smoother performance */
    
    /* Manual scroll functionality */
    .manual-scroll {
        overflow-x: auto !important;
        cursor: grab;
        scrollbar-width: thin;
        scrollbar-color: rgba(168, 85, 247, 0.3) transparent;
    }
    .manual-scroll::-webkit-scrollbar {
        height: 6px;
    }
    .manual-scroll::-webkit-scrollbar-track {
        background: rgba(168, 85, 247, 0.1);
        border-radius: 3px;
    }
    .manual-scroll::-webkit-scrollbar-thumb {
        background: rgba(168, 85, 247, 0.3);
        border-radius: 3px;
    }
    .manual-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(168, 85, 247, 0.5);
    }
    .manual-scroll:active {
        cursor: grabbing;
    }
    /* Removed manual scroll hover-pause for smoother performance */
</style>

        <!-- Special Offers Section -->
        <section id="coupons" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16 animate-section">
                    <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Special Offers</h2>
                    <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                        Use these exclusive coupons for discounts on your courses and services.
                    </p>
                </div>

                <!-- Coupons Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 animate-section" style="animation-delay: 200ms;">
                    <?php foreach ($coupons as $coupon): ?>
                        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group">
                            <!-- Tech Grid Pattern -->
                            <div class="absolute inset-0 opacity-10 pointer-events-none">
                                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                            </div>

                            <!-- Main Content Section -->
                            <div class="relative z-10 px-6 py-6 transition-transform duration-500 group-hover:scale-105">
                                <!-- Coupon Code Display -->
                                <div class="text-center mb-5 p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/50 border border-purple-200/50 dark:border-purple-600/50">
                                    <div class="text-xs font-semibold uppercase tracking-widest text-purple-600 dark:text-purple-300 mb-1">Coupon Code</div>
                                    <h3 class="text-2xl font-black tracking-[0.2em] mb-3 font-mono transform group-hover:scale-105 transition-transform duration-300 text-purple-900 dark:text-purple-100">
                                        <?= htmlspecialchars($coupon['code']) ?>
                                    </h3>

                                    <!-- Discount Display -->
                                    <div class="flex items-center justify-center space-x-2">
                                        <div class="text-4xl font-black text-yellow-500 dark:text-yellow-400 transform group-hover:scale-110 transition-all duration-300">
                                            <?= htmlspecialchars($coupon['discount']) ?>%
                                        </div>
                                        <div class="text-sm font-bold text-purple-600 dark:text-purple-300 uppercase tracking-wider">
                                            OFF
                                        </div>
                                    </div>
                                </div>

                                <!-- Time Limit with Enhanced Design -->
                                <div class="flex items-center justify-center mb-4 p-2 rounded-lg bg-purple-50 dark:bg-purple-900/50 border border-purple-200/40 dark:border-purple-600/40">
                                    <div class="flex items-center space-x-2 text-purple-600 dark:text-purple-300">
                                        <div class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-800 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold uppercase tracking-wide opacity-70">Valid Until</div>
                                            <div class="text-xs font-bold"><?= htmlspecialchars($coupon['time_limit']) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Enhanced CTA Button -->
                                <button class="w-full relative overflow-hidden py-3 px-6 rounded-2xl bg-yellow-400 text-purple-900 font-bold text-base shadow-lg hover:shadow-xl transform hover:scale-[1.02] hover:-translate-y-1 transition-all duration-300 glow-border">
                                    <span class="relative z-10 flex items-center justify-center space-x-2">
                                        <span>Use This Code</span>
                                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Bottom CTA -->
                <div class="text-center mt-16 animate-section" style="animation-delay: 400ms;">
                    <p class="text-purple-700 dark:text-purple-300 mb-4 text-lg">
                        🎉 Don't wait! These offers expire soon!
                    </p>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
    <!-- Decorative Background Blobs -->
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
    <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16 animate-section">
            <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Our Services</h2>
            <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                Comprehensive solutions to elevate your business and career
            </p>
        </div>

        <!-- Horizontal Scrolling Cards -->
        <div class="relative mx-2 md:mx-4 lg:mx-6">
            <!-- Curved Container Background -->
            <div class="bg-purple-100 dark:bg-purple-900 rounded-[3rem] border-2 border-purple-300 dark:border-purple-600 shadow-lg overflow-hidden">
                <!-- Inner padding container -->
                <div class="px-2 py-2 sm:px-3 sm:py-3 md:px-4 md:py-4">
                    <div class="scrolling-wrapper overflow-hidden rounded-[2rem] manual-scroll" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="scrolling-container flex space-x-6 min-w-max pb-4 animate-marquee">
                            <?php 
                            // Duplicate the services array for seamless looping
                            $services_loop = array_merge($services, $services);
                            foreach ($services_loop as $index => $service): ?>
                                <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:scale-105 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group w-[300px] sm:w-[320px] flex-shrink-0 animate-section" style="animation-delay: <?= 100 * $index ?>ms;">
                                    <!-- Tech Grid Background -->
                                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                    </div>

                                    <!-- Image -->
                                    <div class="overflow-hidden rounded-t-3xl">
                                        <?php 
                                            // Use optimized image caching for better performance
                                            $imagePath = getOptimizedImagePath($service['image_data'], $service['image_type'], 'service', $service['id']);
                                        ?>
                                        <img src="<?= $imagePath ?>" 
                                             alt="<?= htmlspecialchars($service['title']) ?>" 
                                             class="w-full h-48 object-cover transform transition-transform duration-500 ease-in-out group-hover:scale-110"
                                             loading="lazy">
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 p-6 transition-transform duration-500 group-hover:scale-105">
                                        <h3 class="text-xl font-bold text-purple-900 dark:text-purple-200 mb-2 group-hover:text-yellow-500 transition-colors duration-300"><?= htmlspecialchars($service['title']) ?></h3>
                            <p class="text-purple-700 dark:text-purple-300 mb-4"><?= htmlspecialchars($service['description']) ?></p>
                            <ul class="list-disc list-inside text-purple-700 dark:text-purple-300 mb-4">
                                <?php foreach (explode(',', $service['points']) as $point): ?>
                                    <li><?= htmlspecialchars(trim($point)) ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <!-- CTA Button -->
                            <button onclick="scrollToSection('contact')" class="w-full relative overflow-hidden py-3 px-6 rounded-2xl bg-yellow-400 text-purple-900 font-bold text-base shadow-lg hover:shadow-xl transform hover:scale-[1.02] hover:-translate-y-1 transition-all duration-300 glow-border">
                                <span class="relative z-10 flex items-center justify-center space-x-2">
                                    <span>Get Started</span>
                                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .scrolling-wrapper::-webkit-scrollbar {
                display: none;
            }
            .animate-marquee {
                animation: marquee 20s linear infinite;
                will-change: transform; /* Improves performance */
            }
            /* Removed scrolling wrapper hover-pause for smoother performance */
            @keyframes marquee {
                0% {
                    transform: translateX(0);
                }
                100% {
                    transform: translateX(-50%); /* Move half the width due to duplicated content */
                }
            }
        </style>
    </div>
</section>


        <!-- Client Section -->
        <section id="client" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-1/3 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16 animate-section">
            <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Our Clients</h2>
            <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                Trusted partnerships delivering exceptional results across industries
            </p>
        </div>

        <!-- Scrolling Container -->
        <div class="relative mx-2 md:mx-4 lg:mx-6 clients-container">
            <!-- Curved Container Background -->
            <div class="bg-gradient-to-r from-purple-100/50 via-purple-50/30 to-purple-100/50 dark:from-purple-900/20 dark:via-purple-800/10 dark:to-purple-900/20 rounded-[3rem] border-2 border-purple-300/70 dark:border-purple-600/60 shadow-lg shadow-purple-200/50 dark:shadow-purple-900/30 backdrop-blur-sm overflow-hidden ring-1 ring-purple-400/20 dark:ring-purple-500/30">
                <!-- Inner padding container -->
                <div class="px-2 py-2 sm:px-3 sm:py-3 md:px-4 md:py-4">
                    <div class="relative overflow-hidden rounded-[2rem] manual-scroll">
                        <div class="flex animate-marquee gap-8" style="animation: marquee 40s linear infinite;">
                            <?php 
                            // Double the clients array for seamless looping
                            $clients_loop = array_merge($clients, $clients);
                            foreach ($clients_loop as $index => $client): ?>
                                <div class="flex-shrink-0 w-80 group">
                                    <!-- Client Card -->
                                    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl transform transition-all duration-500 hover:shadow-3xl hover:border-yellow-400 dark:hover:border-yellow-400 group-hover:border-yellow-400 dark:group-hover:border-yellow-400 h-80">
                                        
                                        <!-- Decorative Background Pattern -->
                                        <div class="absolute inset-0 opacity-5 pointer-events-none">
                                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                        </div>
                                        
                                        <!-- Card Content -->
                                        <div class="relative z-10 p-6 h-full flex flex-col transition-transform duration-500 group-hover:scale-105">
                                            <!-- Company Logo & Header -->
                                            <div class="flex items-center gap-4 mb-6">
                                                <!-- Company Logo -->
                                                <div class="w-24 h-24 rounded-xl bg-gradient-to-br from-purple-100 to-yellow-100 dark:from-purple-800 dark:to-yellow-800 flex items-center justify-center border-2 border-yellow-400/30 dark:border-yellow-400/50 shadow-lg">
                                        <?php if (!empty($client['company_logo'])): ?>
                                            <?php 
                                                // Use optimized image caching for better performance
                                                $imagePath = getOptimizedImagePath($client['company_logo'], 'image/jpeg', 'client', $client['id']);
                                            ?>
                                            <img src="<?= $imagePath ?>" 
                                                 alt="<?= htmlspecialchars($client['company_name']) ?>" 
                                                 class="w-full h-full object-cover rounded-lg"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <i data-lucide="building-2" class="w-10 h-10 text-purple-600 dark:text-purple-300"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Company Name -->
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-purple-900 dark:text-purple-200 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors duration-300 mb-1">
                                            <?= htmlspecialchars($client['company_name']) ?>
                                        </h3>
                                        <p class="text-sm text-purple-600 dark:text-purple-400">Client Partner</p>
                                    </div>
                                </div>
                                
                                <!-- Project Description -->
                                <div class="bg-purple-50/50 dark:bg-purple-900/30 rounded-2xl p-4 border border-purple-200/30 dark:border-purple-700/30 flex-1">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-purple-500 flex items-center justify-center flex-shrink-0 mt-1">
                                            <i data-lucide="briefcase" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-purple-700 dark:text-purple-300 mb-2">Project Details</h4>
                                            <p class="text-purple-900 dark:text-purple-100 text-sm leading-relaxed line-clamp-4">
                                                <?= htmlspecialchars($client['task']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Badge - Bottom Right -->
                            <div class="absolute bottom-4 right-4 z-20">
                                <?php 
                                $status = strtolower($client['status']);
                                $statusColors = [
                                    'active' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
                                    'completed' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-800',
                                    'cancelled' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800'
                                ];
                                $statusClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-900/20 dark:text-gray-300 dark:border-gray-800';
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border shadow-lg <?= $statusClass ?>">
                                    <span class="w-2 h-2 rounded-full mr-2 
                                        <?= $status === 'active' ? 'bg-blue-500' : 
                                           ($status === 'completed' ? 'bg-green-500' : 
                                           ($status === 'pending' ? 'bg-yellow-500' : 'bg-red-500')) ?>">
                                    </span>
                                    <?= ucfirst($client['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Curved fade edges for seamless effect -->
                <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-purple-100/80 to-transparent dark:from-purple-900/40 dark:to-transparent pointer-events-none rounded-l-[3rem]"></div>
                <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-purple-100/80 to-transparent dark:from-purple-900/40 dark:to-transparent pointer-events-none rounded-r-[3rem]"></div>
            </div>
        </div>
        
        <!-- Animation Styles -->
        <style>
            .relative::-webkit-scrollbar {
                display: none;
            }
            @keyframes marquee {
                0% {
                    transform: translateX(0);
                }
                100% {
                    transform: translateX(-50%);
                }
            }
            .animate-marquee {
                animation: marquee 40s linear infinite;
                will-change: transform;
            }
            /* Removed clients container hover-pause for smoother performance */
        </style>
    </div>
</section>



        <!-- Social Media Section -->
        <section id="social-media" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 animate-section">
                    <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">
                        Connect With Us
                    </h2>
                    <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                        Follow our journey and stay updated with the latest insights
                    </p>
                </div>

                <!-- Linktree Preview Section -->
                <div class="mb-16 animate-section" style="animation-delay: 100ms;">
                    <div class="bg-gradient-to-br from-purple-50 to-yellow-50 dark:from-purple-900/50 dark:to-yellow-900/20 rounded-3xl p-8 border-4 border-yellow-400/80 dark:border-yellow-400/90 shadow-xl">
                        <div class="flex flex-col lg:flex-row items-center gap-8">
                            <!-- Linktree Info -->
                            <div class="flex-1 text-center lg:text-left">
                                <div class="flex items-center justify-center lg:justify-start gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-purple-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-link text-white text-xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-purple-900 dark:text-purple-200">Our Linktree</h3>
                                </div>
                                <p class="text-purple-700 dark:text-purple-300 mb-6 leading-relaxed">
                                    Discover all our platforms, projects, and latest updates in one convenient place. 
                                    Connect with Aditya Jain and explore our complete digital ecosystem.
                                </p>
                                <a href="https://linktr.ee/aditya.jain.iitb" target="_blank"
                                   class="inline-flex items-center gap-3 bg-gradient-to-r from-green-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 group">
                                    <i class="fas fa-external-link-alt group-hover:rotate-12 transition-transform duration-300"></i>
                                    Visit Our Linktree
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-300"></i>
                                </a>
                            </div>
                            
                            <!-- Linktree Preview -->
                            <div class="flex-1 max-w-md w-full">
                                <div class="bg-white dark:bg-purple-900/90 rounded-3xl shadow-xl border-4 border-yellow-400/80 dark:border-yellow-400/90 overflow-hidden">
                                    <!-- Mock Linktree Header -->
                                    <div class="bg-gradient-to-r from-green-400 to-purple-600 p-4 text-center">
                                        <div class="w-16 h-16 bg-white rounded-full mx-auto mb-3 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-lg">AJ</span>
                                        </div>
                                        <h4 class="text-white font-bold text-lg">@aditya.jain.iitb</h4>
                                        <p class="text-white/90 text-sm">Tech Entrepreneur | IIT Graduate</p>
                                    </div>
                                    
                                    <!-- Mock Linktree Links -->
                                    <div class="p-4 space-y-3">
                                        <div class="bg-purple-50 dark:bg-purple-800/50 rounded-xl p-3 border border-purple-200/30 dark:border-purple-700/30">
                                            <div class="flex items-center gap-3">
                                                <i class="fab fa-linkedin text-blue-600 text-lg"></i>
                                                <span class="text-purple-900 dark:text-purple-200 font-medium">LinkedIn Profile</span>
                                            </div>
                                        </div>
                                        <div class="bg-purple-50 dark:bg-purple-800/50 rounded-xl p-3 border border-purple-200/30 dark:border-purple-700/30">
                                            <div class="flex items-center gap-3">
                                                <i class="fab fa-youtube text-red-600 text-lg"></i>
                                                <span class="text-purple-900 dark:text-purple-200 font-medium">YouTube Channel</span>
                                            </div>
                                        </div>
                                        <div class="bg-purple-50 dark:bg-purple-800/50 rounded-xl p-3 border border-purple-200/30 dark:border-purple-700/30">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-globe text-purple-600 text-lg"></i>
                                                <span class="text-purple-900 dark:text-purple-200 font-medium">NBT Website</span>
                                            </div>
                                        </div>
                                        <div class="bg-purple-50 dark:bg-purple-800/50 rounded-xl p-3 border border-purple-200/30 dark:border-purple-700/30">
                                            <div class="flex items-center gap-3">
                                                <i class="fab fa-instagram text-pink-600 text-lg"></i>
                                                <span class="text-purple-900 dark:text-purple-200 font-medium">Instagram</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
                <?php /*
                <!-- Enhanced Social Media Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 animate-section" style="animation-delay: 200ms;">
                    <!-- Card Template -->
                    <?php
                    $platforms = [
                        [
                            'icon' => 'fab fa-linkedin-in',
                            'color' => 'blue',
                            'text' => 'blue-700',
                            'bg' => 'from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20',
                            'border' => 'border-blue-500/30',
                            'iconColor' => 'text-blue-600',
                            'iconBg' => 'bg-blue-100 dark:bg-blue-800/50',
                            'data' => $row1
                        ],
                        [
                            'icon' => 'fab fa-instagram',
                            'color' => 'pink',
                            'text' => 'pink-700',
                            'bg' => 'from-pink-50 to-pink-100 dark:from-pink-900/30 dark:to-pink-800/20',
                            'border' => 'border-pink-500/30',
                            'iconColor' => 'text-pink-600',
                            'iconBg' => 'bg-pink-100 dark:bg-pink-800/50',
                            'data' => $row2
                        ],
                        [
                            'icon' => 'fab fa-youtube',
                            'color' => 'red',
                            'text' => 'red-700',
                            'bg' => 'from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/20',
                            'border' => 'border-red-500/30',
                            'iconColor' => 'text-red-600',
                            'iconBg' => 'bg-red-100 dark:bg-red-800/50',
                            'data' => $row3
                        ],
                        [
                            'icon' => 'fab fa-twitter',
                            'color' => 'sky',
                            'text' => 'sky-700',
                            'bg' => 'from-sky-50 to-sky-100 dark:from-sky-900/30 dark:to-sky-800/20',
                            'border' => 'border-sky-500/30',
                            'iconColor' => 'text-sky-600',
                            'iconBg' => 'bg-sky-100 dark:bg-sky-800/50',
                            'iconBg' => 'bg-sky-100 dark:bg-sky-800/50',
                            'data' => $row4
                        ],
                    ];
                    foreach ($platforms as $platform):
                        $data = $platform['data'];
                    ?>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br <?= $platform['bg'] ?> backdrop-blur-sm border <?= $platform['border'] ?> shadow-xl transform transition-all duration-500 hover:shadow-2xl h-64 flex flex-col">
                                <!-- Decorative Pattern -->
                                <div class="absolute inset-0 opacity-5 pointer-events-none">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>

                                <!-- Floating Icon -->
                                <div class="relative z-10 flex-1 flex items-center justify-center transition-transform duration-500 group-hover:scale-105">
                                    <div class="w-20 h-20 <?= $platform['iconBg'] ?> rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                        <i class="<?= $platform['icon'] ?> <?= $platform['iconColor'] ?> text-3xl"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="relative z-10 p-6 text-center">
                                    <h3 class="text-lg font-bold <?= $platform['text'] ?> dark:text-<?= $platform['color'] ?>-300 mb-2">
                                        <?= htmlspecialchars($data['platform']) ?>
                                    </h3>
                                    
                                    <!-- Stats -->
                                    <div class="flex items-center justify-center gap-2 mb-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-purple-700 dark:text-purple-300 text-sm font-medium">
                                            <span class="follower-count font-bold text-yellow-600 dark:text-yellow-400" data-target="<?= htmlspecialchars($data['followers']) ?>">0</span> 
                                            Followers
                                        </span>
                                    </div>
                                    
                                    <!-- CTA -->
                                    <div class="text-xs text-purple-600 dark:text-purple-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        Click to follow →
                                    </div>
                                </div>

                                <!-- Hover Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-<?= $platform['color'] ?>-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Call to Action -->
                <div class="text-center mt-12 animate-section" style="animation-delay: 400ms;">
                    <p class="text-purple-700 dark:text-purple-300 mb-6 text-lg">
                        🚀 Ready to connect? Join our growing community!
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="https://linktr.ee/aditya.jain.iitb" target="_blank"
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-yellow-500 text-white px-6 py-3 rounded-2xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-link"></i>
                            All Links
                        </a>
                        <button onclick="document.getElementById('contact').scrollIntoView({behavior: 'smooth'})"
                                class="inline-flex items-center gap-2 bg-white dark:bg-purple-900 text-purple-600 dark:text-purple-300 border border-purple-300 dark:border-purple-700 px-6 py-3 rounded-2xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-envelope"></i>
                            Get in Touch
                        </button>
                    </div>
                </div>
                */ ?>
         
        

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white dark:bg-purple-950 relative overflow-hidden">
            <!-- Background Blobs -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-yellow-400/20 dark:bg-yellow-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/20 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-2/3 left-1/2 w-48 h-48 bg-yellow-300/20 dark:bg-yellow-400/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16 animate-section">
                    <h2 class="text-4xl font-bold text-purple-900 dark:text-purple-200 mb-4 tracking-tight">Get in Touch</h2>
                    <p class="text-xl text-purple-700 dark:text-purple-300 max-w-3xl mx-auto">
                        Have questions or ready to start your journey? Contact us for a free consultation.
                    </p>
                </div>

                <!-- Contact Form + Image -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="animate-section" style="animation-delay: 200ms;">
                        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl hover:border-yellow-400 dark:hover:border-yellow-400 p-8">
                            <!-- Tech grid pattern -->
                            <div class="absolute inset-0 opacity-10 pointer-events-none">
                                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                            </div>
                            <form method="POST" class="relative z-10 space-y-6">
                                <input type="hidden" name="contact_form" value="1">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-purple-900 dark:text-purple-200">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required
                                        class="mt-2 w-full px-4 py-3 border border-purple-300 dark:border-purple-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 bg-white dark:bg-purple-800 text-purple-900 dark:text-purple-200">
                                </div>
                                <div>
                                    <label for="email_address" class="block text-sm font-medium text-purple-900 dark:text-purple-200">Email Address</label>
                                    <input type="email" id="email_address" name="email_address" placeholder="Enter your email address" required
                                        class="mt-2 w-full px-4 py-3 border border-purple-300 dark:border-purple-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 bg-white dark:bg-purple-800 text-purple-900 dark:text-purple-200">
                                </div>
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-purple-900 dark:text-purple-200">Subject</label>
                                    <input type="text" id="subject" name="subject" placeholder="Enter the subject" required
                                        class="mt-2 w-full px-4 py-3 border border-purple-300 dark:border-purple-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 bg-white dark:bg-purple-800 text-purple-900 dark:text-purple-200">
                                </div>
                                <div>
                                    <label for="message" class="block text-sm font-medium text-purple-900 dark:text-purple-200">Message</label>
                                    <textarea id="message" name="message" rows="5" placeholder="Tell us about your goals and how can we help we..." required
                                        class="mt-2 w-full px-4 py-3 border border-purple-300 dark:border-purple-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 bg-white dark:bg-purple-800 text-purple-900 dark:text-purple-200"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full relative overflow-hidden py-3 px-6 rounded-2xl bg-yellow-400 text-purple-900 font-bold text-base shadow-lg hover:shadow-xl transform hover:scale-[1.02] hover:-translate-y-1 transition-all duration-300 glow-border">
                                    <span class="relative z-10 flex items-center justify-center space-x-2">
                                        <span>Send Message</span>
                                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </span>
                                </button>
                                <?php if (isset($_GET['success']) && $_GET['success'] === 'contact_submitted'): ?>
                                    <p class="mt-4 text-green-500 dark:text-green-400 text-center">Thank you for your message! We'll get back to you soon.</p>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <!-- Contact Image -->
                    <div class="animate-section" style="animation-delay: 400ms;">
                        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-purple-900/90 border-4 border-yellow-400/80 dark:border-yellow-400/90 backdrop-blur-sm shadow-2xl hover:border-yellow-400 dark:hover:border-yellow-400 p-4 group transform transition duration-500 hover:shadow-3xl">
                            <!-- Tech pattern -->
                            <div class="absolute inset-0 opacity-10 pointer-events-none">
                                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, var(--yellow-accent) 1px, transparent 0); background-size: 20px 20px;"></div>
                            </div>
                            <img src="/assert/20250126_145421.jpg" alt="Contact us"
                                class="relative z-10 w-full h-full object-cover rounded-2xl transition-transform duration-500 group-hover:scale-110"
                                loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Footer -->
        <footer class="bg-white dark:bg-purple-950 pt-0 pb-12 mt-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Logo and Description -->
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <img src="/assert/black.png" alt="NBT Logo" class="h-10 w-10 object-contain">
                            <div class="text-lg font-semibold text-purple-900 dark:text-purple-200">NBT</div>
                        </div>
                        <p class="text-purple-700 dark:text-purple-300">
                            Empowering your future with cutting-edge tech education and consultancy.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-200 mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><button onclick="scrollToSection('home')" class="text-purple-700 dark:text-purple-300 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">Home</button></li>
                            <li><button onclick="scrollToSection('about')" class="text-purple-700 dark:text-purple-300 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">About</button></li>
                            <li><button onclick="scrollToSection('services')" class="text-purple-700 dark:text-purple-300 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">Services</button></li>
                            <li><button onclick="scrollToSection('courses')" class="text-purple-700 dark:text-purple-300 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">Courses</button></li>
                            <li><button onclick="scrollToSection('contact')" class="text-purple-700 dark:text-purple-300 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">Contact</button></li>
                            <li>
                                <a href="admin/dashboard.php" class="text-purple-700 dark:text-purple-300 hover:text-yellow-500 dark:hover:text-yellow-400 transition-colors duration-300">
                                    Admin Login
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-200 mb-4">Contact Us</h3>
                        <p class="text-purple-700 dark:text-purple-300 mb-2">Email: info@nbt.com</p>
                        <p class="text-purple-700 dark:text-purple-300 mb-2">Phone: +1 (123) 456-7890</p>
                        <p class="text-purple-700 dark:text-purple-300">Address: 123 Tech Lane, Innovation City</p>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="mt-8 text-center text-purple-700 dark:text-purple-300">
                    <p>&copy; <?php echo date('Y'); ?> Next Bigg Tech. All rights reserved.</p>
                </div>
            </div>
        </footer>




        <!-- JavaScript -->
        <script>
            // Initialize Lucide icons
            lucide.createIcons();

            // Theme toggle
            function toggleTheme() {
                document.documentElement.classList.toggle('dark');
                const themeIcon = document.querySelector('[data-lucide="moon"]') || document.querySelector('[data-lucide="sun"]');
                if (document.documentElement.classList.contains('dark')) {
                    themeIcon.setAttribute('data-lucide', 'sun');
                } else {
                    themeIcon.setAttribute('data-lucide', 'moon');
                }
                lucide.createIcons();
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            }

            // Load theme from localStorage
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.querySelector('[data-lucide="moon"]').setAttribute('data-lucide', 'sun');
                lucide.createIcons();
            }

            // Mobile menu toggle
            function toggleMenu() {
                const menu = document.getElementById('mobile-menu');
                const menuIcon = document.getElementById('menu-icon');
                if (menu.classList.contains('max-h-0')) {
                    menu.classList.remove('max-h-0', 'opacity-0');
                    menu.classList.add('max-h-screen', 'opacity-100');
                    menuIcon.setAttribute('data-lucide', 'x');
                } else {
                    menu.classList.remove('max-h-screen', 'opacity-100');
                    menu.classList.add('max-h-0', 'opacity-0');
                    menuIcon.setAttribute('data-lucide', 'menu');
                }
                lucide.createIcons();
            }

            // Scroll to section with smooth animation
            function scrollToSection(id) {
                const element = document.getElementById(id);
                if (element) {
                    // Get the navbar height to offset the scroll position
                    const navbarHeight = document.querySelector('nav').offsetHeight || 80;
                    const elementPosition = element.offsetTop - navbarHeight - 20;
                    
                    // Custom smooth scroll implementation for better browser support
                    const startPosition = window.pageYOffset;
                    const distance = elementPosition - startPosition;
                    const duration = 1000; // 1 second animation
                    let start = null;
                    
                    function animation(currentTime) {
                        if (start === null) start = currentTime;
                        const timeElapsed = currentTime - start;
                        const run = ease(timeElapsed, startPosition, distance, duration);
                        window.scrollTo(0, run);
                        if (timeElapsed < duration) requestAnimationFrame(animation);
                    }
                    
                    // Easing function for smooth animation
                    function ease(t, b, c, d) {
                        t /= d / 2;
                        if (t < 1) return c / 2 * t * t + b;
                        t--;
                        return -c / 2 * (t * (t - 2) - 1) + b;
                    }
                    
                    requestAnimationFrame(animation);
                    
                    // Close mobile menu if open
                    const menu = document.getElementById('mobile-menu');
                    if (menu && menu.classList.contains('max-h-screen')) {
                        toggleMenu();
                    }
                }
            }

            // Dedicated smooth scroll function for contact section
            function smoothScrollToContact() {
                const contactSection = document.getElementById('contact');
                if (contactSection) {
                    const navbarHeight = document.querySelector('nav').offsetHeight || 80;
                    const targetPosition = contactSection.offsetTop - navbarHeight - 20;
                    
                    // Enhanced smooth scroll with longer duration for better visual effect
                    const startPosition = window.pageYOffset;
                    const distance = targetPosition - startPosition;
                    const duration = 1500; // 1.5 seconds for more noticeable smooth scroll
                    let startTime = null;
                    
                    function smoothAnimation(currentTime) {
                        if (startTime === null) startTime = currentTime;
                        const timeElapsed = currentTime - startTime;
                        const progress = Math.min(timeElapsed / duration, 1);
                        
                        // Smooth easing function (ease-in-out)
                        const easeProgress = progress < 0.5 
                            ? 2 * progress * progress 
                            : 1 - Math.pow(-2 * progress + 2, 3) / 2;
                        
                        const currentPosition = startPosition + (distance * easeProgress);
                        window.scrollTo(0, currentPosition);
                        
                        if (progress < 1) {
                            requestAnimationFrame(smoothAnimation);
                        }
                    }
                    
                    requestAnimationFrame(smoothAnimation);
                    
                    // Close mobile menu if open
                    const menu = document.getElementById('mobile-menu');
                    if (menu && menu.classList.contains('max-h-screen')) {
                        toggleMenu();
                    }
                }
            }

            

            // Counter animation
            document.querySelectorAll('[data-counter]').forEach(element => {
                const target = parseFloat(element.getAttribute('data-counter').replace(/[^0-9.]/g, ''));
                let count = 0;
                const increment = target / 100;
                const updateCount = () => {
                    count += increment;
                    if (count < target) {
                        element.textContent = Math.ceil(count) + element.getAttribute('data-counter').replace(/[0-9.]+/g, '');
                        requestAnimationFrame(updateCount);
                    } else {
                        element.textContent = element.getAttribute('data-counter');
                    }
                };
                updateCount();
            });

            // Team Marquee Pause/Resume Functions
            
            // Video Functions - Direct Fullscreen
            function openVideoModal(videoId, title) {
                // Open YouTube video directly in a new tab/window with autoplay
                const youtubeUrl = `https://www.youtube.com/watch?v=${videoId}&autoplay=1&rel=0`;
                window.open(youtubeUrl, '_blank');
            }

            function closeVideoModal() {
                // No longer needed, but keeping for compatibility
                return;
            }

            // Team Marquee - Smooth Continuous Animation
            // Removed pause/resume functions for better performance
            
            // Performance optimization: Pause animation when not visible
            if (document.getElementById('team-marquee')) {
                const teamSection = document.getElementById('team-marquee').closest('section');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const marquee = document.getElementById('team-marquee');
                        if (marquee) {
                            if (entry.isIntersecting) {
                                marquee.style.animationPlayState = 'running';
                            } else {
                                marquee.style.animationPlayState = 'paused';
                            }
                        }
                    });
                }, { threshold: 0.1 });
                
                if (teamSection) observer.observe(teamSection);
            }


            // Overview carousel animation
            const carousel = document.getElementById('carousel');
            const items = carousel.querySelectorAll('.carousel-item');
            let currentIndex = 0;

            function showSlide(index) {
                if (index >= items.length) index = 0;
                if (index < 0) index = items.length - 1;
                currentIndex = index;

                // Update opacity and scale for all items
                items.forEach((item, i) => {
                    if (i === currentIndex) {
                        item.classList.add('opacity-100', 'scale-100');
                        item.classList.remove('opacity-0', 'scale-95');
                    } else {
                        item.classList.add('opacity-0', 'scale-95');
                        item.classList.remove('opacity-100', 'scale-100');
                    }
                });
            }

            // Auto-advance carousel every 5 seconds
            setInterval(() => {
                showSlide(currentIndex + 1);
            }, 4000);

            // Ensure first slide is shown initially
            showSlide(0);


            //  Word Animation JavaScript 
            const words = ["Bigg Tech", "Innovative", "Future", "Success"];
            const wordElement = document.getElementById('animated-word');
            let currentWordIndex = 0;

            function updateWord() {
                wordElement.style.opacity = 0;
                wordElement.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    wordElement.textContent = words[currentWordIndex];
                    wordElement.style.opacity = 1;
                    wordElement.style.transform = 'translateY(0)';
                    currentWordIndex = (currentWordIndex + 1) % words.length;
                }, 500); // Match fade-out duration
            }

            // Initial word
            wordElement.textContent = words[0];
            wordElement.style.opacity = 1;

            // Update word every 3 seconds
            setInterval(updateWord, 3000);


            // Counter animation for social media
            console.log("Social media counter script loaded.");

            // Mission Video Functions
            let missionVideoLoaded = false;
            const missionVideoUrl = '<?= !empty($mission_video) ? htmlspecialchars($mission['video_url']) : '' ?>';
            
            function loadMissionVideo() {
                if (!missionVideoUrl) {
                    console.log('No mission video URL available');
                    return;
                }
                
                const container = document.getElementById('missionVideoContainer');
                if (!container) {
                    console.log('Mission video container not found');
                    return;
                }
                
                const thumbnail = container.querySelector('.video-thumbnail');
                const player = container.querySelector('.video-player');
                const loading = container.querySelector('.video-loading');
                
                if (!player) {
                    console.log('Video player element not found');
                    // Fallback: open video in new tab
                    window.open(missionVideoUrl, '_blank');
                    return;
                }
                
                const iframe = player.querySelector('iframe');
                const video = player.querySelector('video');
                
                // Show loading state
                if (thumbnail) thumbnail.style.display = 'none';
                if (loading) loading.classList.remove('hidden');
                
                // Load video
                setTimeout(() => {
                    if (iframe && iframe.getAttribute('data-src')) {
                        iframe.src = iframe.getAttribute('data-src');
                        iframe.onload = () => {
                            if (loading) loading.classList.add('hidden');
                            player.classList.remove('hidden');
                            missionVideoLoaded = true;
                        };
                        // Fallback timeout in case onload doesn't fire
                        setTimeout(() => {
                            if (loading) loading.classList.add('hidden');
                            player.classList.remove('hidden');
                            missionVideoLoaded = true;
                        }, 3000);
                    } else if (video) {
                        if (loading) loading.classList.add('hidden');
                        player.classList.remove('hidden');
                        video.play().catch(e => console.log('Video autoplay prevented:', e));
                        missionVideoLoaded = true;
                    } else {
                        console.log('No video element found, opening in new tab');
                        if (loading) loading.classList.add('hidden');
                        if (thumbnail) thumbnail.style.display = 'block';
                        window.open(missionVideoUrl, '_blank');
                    }
                }, 500);
            }
            
            function openVideoInNewTab() {
                if (missionVideoUrl) {
                    window.open(missionVideoUrl, '_blank');
                }
            }
            
            function resetMissionVideo() {
                const container = document.getElementById('missionVideoContainer');
                if (!container) return;
                
                const thumbnail = container.querySelector('.video-thumbnail');
                const player = container.querySelector('.video-player');
                const loading = container.querySelector('.video-loading');
                const iframe = player.querySelector('iframe');
                const video = player.querySelector('video');
                
                // Reset to thumbnail state
                thumbnail.style.display = 'block';
                player.classList.add('hidden');
                loading.classList.add('hidden');
                
                // Clear video sources
                if (iframe) iframe.src = '';
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                }
                
                missionVideoLoaded = false;
            }

            // document.addEventListener("DOMContentLoaded", function() {
            //     function socialMediaCounter(element, target) {
            //         let count = 0;
            //         const speed = 2000;
            //         const increment = Math.ceil(target / speed);

            //         const updateCount = () => {
            //             if (count < target) {
            //                 count += increment;
            //                 element.innerText = Math.min(count, target).toLocaleString();
            //                 requestAnimationFrame(updateCount);
            //             } else {
            //                 element.innerText = target.toLocaleString();
            //             }
            //         };

            //         updateCount();
            //     }

            //     function isInViewport(el) {
            //         const rect = el.getBoundingClientRect();
            //         return (
            //             rect.top >= 0 &&
            //             rect.top <= (window.innerHeight || document.documentElement.clientHeight)
            //         );
            //     }

            //     function startCounterAnimation() {
            //         const section = document.querySelector('#social-media');
            //         if (!section) {
            //             console.error("Social media section is not found");
            //         }

            //         if (isInViewport(section)) {
            //             const counters = document.querySelectorAll(".follower-count");
            //             console.log(`Found ${counters.length} .follower-count elements`);

            //             if (counters.length === 0) {
            //                 console.error("No .follower-count elements found.");
            //                 return;
            //             }

            //             counters.forEach(el => {
            //                 const target = parseFloat(el.dataset.target);
            //                 if (!isNaN(target) && target >= 0) {
            //                     socialMediaCounter(el, target);
            //                 } else {
            //                     console.warn(`Invalid data-target value: ${el.dataset.target}`);
            //                     el.innerText = "0";
            //                 }
            //             });
            //             animated = true;
            //         }
            //     }
            //     let animated = false;

            //     // Check on load in case section is already in viewport
            //     startCounterAnimation();

            //     // Check on scroll
            //     window.addEventListener('scroll', () => {
            //         if (!animated) {
            //             startCounterAnimation();
            //         }
            //     })
            // });



            /*
            // Fetch youtube videos
            document.addEventListener("DOMContentLoaded", function() {
                fetch('./config/get_videos.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const videosDiv = document.getElementById('videos');
                        if (!videosDiv) {
                            console.error('Videos container not found');
                            return;
                        }
                        if (!data.items || !Array.isArray(data.items)) {
                            console.error('Invalid or empty video data');
                            return;
                        }
                        videosDiv.innerHTML = ''; // Clear existing content
                        data.items.forEach((item, index) => {
                            if (item.id.kind === 'youtube#video') {
                                const videoId = item.id.videoId;
                                const title = item.snippet.title;
                                const thumbnail = item.snippet.thumbnails.medium.url;

                                const videoElement = `
                                <div class="bg-purple-50 dark:bg-purple-900/70 rounded-xl shadow-lg p-4 flex flex-col items-center transform transition duration-300 hover:-translate-y-2 animate-section" style="animation-delay: ${100 * index}ms; width: 300px; height: 280px">
                                    <a href="https://www.youtube.com/watch?v=${videoId}" target="_blank" class="block w-full">
                                        <img src="${thumbnail}" alt="${title}" class="w-full h-45 object-cover rounded-lg mb-3">
                                    </a>
                                    <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-200 text-center line-clamp-2 leading-snug">${title}</h3>
                                </div>
                            `;
                                videosDiv.innerHTML += videoElement;
                            }
                        });
                        console.log(`Loaded ${data.items.length} YouTube videos`);
                    })
                    .catch(error => console.error('Error fetching videos:', error));
            });
            */
        </script>
</body>

</html>