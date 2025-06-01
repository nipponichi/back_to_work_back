<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Str;
use Illuminate\Support\Facades\DB;
use Exception;

class MailController extends Controller
{
    public function welcomeMessage(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'message' => 'required|string'
        ]);

        try {
            $data = [
                'email' => $request->email,
                'name' => $request->nombre,
                'message' => $request->mensaje               
            ];

            Mail::to($data['email'])->send(new SendMail(
                ['nombre' => $data['nombre'], 'email' => $data['email']],
                SendMail::TEMPLATE_WELCOME
            ));
            return response()->json(['success' => true, 'message' => 'Correo enviado correctamente WELCOME']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar el correo', 'error' => $e->getMessage()], 500);
        }
    }

    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $user = User::where('email', $request->email)->firstOrFail();

            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => bcrypt($token), 
                'created_at' => now()]
            );
            
            $frontendUrl = config('app.frontend_url')."reset-password?token=$token&email=".urlencode($user->email);

            Mail::to($user->email)->send(new SendMail(
                [
                    'name' => $user->name,
                    'reset_link' => $frontendUrl,
                    'message' => 'Haga clic para restablecer su contraseña'
                ],
                SendMail::TEMPLATE_RESET_PASSWORD,
                'Restablecer contraseña'
            ));

            return response()->json(['success' => true, 'message' => 'Enlace enviado']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al procesar'], 500);
        }
    }

    public function newBid(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nombre' => 'required|string',
            'mensaje' => 'required|string'
        ]);

        try {
            $data = [
                'email' => $request->email,
                'nombre' => $request->nombre,
                'mensaje' => $request->mensaje               
            ];

            Mail::to($data['email'])->send(new SendMail(
                ['nombre' => $data['nombre'], 'mensaje' => $data['mensaje']],
                SendMail::TEMPLATE_NOTIFICATION,
                'Notificación de nueva puja'
            ));

            return response()->json(['success' => true, 'message' => 'Correo enviado correctamente BID']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar el correo', 'error' => $e->getMessage()], 500);
        }
    }
}