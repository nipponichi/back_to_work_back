<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('id');

        if (!ctype_digit(strval($id)) || (int)$id <= 0) {
            return response()->json(['success' => false, 'message' => 'Id format is not correct.', 'data' => ''], 400);
        }

        return $next($request);
    }
}
