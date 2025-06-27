<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionResult extends Model
{
    protected $fillable = [
        'title',
        'description',
        'cluster',
        'count',
        'notes'
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
} 