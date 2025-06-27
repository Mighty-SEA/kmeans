<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionResultItem extends Model
{
    protected $fillable = [
        'decision_result_id',
        'beneficiary_id'
    ];

    /**
     * Get the decision result that owns this item
     */
    public function decisionResult(): BelongsTo
    {
        return $this->belongsTo(DecisionResult::class);
    }

    /**
     * Get the beneficiary for this item
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
} 