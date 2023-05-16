<?php

namespace Almooradi\FilamentEcommerce\Models\Order;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

	/**
	 * Get related product variations values
	 *
	 * @return hasMany
	 */
	public function variationsValues(): HasMany
	{
		return $this->hasMany(OrderProductVariationValue::class, 'order_product_id');
	}
}
