<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DecisionResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'cluster',
        'count',
        'notes',
        'user_id',
        'sort_by',
        'sort_direction',
        'limit',
        'result_data'
    ];

    /**
     * Get items for this decision result
     */
    public function items(): HasMany
    {
        return $this->hasMany(DecisionResultItem::class);
    }

    /**
     * Get beneficiaries for this decision result
     */
    public function beneficiaries()
    {
        return $this->hasManyThrough(
            Beneficiary::class,
            DecisionResultItem::class,
            'decision_result_id',
            'id',
            'id',
            'beneficiary_id'
        );
    }
    
    /**
     * Get the user that owns this decision result
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 