<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Student;
use Exception;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $students = DB::table('students')->get();
            return response()->json(['success' => true, 'message' => 'Students loaded correctly', 'data' => $students], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading students: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validation = [
                'name' => 'required|string|max:32',
                'phone' => 'nullable|string|max:16',
                'age' => 'nullable|integer',
                'password' => 'required|string|max:64',
                'email' => 'required|email|unique:students,email|max:64',
                'gender' => 'nullable|string'
            ];

            $validatedData = $request->validate($validation);

            $id = DB::table('students')->insertGetId([
                'name' => $validatedData['name'],
                'phone' => $validatedData['phone'],
                'age' => $validatedData['age'],
                'password' => bcrypt($validatedData['password']),
                'email' => $validatedData['email'],
                'gender' => $validatedData['gender'],
            ]);

            $student = DB::table('students')->where('id', $id)->first();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Student saved correctly', 'data' => $student], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving student: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $student = DB::table('students')->where('id', $id)->first();

            if ($student === null) {
                return response()->json(['success' => false, 'message' => 'Student not found', 'data' => ''], 404);
            }

            return response()->json(['success' => true, 'message' => 'Student loaded correctly', 'data' => $student], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading student: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 
        DB::beginTransaction();
        try {
            $student = DB::table('students')->where('id', $id)->first();

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found', 'data' => ''], 404);
            }

            $validation = [
                'name' => 'sometimes|string|max:32',
                'phone' => 'nullable|string|max:16',
                'age' => 'nullable|integer',
                'password' => 'sometimes|string|max:64',
                'email' => 'sometimes|email|unique:students,email|max:64',
                'gender' => 'nullable|string'
            ];
            $validatedData = $request->validate($validation);

            $updated = DB::table('students')->where('id', $id)->update($validatedData);

            if ($updated) {          
                $student = DB::table('students')->where('id', $id)->first();
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Student updated correctly', 'data' => $student], 200);
            } else {
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Nothing to update', 'data' => $student], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating student: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $student = DB::table('students')->where('id', $id)->first();

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found', 'data' => ''], 404);
            }

            DB::table('students')->where('id', $id)->delete();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Student deleted correctly', 'data' => $student], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting student: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    public function getSubjects($id)
    {
        try {
            $student = Student::find($id);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found', 'data' => ''], 404);
            }

            return response()->json(['success' => true, 'message' => 'Subjects loaded correctly', 'data' => $student->subjects], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading subjects: ' . $e->getMessage(), 'data' => ''], 500);
        }
    }
}
