<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use Exception;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::all();
        return response()->json($classrooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getTeachers(string $id)
    {
        try {
            $classroom = Classroom::with('teacher')->find($id);
            if (!$classroom) {
                return response()->json(['success' => false, 'message' => 'Classroom not found', 'data' => ''], 404);
            }
            return response()->json(['success' => true, 'message' => 'Teachers loaded correctly', 'data' => $classroom->teacher], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading teachers: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

}
