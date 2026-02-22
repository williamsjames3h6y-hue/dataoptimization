<?php

require_once __DIR__ . '/../core/Database.php';

class AdminController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getStats() {
        $stats = [
            'total_users' => $this->db->selectOne("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
            'total_tasks' => $this->db->selectOne("SELECT COUNT(*) as count FROM tasks")['count'] ?? 0,
            'total_earnings' => $this->db->selectOne("SELECT SUM(total_earned) as total FROM users")['total'] ?? 0,
            'pending_tasks' => $this->db->selectOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'")['count'] ?? 0,
            'active_products' => $this->db->selectOne("SELECT COUNT(*) as count FROM products WHERE status = 'active'")['count'] ?? 0,
        ];

        return ['success' => true, 'stats' => $stats];
    }

    public function getAllUsers($limit = 100, $offset = 0) {
        $users = $this->db->select(
            "SELECT id, email, full_name, role, balance, total_earned, tasks_completed, vip_tier, created_at
             FROM users
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        return ['success' => true, 'users' => $users];
    }

    public function updateUser($userId, $data) {
        $updates = [];
        $params = [];

        if (isset($data['balance'])) {
            $updates[] = "balance = ?";
            $params[] = $data['balance'];
        }
        if (isset($data['role'])) {
            $updates[] = "role = ?";
            $params[] = $data['role'];
        }
        if (isset($data['vip_tier'])) {
            $updates[] = "vip_tier = ?";
            $params[] = $data['vip_tier'];
        }

        if (empty($updates)) {
            return ['success' => false, 'message' => 'No fields to update'];
        }

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";

        $result = $this->db->update($sql, $params);

        return $result ?
            ['success' => true, 'message' => 'User updated successfully'] :
            ['success' => false, 'message' => 'Update failed'];
    }

    public function deleteUser($userId) {
        $result = $this->db->delete("DELETE FROM users WHERE id = ? AND role != 'admin'", [$userId]);

        return $result ?
            ['success' => true, 'message' => 'User deleted successfully'] :
            ['success' => false, 'message' => 'Delete failed'];
    }

    public function getAllProducts() {
        $products = $this->db->select("SELECT * FROM products ORDER BY created_at DESC");
        return ['success' => true, 'products' => $products];
    }

    public function createProduct($data) {
        $productId = $this->db->insert(
            "INSERT INTO products (name, brand, image_url, category, reward, status)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['brand'] ?? '',
                $data['image_url'] ?? '',
                $data['category'] ?? '',
                $data['reward'] ?? 0.50,
                $data['status'] ?? 'active'
            ]
        );

        return $productId ?
            ['success' => true, 'product_id' => $productId, 'message' => 'Product created successfully'] :
            ['success' => false, 'message' => 'Failed to create product'];
    }

    public function updateProduct($productId, $data) {
        $updates = [];
        $params = [];

        $fields = ['name', 'brand', 'image_url', 'category', 'reward', 'status'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updates)) {
            return ['success' => false, 'message' => 'No fields to update'];
        }

        $params[] = $productId;
        $sql = "UPDATE products SET " . implode(", ", $updates) . " WHERE id = ?";

        $result = $this->db->update($sql, $params);

        return $result ?
            ['success' => true, 'message' => 'Product updated successfully'] :
            ['success' => false, 'message' => 'Update failed'];
    }

    public function deleteProduct($productId) {
        $result = $this->db->delete("DELETE FROM products WHERE id = ?", [$productId]);

        return $result ?
            ['success' => true, 'message' => 'Product deleted successfully'] :
            ['success' => false, 'message' => 'Delete failed'];
    }

    public function getAllTasks($limit = 100, $offset = 0) {
        $tasks = $this->db->select(
            "SELECT t.*, u.email as user_email, p.name as product_name
             FROM tasks t
             JOIN users u ON t.user_id = u.id
             JOIN products p ON t.product_id = p.id
             ORDER BY t.submitted_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        return ['success' => true, 'tasks' => $tasks];
    }
}
