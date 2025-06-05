<?php

namespace App\Exports;

use App\Models\Penerima;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenerimaExport implements FromCollection, WithHeadings
{
    protected $columns;
    protected $headings;

    public function __construct($columns = null)
    {
        $this->columns = $columns ?? [
            'nama',
            'alamat',
            'no_hp',
            'usia',
            'jumlah_anak',
            'kelayakan_rumah',
            'pendapatan_perbulan',
        ];
        $this->headings = [
            'nama' => 'Nama',
            'alamat' => 'Alamat',
            'no_hp' => 'No HP',
            'usia' => 'Usia',
            'jumlah_anak' => 'Jumlah Anak',
            'kelayakan_rumah' => 'Kelayakan Rumah',
            'pendapatan_perbulan' => 'Pendapatan Perbulan',
        ];
    }

    public function collection()
    {
        return Penerima::select($this->columns)->get();
    }

    public function headings(): array
    {
        return array_map(fn($col) => $this->headings[$col] ?? $col, $this->columns);
    }
} 