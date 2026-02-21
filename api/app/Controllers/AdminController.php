<?php

namespace App\Controllers;

use App\Database;
use App\Middleware\Auth;

class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getStats()
    {
        Auth::verifyAdmin();

        $totalUsers = $this->db->fetchOne("SELECT COUNT(*) as count FROM users");
        $totalTasks = $this->db->fetchOne("SELECT COUNT(*) as count FROM brand_identification_tasks");
        $completedTasks = $this->db->fetchOne("SELECT COUNT(*) as count FROM brand_identification_tasks WHERE status = 'completed'");
        $totalEarnings = $this->db->fetchOne("SELECT SUM(amount) as total FROM earnings WHERE status = 'completed'");
        $totalProducts = $this->db->fetchOne("SELECT COUNT(*) as count FROM products");

        return [
            'total_users' => (int)$totalUsers['count'],
            'total_tasks' => (int)$totalTasks['count'],
            'completed_tasks' => (int)$completedTasks['count'],
            'total_earnings_paid' => (float)($totalEarnings['total'] ?? 0),
            'total_products' => (int)$totalProducts['count'],
            'active_users' => (int)$totalUsers['count']
        ];
    }

    public function getUsers()
    {
        Auth::verifyAdmin();

        $users = $this->db->fetchAll(
            "SELECT u.id, u.email, u.full_name, u.role, u.vip_tier, u.created_at,
                    up.tasks_completed, up.total_earnings, up.accuracy_score,
                    w.balance
             FROM users u
             LEFT JOIN user_profiles up ON u.id = up.user_id
             LEFT JOIN wallets w ON u.id = w.user_id
             ORDER BY u.created_at DESC"
        );

        return $users;
    }

    public function updateUserRole($userId)
    {
        Auth::verifyAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['role']) || !in_array($data['role'], ['user', 'admin'])) {
            http_response_code(400);
            return ['error' => 'Invalid role'];
        }

        $this->db->execute(
            "UPDATE users SET role = ? WHERE id = ?",
            [$data['role'], $userId]
        );

        return ['message' => 'User role updated'];
    }

    public function deleteUser($userId)
    {
        Auth::verifyAdmin();

        $this->db->execute("DELETE FROM users WHERE id = ?", [$userId]);

        return ['message' => 'User deleted'];
    }

    public function getProducts()
    {
        Auth::verifyAdmin();

        $products = $this->db->fetchAll(
            "SELECT p.*,
                    COUNT(DISTINCT t.id) as total_tasks,
                    COUNT(DISTINCT CASE WHEN t.status = 'completed' THEN t.id END) as completed_tasks
             FROM products p
             LEFT JOIN brand_identification_tasks t ON p.id = t.product_id
             GROUP BY p.id
             ORDER BY p.created_at DESC"
        );

        return $products;
    }

    public function createProduct()
    {
        Auth::verifyAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['brand']) || !isset($data['category']) || !isset($data['image_url'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $this->db->execute(
            "INSERT INTO products (name, brand, category, image_url, description)
             VALUES (?, ?, ?, ?, ?)",
            [$data['name'], $data['brand'], $data['category'], $data['image_url'], $data['description'] ?? null]
        );

        $productId = $this->db->lastInsertId();
        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$productId]);

        return $product;
    }

    public function updateProduct($productId)
    {
        Auth::verifyAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['brand']) || !isset($data['category']) || !isset($data['image_url'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $this->db->execute(
            "UPDATE products SET name = ?, brand = ?, category = ?, image_url = ?, description = ?
             WHERE id = ?",
            [$data['name'], $data['brand'], $data['category'], $data['image_url'], $data['description'] ?? null, $productId]
        );

        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$productId]);

        return $product;
    }

    public function deleteProduct($productId)
    {
        Auth::verifyAdmin();

        $this->db->execute("DELETE FROM products WHERE id = ?", [$productId]);

        return ['message' => 'Product deleted'];
    }

    public function generateTasks()
    {
        Auth::verifyAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['product_id']) || !isset($data['quantity']) || !isset($data['reward_per_task'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$data['product_id']]);
        if (!$product) {
            http_response_code(404);
            return ['error' => 'Product not found'];
        }

        for ($i = 0; $i < $data['quantity']; $i++) {
            $this->db->execute(
                "INSERT INTO brand_identification_tasks (product_id, reward_amount, difficulty_level)
                 VALUES (?, ?, 'medium')",
                [$data['product_id'], $data['reward_per_task']]
            );
        }

        return ['message' => "{$data['quantity']} tasks generated successfully"];
    }

    public function getPaymentGateways()
    {
        Auth::verifyAdmin();

        $gateways = $this->db->fetchAll(
            "SELECT * FROM payment_gateways ORDER BY created_at DESC"
        );

        return $gateways;
    }

    public function createPaymentGateway()
    {
        Auth::verifyAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['type'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $credentials = json_encode($data['credentials'] ?? []);

        $this->db->execute(
            "INSERT INTO payment_gateways (name, type, credentials, is_active, min_withdrawal, max_withdrawal, processing_fee)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['type'],
                $credentials,
                $data['is_active'] ?? 1,
                $data['min_withdrawal'] ?? 10,
                $data['max_withdrawal'] ?? 10000,
                $data['processing_fee'] ?? 0
            ]
        );

        $gatewayId = $this->db->lastInsertId();
        $gateway = $this->db->fetchOne("SELECT * FROM payment_gateways WHERE id = ?", [$gatewayId]);

        return $gateway;
    }

    public function updatePaymentGateway($gatewayId)
    {
        Auth::verifyAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        $credentials = json_encode($data['credentials'] ?? []);

        $this->db->execute(
            "UPDATE payment_gateways
             SET name = ?, type = ?, credentials = ?, is_active = ?, min_withdrawal = ?, max_withdrawal = ?, processing_fee = ?
             WHERE id = ?",
            [
                $data['name'],
                $data['type'],
                $credentials,
                $data['is_active'] ?? 1,
                $data['min_withdrawal'] ?? 10,
                $data['max_withdrawal'] ?? 10000,
                $data['processing_fee'] ?? 0,
                $gatewayId
            ]
        );

        $gateway = $this->db->fetchOne("SELECT * FROM payment_gateways WHERE id = ?", [$gatewayId]);

        return $gateway;
    }

    public function deletePaymentGateway($gatewayId)
    {
        Auth::verifyAdmin();

        $this->db->execute("DELETE FROM payment_gateways WHERE id = ?", [$gatewayId]);

        return ['message' => 'Payment gateway deleted'];
    }
}
