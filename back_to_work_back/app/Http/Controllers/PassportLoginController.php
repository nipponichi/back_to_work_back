<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class PassportLoginController extends Controller
{

    public function signup(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|max:55',
                'email' => 'email|required|unique:users',
                'password' => 'required'
            ]);

            $data['password'] = bcrypt($data['password']);
        
            $user = User::create($data);
        
            $token = $user->createToken('token')->accessToken;

            $answer = [
                'user' => $user,
                'token' => $token
            ];
            return response(['success' => true, 'message' => 'User created successfully', 'data' => $answer], 201);
            
        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => 'Error while create user: ' . $e->getMessage(), 'data' => ''], 500);
        }
        
    }
  
    public function login(Request $request)
    {
        try {
            if (Auth::guard('api')->check()) {
                return response()->json(['success' => true, 'message' => 'Already authenticated'], 200);
            }

            $validatedData = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            $credentials = [
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
            ];

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }

                if ($user->is_blocked) {
                    return response()->json(['success' => false, 'message' => 'Your account has been blocked'], 403);
                }

                $user->load(['userStat', 'categories', 'provinces', 'roles']);

                $token = $user->createToken('token')->accessToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Logged in successfully',
                    'data' => [
                        'user' => $user,
                        'roles' => $user->getRoleNames(),
                        'accessToken' => $token
                    ]
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized login'], 401);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error while logging in: ' . $e->getMessage(),
                'data' => ''
            ], 500);
        }
    }



    public function userProfile()
    {
        try {
            $user = Auth::guard('api')->user();     
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Not logged in user'], 401);
            }
            return response()->json(['success' => true, 'message' => 'Your user profile', 'data' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getting user profile: ' . $e->getMessage(), 'data' => ''], 500);
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
            return response()->json(['success' => false, 'message' => 'Error while logout: ' . $e->getMessage(), 'data' => ''], 500);
        }
        
    }
}
