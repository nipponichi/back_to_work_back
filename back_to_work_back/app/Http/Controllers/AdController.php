<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdPicture;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdController extends Controller
{
    /**
     * Listar todos los anuncios con imágenes/videos.
     */
    public function index()
    {
        try {
            $adds = Ad::with(['pictures:id,ad_id,path,type'])->get();
            return response()->json(['success' => true, 'message' => 'Ads loaded correctly', 'data' => $adds], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading ads: ' . $e->getMessage()], 500);
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
            // Validar datos del anuncio
            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'required|string|max:500', // Ajustado a 500 como en el frontend
                'category_id' => 'required|integer|exists:ads_categories,id',
                'location' => 'required|string|max:50', // Ajustado a 50 como en el frontend
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media' => 'required|array', // Validar que es un array
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,image/jpg,video/mp4', // Validar cada archivo
            ]);
    
            // Crear el anuncio
            $ad = Ad::create($validatedData);
    
            // Procesar los archivos multimedia
            if ($request->hasFile('media')) {
                $mediaFiles = [];
    
                foreach ($request->file('media') as $file) {
                    // Guardar el archivo en el storage
                    $path = $file->store('ads_media', 'public');
                    $mimeType = $file->getMimeType();
    
                    // Determinar el tipo de medio (imagen o video)
                    $type = str_starts_with($mimeType, 'image') ? 'image' : 'video';
    
                    $mediaFiles[] = [
                        'path' => $path,
                        'type' => $type,
                        'ad_id' => $ad->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
    
                // Insertar todos los medios en la base de datos
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
            $ad = Ad::with('pictures')->findOrFail($id);
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

            // Actualizar los datos del anuncio sin tocar las imágenes/videos
            $ad->update($validatedData);

            // Si hay nuevos archivos, los agregamos sin eliminar los existentes
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

            // Eliminar archivos físicos antes de borrar los registros
            foreach ($ad->pictures as $picture) {
                Storage::disk('public')->delete($picture->path);
                $picture->delete();
            }

            // Eliminar el anuncio
            $ad->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ad deleted correctly'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting ad: ' . $e->getMessage()], 500);
        }
    }

}
