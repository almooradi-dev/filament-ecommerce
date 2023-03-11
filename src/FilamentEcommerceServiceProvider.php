<?php

namespace Almooradi\FilamentEcommerce;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource;
use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource;
use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentEcommerceServiceProvider extends PluginServiceProvider
{
	protected array $resources = [
		ProductResource::class,
		CategoryResource::class,
		VariationResource::class,
	];

	public function configurePackage(Package $package): void
	{
		$package->name('filament-ecommerce');
	}

	/**
	 * Botstrap any package services
	 * 
	 * @return void
	 */
	// public function boot()
	// {
	// 	$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

	// 	$this->publishes([
	// 		__DIR__ . '/../assets' => public_path('assets/packages/vendor/filament-ecommerce'),
	// 	], 'public');
	// 	// php artisan vendor:publish --tag=public --force
	// }
}
