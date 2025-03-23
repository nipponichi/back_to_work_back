<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Add;
use App\Models\AddPicture;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AddController extends Controller
{
    /**
     * Listar todos los anuncios con imágenes/videos.
     */
    public function index()
    {
        try {
            $adds = Add::with(['pictures:id,add_id,path,type'])->get();
            return response()->json(['success' => true, 'message' => 'Adds loaded correctly', 'data' => $adds], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading adds: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear un nuevo anuncio con imágenes/videos.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar datos del anuncio
            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'category_id' => 'required|integer|exists:adds_categories,id',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime'
            ]);

            // Crear el anuncio
            $add = Add::create($validatedData);

            // Guardar archivos multimedia si existen
            if ($request->hasFile('media')) {
                $mediaFiles = [];
                foreach ($request->file('media') as $file) {
                    $path = $file->store('adds_media', 'public');
                    $type = str_contains($file->getMimeType(), 'image') ? 'image' : 'video';

                    $mediaFiles[] = [
                        'path' => $path,
                        'type' => $type,
                        'add_id' => $add->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                AddPicture::insert($mediaFiles);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add and media saved', 'data' => $add->load('pictures')], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un anuncio específico con imágenes/videos.
     */
    public function show($id)
    {
        try {
            $add = Add::with('pictures')->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Add loaded correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Add not found'], 404);
        }
    }

    /**
     * Actualizar un anuncio y añadir nuevas imágenes/videos sin borrar las existentes.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $add = Add::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'category_id' => 'required|integer|exists:adds_categories,id',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|max:20480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime'
            ]);

            // Actualizar los datos del anuncio sin tocar las imágenes/videos
            $add->update($validatedData);

            // Si hay nuevos archivos, los agregamos sin eliminar los existentes
            if ($request->hasFile('media')) {
                $mediaFiles = [];
                foreach ($request->file('media') as $file) {
                    $path = $file->store('adds_media', 'public');
                    $type = str_contains($file->getMimeType(), 'image') ? 'image' : 'video';

                    $mediaFiles[] = [
                        'path' => $path,
                        'type' => $type,
                        'add_id' => $add->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                AddPicture::insert($mediaFiles);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add updated correctly', 'data' => $add->load('pictures')], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un anuncio y sus imágenes/videos asociados.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $add = Add::findOrFail($id);

            // Eliminar archivos físicos antes de borrar los registros
            foreach ($add->pictures as $picture) {
                Storage::disk('public')->delete($picture->path);
                $picture->delete();
            }

            // Eliminar el anuncio
            $add->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add deleted correctly'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener un anuncio con todas sus relaciones.
     */
    public function getAdd($id)
    {
        try {
            $add = Add::with(['user', 'pictures', 'adOffer', 'adChat'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Add loaded correctly', 'data' => $add], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading add: ' . $e->getMessage()], 500);
        }
    }
}
