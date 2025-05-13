<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class VerificationController extends Controller
{
public function verify(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'hash' => 'required|string', 'signature' => 'required|string']);

        $user = User::findOrFail($request->id);

        if (! hash_equals((string) $request->hash, sha1($user->email))) {
            return response()->json(['success' => false, 'message' => 'El hash de verificación no coincide'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['success' => true, 'message' => 'El email ya estaba verificado'], 422);
        }

        $user->markEmailAsVerified();

        return response()->json(['success' => true, 'message' => 'Email verificado correctamente'], 200);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['success' => false, 'message' => 'El email ya está verificado'], 422);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['success' => true, 'message' => 'Email de verificación reenviado'], 200);
    }
}
