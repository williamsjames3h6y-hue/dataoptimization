<?php

namespace App\Controllers;

use App\Database;
use App\Middleware\Auth;

class TaskController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getTasks()
    {
        $user = Auth::verify();

        $tasks = $this->db->fetchAll(
            "SELECT t.*, p.name, p.brand, p.category, p.image_url, p.description
             FROM brand_identification_tasks t
             JOIN products p ON t.product_id = p.id
             WHERE t.status = 'available' OR (t.status = 'in_progress' AND t.assigned_to = ?)
             ORDER BY t.created_at DESC
             LIMIT 50",
            [$user['id']]
        );

        return $tasks;
    }

    public function submitTask($taskId)
    {
        $user = Auth::verify();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['selected_brand']) || !isset($data['confidence_level'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }

        $task = $this->db->fetchOne(
            "SELECT * FROM brand_identification_tasks WHERE id = ?",
            [$taskId]
        );

        if (!$task) {
            http_response_code(404);
            return ['error' => 'Task not found'];
        }

        if ($task['status'] === 'completed') {
            http_response_code(400);
            return ['error' => 'Task already completed'];
        }

        try {
            $this->db->execute(
                "INSERT INTO task_submissions (task_id, user_id, selected_brand, confidence_level, notes)
                 VALUES (?, ?, ?, ?, ?)",
                [$taskId, $user['id'], $data['selected_brand'], $data['confidence_level'], $data['notes'] ?? null]
            );

            $this->db->execute(
                "UPDATE brand_identification_tasks SET status = 'completed', assigned_to = ? WHERE id = ?",
                [$user['id'], $taskId]
            );

            $this->db->execute(
                "UPDATE wallets SET balance = balance + ?, pending_balance = pending_balance + ?, total_earned = total_earned + ?
                 WHERE user_id = ?",
                [$task['reward_amount'], $task['reward_amount'], $task['reward_amount'], $user['id']]
            );

            $this->db->execute(
                "UPDATE user_profiles SET tasks_completed = tasks_completed + 1, total_earnings = total_earnings + ?
                 WHERE user_id = ?",
                [$task['reward_amount'], $user['id']]
            );

            $this->db->execute(
                "INSERT INTO earnings (user_id, amount, type, status, description)
                 VALUES (?, ?, 'task_completion', 'completed', ?)",
                [$user['id'], $task['reward_amount'], "Task #{$taskId} completed"]
            );

            return ['message' => 'Task submitted successfully', 'reward' => $task['reward_amount']];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Failed to submit task'];
        }
    }

    public function getTaskStats()
    {
        $user = Auth::verify();

        $stats = $this->db->fetchOne(
            "SELECT
                tasks_completed,
                total_earnings,
                accuracy_score
             FROM user_profiles
             WHERE user_id = ?",
            [$user['id']]
        );

        $wallet = $this->db->fetchOne(
            "SELECT balance, pending_balance FROM wallets WHERE user_id = ?",
            [$user['id']]
        );

        return [
            'tasks_completed' => (int)$stats['tasks_completed'],
            'total_earnings' => (float)$stats['total_earnings'],
            'accuracy_score' => (float)$stats['accuracy_score'],
            'available_balance' => (float)$wallet['balance'],
            'pending_balance' => (float)$wallet['pending_balance']
        ];
    }
}
