<?php

require_once __DIR__ . '/../core/Database.php';

class TaskController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getProducts() {
        $products = $this->db->select(
            "SELECT * FROM products WHERE status = 'active' ORDER BY RAND() LIMIT 10"
        );

        return ['success' => true, 'products' => $products];
    }

    public function submitTask($userId, $data) {
        $this->db->beginTransaction();

        try {
            $taskId = $this->db->insert(
                "INSERT INTO tasks (user_id, product_id, brand_selection, confidence_level, reward_amount)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $userId,
                    $data['product_id'],
                    $data['brand_selection'],
                    $data['confidence_level'] ?? 0,
                    $data['reward_amount'] ?? 0.50
                ]
            );

            $this->db->update(
                "UPDATE users
                 SET balance = balance + ?,
                     total_earned = total_earned + ?,
                     tasks_completed = tasks_completed + 1
                 WHERE id = ?",
                [$data['reward_amount'] ?? 0.50, $data['reward_amount'] ?? 0.50, $userId]
            );

            $this->db->insert(
                "INSERT INTO transactions (user_id, type, amount, description)
                 VALUES (?, 'earning', ?, ?)",
                [$userId, $data['reward_amount'] ?? 0.50, 'Task completion reward']
            );

            $this->db->commit();

            return ['success' => true, 'task_id' => $taskId, 'message' => 'Task submitted successfully'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Failed to submit task'];
        }
    }

    public function getUserTasks($userId) {
        $tasks = $this->db->select(
            "SELECT t.*, p.name as product_name, p.image_url
             FROM tasks t
             JOIN products p ON t.product_id = p.id
             WHERE t.user_id = ?
             ORDER BY t.submitted_at DESC
             LIMIT 50",
            [$userId]
        );

        return ['success' => true, 'tasks' => $tasks];
    }

    public function getVIPTiers() {
        $tiers = $this->db->select("SELECT * FROM vip_tiers ORDER BY price ASC");

        foreach ($tiers as &$tier) {
            if (isset($tier['features'])) {
                $tier['features'] = json_decode($tier['features'], true);
            }
        }

        return ['success' => true, 'tiers' => $tiers];
    }
}
