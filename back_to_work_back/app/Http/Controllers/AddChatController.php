<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddChat;
use Illuminate\Support\Facades\DB;
use Exception;

class AddChatController extends Controller
{
    /**
     * Obtener todos los mensajes.
     */
    public function index()
    {
        try {
            $chats = AddChat::with(['addChat', 'sender', 'receiver'])->get();
            return response()->json(['success' => true, 'message' => 'Chats loaded correctly', 'data' => $chats], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading chats: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Guardar un nuevo mensaje en el chat.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar los datos del mensaje
            $validatedData = $request->validate([
                'message' => 'required|string|max:1000',
                'is_read' => 'required|boolean',
                'add_id' => 'required|integer|exists:adds,id',
                'sender_id' => 'required|integer|exists:users,id',
                'receiver_id' => 'required|integer|exists:users,id',
            ]);

            // Crear el mensaje en el chat
            $chat = AddChat::create($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Message sent successfully', 'data' => $chat], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener mensajes por anuncio (add_id).
     */
    public function getMessagesByAd($add_id)
    {
        try {
            $messages = AddChat::where('add_id', $add_id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json(['success' => true, 'message' => 'Messages loaded correctly', 'data' => $messages], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading messages: ' . $e->getMessage()], 500);
        }
    }
}
