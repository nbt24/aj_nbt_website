<?php
/**
 * Quick Start Guide Popup
 * Shows essential optimization steps for new admin users
 */
?>

<!-- Quick Start Modal (can be triggered from dashboard) -->
<div id="quickStartModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white p-6 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">🚀 Welcome to NBT Admin!</h2>
                    <p class="text-purple-100">Let's get your website running at peak performance</p>
                </div>
                <button onclick="closeQuickStart()" class="text-white hover:text-gray-200 text-2xl">×</button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Step 1 -->
            <div class="mb-6 p-4 bg-green-50 rounded-lg border-l-4 border-green-400">
                <h3 class="text-lg font-semibold text-green-800 mb-2">📊 Step 1: Check Website Health</h3>
                <p class="text-green-700 mb-3">Start by checking your website's current performance.</p>
                <a href="health_check.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-block">
                    Check Health Now
                </a>
            </div>

            <!-- Step 2 -->
            <div class="mb-6 p-4 bg-orange-50 rounded-lg border-l-4 border-orange-400">
                <h3 class="text-lg font-semibold text-orange-800 mb-2">📸 Step 2: Optimize Your Images</h3>
                <p class="text-orange-700 mb-3">Before uploading any images, compress them to improve speed.</p>
                <div class="space-x-2">
                    <a href="https://tinypng.com" target="_blank" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded inline-block">
                        Go to TinyPNG
                    </a>
                    <a href="image_checker.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-block">
                        Test Image Size
                    </a>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="mb-6 p-4 bg-purple-50 rounded-lg border-l-4 border-purple-400">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">📚 Step 3: Read the Complete Guide</h3>
                <p class="text-purple-700 mb-3">Learn all the best practices for maintaining your website.</p>
                <a href="documentation.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded inline-block">
                    Open Full Guide
                </a>
            </div>

            <!-- Quick Tips -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">💡 Daily Habits for Success</h3>
                <ul class="text-blue-700 space-y-2 text-sm">
                    <li>✅ <strong>Always compress images</strong> at TinyPNG.com before uploading</li>
                    <li>✅ <strong>Check website health</strong> once per week (look for green lights)</li>
                    <li>✅ <strong>Keep content under 150 words</strong> per section</li>
                    <li>✅ <strong>Test on mobile</strong> after making changes</li>
                    <li>✅ <strong>Run maintenance</strong> monthly using the auto-maintenance tool</li>
                </ul>
            </div>

            <!-- Emergency Contacts -->
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">🆘 When to Get Help</h3>
                <ul class="text-yellow-700 space-y-1 text-sm">
                    <li>• Red health indicators persist after auto-maintenance</li>
                    <li>• Website takes more than 5 seconds to load</li>
                    <li>• Multiple error messages appear</li>
                    <li>• Admin panel becomes unresponsive</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-6 rounded-b-xl">
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" id="dontShowAgain" class="mr-2">
                    <span class="text-sm text-gray-600">Don't show this again</span>
                </label>
                <div class="space-x-2">
                    <button onclick="closeQuickStart()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">
                        Maybe Later
                    </button>
                    <button onclick="startHealthCheck()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Let's Start! 🚀
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showQuickStart() {
    document.getElementById('quickStartModal').classList.remove('hidden');
}

function closeQuickStart() {
    const dontShow = document.getElementById('dontShowAgain').checked;
    if (dontShow) {
        localStorage.setItem('nbt_quick_start_shown', 'true');
    }
    document.getElementById('quickStartModal').classList.add('hidden');
}

function startHealthCheck() {
    closeQuickStart();
    window.location.href = 'health_check.php';
}

// Auto-show for new users (optional)
document.addEventListener('DOMContentLoaded', function() {
    const hasSeenQuickStart = localStorage.getItem('nbt_quick_start_shown');
    // Uncomment the next line if you want to auto-show for new users
    // if (!hasSeenQuickStart) showQuickStart();
});
</script>
