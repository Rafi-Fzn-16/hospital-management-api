<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffScheduleController extends Controller
{
    // Menampilkan semua jadwal staff
    public function index()
    {
        $schedules = StaffSchedule::with('staff')->get();

        return response()->json([
            'data' => $schedules
        ], 200);
    }

    // Menambahkan jadwal staff
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'week' => 'required|integer|min:1',
            'present' => 'sometimes|boolean',
            'status' => 'required|in:scheduled,onhold,present,absent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data jadwal staff tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Menyesuaikan kolom present dengan status
        if (isset($data['status'])) {
            $data['present'] = $data['status'] === 'present' ? 1 : 0;
        }

        $schedule = StaffSchedule::create($data);

        return response()->json([
            'message' => 'Jadwal staff berhasil ditambahkan.',
            'data' => $schedule,
        ], 201);
    }

    // Menampilkan detail jadwal
    public function show(string $id)
    {
        $schedule = StaffSchedule::with('staff')->find($id);

        if (!$schedule) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Jadwal staff tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $schedule
        ], 200);
    }

    // Memperbarui jadwal staff
    public function update(Request $request, string $id)
    {
        $schedule = StaffSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Jadwal staff tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'staff_id' => 'sometimes|required|exists:staff,id',
            'week' => 'sometimes|required|integer|min:1',
            'present' => 'sometimes|boolean',
            'status' => 'sometimes|required|in:scheduled,onhold,present,absent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data jadwal staff tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (isset($data['status'])) {
            $data['present'] = $data['status'] === 'present' ? 1 : 0;
        }

        $schedule->update($data);

        return response()->json([
            'message' => 'Jadwal staff berhasil diperbarui.',
            'data' => $schedule,
        ], 200);
    }

    // Menghapus jadwal staff
    public function destroy(string $id)
    {
        $schedule = StaffSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Jadwal staff tidak ditemukan.',
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'Jadwal staff berhasil dihapus.',
        ], 200);
    }
}
