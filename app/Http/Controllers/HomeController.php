<?php

namespace App\Http\Controllers;

use App\Models\KeluhanPelanggan;
use App\Models\KeluhanStatusHistori;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function summary() 
    {
        try {
            //chart pie
            $totalPie = KeluhanPelanggan::count();
            $pie = KeluhanPelanggan::select(
                'status_keluhan',
                DB::raw("
                    CASE 
                        WHEN status_keluhan = 0 THEN 'Received'
                        WHEN status_keluhan = 1 THEN 'In Process'
                        WHEN status_keluhan = 2 THEN 'Done'
                        ELSE 'Unknown'
                    END as label
                "),
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy('status_keluhan')
            ->orderBy('status_keluhan', 'asc')
            ->get();

            //bar chart
            $labelBar = [];
            $receivedVal = [];
            $proccessVal = [];
            $doneVal = [];

            for ($i = 5; $i >= 0; $i--) {
                $carbon = Carbon::now()->subMonth($i);
                $bulan = $carbon->format('m');
                $tahun = $carbon->format('Y');

                $labelBar[] = $carbon->format('M');
                for ($j = 0; $j < 3; $j++) {
                    $keluhan = KeluhanStatusHistori::select(DB::raw('COUNT(*) as jumlah'))->where('status_keluhan', $j)
                        ->where(DB::raw('YEAR(updated_at)'), $tahun)
                        ->where(DB::raw('MONTH(updated_at)'), $bulan)
                        ->first();
                    if ($j == 0) {
                        if ($keluhan) {
                            $receivedVal[] = $keluhan->jumlah;
                        } else {
                            $receivedVal[] = 0;
                        }
                    } else if ($j == 1) {
                        if ($keluhan) {
                            $proccessVal[] = $keluhan->jumlah;
                        } else {
                            $proccessVal[] = 0;
                        }
                    } else if ($j == 2) {
                        if ($keluhan) {
                            $doneVal[] = $keluhan->jumlah;
                        } else {
                            $doneVal[] = 0;
                        }
                    }
                }
            }

            $chartBar = [
                [
                    'label' => 'Received',
                    'data' => $receivedVal,
                    'backgroundColor' => '#f5ce0a'
                ],
                [
                    'label' => 'In Proccess',
                    'data' => $proccessVal,
                    'backgroundColor' => '#34c9eb'
                ],
                [
                    'label' => 'Done',
                    'data' => $doneVal,
                    'backgroundColor' => '#229620'
                ]
            ];

            //table
            $table = KeluhanPelanggan::select('nama', 'email', 'created_at')->orderBy('created_at', 'asc')->limit(10)->get();
            $table = $table->map(function ($item) {
                $now = Carbon::now();
                $createDate = Carbon::parse($item->created_at);
                $item->selisih_hari = $createDate->diffInDays($now);
                return $item;
            });

            return response()->json([
                'pie' => $pie,
                'total_pie' => $totalPie,
                'table' => $table,
                'bar' => [
                    'label' => $labelBar,
                    'data' => $chartBar
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        } 
    }
}
