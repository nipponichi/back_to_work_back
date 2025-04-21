<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdOffer;
use Illuminate\Support\Facades\DB;
use Exception;

class AdOfferController extends Controller
{
    /**
     * Mostrar todas las ofertas.
     */
    public function index()
    {
        try {
            $offers = AdOffer::with(['ad', 'user'])->get();
            return response()->json(['success' => true, 'message' => 'Offers loaded correctly', 'data' => $offers], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading offers: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Guardar una nueva oferta.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar los datos de la oferta
            $validatedData = $request->validate([
                'bid' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:255',
                'is_valid' => 'required|boolean',
                'ad_id' => 'required|integer|exists:ads,id',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            // Crear la oferta
            $offer = AdOffer::create($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Offer created successfully', 'data' => $offer], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error creating offer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una oferta específica.
     */
    public function show($id)
    {
        try {
            $offer = AdOffer::with(['ad', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Offer loaded correctly', 'data' => $offer], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Offer not found'], 404);
        }
    }

    /**
     * Actualizar una oferta existente.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $offer = AdOffer::findOrFail($id);

            $validatedData = $request->validate([
                'bid' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:255',
                'is_valid' => 'required|boolean',
                'ad_id' => 'required|integer|exists:ads,id',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $offer->update($validatedData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Offer updated successfully', 'data' => $offer], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating offer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una oferta.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $offer = AdOffer::findOrFail($id);
            $offer->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Offer deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting offer: ' . $e->getMessage()], 500);
        }
    }

    /**
 * Obtener todas las ofertas por ID de anuncio.
 */
public function getOffersByAdId($adId)
{
    try {
        $offers = AdOffer::where('ad_id', $adId)
            ->with(['ad', 'user']) // Incluye relaciones completas
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Offers loaded correctly',
            'data' => $offers
        ], 200);
    } catch (\Throwable $e) {


        return response()->json([
            'success' => false,
            'message' => 'Error loading offers: ' . $e->getMessage()
        ], 500);
    }
}

}
