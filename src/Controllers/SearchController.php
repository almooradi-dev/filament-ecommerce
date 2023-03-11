<?php

namespace Almooradi\FilamentEcommerce\Controllers;

use Almooradi\FilamentEcommerce\Models\Product\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
	/**
	 * Get search results as JSON format (used for APIs)
	 *
	 * @param Request $request
	 * @return array
	 */
	public function json(Request $request): array
	{
		$results = $this->getSearchResults($request->search);

		return [
			'success' => true,
			'data' => $results
		];
	}

	/**
	 * Get search results as rendered HTML (used for APIs)
	 *
	 * @param Request $request
	 * @return array
	 */
	public function html(Request $request): array
	{
		$results = $this->getSearchResults($request->search);

		$html = view('components.search_input.list-items', ['items' => $results['products']])->render();

		return [
			'success' => true,
			'data' => $html
		];
	}

	/**
	 * Get search results
	 *
	 * @param string $value
	 * @return array
	 */
	public function getSearchResults(string $value): array
	{
		if (!$value) {
			return [];
		}

		$products = Product::query()
			->wherePublished()
			// ->with('categories') // TODO:
			->whereInContent($value)
			->orderByRelevance($value)
			->take(10)
			->get();
		// ->each(function ($product) {
		// 	return $product->convertCurrency()->calculateWithVat(); // TODO: multicurrency, vat
		// })

		$products = $products->map(function ($product) {
			$product['url'] = route('products.show', $product->slug);
			return $product;
		});

		// TODO:
		// $categories = Category::query()
		// 	->whereActive()
		// 	->where('title', 'like', '%' . $value . '%')
		// 	->get()
		// 	->sortBy('title');

		// TODO:
		// $categories = $categories->map(function ($category) {
		// 	$category['url'] = route('products', ['categories' => $category->id]);
		// 	return $category;
		// });

		return [
			'products'   => $products,
			// 'categories' => $categories
		];
	}
}
