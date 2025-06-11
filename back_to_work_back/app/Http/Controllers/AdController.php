<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdPicture;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    public function getAuthUser()
    {
        return Auth::guard('api')->user();
    }

    /**
     * Listar todos los anuncios con imágenes/videos.
     */
    public function index()
    {
        try {
            $authUser = $this->getAuthUser();

            if ($authUser->hasRole('admin')) {
                $ads = Ad::with(['pictures:id,ad_id,path,type', 'adOffer', 'user.userStat', 'category'])->get();

                return response()->json(['success' => true, 'message' => 'Anuncios cargados correctamente', 'data' => $ads], 200);
            }

            $ads = Ad::with(['pictures:id,ad_id,path,type', 'adOffer', 'user.userStat'])
                ->where(function ($query) use ($authUser) {
                    $query->where('is_verified', true)
                        ->orWhere(function ($subQuery) use ($authUser) {
                            $subQuery->where('user_id', $authUser->id)
                                    ->where('is_verified', false);
                        });
                })
                ->whereDoesntHave('adOffer', function ($query) {
                    $query->where('is_paid', true);
                })
                ->get();

            return response()->json(['success' => true, 'message' => 'Anuncios cargados correctamente', 'data' => $ads], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false,'message' => 'Error cargando anuncios: ' . $e->getMessage()], 500);
        }
    }

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
                
            $validatedData['is_verified'] = false;
            $ad = Ad::create($validatedData);

            if ($request->hasFile('media')) {
                $mediaFiles = [];
                
                foreach ($request->file('media') as $file) {
                    $path = $file->store('ads_media', 'public');
                    $mimeType = $file->getMimeType();
                    $type = str_starts_with($mimeType, 'image') ? 'image' : 'video';
    
                    $mediaFiles[] = [
                        'path' =>'/storage/'.$path,
                        'type' => $type,
                        'ad_id' => $ad->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                AdPicture::insert($mediaFiles);
            }
    
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Anuncio guardado correctamente', 'data' => $ad->load('pictures')], 201);
    
        } catch (Exception $e) {
            DB::rollBack();
    
            return response()->json(['success' => false, 'message' => 'Error guardando anuncio: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un anuncio específico con imágenes/videos.
     */
    public function show($id)
    {
        try {
            $ad = Ad::with(['pictures:id,ad_id,path,type', 'adOffer', 'user.userStat', 'category'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Anuncio cargado correctamente', 'data' => $ad], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Anuncio no encontrado', 'data' => ''], 404);
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
                'pro_is_done' => 'required|boolean',
                'customer_is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime'
            ]);

            $validatedData['is_verified'] = false;
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
            $ads = Ad::where('user_id', $userId)->with(['pictures:id,ad_id,path,type', 'adOffer', 'user.userStat'])->get();
            return response()->json(['success' => true, 'message' => 'Ads loaded correctly', 'data' => $ads], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading ads: ' . $e->getMessage()], 500);
        }
    }

    public function getAdsWhereIAm()
    {
        try {
            $authUser = $this->getAuthUser();  
            $ads = Ad::getAdsInvolvedByUser($authUser->id);

            return response()->json(['success' => true, 'message' => 'Ads where you are involved (no paid offers from others)', 'data' => $ads], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function markAsDone(Request $request)
    {
        try {
            $authUser = $this->getAuthUser();
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }
            
            $ad = Ad::findOrFail($request->id);

            $hasPaidOffer = $ad->adOffer()->where('is_paid', true)->exists();
            if (!$hasPaidOffer) {
                return response()->json(['success' => false, 'message' => 'El anuncio no tiene ofertas pagadas'], 400);
            }

            if ($authUser->is_pro) {

                $isOfferer = $ad->adOffer()
                    ->where('is_paid', true)
                    ->where('user_id', $authUser->id)
                    ->exists();
                
                if (!$isOfferer) {
                    return response()->json(['success' => false, 'message' => 'No eres el profesional asignado a este trabajo'], 403);
                }

                $ad->pro_is_done = true;
                $message = 'Has marcado este trabajo como completado. Esperando confirmación del cliente.';
            } else {

                if ($ad->user_id != $authUser->id) {
                    return response()->json(['success' => false, 'message' => 'No eres el dueño de este anuncio'], 403);
                }

                if (!$ad->pro_is_done) {
                    return response()->json(['success' => false, 'message' => 'El profesional aún no ha marcado el trabajo como completado'], 400);
                }

                $ad->customer_is_done = true;
                $message = 'Has confirmado la finalización del trabajo.';
            }

            $ad->save();

            return response()->json(['success' => true, 'message' => $message, 'data' => ['pro_is_done' => $ad->pro_is_done, 'customer_is_done' => $ad->customer_is_done], 200]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Anuncio no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el estado', 'error' => $e->getMessage()], 500);
        }
    }
    public function uploadPicture(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048', 'ad_id' => 'required|exists:ads,id']);

        $path = $request->file('image')->store('ads', 'public');

        $picture = AdPicture::create(['ad_id' => $request->ad_id, 'path' => 'storage/' . $path, 'type' => 'image']);

        return response()->json(['success' => true, 'picture' => $picture]);
    }

    public function deletePicture($id)
    {
        $picture = AdPicture::findOrFail($id);
        Storage::disk('public')->delete(str_replace('storage/', '', $picture->path));
        $picture->delete();

        return response()->json(['success' => true]);
    }

    public function verifyAd($id)
    {
        try {
            $authUser = $this->getAuthUser();
            if (!$authUser->hasRole('admin')) {
                return response()->json(['success' => false, 'message' => 'No autorizado', 'data' => ''], 403);
            }

            $ad = Ad::findOrFail($id);
            if($ad->is_verified) {
                $ad->is_verified = false;
                $ad->save();
                return response()->json(['success' => true, 'message' => 'Anuncio bloqueado correctamente', 'data' => $ad], 200);
            } else {
                $ad->is_verified = true;
                $ad->save();
                return response()->json(['success' => true, 'message' => 'Anuncio verificado correctamente', 'data' => $ad], 200);
            }

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al verificar el anuncio'], 500);
        }
    }

}
