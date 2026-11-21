<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TreatmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TreatmentCategoryController extends Controller
{
    // GET /api/treatment_categories
    public function index()
    {
        $categories = TreatmentCategory::with('treatments')->get();

        return response()->json([
            'data' => $categories,
        ], 200);
    }

    // POST /api/treatment_categories
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data kategori treatment tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $category = TreatmentCategory::create(
            $validator->validated()
        );

        return response()->json([
            'message' => 'Kategori treatment berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    // GET /api/treatment_categories/{id}
    public function show(string $id)
    {
        $category = TreatmentCategory::with('treatments')->find($id);

        if (!$category) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Kategori treatment tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $category,
        ], 200);
    }

    // PUT/PATCH /api/treatment_categories/{id}
    public function update(Request $request, string $id)
    {
        $category = TreatmentCategory::find($id);

        if (!$category) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Kategori treatment tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Data kategori treatment tidak valid.',
                'details' => $validator->errors(),
            ], 422);
        }

        $category->update($validator->validated());

        return response()->json([
            'message' => 'Kategori treatment berhasil diperbarui.',
            'data' => $category,
        ], 200);
    }

    // DELETE /api/treatment_categories/{id}
    public function destroy(string $id)
    {
        $category = TreatmentCategory::find($id);

        if (!$category) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Kategori treatment tidak ditemukan.',
            ], 404);
        }

        if ($category->treatments()->exists()) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh treatment.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori treatment berhasil dihapus.',
        ], 200);
    }
}
