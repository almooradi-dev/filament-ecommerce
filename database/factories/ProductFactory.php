<?php

namespace Almooradi\FilamentEcommerce\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->title();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'show_in' => ['home'],
            'status' => 'active', // TODO: constants
            'media_files' => [fake()->image('public/storage/products', 640, 480, null, false)]
        ];
    }
}
