<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormalizationResult extends Model
{
    protected $fillable = [
        'beneficiary_id',
        'usia_normalized',
        'jumlah_anak_normalized',
        'kelayakan_rumah_normalized',
        'pendapatan_perbulan_normalized'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
