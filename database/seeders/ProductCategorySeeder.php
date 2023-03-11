<?php

namespace Almooradi\FilamentEcommerce\Database\seeders;

use Almooradi\FilamentEcommerce\Models\Category;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $productsIds = Product::all()->pluck('id')->toArray();
        $categoriesIds = Category::all()->pluck('id')->toArray();

        $productsCategories = [];
        foreach ($productsIds as $productId) {
            $productsCategories[] = [
                'product_id' => $productId,
                'category_id' => $categoriesIds[random_int(0, count($categoriesIds) - 1)],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ];
        }

        DB::table('shop_products_categories')->insert($productsCategories);
    }
}
