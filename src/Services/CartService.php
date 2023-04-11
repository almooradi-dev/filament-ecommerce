<?php

namespace Almooradi\FilamentEcommerce\Services;

use Almooradi\FilamentEcommerce\Models\Cart;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartService
{
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
		if (!($quantity > 0)) {
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
			// TODO: Log error
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
			// TODO: Log error
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
				return collect(session('cart.' . $cartKey));
			}

			return collect(session('cart'));
		} else {
			$cartItems = Cart::query() // TODO: use model relation trait
				->where('user_id', auth()->id())
				->when($cartKey, fn ($query) => $query->where('key', $cartKey))
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
	 * @return Collection|null
	 */
	private function getItem(int $productId, string $cartKey = 'default'): Collection|null
	{
		$items = collect(session('cart.' . $cartKey));
		// dd(session()->all());

		$item = $items->where('product_id', $productId)->first();

		return $item ? collect($item) : null;
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
}
