<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClusteringResult extends Model
{
    protected $fillable = [
        'beneficiary_id', 'cluster', 'silhouette'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
