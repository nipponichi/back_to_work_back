<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddChat;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Routing\Controller;

class AddChatController extends Controller
{
    public function __construct() {
        $this->middleware(['can: read chats'])->only('index', 'show');
        $this->middleware(['can: delete chats'])->only('destroy');
        $this->middleware(['can: delete full chats'])->only('destroyMessage');
        $this->middleware(['can: update chats'])->only('update');
        $this->middleware(['can: create chats'])->only('create');
    }
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
            $validatedData = $request->validate([
                'message' => 'required|string|max:1000',
                'is_read' => 'required|boolean',
                'add_id' => 'required|integer|exists:adds,id',
                'sender_id' => 'required|integer|exists:users,id',
                'receiver_id' => 'required|integer|exists:users,id',
            ]);

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
    public function show($id)
    {
        try {
            $messages = AddChat::where('add_id', $id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json(['success' => true, 'message' => 'Messages loaded correctly', 'data' => $messages], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading messages: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            $message = AddChat::findOrFail($id);
            $message->delete();
            return response()->json(['success' => true, 'message' => 'Message deleted correctly', 'data' => ''], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting message: ' . $e->getMessage()], 500);
        }
    }

    public function destroyChat($add_Id) {
        try {
            $chat = AddChat::findOrFail($add_Id, 'add_id');
            $chat->delete();
            return response()->json(['success' => true, 'message' => 'Chat deleted correctly', 'data' => ''], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting chat: ' . $e->getMessage()], 500);
        }
    }
}
