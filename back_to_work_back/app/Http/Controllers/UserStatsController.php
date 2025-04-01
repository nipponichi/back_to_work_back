<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdPicture;
use App\Models\UserStat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserStatsController extends Controller
{
    /**
     * Listar todos los comentarios
     */
    public function index()
    {
        try {
            $userstats = UserStat::with(['ad', 'user'])->get();
            return response()->json(['success' => true, 'message' => 'Userstats loaded correctly', 'data' => $userstats], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading userstats: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear un nuevo comentario
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar datos del anuncio
            $validatedData = $request->validate([
                'quality_price' => 'required|integer|max:32',
                'customer_care' => 'required|integer|max:32',
                'timing' => 'required|integer|max:32',
                'review' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:users,id',
                'ad_id' => 'required|integer|exists:ads,id',
            ]);

            // Crear el comentario
            $userStat = UserStat::create($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Userstat saved', 'data'], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving userstat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un comentario especifico
     */
    public function show($id)
    {
        try {
            $userstat = UserStat::with(['ad', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Userstat loaded correctly', 'data' => $userstat], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Userstat not found'], 404);
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
                'quality_price' => 'required|integer|max:32',
                'customer_care' => 'required|integer|max:32',
                'timing' => 'required|integer|max:32',
                'review' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:users,id',
                'ad_id' => 'required|integer|exists:ads,id',
            ]);

            // Actualizar los datos del comentario
            $userstat->update($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Userstat updated correctly', 'data'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating userstat: ' . $e->getMessage()], 500);
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
            // Eliminar el comentario
            $userstat->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Userstat deleted correctly'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting userstat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener un comentario concreto
     */
    public function getUserstat($id)
    {
        try {
            $userstat = Userstat::with(['ad', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Userstat loaded correctly', 'data' => $userstat], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading userstat: ' . $e->getMessage()], 500);
        }
    }
}
