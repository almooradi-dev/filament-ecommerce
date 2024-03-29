<?php

namespace Almooradi\FilamentEcommerce\Models\Product;

use Almooradi\FilamentEcommerce\Models\Variation\VariationValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
	use HasFactory;

	protected $table = 'shop_products_variations';

	protected $guarded = [];
}
