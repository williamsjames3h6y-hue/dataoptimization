<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = strtok($requestUri, '?');

$mockUsers = [
    'admin@example.com' => [
        'id' => 1,
        'email' => 'admin@example.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'full_name' => 'Admin User',
        'role' => 'admin',
        'vip_tier' => 'platinum'
    ]
];

$mockToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxLCJleHAiOjE3NTk1ODAxMjB9.mock_signature';

try {
    $response = null;

    if (preg_match('#^/api/auth/signup$#', $requestUri) && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = rand(100, 999);
        $response = [
            'user' => [
                'id' => $userId,
                'email' => $data['email'],
                'role' => 'user',
                'full_name' => $data['full_name'] ?? null,
                'vip_tier' => 'free'
            ],
            'session' => ['access_token' => $mockToken]
        ];
    }
    elseif (preg_match('#^/api/auth/signin$#', $requestUri) && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $mockUsers[$data['email']] ?? null;

        if ($user && password_verify($data['password'], $user['password'])) {
            unset($user['password']);
            $response = [
                'user' => $user,
                'session' => ['access_token' => $mockToken]
            ];
        } else {
            http_response_code(401);
            $response = ['error' => 'Invalid credentials'];
        }
    }
    elseif (preg_match('#^/api/auth/user$#', $requestUri) && $requestMethod === 'GET') {
        $response = ['user' => $mockUsers['admin@example.com']];
    }
    elseif (preg_match('#^/api/tasks$#', $requestUri) && $requestMethod === 'GET') {
        $response = [
            [
                'id' => 1,
                'name' => 'Premium Wireless Headphones',
                'brand' => 'Sony',
                'category' => 'Electronics',
                'image_url' => '/products/P1.jpg',
                'reward_amount' => 5.00,
                'status' => 'available'
            ]
        ];
    }
    elseif (preg_match('#^/api/tasks/stats$#', $requestUri) && $requestMethod === 'GET') {
        $response = [
            'tasks_completed' => 0,
            'total_earnings' => 0.00,
            'accuracy_score' => 0.00,
            'available_balance' => 0.00,
            'pending_balance' => 0.00
        ];
    }
    elseif (preg_match('#^/api/admin/stats$#', $requestUri) && $requestMethod === 'GET') {
        $response = [
            'total_users' => 1,
            'total_tasks' => 5,
            'completed_tasks' => 0,
            'total_earnings_paid' => 0.00,
            'total_products' => 5,
            'active_users' => 1
        ];
    }
    elseif (preg_match('#^/api/admin/users$#', $requestUri) && $requestMethod === 'GET') {
        $response = [
            [
                'id' => 1,
                'email' => 'admin@example.com',
                'full_name' => 'Admin User',
                'role' => 'admin',
                'vip_tier' => 'platinum',
                'tasks_completed' => 0,
                'total_earnings' => 0.00,
                'balance' => 0.00
            ]
        ];
    }
    elseif (preg_match('#^/api/admin/products$#', $requestUri) && $requestMethod === 'GET') {
        $response = [
            [
                'id' => 1,
                'name' => 'Premium Wireless Headphones',
                'brand' => 'Sony',
                'category' => 'Electronics',
                'image_url' => '/products/P1.jpg',
                'total_tasks' => 1,
                'completed_tasks' => 0
            ]
        ];
    }
    else {
        http_response_code(404);
        $response = ['error' => 'Not found'];
    }

    echo json_encode($response);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
