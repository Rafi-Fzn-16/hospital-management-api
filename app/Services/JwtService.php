<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;
use Throwable;

class JwtService
{
    // Membuat JWT
    public function generate(User $user): string
    {
        $secret = config('jwt.secret');

        if (empty($secret)) {
            throw new RuntimeException('JWT_SECRET belum dikonfigurasi.');
        }

        $now = time();

        $payload = [
            'iss' => config('app.url'),
            'sub' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'scope' => config("permissions.{$user->role}", []),
            'iat' => $now,
            'nbf' => $now,
            'jti' => bin2hex(random_bytes(16)),
        ];

        return JWT::encode(
            $payload,
            $secret,
            config('jwt.algorithm', 'HS256')
        );
    }

    // Memvalidasi dan membaca JWT
    public function decode(string $token): ?object
    {
        try {
            $secret = config('jwt.secret');

            if (empty($secret)) {
                throw new RuntimeException('JWT_SECRET belum dikonfigurasi.');
            }

            return JWT::decode(
                $token,
                new Key(
                    $secret,
                    config('jwt.algorithm', 'HS256')
                )
            );

        } catch (Throwable $e) {
            return null;
        }
    }
}
