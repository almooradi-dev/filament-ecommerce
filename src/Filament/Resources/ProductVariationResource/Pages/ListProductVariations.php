<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Almooradi\FilamentEcommerce\Models\Product\Product;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListProductVariations extends ListRecords
{
    protected static string $resource = ProductVariationResource::class;

    protected function getSubheading(): string | Htmlable | null
    {
        $parentProduct = Product::findOrFail(static::$resource::getParentId());

        return 'Parent Product: ' . $parentProduct->title;
    }
    
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
