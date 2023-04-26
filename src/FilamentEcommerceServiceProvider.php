<?php

namespace Almooradi\FilamentEcommerce;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductResource;
use Almooradi\FilamentEcommerce\Filament\Resources\CategoryResource;
use Almooradi\FilamentEcommerce\Filament\Resources\Order\OrderResource;
use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Almooradi\FilamentEcommerce\Filament\Resources\VariationResource;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentEcommerceServiceProvider extends PluginServiceProvider
{
	protected array $resources = [
		ProductResource::class,
		CategoryResource::class,
		VariationResource::class,
		ProductVariationResource::class,
		OrderResource::class,
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
	public function boot()
	{
		parent::packageBooted();

		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-ecommerce');

		$this->publishes([
			__DIR__ . '/../assets' => public_path('assets/packages/vendor/filament-ecommerce'),
		], 'public');
		// 	// php artisan vendor:publish --tag=public --force
	}
}
