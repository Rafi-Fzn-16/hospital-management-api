<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    // GET /api/doctors
    public function index()
    {
        $doctors = Doctor::all();

        return response()->json([
            'data' => $doctors,
        ], 200);
    }

    // POST /api/doctors
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data dokter tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $doctor = Doctor::create($validator->validated());

        return response()->json([
            'message' => 'Data dokter berhasil ditambahkan.',
            'data' => $doctor,
        ], 201);
    }

    // GET /api/doctors/{id}
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $doctor = Doctor::with('appointments')->find($id);

        if (!$doctor) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data dokter tidak ditemukan.',
            ], 404);
        }

        if (
            $user->role !== 'admin' &&
            !($user->role === 'nurse') &&
            !($user->role === 'doctor' && (string) $user->doctor_id === (string) $id)
        ) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        return response()->json([
            'data' => $doctor,
        ], 200);
    }

    // PUT/PATCH /api/doctors/{id}
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data dokter tidak ditemukan.',
            ], 404);
        }

        if (
            $user->role !== 'admin' &&
            !($user->role === 'doctor' && (string) $user->doctor_id === (string) $id)
        ) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'specialization' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data dokter tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $doctor->update($validator->validated());

        return response()->json([
            'message' => 'Data dokter berhasil diperbarui.',
            'data' => $doctor,
        ], 200);
    }

    // DELETE /api/doctors/{id}
    public function destroy(string $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data dokter tidak ditemukan.',
            ], 404);
        }

        $doctor->delete();

        return response()->json([
            'message' => 'Data dokter berhasil dihapus.',
        ], 200);
    }
}
