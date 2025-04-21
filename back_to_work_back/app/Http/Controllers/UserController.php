<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['categories', 'provinces'])->where('is_pro', true)->get();
            return response()->json(['success' => true, 'message' => 'Users loaded correctly', 'data' => $users], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading users: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'User loaded correctly', 'data' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }
}
