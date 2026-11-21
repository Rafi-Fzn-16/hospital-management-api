<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request, JwtService $jwtService)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data login tidak lengkap.',
                'details' => $validator->errors(),
            ], 422);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Authentication Failed',
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Role selalu diambil dari database
        $token = $jwtService->generate($user);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $token = $request->attributes->get('jwt_token');
        $payload = $request->attributes->get('jwt_payload');

        if (!$token || !$payload) {
            return response()->json([
                'message' => 'Token tidak ditemukan.',
            ], 401);
        }

        // Simpan hash token ke blacklist
        DB::table('jwt_blacklist')->insert([
            'token_hash' => hash('sha256', $token),
            'expires_at' => null,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    // AUTH ME
    public function me(Request $request)
    {
        $payload = $request->attributes->get('jwt_payload');

        return response()->json([
            'id' => $payload->sub,
            'username' => $payload->username,
            'role' => $payload->role,
            'scope' => $payload->scope ?? [],
        ]);
    }
}
