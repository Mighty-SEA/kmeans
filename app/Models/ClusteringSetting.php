<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClusteringSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'num_clusters',
        'max_iterations',
        'is_default',
        'attributes',
        'normalization'
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
        'attributes' => 'array',
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
                'name' => 'Default Setting',
                'num_clusters' => 3,
                'max_iterations' => 100,
                'is_default' => true,
                'normalization' => 'robust',
                'attributes' => [
                    'income' => true,
                    'dependents' => true,
                    'house_status' => true,
                    'house_condition' => true,
                ]
            ]
        );
    }
}
