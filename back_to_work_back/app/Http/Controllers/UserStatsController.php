<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdPicture;
use App\Models\UserStat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Workbench\App\Models\User;

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
        $validatedData = $request->validate([
            'customer_care' => 'required|integer|max:10',
            'review' => 'required|string|max:255',
            'ad_id' => 'required|integer|exists:ads,id',
            // Eliminamos user_id de la validación
        ]);

        $user = $request->user(); 

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        $userStat = UserStat::create([
            'user_id' => $user->id,
            'ad_id' => $validatedData['ad_id'],
            'customer_care' => $validatedData['customer_care'],
            'review' => $validatedData['review'],
        ]);

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Valoración guardada con éxito']);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar la valoración: ' . $e->getMessage()
        ], 500);
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
                'customer_care' => 'required|integer|max:32',
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

    public function getStatsByUser()
{
    //Log::info('getStatsByUser llamado');

    //$user = $request->user(); 
    $user = Auth::guard('api')->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'No autenticado',
        ], 401);
    }

    try {
        $userStats = UserStat::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $userStats,
        ]);
    } catch (Exception $e) {
        Log::error('Error al obtener valoraciones: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener las valoraciones del usuario',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function countTotalRatingsByUser($userId)
{
    $count = UserStat::where('user_id', $userId)->count();

    return response()->json([
        'success' => true,
        'count' => $count
    ]);
}

public function listRatingsByUser($userId)
{
    $ratings = UserStat::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

    return response()->json([
        'success' => true,
        'data' => $ratings
    ]);
}
    
}
