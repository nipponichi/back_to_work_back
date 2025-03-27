<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdCategory;
use Illuminate\Support\Facades\DB;
use Exception;

class AdCategoryController extends Controller
{
    /**
     * Obtener todas las categorías.
     */
    public function index()
    {
        try {
            $categories = AdCategory::all();
            return response()->json(['success' => true, 'message' => 'Categories loaded successfully', 'data' => $categories], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading categories: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear una nueva categoría.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'category' => 'required|string|max:255|unique:adds_categories,category',
                'description' => 'nullable|string|max:500',
            ]);

            $category = AdCategory::create($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category created successfully', 'data' => $category], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error creating category: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una categoría específica.
     */
    public function show($id)
    {
        try {
            $category = AdCategory::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Category loaded successfully', 'data' => $category], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $category = AdCategory::findOrFail($id);

            $validatedData = $request->validate([
                'category' => 'required|string|max:255|unique:ads_categories,category,' . $id,
                'description' => 'nullable|string|max:500',
            ]);

            $category->update($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category updated successfully', 'data' => $category], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating category: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $category = AdCategory::findOrFail($id);
            $category->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting category: ' . $e->getMessage()], 500);
        }
    }
}
