<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwt
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // Ambil token Bearer
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Token Bearer tidak ditemukan.',
            ], 401);
        }

        // Validasi JWT
        $jwtService = app(JwtService::class);
        $payload = $jwtService->decode($token);

        if (!$payload) {
            return response()->json([
                'error' => 'Invalid Token',
                'message' => 'Token tidak valid.',
            ], 401);
        }

        // Pastikan claim penting tersedia
        if (
            !isset($payload->sub) ||
            !isset($payload->role)
        ) {
            return response()->json([
                'error' => 'Invalid Token',
                'message' => 'Payload token tidak lengkap.',
            ], 401);
        }

        // Periksa blacklist
        $tokenHash = hash('sha256', $token);

        $isBlacklisted = DB::table('jwt_blacklist')
            ->where('token_hash', $tokenHash)
            ->exists();

        if ($isBlacklisted) {
            return response()->json([
                'error' => 'Token Revoked',
                'message' => 'Token sudah tidak dapat digunakan.',
            ], 401);
        }

        // Cari pengguna berdasarkan ID dari token
        $user = User::find($payload->sub);

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Pengguna tidak ditemukan.',
            ], 401);
        }

        // Hubungkan user ke request Laravel
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Simpan payload untuk kebutuhan controller
        $request->attributes->set('jwt_payload', $payload);
        $request->attributes->set('jwt_token', $token);

        return $next($request);
    }
}
