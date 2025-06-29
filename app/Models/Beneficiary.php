<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory;
    
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
