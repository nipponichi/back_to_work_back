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
                return response()->json(['succes'=> true, 'message' => 'Already authentified'], 200);
            }
    
            $field = filter_var($request->name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
    
            $validations = [
                'password' => 'required|string',
            ];
            
            if ($field === 'email') {
                $validations['name'] = 'required|email';
            } else {
                $validations['name'] = 'required|string';
            }
    
            $validatedData = $request->validate($validations);
    
            $data = [
                $field => $validatedData['name'],
                'password' => $validatedData['password'],
            ];
    
            if (Auth::attempt($data)) {
                $user = Auth::user();
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }
                $token = $user->createToken('token')->accessToken;
                $answer = [
                    'user' => $user,
                    'accessToken' => $token
                ];
                return response()->json(['success' => true, 'message' => 'Logged succesfully', 'data' => $answer], 200);
            }
            return response()->json(['success' => false, 'message' => 'Unauthorized login'], 401);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error while login: ' . $e->getMessage(), 'data' => ''], 500);
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
