<?php

namespace Almooradi\FilamentEcommerce\Services;

use Almooradi\FilamentEcommerce\Models\Cart;
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
	public function add(int  $productId, int $quantity = 1, string $cartKey = 'default'): bool
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

				session()->put('cart.' . $cartKey, $newItems);
			}
			
			// It logged in
			else {
				Cart::updateOrCreate(
					[
						'user_id' => auth()->id(),
						'product_id' => $productId,
						'key' => $cartKey
					],
					['quantity' => DB::raw('quantity + ' + $quantity)]
				);
			}
		} catch (Throwable $th) {
			return false;
		}

		return true;
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
}
