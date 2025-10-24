<?php
$publicIndex = __DIR__ . '/public/index.php';
if (file_exists($publicIndex)) {
    require $publicIndex;
    exit;
}

// Kalau tidak ditemukan, tampilkan pesan error
http_response_code(500);
echo "<h1>500 Internal Server Error</h1>";
echo "<p>File public/index.php tidak ditemukan.</p>";
