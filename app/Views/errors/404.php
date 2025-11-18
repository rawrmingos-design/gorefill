<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen flex items-center justify-center">
    <div class="text-center max-w-2xl mx-4">
        <div class="animate__animated animate__fadeIn">
            <!-- 404 Illustration -->
            <div class="mb-8">
                <div class="text-9xl font-bold text-gray-300 animate__animated animate__bounceIn">
                    404
                </div>
            </div>
            
            <!-- Icon -->
            <div class="mb-8">
                <svg class="w-32 h-32 text-gray-400 mx-auto animate__animated animate__fadeInDown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <!-- Message Card -->
            <div class="bg-white rounded-lg shadow-xl p-8 mb-8 animate__animated animate__zoomIn">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">
                    Oops! Page Not Found
                </h1>
                <p class="text-xl text-gray-600 mb-6">
                    Halaman yang Anda cari tidak ditemukan atau sedang dalam tahap pengembangan.
                </p>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                    <p class="text-yellow-800">
                        <strong>Route:</strong> <code class="bg-yellow-100 px-2 py-1 rounded"><?php echo e($_GET['route'] ?? 'unknown'); ?></code>
                    </p>
                </div>
                
                <!-- Suggested Links -->
                <div class="text-left">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Mungkin Anda mencari:</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="?route=home" class="text-blue-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Homepage
                            </a>
                        </li>
                        <li>
                            <a href="?route=products" class="text-blue-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                                </svg>
                                Products
                            </a>
                        </li>
                        <li>
                            <a href="?route=auth.login" class="text-blue-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="?route=test.routing" class="text-blue-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Test Routing System
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Action Button -->
            <a href="?route=home" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition transform hover:scale-105 shadow-lg">
                ← Back to Homepage
            </a>
            
            <!-- Debug Info (only in development) -->
            <?php if ($config['app']['debug']): ?>
            <div class="mt-8 bg-gray-800 text-white rounded-lg p-6 text-left text-sm">
                <h4 class="font-bold mb-2">🐛 Debug Information:</h4>
                <ul class="space-y-1 font-mono">
                    <li><strong>Route:</strong> <?php echo e($_GET['route'] ?? 'none'); ?></li>
                    <li><strong>Method:</strong> <?php echo e($_SERVER['REQUEST_METHOD']); ?></li>
                    <li><strong>URI:</strong> <?php echo e($_SERVER['REQUEST_URI']); ?></li>
                    <li><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Footer -->
            <div class="mt-8 text-gray-500 text-sm">
                <p>© 2025 GoRefill - Developed by ❤︎</p>
            </div>
        </div>
    </div>
</body>
</html>
