<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdPicture;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdController extends Controller
{
    /**
     * Listar todos los anuncios con imágenes/videos.
     */
public function index()
{
    try {
        $ads = Ad::with(['pictures:id,ad_id,path,type', 'adOffer'])
            ->whereDoesntHave('adOffer', function($query) {
                $query->where('is_paid', true);
            })
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Ads loaded correctly (excluding those with paid offers)',
            'data' => $ads
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading ads: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Crear un nuevo anuncio con imágenes/videos.
     */

     /*
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar datos del anuncio
            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'category_id' => 'required|integer|exists:ads_categories,id',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime'
            ]);

            // Crear el anuncio
            $ad = Ad::create($validatedData);

            // Guardar archivos multimedia si existen
            if ($request->hasFile('media')) {
                $mediaFiles = [];
                foreach ($request->file('media') as $file) {
                    $path = $file->store('ads_media', 'public');
                    $type = str_contains($file->getMimeType(), 'image') ? 'image' : 'video';

                    $mediaFiles[] = [
                        'path' => $path,
                        'type' => $type,
                        'ad_id' => $ad->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                AdPicture::insert($mediaFiles);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ad and media saved', 'data' => $ad->load('pictures')], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving ad: ' . $e->getMessage()], 500);
        }
    }
    */

    public function store(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:500',
                'category_id' => 'required|integer|exists:ads_categories,id',
                'location' => 'required|string|max:50',
                'user_id' => 'required|integer|exists:users,id',
                'due_date' => 'nullable|date',
                'media' => 'sometimes|array',
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,image/jpg,video/mp4',
            ]);
                

            $ad = Ad::create($validatedData);

            if ($request->hasFile('media')) {
                $mediaFiles = [];
                
                foreach ($request->file('media') as $file) {
                    $path = $file->store('ads_media', 'public');
                    $mimeType = $file->getMimeType();
                    $type = str_starts_with($mimeType, 'image') ? 'image' : 'video';
    
                    $mediaFiles[] = [
                        'path' => $path,
                        'type' => $type,
                        'ad_id' => $ad->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                AdPicture::insert($mediaFiles);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Ad and media saved successfully',
                'data' => $ad->load('pictures')
            ], 201);
    
        } catch (Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Error saving ad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un anuncio específico con imágenes/videos.
     */
    public function show($id)
    {
        try {
            $ad = Ad::with('pictures', 'user', 'adOffer')->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Ad loaded correctly', 'data' => $ad], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ad not found'], 404);
        }
    }

    /**
     * Actualizar un anuncio y añadir nuevas imágenes/videos sin borrar las existentes.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $ad = Ad::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'category_id' => 'required|integer|exists:ads_categories,id',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime'
            ]);


            $ad->update($validatedData);

            if ($request->hasFile('media')) {
                $mediaFiles = [];
                foreach ($request->file('media') as $file) {
                    $path = $file->store('ads_media', 'public');
                    $type = str_contains($file->getMimeType(), 'image') ? 'image' : 'video';

                    $mediaFiles[] = [
                        'path' => $path,
                        'type' => $type,
                        'ad_id' => $ad->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                AdPicture::insert($mediaFiles);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ad updated correctly', 'data' => $ad->load('pictures')], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating ad: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un anuncio y sus imágenes/videos asociados.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ad = Ad::findOrFail($id);

            foreach ($ad->pictures as $picture) {
                Storage::disk('public')->delete($picture->path);
                $picture->delete();
            }

            $ad->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ad deleted correctly'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting ad: ' . $e->getMessage()], 500);
        }
    }

    public function getAdsByUserId($userId)
    {
        try {
            $ads = Ad::where('user_id', $userId)->with(['pictures:id,ad_id,path,type', 'adOffer'])->get();
            return response()->json(['success' => true, 'message' => 'Ads loaded correctly', 'data' => $ads], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading ads: ' . $e->getMessage()], 500);
        }
    }

    public function getAdsWhereIAm()
    {
        try {
            $user = Auth::guard('api')->user();    
            $ads = Ad::getAdsInvolvedByUser($user->id);

            return response()->json(['success' => true, 'message' => 'Ads where you are involved (no paid offers from others)', 'data' => $ads], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

public function markAsDone(Request $request)
{
    try {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }
        
        $ad = Ad::findOrFail($request->id);

        // Verificar que el anuncio tiene una oferta pagada usando adOffer
        $hasPaidOffer = $ad->adOffer()->where('is_paid', true)->exists();
        if (!$hasPaidOffer) {
            return response()->json([   
                'success' => false,
                'message' => 'El anuncio no tiene ofertas pagadas'
            ], 400);
        }

        // Verificar relación usuario-anuncio
        if ($user->is_pro) {
            // Para profesionales: verificar que es el profesional de la oferta pagada
            $isOfferer = $ad->adOffer()
                ->where('is_paid', true)
                ->where('user_id', $user->id)
                ->exists();
            
            if (!$isOfferer) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eres el profesional asignado a este trabajo'
                ], 403);
            }

            // Marcar como completado por el profesional
            $ad->pro_is_done = true;
            $message = 'Has marcado este trabajo como completado. Esperando confirmación del cliente.';
        } else {
            // Para clientes: verificar que es el dueño del anuncio
            if ($ad->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eres el dueño de este anuncio'
                ], 403);
            }

            // Verificar que el profesional ya marcó como completado
            if (!$ad->pro_is_done) {
                return response()->json([
                    'success' => false,
                    'message' => 'El profesional aún no ha marcado el trabajo como completado'
                ], 400);
            }

            // Marcar como completado por el cliente
            $ad->customer_is_done = true;
            $message = 'Has confirmado la finalización del trabajo.';
        }

        $ad->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'pro_is_done' => (bool)$ad->pro_is_done,
                'customer_is_done' => (bool)$ad->customer_is_done,
                'is_completed' => $ad->pro_is_done && $ad->customer_is_done
            ]
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Anuncio no encontrado'
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el estado',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
