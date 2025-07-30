<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class BeneficiaryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];
    private $failures = [];
    private $rowNumber = 1; // Start from 1 since we have header

    public function model(array $row)
    {
        $this->rowNumber++;
        
        // Skip jika semua kolom kosong
        if (empty(array_filter($row))) {
            return null;
        }

        // Format data sebelum membuat model
        $formattedData = $this->formatRowData($row);

        return new Beneficiary($formattedData);
    }

    /**
     * Format data dari Excel sebelum validasi dan pembuatan model
     */
    private function formatRowData(array $row)
    {
        return [
            'nik' => $this->convertToString($row['nik'] ?? ''),
            'nama' => trim($row['nama'] ?? ''),
            'alamat' => trim($row['alamat'] ?? ''),
            'no_hp' => $this->convertToString($row['no_hp'] ?? ''),
            'usia' => $this->convertToInteger($row['usia'] ?? 0),
            'jumlah_anak' => $this->convertToInteger($row['jumlah_anak'] ?? 0),
            'kelayakan_rumah' => $this->convertToNumeric($row['kelayakan_rumah'] ?? 0),
            'pendapatan_perbulan' => $this->convertToNumeric($row['pendapatan_perbulan'] ?? 0),
        ];
    }

    /**
     * Konversi value ke string dengan handling khusus untuk NIK
     */
    private function convertToString($value)
    {
        // Jika null atau kosong, return string kosong
        if (is_null($value) || $value === '') {
            return '';
        }
        
        // Jika numeric (int, float, atau string numeric), konversi ke string
        if (is_numeric($value)) {
            // Untuk angka yang sangat besar (seperti NIK), gunakan number_format tanpa separator
            if (is_float($value)) {
                return number_format($value, 0, '', '');
            }
            return (string) $value;
        }
        
        // Jika sudah string, trim whitespace
        if (is_string($value)) {
            return trim($value);
        }
        
        // Untuk tipe data lain, konversi ke string
        return (string) $value;
    }

    /**
     * Konversi value ke integer
     */
    private function convertToInteger($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        if (is_numeric($value)) {
            return (int) $value;
        }
        
        return 0;
    }

    /**
     * Konversi value ke numeric
     */
    private function convertToNumeric($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        return 0;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'unique:beneficiaries,nik'],
            'nama' => ['required'],
            'alamat' => ['required'],
            'no_hp' => ['required'],
            'usia' => ['required', 'numeric', 'min:1', 'max:120'],
            'jumlah_anak' => ['required', 'numeric', 'min:0', 'max:20'],
            'kelayakan_rumah' => ['required', 'numeric', 'min:0', 'max:5'],
            'pendapatan_perbulan' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nik.required' => 'NIK tidak boleh kosong',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama.required' => 'Nama tidak boleh kosong',
            'alamat.required' => 'Alamat tidak boleh kosong',
            'no_hp.required' => 'No HP tidak boleh kosong',
            'usia.required' => 'Usia tidak boleh kosong',
            'usia.numeric' => 'Usia harus berupa angka',
            'usia.min' => 'Usia minimal 1 tahun',
            'usia.max' => 'Usia maksimal 120 tahun',
            'jumlah_anak.required' => 'Jumlah anak tidak boleh kosong',
            'jumlah_anak.numeric' => 'Jumlah anak harus berupa angka',
            'jumlah_anak.min' => 'Jumlah anak minimal 0',
            'jumlah_anak.max' => 'Jumlah anak maksimal 20',
            'kelayakan_rumah.required' => 'Kelayakan rumah tidak boleh kosong',
            'kelayakan_rumah.numeric' => 'Kelayakan rumah harus berupa angka',
            'kelayakan_rumah.min' => 'Kelayakan rumah minimal 0 (0=tidak punya rumah/ngontrak, 1-5=tingkat kelayakan)',
            'kelayakan_rumah.max' => 'Kelayakan rumah maksimal 5',
            'pendapatan_perbulan.required' => 'Pendapatan per bulan tidak boleh kosong',
            'pendapatan_perbulan.numeric' => 'Pendapatan per bulan harus berupa angka',
            'pendapatan_perbulan.min' => 'Pendapatan per bulan tidak boleh negatif',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = [
            'row' => $this->rowNumber,
            'error' => $e->getMessage()
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ];
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function hasErrors()
    {
        return !empty($this->errors) || !empty($this->failures);
    }
} 