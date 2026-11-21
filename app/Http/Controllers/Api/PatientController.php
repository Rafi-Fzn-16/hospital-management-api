<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    // GET /api/patients
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'doctor') {
            if (!$user->doctor_id) {
                $patients = collect();
            } else {
                $patients = Patient::whereIn('id', function ($query) use ($user) {
                    $query->select('patient_id')
                        ->from('appointments')
                        ->where('doctor_id', $user->doctor_id);
                })->get();
            }
        } else {
            $patients = Patient::all();
        }

        return response()->json([
            'data' => $patients,
        ], 200);
    }

    // POST /api/patients
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:30',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data pasien tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $patient = Patient::create($validator->validated());

        return response()->json([
            'message' => 'Data pasien berhasil ditambahkan.',
            'data' => $patient,
        ], 201);
    }

    // GET /api/patients/{id}
    public function show(Request $request, string $id)
    {
        $patient = Patient::with([
            'appointments',
            'bedAdmissions',
            'billings',
        ])->find($id);

        if (!$patient) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data pasien tidak ditemukan.',
            ], 404);
        }

        $user = $request->user();

        if ($user->role === 'doctor') {
            $hasAppointment = $user->doctor_id &&
                Appointment::where('patient_id', $id)
                    ->where('doctor_id', $user->doctor_id)
                    ->exists();

            if (!$hasAppointment) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Akses ditolak.',
                ], 403);
            }
        }

        return response()->json([
            'data' => $patient,
        ], 200);
    }

    // PUT/PATCH /api/patients/{id}
    public function update(Request $request, string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data pasien tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'birth_date' => 'sometimes|required|date',
            'gender' => 'sometimes|required|in:L,P',
            'phone' => 'sometimes|nullable|string|max:30',
            'address' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data pasien tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $patient->update($validator->validated());

        return response()->json([
            'message' => 'Data pasien berhasil diperbarui.',
            'data' => $patient,
        ], 200);
    }

    // DELETE /api/patients/{id}
    public function destroy(string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data pasien tidak ditemukan.',
            ], 404);
        }

        $patient->delete();

        return response()->json([
            'message' => 'Data pasien berhasil dihapus.',
        ], 200);
    }
}
