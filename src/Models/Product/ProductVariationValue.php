<?php

namespace Almooradi\FilamentEcommerce\Models\Product;

use Almooradi\FilamentEcommerce\Models\Variation\VariationValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariationValue extends Model
{
	use HasFactory;

	protected $table = 'shop_products_variations_values';

	protected $guarded = [];

	/**
	 * Get related variation value
	 *
	 * @return BelongsTo
	 */
	public function variationValue(): BelongsTo
	{
		return $this->belongsTo(VariationValue::class, 'variation_value_id');
	}
}
