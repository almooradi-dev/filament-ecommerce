<?php

namespace Almooradi\FilamentEcommerce\Controllers;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use Almooradi\FilamentEcommerce\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
	protected $cartService;

	function __construct(CartService $cartService)
	{
		$this->cartService = $cartService;
	}

	/**
	 * Get cart items
	 *
	 * @return array|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function index(Request $request): array|View|Factory
	{
		$cartItems = $this->cartService->getAll();

		if ($request->ajax()) {
			return [
				'success' => true,
				'data' => $cartItems
			];
		}

		$data['items'] = $cartItems;

		return view('web.frontend.sections.cart.index', $data); // TODO: Put the view path in the config file
	}

	/**
	 * Add to cart item
	 *
	 * @return JsonResponse
	 */
	public function add(Request $request, Product $product): JsonResponse
	{
		if ($this->cartService->add($product->id, $request->quantity)) {
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

	/**
	 * Update cart item
	 *
	 * @return JsonResponse
	 */
	public function update(Request $request, Product $product): JsonResponse
	{
		if ($this->cartService->update($product->id, $request->quantity)) {
			return response()->json([
				'success' => true,
				'message' => 'Cart updated'
			]);
		}

		return response()->json([
			'error' => true,
			'message' => 'Something wrong happened'
		], 400);
	}

	/**
	 * Remove cart item
	 *
	 * @return JsonResponse
	 */
	public function remove(Product $product): JsonResponse
	{
		if ($this->cartService->remove($product->id)) {
			return response()->json([
				'success' => true,
				'message' => 'Product removed from the cart'
			]);
		}

		return response()->json([
			'error' => true,
			'message' => 'Something wrong happened'
		], 400);
	}

	/**
	 * Empty cart
	 *
	 * @return JsonResponse
	 */
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
