<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Adds;
use Exception;

class AddsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $adds = DB::table('adds')->get();
            return response()->json(['success' => true, 'message' => 'Adds loaded correctly', 'data' => $adds], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading adds: ' . $e->getMessage(), 'data' => ''], 500);
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
                'phone' => 'nullable|string|max:16',
                'age' => 'nullable|integer',
                'category' => 'required|string|max:64',
                'short_des' => 'nullable|string'
            ]);

            $id = DB::table('adds')->insertGetId($validatedData);
            $add = DB::table('adds')->where('id', $id)->first();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add saved correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving add: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $add = DB::table('adds')->where('id', $id)->first();

            if (!$add) {
                return response()->json(['success' => false, 'message' => 'Add not found', 'data' => ''], 404);
            }

            return response()->json(['success' => true, 'message' => 'Add loaded correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading add: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 
        DB::beginTransaction();
        try {
            $add = DB::table('adds')->where('id', $id)->first();

            if (!$add) {
                return response()->json(['success' => false, 'message' => 'Add not found', 'data' => ''], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:32',
                'phone' => 'nullable|string|max:16',
                'age' => 'nullable|integer',
                'category' => 'required|string|max:64',
                'short_des' => 'nullable|string'
            ]);

            $updated = DB::table('adds')->where('id', $id)->update($validatedData);
            
            if ($updated) {          
                $add = DB::table('adds')->where('id', $id)->first();
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Add updated correctly', 'data' => $add], 200);
            } else {
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Nothing to update', 'data' => $add], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating add: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $add = DB::table('adds')->where('id', $id)->first();

            if (!$add) {
                return response()->json(['success' => false, 'message' => 'Add not found', 'data' => ''], 404);
            }

            DB::table('adds')->where('id', $id)->delete();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add deleted correctly', 'data' => null], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting add: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    public function getAdd($id)
    {
        try {
            $add = Adds::find($id);
            if (!$add) {
                return response()->json(['success' => false, 'message' => 'Add not found', 'data' => ''], 404);
            }

            return response()->json(['success' => true, 'message' => 'Adds loaded correctly', 'data' => $add->subjects], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading subjects: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }
}
