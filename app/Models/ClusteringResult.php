<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClusteringResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'beneficiary_id', 'cluster', 'silhouette', 'num_clusters', 'max_iterations', 
        'execution_time', 'cluster_data', 'centroids'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
