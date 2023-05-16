<?php

namespace Almooradi\FilamentEcommerce\Models\Order;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProductVariationValue extends Model
{
	use SoftDeletes;

	protected $table = 'shop_orders_products_variations_values';

	protected $guarded = [];
}
