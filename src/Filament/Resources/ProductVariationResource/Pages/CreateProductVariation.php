<?php

namespace Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource\Pages;

use Almooradi\FilamentEcommerce\Filament\Resources\ProductVariationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductVariation extends CreateRecord
{
    protected static string $resource = ProductVariationResource::class;

    protected function beforeCreate(): void
    // protected function afterCreate(): void
    {
        $selectedVariations = $this->data['variations'];
        dd($selectedVariations);
    }
}
