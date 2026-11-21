<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        // Pastikan pengguna sudah login
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Ambil permission berdasarkan role
        $permissions = config("permissions.{$user->role}", []);

        // Admin memiliki seluruh permission
        if (in_array('*', $permissions)) {
            return $next($request);
        }

        // Periksa permission pengguna
        if (!in_array($permission, $permissions)) {
            return response()->json([
                'message' => 'Akses ditolak.',
            ], 403);
        }

        return $next($request);
    }
}
