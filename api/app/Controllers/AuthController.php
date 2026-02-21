<?php

namespace App\Controllers;

use App\Database;
use App\Middleware\Auth;

class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function signup()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return ['error' => 'Email and password are required'];
        }

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            http_response_code(400);
            return ['error' => 'Invalid email format'];
        }

        $existing = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            http_response_code(400);
            return ['error' => 'User already exists'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $fullName = $data['full_name'] ?? null;

        try {
            $this->db->execute(
                "INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)",
                [$email, $hashedPassword, $fullName]
            );

            $userId = $this->db->lastInsertId();

            $this->db->execute(
                "INSERT INTO user_profiles (user_id) VALUES (?)",
                [$userId]
            );

            $this->db->execute(
                "INSERT INTO wallets (user_id) VALUES (?)",
                [$userId]
            );

            $user = $this->db->fetchOne(
                "SELECT id, email, role, full_name, vip_tier FROM users WHERE id = ?",
                [$userId]
            );

            $token = Auth::generateToken($userId);

            return [
                'user' => $user,
                'session' => ['access_token' => $token]
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Registration failed'];
        }
    }

    public function signin()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return ['error' => 'Email and password are required'];
        }

        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE email = ?",
            [$data['email']]
        );

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            return ['error' => 'Invalid credentials'];
        }

        $token = Auth::generateToken($user['id']);

        unset($user['password']);

        return [
            'user' => $user,
            'session' => ['access_token' => $token]
        ];
    }

    public function signout()
    {
        return ['message' => 'Logged out successfully'];
    }

    public function getUser()
    {
        $user = Auth::verify();
        return ['user' => $user];
    }
}
