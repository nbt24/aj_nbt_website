<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Read the optimization guide
$optimizationGuide = file_get_contents('../OPTIMIZATION_GUIDE.md');
$maintenanceChecklist = file_get_contents('../MAINTENANCE_CHECKLIST.md');

// Simple markdown to HTML conversion
function markdownToHtml($text) {
    // Headers
    $text = preg_replace('/^### (.*$)/m', '<h3 class="text-lg font-semibold text-purple-800 mt-6 mb-3">$1</h3>', $text);
    $text = preg_replace('/^## (.*$)/m', '<h2 class="text-xl font-bold text-purple-900 mt-8 mb-4">$1</h2>', $text);
    $text = preg_replace('/^# (.*$)/m', '<h1 class="text-2xl font-bold text-purple-900 mt-8 mb-6">$1</h1>', $text);
    
    // Links
    $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" target="_blank" class="text-blue-500 hover:text-blue-700 underline">$1</a>', $text);
    
    // Bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-semibold">$1</strong>', $text);
    
    // Code/paths
    $text = preg_replace('/`([^`]+)`/', '<code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">$1</code>', $text);
    
    // Checkboxes
    $text = preg_replace('/- \[ \]/', '☐', $text);
    $text = preg_replace('/- \[x\]/', '✅', $text);
    
    // Lists
    $text = preg_replace('/^- (.*$)/m', '<li class="ml-4 mb-1">• $1</li>', $text);
    
    // Paragraphs
    $text = preg_replace('/^([^<\n\r].*)$/m', '<p class="mb-3">$1</p>', $text);
    
    // Horizontal rules
    $text = preg_replace('/^---$/m', '<hr class="my-6 border-gray-300">', $text);
    
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Optimization Guide - NBT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../assert/black.png">
    <style>
        .tab-button.active {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
        }
        .documentation {
            max-height: 70vh;
            overflow-y: auto;
        }
        .print-friendly {
            background: white;
            color: black;
        }
    </style>
</head>
<body class="bg-purple-50 font-sans min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-lg font-semibold text-purple-900">NBT Admin - Optimization Guide</div>
                <div>
                    <a href="dashboard.php" class="text-purple-900 hover:text-yellow-500 px-4">Dashboard</a>
                    <a href="health_check.php" class="text-purple-900 hover:text-yellow-500 px-4">Health Check</a>
                    <a href="logout.php" class="bg-yellow-500 text-purple-900 px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6 mt-20">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-purple-800 mb-4">📚 Website Optimization Documentation</h1>
            <p class="text-gray-600">Complete guide for managing your website's performance</p>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-purple-800 mb-4">🚀 Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="health_check.php" class="bg-green-500 hover:bg-green-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    📊 Health Check
                </a>
                <a href="image_checker.php" class="bg-orange-500 hover:bg-orange-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    📸 Image Checker
                </a>
                <a href="maintenance.php" class="bg-red-500 hover:bg-red-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🔧 Auto Maintenance
                </a>
                <a href="https://tinypng.com" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-4 rounded-lg transition-colors">
                    🗜️ TinyPNG
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-8">
            <div class="flex border-b">
                <button class="tab-button active px-6 py-3 font-semibold rounded-tl-lg" onclick="showTab('guide')">
                    📖 Optimization Guide
                </button>
                <button class="tab-button px-6 py-3 font-semibold" onclick="showTab('checklist')">
                    📋 Maintenance Checklist
                </button>
                <button class="tab-button px-6 py-3 font-semibold" onclick="showTab('tools')">
                    🛠️ Tools Overview
                </button>
            </div>

            <!-- Optimization Guide Tab -->
            <div id="guide-tab" class="documentation p-6">
                <div class="prose max-w-none">
                    <?= markdownToHtml($optimizationGuide) ?>
                </div>
            </div>

            <!-- Maintenance Checklist Tab -->
            <div id="checklist-tab" class="documentation p-6 hidden">
                <div class="prose max-w-none">
                    <?= markdownToHtml($maintenanceChecklist) ?>
                </div>
            </div>

            <!-- Tools Overview Tab -->
            <div id="tools-tab" class="documentation p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Admin Tools -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-purple-800 mb-4">🔧 Admin Tools</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>Health Check</strong>
                                    <p class="text-sm text-gray-600">Monitor website performance</p>
                                </div>
                                <a href="health_check.php" class="bg-green-500 text-white px-3 py-1 rounded text-sm">Open</a>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>Image Checker</strong>
                                    <p class="text-sm text-gray-600">Test image sizes before upload</p>
                                </div>
                                <a href="image_checker.php" class="bg-orange-500 text-white px-3 py-1 rounded text-sm">Open</a>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>Auto Maintenance</strong>
                                    <p class="text-sm text-gray-600">One-click optimization</p>
                                </div>
                                <a href="maintenance.php" class="bg-red-500 text-white px-3 py-1 rounded text-sm">Run</a>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>Performance Monitor</strong>
                                    <p class="text-sm text-gray-600">Detailed performance analysis</p>
                                </div>
                                <a href="../performance_monitor.php" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">View</a>
                            </div>
                        </div>
                    </div>

                    <!-- External Tools -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-purple-800 mb-4">🌐 External Tools</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>TinyPNG</strong>
                                    <p class="text-sm text-gray-600">Compress images (Primary)</p>
                                </div>
                                <a href="https://tinypng.com" target="_blank" class="bg-green-500 text-white px-3 py-1 rounded text-sm">Visit</a>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>Compressor.io</strong>
                                    <p class="text-sm text-gray-600">Alternative compression tool</p>
                                </div>
                                <a href="https://compressor.io" target="_blank" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">Visit</a>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <strong>Optimizilla</strong>
                                    <p class="text-sm text-gray-600">Backup compression option</p>
                                </div>
                                <a href="https://optimizilla.com" target="_blank" class="bg-purple-500 text-white px-3 py-1 rounded text-sm">Visit</a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Reference -->
                    <div class="bg-gray-50 rounded-lg p-6 md:col-span-2">
                        <h3 class="text-lg font-semibold text-purple-800 mb-4">📏 Quick Size Reference</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-100 p-4 rounded">
                                <h4 class="font-semibold text-green-800">✅ Perfect Size</h4>
                                <ul class="text-sm text-green-700 mt-2">
                                    <li>• Team photos: 400x400px, &lt;200KB</li>
                                    <li>• Course images: 600x400px, &lt;300KB</li>
                                    <li>• Company logos: 200x200px, &lt;100KB</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded">
                                <h4 class="font-semibold text-yellow-800">⚠️ Acceptable</h4>
                                <ul class="text-sm text-yellow-700 mt-2">
                                    <li>• File size: 200KB - 500KB</li>
                                    <li>• Dimensions: up to 1200x1200px</li>
                                    <li>• Should compress if possible</li>
                                </ul>
                            </div>
                            <div class="bg-red-100 p-4 rounded">
                                <h4 class="font-semibold text-red-800">❌ Too Large</h4>
                                <ul class="text-sm text-red-700 mt-2">
                                    <li>• File size: Over 500KB</li>
                                    <li>• Dimensions: Over 1200x1200px</li>
                                    <li>• Must compress before upload!</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-y-4">
            <div class="space-x-4">
                <button onclick="printDoc()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg">
                    🖨️ Print Guide
                </button>
                <a href="dashboard.php" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg inline-block">
                    🏠 Back to Dashboard
                </a>
            </div>
            <p class="text-sm text-gray-500">
                💡 Bookmark this page for easy access to optimization guides
            </p>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.getElementById('guide-tab').classList.add('hidden');
            document.getElementById('checklist-tab').classList.add('hidden');
            document.getElementById('tools-tab').classList.add('hidden');
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function printDoc() {
            window.print();
        }

        // Make external links open in new tab
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="http"]');
            links.forEach(link => {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noopener noreferrer');
            });
        });
    </script>
</body>
</html>
