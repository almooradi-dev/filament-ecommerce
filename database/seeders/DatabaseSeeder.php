<?php

namespace Almooradi\FilamentEcommerce\Database\seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ProductCategorySeeder::class,
        ]);
    }
}
