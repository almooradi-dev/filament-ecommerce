<?php

namespace Almooradi\FilamentEcommerce\Models\Variation;

use Almooradi\FilamentEcommerce\Traits\HasShowIn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shop_variations';

    protected $guarded = [];

    /**
     * Scope active variations
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeWhereActive($query)
    {
        $query->where('status', 1);
    }

    /**
     * Get related values
     *
     * @return HasMany
     */
    public function values(): HasMany
    {
        return $this->hasMany(VariationValue::class, 'variation_id');
    }
}
