<?php

namespace App\Middleware;

class Auth
{
    public static function verify()
    {
        $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $user = self::verifyToken($token);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }

        return $user;
    }

    public static function verifyAdmin()
    {
        $user = self::verify();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            exit;
        }

        return $user;
    }

    public static function generateToken($userId)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $secret = $config['jwt_secret'];

        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode([
            'user_id' => $userId,
            'exp' => time() + (7 * 24 * 60 * 60)
        ]));

        $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    public static function verifyToken($token)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $secret = $config['jwt_secret'];

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        $validSignature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
        if ($signature !== $validSignature) {
            return false;
        }

        $payloadData = json_decode(base64_decode($payload), true);
        if (!$payloadData || !isset($payloadData['exp']) || $payloadData['exp'] < time()) {
            return false;
        }

        $db = \App\Database::getInstance();
        $user = $db->fetchOne(
            "SELECT id, email, role, full_name, vip_tier FROM users WHERE id = ?",
            [$payloadData['user_id']]
        );

        return $user ?: false;
    }
}
