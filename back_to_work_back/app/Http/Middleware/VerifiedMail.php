<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Exception;

class VerifiedMail
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $email = $request->route('email') ?? $request->input('email');
            $user = User::where('email', $email)->first();
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['success' => false, 'message' => 'Email not verified'], 403);
            }
            return $next($request);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}