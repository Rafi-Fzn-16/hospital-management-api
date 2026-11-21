<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;

class CombinedController extends Controller
{
    // Mengambil seluruh data terkait pasien
    public function patient(string $patient_id)
    {
        $patient = Patient::with([
            'appointments.doctor',
            'appointments.treatments.category',
            'bedAdmissions',
            'billings.treatment',
        ])->find($patient_id);

        if (!$patient) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data pasien tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'patient' => $patient,
                'appointments' => $patient->appointments,
                'treatments' => $patient->appointments
                    ->flatMap->treatments
                    ->values(),
                'bed_admissions' => $patient->bedAdmissions,
                'billings' => $patient->billings,
            ],
        ], 200);
    }
}
