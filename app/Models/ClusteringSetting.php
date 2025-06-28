<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClusteringSetting extends Model
{
    protected $fillable = [
        'num_clusters',
        'normalization'
    ];
    
    /**
     * Mendapatkan pengaturan clustering saat ini
     * 
     * @return \App\Models\ClusteringSetting
     */
    public static function getCurrentSettings()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'num_clusters' => 3,
                'normalization' => 'robust'
            ]
        );
    }
}
