<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $table = 'beneficiaries';
    protected $guarded = ['id'];
    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'usia',
        'jumlah_anak',
        'kelayakan_rumah',
        'pendapatan_perbulan',
        'nik',
    ];

    public function clusteringResult()
    {
        return $this->hasOne(ClusteringResult::class);
    }

    public function normalizationResult()
    {
        return $this->hasOne(NormalizationResult::class);
    }
}
