<?php

namespace Almooradi\FilamentEcommerce\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CategoryFactory extends Factory
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
            'is_active' => random_int(0, 1),
        ];
    }
}
