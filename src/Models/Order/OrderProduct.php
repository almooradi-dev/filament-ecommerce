<?php

namespace Almooradi\FilamentEcommerce\Models\Order;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
	use SoftDeletes;

	protected $table = 'shop_orders_products';

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
