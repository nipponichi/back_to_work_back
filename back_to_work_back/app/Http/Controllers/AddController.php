<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Add;
use Exception;
use Illuminate\Support\Facades\DB;

class AddController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $adds = Add::all();
            return response()->json(['success' => true, 'message' => 'Adds loaded correctly', 'data' => $adds], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading adds: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id'
            ]);

            $add = Add::create($validatedData);
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add saved correctly', 'data' => $add], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $add = Add::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Add loaded correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Add not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $add = Add::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id'
            ]);

            $add->update($validatedData);
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add updated correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $add = Add::findOrFail($id);
            $add->delete();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add deleted correctly'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get a single add with its relationships.
     */
    public function getAdd($id)
    {
        try {
            $add = Add::with(['user', 'adPicture', 'adOffer', 'adChat'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Add loaded correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading add: ' . $e->getMessage()], 500);
        }
    }
}
