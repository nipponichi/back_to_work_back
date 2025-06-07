<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserStat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserStatsController extends Controller
{
    /**
     * Listar todos los comentarios
     */
    public function index()
    {
        try {
            $userstats = UserStat::with(['ad', 'user'])->get();
            return response()->json(['success' => true, 'message' => 'Valoraciones cargadas correctamente', 'data' => $userstats], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error cargando valoraciones: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear un nuevo comentario
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validatedData = $request->validate([
                'rating' => 'required|integer|max:10',
                'review' => 'required|string|max:255',
                'ad_id' => 'required|integer|exists:ads,id',
                'receiver_id' => 'required|integer|exists:users,id',
                'sender_id' => 'required|integer|exists:users,id',
            ]);

            $userstat = UserStat::create([
                'sender_id' => $validatedData['sender_id'],
                'receiver_id' => $validatedData['receiver_id'],
                'ad_id' => $validatedData['ad_id'],
                'rating' => $validatedData['rating'],
                'review' => $validatedData['review'],
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Valoración guardada con éxito', 'data' => $userstat], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar la valoración: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un comentario especifico
     */
    public function show($id)
    {
        try {
            $userstat = UserStat::with(['ad', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Valoración cargada con éxito', 'data' => $userstat], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Valoración no encontrada: ' . $e->getMessage() ], 404);
        }
    }

    /**
     * Actualizar un comentario
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $userstat = UserStat::findOrFail($id);

            $validatedData = $request->validate([
                'rating' => 'required|integer|max:32',
                'review' => 'required|string|max:255',
                'ad_id' => 'required|integer|exists:ads,id',
            ]);

            $userstat->update($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Valoración actualizada correctamente', 'data' => $userstat], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error actualizando valoración: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un comentario
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $userstat = Userstat::findOrFail($id);
            $userstat->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Valoración eliminada correctamente', 'data' => $userstat], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error eliminando valoración: ' . $e->getMessage()], 500);
        }
    }
}
