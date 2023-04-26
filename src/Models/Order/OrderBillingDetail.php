<?php

namespace Almooradi\FilamentEcommerce\Models\Order;

use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBillingDetail extends Model
{
	use SoftDeletes;

	protected $table = 'shop_orders_billing_details';

	protected $guarded = [];

	/**
	 * Get related country
	 *
	 * @return BelongsTo
	 */
	public function countryModel(): BelongsTo
	{
		return $this->belongsTo(Country::class, 'country', 'code'); // TODO: model and columns to be dynamic
	}

	/**
	 * Get related state
	 *
	 * @return BelongsTo
	 */
	public function stateModel(): BelongsTo
	{
		return $this->belongsTo(State::class, 'state', 'code'); // TODO: model and columns to be dynamic
	}
	/**
	 * Get related city
	 *
	 * @return BelongsTo
	 */
	public function cityModel(): BelongsTo
	{
		return $this->belongsTo(City::class, 'city', 'id'); // TODO: model and columns to be dynamic
	}
}
