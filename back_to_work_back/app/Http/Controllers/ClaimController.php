<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Exception;

class ClaimController extends Controller
{
    public function getAuthUser()
    {
        return Auth::guard('api')->user();
    }
    public function index()
    {
        $authUser = $this->getAuthUser();
 try {
        $claims = '';

        if ($authUser->hasRole('admin')) {
            $claims = Claim::with(['sender', 'receiver', 'ad', 'bid', 'userStat'])->get();
        } else {
            $claims = Claim::with(['sender', 'receiver', 'ad', 'bid', 'userStat'])
            ->where('sender_id', $authUser->id)
            ->get();
        }
        
        return response()->json(['success' => true, 'message'=> 'Reclamaciones cargadas correctamente', 'data' => $claims]);
        
    } catch (Exception $e) {
        return response()->json(['success' => true, 'message'=> 'Error cargando reclamaciones: ' . $e , 'data' => '']);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'ad_id' => 'nullable|exists:ads,id',
            'bid_id' => 'nullable|exists:ads_offers,id',
            'user_stats_id' => 'nullable|exists:user_stats,id',
            'images.*' => 'file|image|mimes:jpeg,png,jpg|max:2048',
            'reason' => 'required|string',
            'admin_notes' => 'nullable|string'
        ]);

        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('claims', 'public');
                $paths[] = 'storage/' . $path;
            }
        }

        $validated['images'] = $paths;
        $validated['status'] = 'pending';

        $claim = Claim::create($validated);

        return response()->json(['success' => true, 'message' => 'Reclamación guardada con éxito', 'data' => $claim], 201);
    }


    public function show($id)
    {
        $claim = Claim::with(['sender', 'receiver', 'ad', 'bid', 'userStat'])->findOrFail($id);
        return response()->json(['success' => true, 'message' => 'Reclamación cargada correctamente', 'data' => $claim]);
    }

    public function update(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);

        $validated = $request->validate([
            'status' => ['sometimes', Rule::in(['pending', 'in_review', 'resolved', 'rejected', 'escalated', 'waiting_user'])],
            'admin_notes' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string'
        ]);

        $claim->update($validated);

        return response()->json([
            'success' => true,
            'data' => $claim
        ]);
    }

    public function destroy($id)
    {
        $claim = Claim::findOrFail($id);
        $claim->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reclamación eliminada correctamente.'
        ]);
    }
}
