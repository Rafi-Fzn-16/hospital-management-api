<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TreatmentController extends Controller
{
    // GET /api/treatments
    public function index()
    {
        $treatments = Treatment::with([
            'appointment.patient',
            'appointment.doctor',
            'category',
        ])->get();

        return response()->json([
            'data' => $treatments,
        ], 200);
    }

    // POST /api/treatments
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer|exists:appointments,id',
            'category_id' => 'sometimes|required|integer|exists:treatment_categories,id',
            'treatment_type' => 'required|string|max:100',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'treatment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data treatment tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $treatment = Treatment::create($validator->validated());

        $treatment->load([
            'appointment.patient',
            'appointment.doctor',
            'category',
        ]);

        return response()->json([
            'message' => 'Treatment berhasil ditambahkan.',
            'data' => $treatment,
        ], 201);
    }

    // GET /api/treatments/{id}
    public function show(string $id)
    {
        $treatment = Treatment::with([
            'appointment.patient',
            'appointment.doctor',
            'category',
            'billings',
        ])->find($id);

        if (!$treatment) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data treatment tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $treatment,
        ], 200);
    }

    // PUT/PATCH /api/treatments/{id}
    public function update(Request $request, string $id)
    {
        $treatment = Treatment::find($id);

        if (!$treatment) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data treatment tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'appointment_id' => 'sometimes|required|integer|exists:appointments,id',
            'category_id' => 'sometimes|required|integer|exists:treatment_categories,id',
            'treatment_type' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|required|string',
            'cost' => 'sometimes|required|numeric|min:0',
            'treatment_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data treatment tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $treatment->update($validator->validated());

        $treatment->load([
            'appointment.patient',
            'appointment.doctor',
            'category',
        ]);

        return response()->json([
            'message' => 'Data treatment berhasil diperbarui.',
            'data' => $treatment,
        ], 200);
    }

    // DELETE /api/treatments/{id}
    public function destroy(string $id)
    {
        $treatment = Treatment::find($id);

        if (!$treatment) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data treatment tidak ditemukan.',
            ], 404);
        }

        // Cegah penghapusan jika masih digunakan oleh billing
        if ($treatment->billings()->exists()) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Treatment tidak dapat dihapus karena masih digunakan dalam billing.',
            ], 409);
        }

        $treatment->delete();

        return response()->json([
            'message' => 'Treatment berhasil dihapus.',
        ], 200);
    }
}
