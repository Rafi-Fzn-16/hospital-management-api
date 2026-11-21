<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingController extends Controller
{
    // Menampilkan semua data billing
    public function index()
    {
        $billings = Billing::with([
            'patient',
            'treatment'
        ])->get();

        return response()->json([
            'data' => $billings
        ], 200);
    }

    // Menambahkan billing baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'required|exists:treatments,id',
            'bill_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:100',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data billing tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $billing = Billing::create($validator->validated());

        return response()->json([
            'message' => 'Billing berhasil ditambahkan.',
            'data' => $billing,
        ], 201);
    }

    // Menampilkan detail billing
    public function show(string $id)
    {
        $billing = Billing::with([
            'patient',
            'treatment'
        ])->find($id);

        if (!$billing) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data billing tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $billing
        ], 200);
    }

    // Memperbarui billing
    public function update(Request $request, string $id)
    {
        $billing = Billing::find($id);

        if (!$billing) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data billing tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'sometimes|required|exists:patients,id',
            'treatment_id' => 'sometimes|required|exists:treatments,id',
            'bill_date' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0',
            'payment_method' => 'sometimes|required|string|max:100',
            'payment_status' => 'sometimes|required|in:pending,paid,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data billing tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $billing->update($validator->validated());

        return response()->json([
            'message' => 'Billing berhasil diperbarui.',
            'data' => $billing,
        ], 200);
    }

    // Menghapus billing
    public function destroy(string $id)
    {
        $billing = Billing::find($id);

        if (!$billing) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Data billing tidak ditemukan.',
            ], 404);
        }

        $billing->delete();

        return response()->json([
            'message' => 'Billing berhasil dihapus.',
        ], 200);
    }
}
