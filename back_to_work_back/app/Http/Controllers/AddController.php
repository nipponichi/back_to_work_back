<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Add;
use App\Models\AddPicture;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class AddController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:read adds'])->only('index', 'show');
        $this->middleware(['can:create adds'])->only('store');
        $this->middleware(['can:update adds'])->only('update');
        $this->middleware(['can:delete adds'])->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $adds = Add::with('pictures')->get();
            return response()->json(['success' => true, 'message' => 'Adds loaded correctly', 'data' => $adds], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading adds: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created Add with media files.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar datos del anuncio
            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|mimes:jpg,jpeg,png,mp4,mov|max:20480' // Máx. 20MB por archivo
            ]);

            // Crear el anuncio
            $add = Add::create($validatedData);

            // Guardar archivos multimedia si existen
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('adds_media', 'public'); // Guarda en storage/app/public/adds_media
                    $type = str_contains($file->getMimeType(), 'image') ? 'image' : 'video';

                    AddPicture::create([
                        'path' => $path,
                        'type' => $type,
                        'add_id' => $add->id,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add and media saved', 'data' => $add->load('pictures')], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource with media.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $add = Add::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'description' => 'nullable|string|max:255',
                'due_date' => 'nullable|date|after_or_equal:today',
                'location' => 'required|string|max:64',
                'is_done' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'media.*' => 'file|mimes:jpg,jpeg,png,mp4,mov|max:20480' // Permitir nuevas imágenes/videos opcionales
            ]);

            // Actualizar los datos principales del Add
            $add->update($validatedData);

            // Si hay nuevos archivos, eliminamos los anteriores y agregamos los nuevos
            if ($request->hasFile('media')) {
                
                foreach ($add->pictures as $picture) {
                    Storage::disk('public')->delete($picture->path);
                    $picture->delete();
                }

                foreach ($request->file('media') as $file) {
                    $path = $file->store('adds_media', 'public');
                    $type = str_contains($file->getMimeType(), 'image') ? 'image' : 'video';

                    AddPicture::create([
                        'path' => $path,
                        'type' => $type,
                        'add_id' => $add->id,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add updated correctly', 'data' => $add->load('pictures')], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating add: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $add = Add::findOrFail($id);

            foreach ($add->pictures as $picture) {
                Storage::disk('public')->delete($picture->path);
                $picture->delete();
            }

            $add->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Add deleted correctly'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting add: ' . $e->getMessage()], 500);
        }
    }

}
