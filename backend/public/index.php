<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/TaskController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../middleware/Auth.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];

$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/api', '', $path);

try {
    switch ($path) {
        case '/auth/register':
            if ($requestMethod === 'POST') {
                $controller = new AuthController();
                $result = $controller->register($data);
                echo json_encode($result);
            }
            break;

        case '/auth/login':
            if ($requestMethod === 'POST') {
                $controller = new AuthController();
                $result = $controller->login($data);
                echo json_encode($result);
            }
            break;

        case '/auth/profile':
            if ($requestMethod === 'GET') {
                $user = AuthMiddleware::authenticate();
                $controller = new AuthController();
                $result = $controller->getProfile($user['user_id']);
                echo json_encode($result);
            }
            break;

        case '/products':
            if ($requestMethod === 'GET') {
                $controller = new TaskController();
                $result = $controller->getProducts();
                echo json_encode($result);
            }
            break;

        case '/tasks/submit':
            if ($requestMethod === 'POST') {
                $user = AuthMiddleware::authenticate();
                $controller = new TaskController();
                $result = $controller->submitTask($user['user_id'], $data);
                echo json_encode($result);
            }
            break;

        case '/tasks':
            if ($requestMethod === 'GET') {
                $user = AuthMiddleware::authenticate();
                $controller = new TaskController();
                $result = $controller->getUserTasks($user['user_id']);
                echo json_encode($result);
            }
            break;

        case '/vip-tiers':
            if ($requestMethod === 'GET') {
                $controller = new TaskController();
                $result = $controller->getVIPTiers();
                echo json_encode($result);
            }
            break;

        case '/admin/stats':
            if ($requestMethod === 'GET') {
                AuthMiddleware::requireAdmin();
                $controller = new AdminController();
                $result = $controller->getStats();
                echo json_encode($result);
            }
            break;

        case '/admin/users':
            AuthMiddleware::requireAdmin();
            $controller = new AdminController();

            if ($requestMethod === 'GET') {
                $result = $controller->getAllUsers();
                echo json_encode($result);
            }
            break;

        case (preg_match('/^\/admin\/users\/(\d+)$/', $path, $matches) ? true : false):
            AuthMiddleware::requireAdmin();
            $userId = $matches[1];
            $controller = new AdminController();

            if ($requestMethod === 'PUT') {
                $result = $controller->updateUser($userId, $data);
                echo json_encode($result);
            } elseif ($requestMethod === 'DELETE') {
                $result = $controller->deleteUser($userId);
                echo json_encode($result);
            }
            break;

        case '/admin/products':
            AuthMiddleware::requireAdmin();
            $controller = new AdminController();

            if ($requestMethod === 'GET') {
                $result = $controller->getAllProducts();
                echo json_encode($result);
            } elseif ($requestMethod === 'POST') {
                $result = $controller->createProduct($data);
                echo json_encode($result);
            }
            break;

        case (preg_match('/^\/admin\/products\/(\d+)$/', $path, $matches) ? true : false):
            AuthMiddleware::requireAdmin();
            $productId = $matches[1];
            $controller = new AdminController();

            if ($requestMethod === 'PUT') {
                $result = $controller->updateProduct($productId, $data);
                echo json_encode($result);
            } elseif ($requestMethod === 'DELETE') {
                $result = $controller->deleteProduct($productId);
                echo json_encode($result);
            }
            break;

        case '/admin/tasks':
            if ($requestMethod === 'GET') {
                AuthMiddleware::requireAdmin();
                $controller = new AdminController();
                $result = $controller->getAllTasks();
                echo json_encode($result);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
