<?php

namespace App\Http\Controllers;

use App\Exports\KeluhanPelangganExport;
use App\Models\KeluhanPelanggan;
use App\Models\KeluhanStatusHistori;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class KeluhanPelangganController extends Controller
{
    public function index(Request $request)
    {
        $keluhan = KeluhanPelanggan::select(
            '*',
            DB::raw("
            IF(CHAR_LENGTH(keluhan) > 50, 
            CONCAT(SUBSTRING(keluhan, 1, 50), '. . .'), 
            keluhan
            ) as keluhan
        "))
        ->orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $search = $request->search;
            $keluhan->where(function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_hp', 'like', "%{$search}%");
            });
        }
        return $keluhan->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|min:3|max:50',
            'email' => 'required|email|max:100',
            'nomor_hp' => 'nullable|numeric',
            // 'status_keluhan' => 'required|in:0,1,2',
            'keluhan' => 'required|min:50|max:255',
        ]);

        try {
            DB::beginTransaction();

            $keluhan = KeluhanPelanggan::create($validated);

            KeluhanStatusHistori::create([
                'keluhan_id' => $keluhan->id,
                'status_keluhan' => '0',
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Data keluhan berhasil disimpan.',
                'data' => $keluhan
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Create Keluhan Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $keluhan = KeluhanPelanggan::with('histori')->findOrFail($id);
        return response()->json($keluhan);
    }

    public function update(Request $request, $id) 
    {
        $validated = $request->validate([
            'nama' => 'required|min:3|max:50',
            'email' => 'required|email|max:100',
            'nomor_hp' => 'nullable|numeric',
            'keluhan' => 'required|min:50|max:255',
        ]);

        try {
            $keluhan = KeluhanPelanggan::findOrFail($id);
            $keluhan->update($validated);

            return response()->json([
                'message' => 'Data berhasil diubah'
            ], 200);
        } catch (Exception $e) {
            Log::error('Update Keluhan Error: ' . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $keluhan = KeluhanPelanggan::findOrFail($id);
            $keluhan->delete();

            return response()->json([
                'message' => 'Data keluhan berhasil dihapus.'
            ], 200);
        } catch (Exception $e) {
            Log::error('Gagal menghapus keluhan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }

    public function exportExcel($format) 
    {
        $allowed = ['xlsx', 'csv'];
        if (!in_array($format, $allowed)) {
            abort(400, 'Format tidak didukung');
        }

        return Excel::download(new KeluhanPelangganExport, "keluhan_pelangan.$format");
    }

    public function exportPdf()
    {
        $data = KeluhanPelanggan::selectRaw("
            id, nama, email, nomor_hp, keluhan, created_at,
            CASE 
                WHEN status_keluhan = 0 THEN 'Received'
                WHEN status_keluhan = 1 THEN 'In Process'
                WHEN status_keluhan = 2 THEN 'Done'
                ELSE '-' 
            END as status_keluhan
        ")
        ->orderBy('created_at', 'desc')
        ->get();
        
        $pdf = Pdf::loadView('exports.keluhan', ['keluhan' => $data]);

        return $pdf->download('keluhan_pelanggan.pdf');
    }

    public function exportTxt()
    {
        $data = KeluhanPelanggan::selectRaw("
            nama, email, nomor_hp, keluhan,
            CASE  
                WHEN status_keluhan = 0 THEN 'Received'
                WHEN status_keluhan = 1 THEN 'In Process'
                WHEN status_keluhan = 2 THEN 'Done'
                ELSE '-' 
            END as status_keluhan,
            created_at
        ")
        ->orderBy('created_at', 'desc')
        ->get();

        $lines = [];
        $lines[] = "Nama\tEmail\tNomor HP\tKeluhan\tStatus\tTanggal";

        foreach ($data as $row) {
            $lines[] = implode("\t", [
                $row->nama,
                $row->email,
                $row->nomor_hp,
                $row->keluhan,
                $row->status_keluhan,
                Carbon::parse($row->created_at)->format('Y-m-d H:i'),
            ]);
        }

        $content = implode("\n", $lines);

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="keluhan_pelanggan.txt"');
    }
}
