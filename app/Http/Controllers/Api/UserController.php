<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        $users = User::select('id', 'username', 'role', 'created_at')
            ->get();

        return response()->json([
            'data' => $users,
        ], 200);
    }

    // POST /api/users
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,doctor,nurse,receptionist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data pengguna tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ],
        ], 201);
    }

    // GET /api/users/{id}
    public function show(string $id)
    {
        $user = User::select('id', 'username', 'role', 'created_at')
            ->find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $user,
        ], 200);
    }

    // PUT/PATCH /api/users/{id}
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|in:admin,doctor,nurse,receptionist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data pengguna tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $user->fill($validator->validated());
        $user->save();

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ],
        ], 200);
    }

    // DELETE /api/users/{id}
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ], 200);
    }
}
