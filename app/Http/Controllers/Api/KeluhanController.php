<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeluhanPelanggan;
use App\Models\KeluhanStatusHistori;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeluhanController extends Controller
{
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_keluhan' => 'required|in:0,1,2',
        ]);

        try {
            DB::beginTransaction();
            $keluhan = KeluhanPelanggan::findOrFail($id);

            if ($keluhan->status_keluhan == $request->status_keluhan) {
                return response()->json([
                    'message' => 'Status keluhan sudah dalam keadaan tersebut.',
                ], 400);
            }

            $keluhan->update([
                'status_keluhan' => $request->status_keluhan,
            ]);

            KeluhanStatusHistori::create([
                'keluhan_id' => $id,
                'status_keluhan' => $request->status_keluhan,
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Status keluhan berhasil diperbarui.',
                'data' => $keluhan,
            ]);
        } catch (Exception $e) {
            Log::error('Update status keluhan error: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteStatus(Request $request, $id) 
    {
        $request->validate([
            'status_keluhan' => 'required|in:0,1,2',
        ]);

        $histori = KeluhanStatusHistori::where('keluhan_id', $id)
            ->where('status_keluhan', $request->status_keluhan)
            ->first();

        if (!$histori) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $histori->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus'
        ], 200);
    }

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}
