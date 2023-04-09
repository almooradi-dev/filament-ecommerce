<?php

namespace Almooradi\FilamentEcommerce\Database\seeders;

use Almooradi\FilamentEcommerce\Constants\CategoryStatus;
use Almooradi\FilamentEcommerce\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Category::insert([
            [
                'title' => 'Soap',
                'slug' => 'soap',
                'short_description' => null,
                'image' => 'filament-ecommerce/categories/gK5S5XKn0V5SZh3crBHDhcYDl76lYc-metabmF0dXJhbC1nMGIxZTVmNWEyXzY0MC5qcGc=-.jpg',
                'status' => CategoryStatus::ACTIVE,
                'show_in' => '["home"]',
                'parent_category_id' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'title' => 'Skin Care',
                'slug' => 'skin-care',
                'short_description' => null,
                'image' => 'filament-ecommerce/categories/OThOuFUzaNZp8huOHqkAOv9eUoyKlL-metadG93ZmlxdS1iYXJiaHVpeWEtMEJFcUxmUXdWek0tdW5zcGxhc2guanBn-.jpg',
                'status' => CategoryStatus::ACTIVE,
                'show_in' => '["home"]',
                'parent_category_id' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'title' => 'Shampoo',
                'slug' => 'shampoo',
                'short_description' => null,
                'image' => 'filament-ecommerce/categories/lZ900phJMvA8uvHbg7lhYfcsTxLROm-metacG12LWNoYW1hcmEtT1hZT0ZUOWdUT0UtdW5zcGxhc2guanBn-.jpg',
                'status' => CategoryStatus::ACTIVE,
                'show_in' => '["home"]',
                'parent_category_id' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'title' => 'Body Soap',
                'slug' => 'body-soap',
                'short_description' => null,
                'image' => null,
                'status' => CategoryStatus::ACTIVE,
                'show_in' => '[]',
                'parent_category_id' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
        ]);
    }
}
