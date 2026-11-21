<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    // Menampilkan semua data staff
    public function index()
    {
        $staff = Staff::with('staffSchedules')->get();

        return response()->json([
            'data' => $staff
        ], 200);
    }

    // Menambahkan staff
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:50',
            'department' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data staff tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $staff = Staff::create($validator->validated());

        return response()->json([
            'message' => 'Staff berhasil ditambahkan.',
            'data' => $staff,
        ], 201);
    }

    // Menampilkan detail staff
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $staff = Staff::with('staffSchedules')->find($id);

        if (!$staff) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data staff tidak ditemukan.',
            ], 404);
        }

        if (
            $user->role !== 'admin' &&
            !(
                in_array($user->role, ['nurse', 'receptionist']) &&
                (string) $user->staff_id === (string) $id
            )
        ) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        return response()->json([
            'data' => $staff
        ], 200);
    }

    // Memperbarui data staff
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data staff tidak ditemukan.',
            ], 404);
        }

        if (
            $user->role !== 'admin' &&
            !(
                in_array($user->role, ['nurse', 'receptionist']) &&
                (string) $user->staff_id === (string) $id
            )
        ) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'role' => 'sometimes|required|string|max:50',
            'department' => 'sometimes|nullable|string|max:100',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data staff tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $staff->update($validator->validated());

        return response()->json([
            'message' => 'Data staff berhasil diperbarui.',
            'data' => $staff,
        ], 200);
    }

    // Menghapus staff
    public function destroy(string $id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data staff tidak ditemukan.',
            ], 404);
        }

        $staff->delete();

        return response()->json([
            'message' => 'Staff berhasil dihapus.',
        ], 200);
    }
}
