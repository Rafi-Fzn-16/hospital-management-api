<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BedAdmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BedAdmissionController extends Controller
{
    // Menampilkan semua data rawat inap
    public function index()
    {
        $admissions = BedAdmission::with('patient')->get();

        return response()->json([
            'data' => $admissions
        ], 200);
    }

    // Menambahkan data rawat inap
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'service' => 'required|string|max:50',
            'bed_week' => [
                'required',
                'integer',
                Rule::exists('hospital_beds', 'week')
                    ->where('month', $request->bed_month)
                    ->where('service', $request->service),
            ],
            'bed_month' => 'required|integer|min:1|max:12',
            'admission_date' => 'required|date',
            'status' => 'sometimes|in:admitted,discharged',
            'discharge_date' => 'sometimes|required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data rawat inap tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data['status'] = $data['status'] ?? 'admitted';

        if ($data['status'] === 'admitted') {
            $data['discharge_date'] = 'Belum keluar';
        } elseif (
            empty($data['discharge_date']) ||
            $data['discharge_date'] === 'Belum keluar'
        ) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Tanggal keluar wajib diisi jika pasien sudah discharged.',
            ], 422);
        }

        $admission = BedAdmission::create($data);

        return response()->json([
            'message' => 'Data rawat inap berhasil ditambahkan.',
            'data' => $admission,
        ], 201);
    }

    // Menampilkan detail rawat inap
    public function show(string $id)
    {
        $admission = BedAdmission::with('patient')->find($id);

        if (!$admission) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data rawat inap tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $admission
        ], 200);
    }

    // Memperbarui data rawat inap
    public function update(Request $request, string $id)
    {
        $admission = BedAdmission::find($id);

        if (!$admission) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data rawat inap tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'sometimes|required|exists:patients,id',
            'service' => 'sometimes|required|string|max:50',
            'bed_week' => 'sometimes|required|integer',
            'bed_month' => 'sometimes|required|integer|min:1|max:12',
            'admission_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:admitted,discharged',
            'discharge_date' => 'sometimes|required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data rawat inap tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $status = $data['status'] ?? $admission->status;

        if ($status === 'admitted') {
            $data['discharge_date'] = 'Belum keluar';
        } elseif (
            $status === 'discharged' &&
            (
                empty($data['discharge_date'] ?? $admission->discharge_date) ||
                ($data['discharge_date'] ?? $admission->discharge_date) === 'Belum keluar'
            )
        ) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Tanggal keluar wajib diisi jika pasien sudah discharged.',
            ], 422);
        }

        $admission->update($data);

        return response()->json([
            'message' => 'Data rawat inap berhasil diperbarui.',
            'data' => $admission,
        ], 200);
    }

    // Menghapus data rawat inap
    public function destroy(string $id)
    {
        $admission = BedAdmission::find($id);

        if (!$admission) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data rawat inap tidak ditemukan.',
            ], 404);
        }

        $admission->delete();

        return response()->json([
            'message' => 'Data rawat inap berhasil dihapus.',
        ], 200);
    }
}
