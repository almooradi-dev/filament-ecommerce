<?php

namespace Almooradi\FilamentEcommerce\Models;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
	protected $table = 'shop_cart';

	protected $guarded = [];

	/**
	 * Get related product
	 *
	 * @return BelongsTo
	 */
	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'product_id');
	}
}
