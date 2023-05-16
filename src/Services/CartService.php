<?php

namespace Almooradi\FilamentEcommerce\Services;

use Almooradi\FilamentEcommerce\Models\Cart;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CartService
{
	function __construct()
	{
		if (!session()->has('cart')) {
			session()->put('cart', ['default' => []]);
		}
	}

	/**
	 * Add to cart
	 *
	 * @param int $productId
	 * @param int $quantity
	 * @param string $cartKey
	 * @return bool
	 */
	public function add(int $productId, int $quantity = 1, string $cartKey = 'default'): bool
	{
		if (!($productId > 0) || !($quantity > 0)) {
			return false;
		}

		try {
			$currentProductItem = $this->getItem($productId, $cartKey);

			// If guest => save in the session
			if (auth()->guest()) {
				$items = collect(session('cart.' . $cartKey));
				$newItems = [];

				if (!$items) {
					return false;
				}
				if ($currentProductItem) {
					$newItems = $items->map(function ($item) use ($productId, $quantity) {
						if ($item['product_id'] == $productId) {
							$item['quantity'] += $quantity;
						}

						return $item;
					})->toArray();
				} else {
					$currentProductItem = collect([
						'product_id' => $productId,
						'quantity' => $quantity,
					]);

					$newItems = $items->push($currentProductItem)->toArray();
				}

				// Remove null items
				$newItems = array_filter($newItems);

				session()->put('cart.' . $cartKey, $newItems);
			}

			// It logged in
			else {
				$dbItem = Cart::firstOrCreate(
					[
						'user_id' => auth()->id(),
						'product_id' => $productId,
						'key' => $cartKey
					],
					['quantity' => 0]
				);
				$dbItem->increment('quantity', $quantity);
			}
		} catch (Throwable $th) {
			Log::error($th);

			return false;
		}

		return true;
	}

	/**
	 * Update cart item
	 *
	 * @param int $productId
	 * @param int $quantity
	 * @param string $cartKey
	 * @return bool
	 */
	public function update(int $productId, int $quantity, string $cartKey = 'default'): bool
	{
		if (!($productId > 0) || !($quantity > 0)) {
			return false;
		}

		try {
			$currentProductItem = $this->getItem($productId, $cartKey);
			if (!$currentProductItem) {
				return false;
			}

			// If guest => save in the session
			if (auth()->guest()) {
				$items = collect(session('cart.' . $cartKey));
				$newItems = [];

				if (!$items) {
					return false;
				}

				$newItems = $items->map(function ($item) use ($productId, $quantity) {
					if ($item['product_id'] == $productId) {
						$item['quantity'] += $quantity;
					}

					return $item;
				})->toArray();

				// Remove null items
				$newItems = array_filter($newItems);

				session()->put('cart.' . $cartKey, $newItems);
			}

			// It logged in
			else {
				$currentProductItem->update(['quantity' => $quantity]);
			}
		} catch (Throwable $th) {
			Log::error($th);

			return false;
		}

		return true;
	}

	/**
	 * Remove from cart
	 *
	 * @param int $productId
	 * @param string $cartKey
	 * @return bool
	 */
	public function remove(int $productId, string $cartKey = 'default'): bool
	{
		try {
			$currentProductItem = $this->getItem($productId, $cartKey);

			// If guest => remove from the session
			if (auth()->guest()) {
				$items = collect(session('cart.' . $cartKey) ?? []);
				$newItems = [];

				if ($currentProductItem) {
					$newItems = $items->map(function ($item) use ($productId) {
						if ($item['product_id'] != $productId) {
							return $item;
						}
					})->toArray();
				}

				// Remove null items
				$newItems = array_filter($newItems);

				session()->put('cart.' . $cartKey, $newItems);

				return true;
			}

			// It logged in
			else {
				return Cart::where([
					'user_id' => auth()->id(),
					'product_id' => $productId,
					'key' => $cartKey
				])->delete();
			}
		} catch (Throwable $th) {
			Log::error($th);

			return false;
		}

		return false;
	}

	/**
	 * Get all cart items
	 *
	 * @param string|null $cartKey
	 * @param boolean $forceSession
	 * @return Collection
	 */
	public function getAll(string|null $cartKey = 'default', bool $forceSession = false): Collection
	{
		if (auth()->guest() || $forceSession) {
			if ($cartKey) {
				$cartItems = collect(session('cart.' . $cartKey));

				$products = Product::with(['parentProduct', 'productVariationsValues.variationValue'])->find($cartItems->pluck('product_id')->toArray())->keyBy('id');
				$cartItems = $cartItems->map(function ($item) use ($products) {
					$item['product'] = $products[$item['product_id']] ?? null;

					return $item;
				});

				return $cartItems;

				// TODO: Add "groupBy('key')" like the one for logged in users
			}

			return collect(session('cart'));
		} else {
			$cartItems = Cart::query() // TODO: use model relation trait
				->where('user_id', auth()->id())
				->when($cartKey, fn ($query) => $query->where('key', $cartKey))
				->whereHas('product')
				->with(['product.parentProduct', 'product.productVariationsValues.variationValue'])
				->get();

			if (!$cartKey) {
				return $cartItems->groupBy('key');
			}

			return $cartItems;
		}
	}

	/**
	 * Get cart item by product ID
	 *
	 * @param int $productId
	 * @param string $cartKey
	 * @return Collection|Cart|null
	 */
	private function getItem(int $productId, string $cartKey = 'default'): Collection|Cart|null
	{
		$item = null;

		if (auth()->guest()) {
			$items = collect(session('cart.' . $cartKey));
			$item = $items->where('product_id', $productId)->first();
			$item = $item ? collect($item) : null;
		} else {
			$item = Cart::where([
				'user_id' => auth()->id(),
				'product_id' => $productId,
				'key' => $cartKey
			])->first();
		}

		return $item;
	}

	/**
	 * Empty cart items
	 *
	 * @param string $cartKey
	 * @return boolean
	 */
	public function empty(string $cartKey = 'default'): bool
	{
		try {
			session()->put('cart.' . $cartKey, []);

			if (auth()->user()) {
				Cart::where('user_id', auth()->id())->where('key', $cartKey)->delete();
			}
		} catch (Throwable $th) {
			Log::error($th);

			return false;
		}

		return true;
	}

	/**
	 * 
	 * 
	 * Must be used before "request()->session()->regenerate();"
	 *
	 * @param string $criteria add, keep_smallest, keep_largest, keep_session, keep_auth
	 * @return boolean
	 */
	public function syncGuestCartWithAuth($criteria = 'add'): bool
	{
		if (auth()->guest()) {
			return false;
		}

		// TODO: Set $criteria from client config file
		// TODO: Continue other criterias

		$sessionCarts = $this->getAll(null, true);

		if ($criteria == 'add') {
			foreach ($sessionCarts as $key => $cartItems) {
				foreach ($cartItems as $item) {
					if (!($item['quantity'] > 0)) {
						continue;
					}

					$dbItem = Cart::query()
						->where('key', $key)
						->where('user_id', auth()->id())
						->where('product_id', $item['product_id'])
						->first();

					if ($dbItem) {
						$dbItem->increment('quantity', $item['quantity']);
					} else {
						$dbItem = Cart::create([
							'key' => $key,
							'user_id' => auth()->id(),
							'product_id' => $item['product_id'],
							'quantity' => $item['quantity'],
						]);
					}

					// TODO: use insert and upsert
				}
			}
		} else if ($criteria == 'keep_auth') {
			return true;
		}

		return false;
	}

	/**
	 * Get cart item costs (shipping, subtotal, total)
	 *
	 * @param string $cartKey
	 * @return array
	 */
	public function getCosts(string $cartKey = 'default', $items = null): array
	{
		$items = $items ?? $this->getAll($cartKey);

		$shippingCost = env('SHOP_SHIPPING_COST', 0); // TODO: Remove fixed shipping rate and make it dynamic from admin panel
		$subtotal = 0;
		foreach ($items as $item) {
			$product = $item->product ?? Product::find($item->product_id);

			if (!$product) {
				continue;
			}

			$subtotal += $item->quantity * $product->final_unit_price;
		}

		return [
			'shipping' => $shippingCost,
			'subtotal' => $subtotal,
			'total' => $shippingCost + $subtotal
		];
	}
}
