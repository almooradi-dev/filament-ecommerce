<?php

namespace Almooradi\FilamentEcommerce\Models\Order;

use Almooradi\FilamentEcommerce\Constants\Order\OrderShippingStatus;
use Almooradi\FilamentEcommerce\Constants\Order\OrderStatus;
use Almooradi\FilamentEcommerce\Constants\Order\PaymentMethod;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
	use SoftDeletes;

	protected $table = 'shop_orders';

	protected $guarded = [];

	/**
	 * Generate unique order number
	 *
	 * @return string
	 */
	public function generateOrderNumber(): string
	{
		$prefix = 'INV';
		$year = date("Y");
		$month = date("m");

		$ordersCount = Order::whereYear('created_at', $year)->count();

		$orderNumber = str_pad($ordersCount + 1, 8, '0', STR_PAD_LEFT);

		return $prefix . '-' . $year . $month . '-' . $orderNumber;
	}

	/**
	 * Get related billing details
	 *
	 * @return HasOne
	 */
	public function billingDetails(): HasOne
	{
		return $this->hasOne(OrderBillingDetail::class);
	}

	/**
	 * Get related order products details
	 *
	 * @return HasMany
	 */
	public function orderProducts(): HasMany
	{
		return $this->hasMany(OrderProduct::class, 'order_id');
	}

	/**
	 * Get related products
	 *
	 * @return HasManyThrough
	 */
	public function products(): HasManyThrough
	{
		return $this->hasManyThrough(Product::class, OrderProduct::class, 'order_id', 'id', 'id', 'product_id');
	}

	/**
	 * Get "Payment Method Label" Attribute
	 *
	 * @return string
	 */
	public function getPaymentMethodLabelAttribute(): string
	{
		return PaymentMethod::ALL[$this->payment_method] ?? '';
	}

	/**
	 * Get "Status Label" Attribute
	 *
	 * @return string
	 */
	public function getStatusLabelAttribute(): string
	{
		return OrderStatus::ALL[$this->status] ?? '';
	}

	/**
	 * Get "Shipping Status Label" Attribute
	 *
	 * @return string
	 */
	public function getShippingStatusLabelAttribute(): string
	{
		return OrderShippingStatus::ALL[$this->shipping_status] ?? '';
	}
}
