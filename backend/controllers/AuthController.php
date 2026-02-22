<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../config/jwt.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function register($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        $existingUser = $this->db->selectOne(
            "SELECT id FROM users WHERE email = ?",
            [$email]
        );

        if ($existingUser) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $userId = $this->db->insert(
            "INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)",
            [$email, $hashedPassword, $data['full_name'] ?? '']
        );

        if ($userId) {
            $token = JWTConfig::generateToken($userId, $email, 'user');
            return [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $userId,
                    'email' => $email,
                    'role' => 'user',
                    'balance' => 0,
                    'vip_tier' => 'Free'
                ]
            ];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    public function login($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        $user = $this->db->selectOne(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $token = JWTConfig::generateToken($user['id'], $user['email'], $user['role']);

        unset($user['password']);

        return [
            'success' => true,
            'token' => $token,
            'user' => $user
        ];
    }

    public function getProfile($userId) {
        $user = $this->db->selectOne(
            "SELECT id, email, full_name, role, balance, total_earned, tasks_completed, vip_tier, created_at
             FROM users WHERE id = ?",
            [$userId]
        );

        if ($user) {
            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'message' => 'User not found'];
    }
}
