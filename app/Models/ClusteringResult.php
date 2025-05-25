<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClusteringResult extends Model
{
    protected $fillable = [
        'penerima_id', 'cluster'
    ];

    public function penerima()
    {
        return $this->belongsTo(Penerima::class);
    }
}
