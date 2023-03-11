<?php

namespace Almooradi\FilamentEcommerce\Models\Variation;

use Almooradi\FilamentEcommerce\Traits\HasShowIn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shop_variations_values';

    protected $guarded = [];

	/**
     * Get related variation
     *
     * @return BelongsTo
     */
    public function values(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
