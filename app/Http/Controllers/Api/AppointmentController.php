<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    // GET /api/appointments
    public function index()
    {
        $appointments = Appointment::with([
            'patient',
            'doctor',
        ])->get();

        return response()->json([
            'data' => $appointments,
        ], 200);
    }

    // POST /api/appointments
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer|exists:patients,id',
            'doctor_id' => 'required|integer|exists:doctors,id',
            'appointment_date' => 'required|date',
            'complaint' => 'nullable|string',
            'status' => 'sometimes|required|in:scheduled,done,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data appointment tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $appointment = Appointment::create($validator->validated());

        $appointment->load(['patient', 'doctor']);

        return response()->json([
            'message' => 'Appointment berhasil ditambahkan.',
            'data' => $appointment,
        ], 201);
    }

    // GET /api/appointments/{id}
    public function show(string $id)
    {
        $appointment = Appointment::with([
            'patient',
            'doctor',
            'treatments',
        ])->find($id);

        if (!$appointment) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Appointment tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $appointment,
        ], 200);
    }

    // PUT/PATCH /api/appointments/{id}
    public function update(Request $request, string $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Appointment tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'sometimes|required|integer|exists:patients,id',
            'doctor_id' => 'sometimes|required|integer|exists:doctors,id',
            'appointment_date' => 'sometimes|required|date',
            'complaint' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:scheduled,done,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data appointment tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $appointment->update($validator->validated());

        $appointment->load(['patient', 'doctor']);

        return response()->json([
            'message' => 'Appointment berhasil diperbarui.',
            'data' => $appointment,
        ], 200);
    }

    // DELETE /api/appointments/{id}
    public function destroy(string $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Appointment tidak ditemukan.',
            ], 404);
        }

        $appointment->delete();

        return response()->json([
            'message' => 'Appointment berhasil dihapus.',
        ], 200);
    }
}
