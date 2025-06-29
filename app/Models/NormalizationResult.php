<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NormalizationResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'beneficiary_id',
        'usia_normalized',
        'jumlah_anak_normalized',
        'kelayakan_rumah_normalized',
        'pendapatan_perbulan_normalized',
        'normalized_data',
        'min_values',
        'max_values'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
