<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use Exception;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::all();
        return response()->json($subjects);
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

    public function getStudents(string $id)
    {
        try {
            $subject = Subject::with('students')->find($id);
            if (!$subject) {
                return response()->json(['success' => false, 'message' => 'Subject not found', 'data' => ''], 404);
            }
            return response()->json(['success' => true, 'message' => 'Students loaded correctly', 'data' => $subject->students], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading students: ' . $e->getMessage(), 'data' => ''], 500);
        }

    }

    public function getTeachers(string $id)
    {
        try {
            $subject = Subject::with('teacher')->find($id);
            if (!$subject) {
                return response()->json(['success' => false, 'message' => 'Subject not found', 'data' => ''], 404);
            }
            return response()->json(['success' => true, 'message' => 'Teachers loaded correctly', 'data' => $subject->teacher], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading teachers: ' . $e->getMessage(), 'data' => ''], 500);
        }

    }
}
