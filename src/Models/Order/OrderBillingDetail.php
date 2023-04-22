<?php

namespace Almooradi\FilamentEcommerce\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBillingDetail extends Model
{
	use SoftDeletes;

	protected $table = 'shop_orders_billing_details';

	protected $guarded = [];
}
