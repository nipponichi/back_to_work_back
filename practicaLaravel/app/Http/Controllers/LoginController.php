<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {

            if (Auth::guard('api')->check()) {
                return response()->json(['succes'=> true, 'message' => 'Already authentified'], 200);
            }
        
            $data = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);
        
            if (Auth::attempt($data)) {
                $user = Auth::user();
 
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }
                $token = $user->createToken('token');
                $result = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token
                ];

                return response()->json(['succes'=> true, 'message' => 'Logged in successfully', 'result' => $result], 200);
            }
        
            return response()->json(['success' => false, 'message' => 'Unauthorized login'], 401);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error while login' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Not logged in user'], 401);
            }
            
            $user->tokens()->delete();
    
            return response()->json(['success' => true,'message' =>'Logged out successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error while logout' . $e->getMessage(), 'data' => ''], 500);
        }

        
    }

    public function signup() {
        try {
            return response()->json(['success' => true, 'message' => 'Create your user profile'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error signing up' . $e->getMessage(), 'data' => ''], 500);
        }
    }

    public function userProfile()
    {
        try {
            $user = Auth::guard('api')->user();
            
            if ($user) {
                return response()->json(['success' => true, 'message' => 'Create your user profile', 'data' => $user], 200);
            }
            
            return response()->json(['success' => false, 'message' => 'Not logged in user'], 401);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getting user profile' . $e->getMessage(), 'data' => ''], 500);
        }

    }
}
