<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Str;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['categories', 'provinces', 'userStat'])->where('is_pro', true)->where('is_pro', true)->get();
            return response()->json(['success' => true, 'message' => 'Users loaded correctly', 'data' => $users], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading users: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with(['categories', 'provinces', 'userStat'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'User loaded correctly', 'data' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            
            $validatedData = $request->validate([
                'user_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string|max:20',
                'province_id' => 'required',
                'is_pro' => 'boolean',
            ]);

            $user = User::create([
                'user_name' => $validatedData['user_name'],
                'name' => $validatedData['name'],
                'password' => bcrypt($request->input('password')),
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'] ?? null,
                'province_id' => $validatedData['province_id'],
                'is_pro' => $validatedData['is_pro'] ?? false,
            ]);

            if ($user->is_pro && !empty($validatedData['categories'])) {
                $user->categories()->sync($validatedData['categories']);
            }
    
            $user->load(['categories', 'provinces']);

            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            Mail::to($user->email)->send(new SendMail([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'signature' => explode('=', parse_url($signedUrl, PHP_URL_QUERY))[1]
            ], SendMail::TEMPLATE_WELCOME));
    
            return response()->json(['success' => true, 'message' => 'User created successfully', 'data' => $user], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
    
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'province_id' => 'nullable|integer',
            'password' => [
                'string',
                'min:4',
            ],
        ]); 

        try {
            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            $user->province_id = $validated['province_id'] ?? null;

            if (!empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }

            $user->save();

            return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente', 'user' => $request->all()], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el usuario', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()], 500);
        }
    }

    public function sendPasswordResetEmail($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Password reset email sent successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error sending password reset email: ' . $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:4|confirmed'
        ]);

        try {

            $user = User::where('email', $request->email)->firstOrFail();
            $user->password = bcrypt($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json(['success' => true, 'message' => 'Contraseña actualizada']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }

    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = true; 
        $user->save();

        return response()->json(['success' => true]);
    }   

    public function unblockUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = false; 
        $user->save();

        return response()->json(['success' => true]);
    }

}
