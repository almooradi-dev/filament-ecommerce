<?php

namespace Almooradi\FilamentEcommerce\Models\Product;

use Almooradi\FilamentEcommerce\Constants\Gender;
use Almooradi\FilamentEcommerce\Constants\ProductStatus;
use Almooradi\FilamentEcommerce\Constants\SortingOption;
use Almooradi\FilamentEcommerce\Models\Category;
use Almooradi\FilamentEcommerce\Models\Variation\Variation;
use Almooradi\FilamentEcommerce\Services\CartService;
use Almooradi\FilamentEcommerce\Traits\HasShowIn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductVariation extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'shop_products_variations';

	protected $guarded = [];
}
