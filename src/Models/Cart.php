<?php

namespace Almooradi\FilamentEcommerce\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
	protected $table = 'shop_cart';

	protected $primaryKey = null;
	public $incrementing = false;

	protected $guarded = [];
}
