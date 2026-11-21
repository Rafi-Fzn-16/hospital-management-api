<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HospitalBedController extends Controller
{
    // Menampilkan semua data tempat tidur
    public function index()
    {
        $beds = DB::table('hospital_beds')->get();

        return response()->json([
            'data' => $beds
        ], 200);
    }

    // Menambahkan data tempat tidur
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'week' => 'required|integer|min:1',
            'month' => 'required|integer|min:1|max:12',
            'service' => 'required|string|max:50',
            'available_beds' => 'required|integer|min:0',
            'patients_request' => 'sometimes|integer|min:0',
            'patients_admitted' => 'sometimes|integer|min:0',
            'patients_refused' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data tempat tidur tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $exists = DB::table('hospital_beds')
            ->where('week', $data['week'])
            ->where('month', $data['month'])
            ->where('service', $data['service'])
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Data tempat tidur untuk minggu, bulan, dan layanan tersebut sudah tersedia.',
            ], 409);
        }

        $data['patients_request'] ??= 0;
        $data['patients_admitted'] ??= 0;
        $data['patients_refused'] ??= 0;

        DB::table('hospital_beds')->insert($data);

        $bed = DB::table('hospital_beds')
            ->where('week', $data['week'])
            ->where('month', $data['month'])
            ->where('service', $data['service'])
            ->first();

        return response()->json([
            'message' => 'Data tempat tidur berhasil ditambahkan.',
            'data' => $bed,
        ], 201);
    }

    // Menampilkan detail tempat tidur
    public function show(string $week, string $month, string $service)
    {
        $bed = DB::table('hospital_beds')
            ->where('week', $week)
            ->where('month', $month)
            ->where('service', $service)
            ->first();

        if (!$bed) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data tempat tidur tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $bed
        ], 200);
    }

    // Memperbarui data tempat tidur
    public function update(Request $request, string $week, string $month, string $service)
    {
        $bed = DB::table('hospital_beds')
            ->where('week', $week)
            ->where('month', $month)
            ->where('service', $service);

        if (!$bed->exists()) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data tempat tidur tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'available_beds' => 'sometimes|required|integer|min:0',
            'patients_request' => 'sometimes|required|integer|min:0',
            'patients_admitted' => 'sometimes|required|integer|min:0',
            'patients_refused' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data tempat tidur tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $bed->update($data);

        $updatedBed = DB::table('hospital_beds')
            ->where('week', $week)
            ->where('month', $month)
            ->where('service', $service)
            ->first();

        return response()->json([
            'message' => 'Data tempat tidur berhasil diperbarui.',
            'data' => $updatedBed,
        ], 200);
    }

    // Menghapus data tempat tidur
    public function destroy(string $week, string $month, string $service)
    {
        $bed = DB::table('hospital_beds')
            ->where('week', $week)
            ->where('month', $month)
            ->where('service', $service);

        if (!$bed->exists()) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data tempat tidur tidak ditemukan.',
            ], 404);
        }

        $bed->delete();

        return response()->json([
            'message' => 'Data tempat tidur berhasil dihapus.',
        ], 200);
    }
}
