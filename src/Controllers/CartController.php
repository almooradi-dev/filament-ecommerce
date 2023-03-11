<?php

namespace Almooradi\FilamentEcommerce\Controllers;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use Almooradi\FilamentEcommerce\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
	protected $cartService;

	function __construct(CartService $cartService)
	{
		$this->cartService = $cartService;

		if (!session()->has('cart')) {
			session()->put('cart', ['default' => []]);
		}
	}

	public function add(Request $request)
	{
		if ($this->cartService->add(1, 1)) {
			return response()->json([
				'success' => true,
				'message' => 'Product added to cart'
			]);
		}

		return response()->json([
			'error' => true,
			'message' => 'Something wrong happened'
		], 400);
	}

	public function empty(Request $request): JsonResponse
	{
		if ($this->cartService->empty()) {
			return response()->json([
				'success' => true,
				'message' => 'Cart has been successfully emptied'
			]);
		}

		return response()->json([
			'error' => true,
			'message' => 'Something wrong happened'
		], 400);
	}
}
