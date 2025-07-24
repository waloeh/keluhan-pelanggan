<?php
namespace App\Exports;

use App\Models\KeluhanPelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KeluhanPelangganExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = KeluhanPelanggan::selectRaw("
            nama, email, nomor_hp,
            CASE 
                WHEN status_keluhan = 0 THEN 'Received'
                WHEN status_keluhan = 1 THEN 'In Process'
                WHEN status_keluhan = 2 THEN 'Done'
                ELSE '-' 
            END as status_keluhan,
            keluhan, created_at
        ")
        ->orderBy('created_at', 'desc')
        ->get();
        return $data;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Nomor HP',
            'Status Keluhan',
            'Keluhan',
            'Tanggal Dibuat',
        ];
    }
}
