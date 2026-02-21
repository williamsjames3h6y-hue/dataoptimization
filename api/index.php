<?php

header('Content-Type: application/json');

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $dotenv = parse_ini_file($envFile);
    if ($dotenv) {
        foreach ($dotenv as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

$config = require __DIR__ . '/config/app.php';

$allowedOrigins = $config['cors_allowed_origins'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Middleware/Auth.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/TaskController.php';
require_once __DIR__ . '/app/Controllers/AdminController.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $requestUri = substr($requestUri, strlen($basePath));
}

$requestUri = strtok($requestUri, '?');

try {
    $response = null;

    if (preg_match('#^/api/auth/signup$#', $requestUri) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\AuthController();
        $response = $controller->signup();
    }
    elseif (preg_match('#^/api/auth/signin$#', $requestUri) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\AuthController();
        $response = $controller->signin();
    }
    elseif (preg_match('#^/api/auth/signout$#', $requestUri) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\AuthController();
        $response = $controller->signout();
    }
    elseif (preg_match('#^/api/auth/user$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\AuthController();
        $response = $controller->getUser();
    }
    elseif (preg_match('#^/api/tasks$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\TaskController();
        $response = $controller->getTasks();
    }
    elseif (preg_match('#^/api/tasks/(\d+)/submit$#', $requestUri, $matches) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\TaskController();
        $response = $controller->submitTask($matches[1]);
    }
    elseif (preg_match('#^/api/tasks/stats$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\TaskController();
        $response = $controller->getTaskStats();
    }
    elseif (preg_match('#^/api/admin/stats$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->getStats();
    }
    elseif (preg_match('#^/api/admin/users$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->getUsers();
    }
    elseif (preg_match('#^/api/admin/users/(\d+)/role$#', $requestUri, $matches) && $requestMethod === 'PUT') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->updateUserRole($matches[1]);
    }
    elseif (preg_match('#^/api/admin/users/(\d+)$#', $requestUri, $matches) && $requestMethod === 'DELETE') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->deleteUser($matches[1]);
    }
    elseif (preg_match('#^/api/admin/products$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->getProducts();
    }
    elseif (preg_match('#^/api/admin/products$#', $requestUri) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->createProduct();
    }
    elseif (preg_match('#^/api/admin/products/(\d+)$#', $requestUri, $matches) && $requestMethod === 'PUT') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->updateProduct($matches[1]);
    }
    elseif (preg_match('#^/api/admin/products/(\d+)$#', $requestUri, $matches) && $requestMethod === 'DELETE') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->deleteProduct($matches[1]);
    }
    elseif (preg_match('#^/api/admin/tasks/generate$#', $requestUri) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->generateTasks();
    }
    elseif (preg_match('#^/api/admin/payment-gateways$#', $requestUri) && $requestMethod === 'GET') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->getPaymentGateways();
    }
    elseif (preg_match('#^/api/admin/payment-gateways$#', $requestUri) && $requestMethod === 'POST') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->createPaymentGateway();
    }
    elseif (preg_match('#^/api/admin/payment-gateways/(\d+)$#', $requestUri, $matches) && $requestMethod === 'PUT') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->updatePaymentGateway($matches[1]);
    }
    elseif (preg_match('#^/api/admin/payment-gateways/(\d+)$#', $requestUri, $matches) && $requestMethod === 'DELETE') {
        $controller = new \App\Controllers\AdminController();
        $response = $controller->deletePaymentGateway($matches[1]);
    }
    else {
        http_response_code(404);
        $response = ['error' => 'Not found'];
    }

    echo json_encode($response);

} catch (\Exception $e) {
    http_response_code(500);
    $errorMessage = $config['debug'] ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : 'Internal server error';
    echo json_encode([
        'error' => $errorMessage
    ]);
}
