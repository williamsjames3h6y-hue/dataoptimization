<?php

require_once __DIR__ . '/api/vendor/autoload.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (strpos($requestUri, '/api/') === 0) {
    require __DIR__ . '/api/public/index.php';
    exit;
}

$htmlFile = __DIR__ . '/dist/index.html';
if (file_exists($htmlFile)) {
    readfile($htmlFile);
} else {
    http_response_code(404);
    echo 'Application not built. Please run: npm run build';
}
