<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
        
    public function getAuthUser()
    {
        return Auth::guard('api')->user();
    }

    public function index()
    {
        try {

            $authUser = $this->getAuthUser();

            if ($authUser->hasRole('admin')) {
                $users = User::get();
                return response()->json(['success' => true, 'message' => 'Usuarios cargados correctamente', 'data' => $users], 200);
            }

            $users = User::with(['categories', 'provinces', 'userStat'])->where('is_pro', true)->get();

            return response()->json(['success' => true, 'message' => 'Usuarios cargados correctamente', 'data' => $users], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error cargando usuarios: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $authUser = $this->getAuthUser();

            if ($authUser->hasRole('admin')) {
                $user = User::with(['categories', 'provinces', 'userStat'])->findOrFail($id);
                return response()->json(['success' => true, 'message' => 'Usuarios cargados', 'data' => $user], 200);
            }

            $user = User::with(['categories', 'provinces', 'userStat'])->findOrFail($id);

            return response()->json(['success' => true, 'message' => 'Usuario cargados correctamente', 'data' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
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
                'password' => ['string','min:4',],
            ]);

            $imagePath = null;

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('users', 'public');
                $imagePath = 'storage/' . $path;
            }

            $user = User::create([
                'user_name'   => $validatedData['user_name'],
                'name'        => $validatedData['name'],
                'email'       => $validatedData['email'],
                'password'    => bcrypt($request->input('password')),
                'phone'       => $validatedData['phone'] ?? null,
                'province_id' => $validatedData['province_id'],
                'is_pro'      => $validatedData['is_pro'] ?? false,
                'image'       => $imagePath,
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

            $authUser = $this->getAuthUser();

            if ($authUser && $authUser->hasRole('admin')) {
                $roleToAssign = $request->rol;
            } else {
                $roleToAssign = 'user';
            }

            $user->assignRole($roleToAssign);
            

            Mail::to($user->email)->send(new SendMail([
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'signature' => explode('=', parse_url($signedUrl, PHP_URL_QUERY))[1]
            ], SendMail::TEMPLATE_WELCOME));

            return response()->json(['success' => true, 'message' => 'Usuario creado correctamente', 'data' => $user], 201);


        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creando usuario: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'province_id' => 'nullable|integer',
            'password' => ['string','min:4',],
        ]);

        try {
            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            $user->province_id = $validated['province_id'] ?? null;

            if ($request->hasFile('image')) {
                $this->handleImageUpload($request, $user);
            }
            if (!empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }

            $user->save();

            $authUser = $this->getAuthUser();

            if ($authUser && $authUser->hasRole('admin')) {
                $roleToAssign = $request->rol;
            } else {
                $roleToAssign = 'user';
            }

            $user->assignRole($roleToAssign);

            return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente', 'data' => $user], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el usuario', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            
            $authUser = $this->getAuthUser();

            $user = User::findOrFail($id);
            if ($authUser->id === $user->id || $authUser->hasRole('admin')) {
                
                if ($user->image && Storage::disk('public')->exists(str_replace('storage/', '', $user->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->image));
                }
                $user->delete();

                return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente', 'data' => $authUser], 200);
            }

            return response()->json(['success' => false, 'message' => 'No tienes permisos para eliminar el usuario', 'data' => ''], 403);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error eliminando usuario: ' . $e->getMessage()], 500);
        }
    }

    public function sendPasswordResetEmail($id)
    {
        try {
            $user = User::findOrFail($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Usuario no encontrado', 'data' => ''], 404);
            }

            return response()->json(['success' => true, 'message' => 'Email enviado', 'data' => ''], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error enviando el email: ' . $e->getMessage()], 500);
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

            return response()->json(['success' => true, 'message' => 'Contraseña actualizada', 'data' => ''], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' .$e->getMessage()], 500);
        }
    }

    public function blockUser($id)
    {
        $authUser = $this->getAuthUser();
        if (!$authUser->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para bloquear usuarios', 'data' => ''], 403);
        }
        $user = User::findOrFail($id);
        $user->is_blocked = true; 
        $user->save();

        return response()->json(['success' => true]);
    }   

    public function unblockUser($id)
    {
        $authUser = $this->getAuthUser();
        if (!$authUser->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para desbloquear usuarios', 'data' => ''], 403);
        }
        $user = User::findOrFail($id);
        $user->is_blocked = false; 
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updateImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|file|image|mimes:jpg,jpeg,png',
        ]);

        try {
            $user = User::findOrFail($id);

            $image = $this->handleImageUpload($request, $user);

            if ($image) {
                return response()->json(['success' => true, 'message' => 'Imagen actualizada correctamente', 'image' => $image], 200);
            }

            return response()->json(['success' => false, 'message' => 'No se envió ninguna imagen'], 400);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar imagen: ' . $e->getMessage()], 500);
        }
    }


    private function handleImageUpload(Request $request, User $user)
    {
        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists(str_replace('storage/', '', $user->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->image));
            }

            $path = $request->file('image')->store('users', 'public');
            $user->image = 'storage/' . $path;
            $user->save();

            return $user->image;
        }

        return null;
    }

}
