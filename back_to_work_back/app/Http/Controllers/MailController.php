<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Exception;

class MailController extends Controller
{
    public function welcomeMessage(Request $request)
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
            ['nombre' => $data['nombre'], 'email' => $data['email']],
            SendMail::TEMPLATE_WELCOME
        ));
            return response()->json(['success' => true, 'message' => 'Correo enviado correctamente WELCOME']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar el correo', 'error' => $e->getMessage()], 500);
        }
    }

    public function passwordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nombre' => 'required|string',
            'mensaje' => 'required|string'
        ]);
            // Token prueba
            $token = '1234';
        try {
            $data = [
                'email' => $request->email,
                'nombre' => $request->nombre,
                'mensaje' => $request->mensaje               
            ];

            Mail::to($data['email'])->send(new SendMail(
                ['nombre' => $data['nombre'], 'mensaje' => $data['mensaje']],
                SendMail::TEMPLATE_RESET_PASSWORD,
                'Restablecimiento de contraseña'
            ));

            return response()->json(['success' => true, 'message' => 'Correo enviado correctamente RESET']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar el correo', 'error' => $e->getMessage()], 500);
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